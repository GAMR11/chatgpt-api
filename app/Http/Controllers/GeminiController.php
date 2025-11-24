<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiController extends Controller
{
    private string $model = 'gemini-2.5-flash-lite';
    private string $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models';

    public function chat(Request $request)
    {
        // Validación de entrada
        $validated = $request->validate([
            'message' => 'required|string|max:5000',
            'temperature' => 'nullable|numeric|min:0|max:2',
            'maxTokens' => 'nullable|integer|min:1|max:8192',
        ]);
        
        // Obtener API Key desde config en lugar de env directo
        $apiKey = config('services.gemini.api_key');
        
        if (!$apiKey) {
            Log::error('Gemini API Key no configurada');
            return response()->json([
                'success' => false,
                'error' => 'Servicio no disponible. Contacta al administrador.'
            ], 503);
        }
        
        try {
            $response = Http::timeout(30)
                ->retry(2, 100) // Reintenta 2 veces si falla
                ->post(
                    "{$this->apiUrl}/{$this->model}:generateContent?key={$apiKey}",
                    [
                        'contents' => [
                            [
                                'parts' => [
                                    ['text' => $validated['message']]
                                ]
                            ]
                        ],
                        'generationConfig' => [
                            'temperature' => $validated['temperature'] ?? 0.7,
                            'maxOutputTokens' => $validated['maxTokens'] ?? 2048,
                        ],
                        'safetySettings' => [
                            [
                                'category' => 'HARM_CATEGORY_HARASSMENT',
                                'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                            ],
                            [
                                'category' => 'HARM_CATEGORY_HATE_SPEECH',
                                'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                            ]
                        ]
                    ]
                );

            if ($response->successful()) {
                $data = $response->json();
                
                // Verificar si hay contenido en la respuesta
                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    return response()->json([
                        'success' => true,
                        'data' => [
                            'message' => $data['candidates'][0]['content']['parts'][0]['text'],
                            'model' => $this->model,
                        ]
                    ], 200);
                }
                
                // Si el contenido fue bloqueado por seguridad
                if (isset($data['candidates'][0]['finishReason'])) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Contenido bloqueado por políticas de seguridad',
                        'reason' => $data['candidates'][0]['finishReason']
                    ], 400);
                }
                
                Log::warning('Respuesta sin contenido de Gemini', ['data' => $data]);
                return response()->json([
                    'success' => false,
                    'error' => 'No se recibió respuesta válida del modelo'
                ], 500);
            }

            // Manejo de errores específicos de la API
            $errorData = $response->json();
            $errorMessage = $errorData['error']['message'] ?? 'Error desconocido';
            
            Log::error('Error de Gemini API', [
                'status' => $response->status(),
                'error' => $errorMessage
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Error al procesar la solicitud',
                'message' => $errorMessage
            ], $response->status());
            
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Error de conexión con Gemini', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'error' => 'Error de conexión con el servicio de IA'
            ], 503);
            
        } catch (\Exception $e) {
            Log::error('Excepción en Gemini chat', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Endpoint para verificar el estado del servicio
     */
    public function healthCheck()
    {
        $apiKey = config('services.gemini.api_key');
        
        return response()->json([
            'status' => 'ok',
            'service' => 'gemini',
            'model' => $this->model,
            'api_key_configured' => !empty($apiKey)
        ], 200);
    }
}