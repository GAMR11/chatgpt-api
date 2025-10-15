<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GeminiController extends Controller
{
    private $model = 'gemini-2.0-flash-exp';

    public function chat(Request $request)
    {
        $request->validate(['message' => 'required|string|max:5000']);
        
        $apiKey = env('GEMINI_API_KEY');
        
        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'error' => 'API Key no configurada'
            ], 500);
        }
        
        try {
            $response = Http::timeout(30)->post(
                "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$apiKey}",
                [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $request->message]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'maxOutputTokens' => 2048,
                    ]
                ]
            );

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    return response()->json([
                        'success' => true,
                        'response' => $data['candidates'][0]['content']['parts'][0]['text'],
                        'model' => $this->model
                    ]);
                }
                
                return response()->json([
                    'success' => false,
                    'error' => 'No se recibió respuesta del modelo',
                    'data' => $data
                ], 500);
            }

            return response()->json([
                'success' => false,
                'error' => 'Error al comunicarse con Gemini',
                'status' => $response->status(),
                'details' => $response->json()
            ], $response->status());
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Excepción capturada',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}