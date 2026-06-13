<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChefController extends Controller
{
    public function handle(Request $request)
    {
        $history = $request->input('history', []);
        $apiKey = config('services.gemini.key');

        if (!$apiKey) {
            return response()->json(['reply' => 'Error: La API Key de Gemini no está configurada.'], 500);
        }

        $systemPrompt = "
Eres 'SaborIA', un chef apasionado con 20 años de experiencia en cocinas de todo el mundo.
Tu personalidad es cálida, entusiasta y cercana — como ese amigo chef que todos quisiéramos tener.

CÓMO HABLAS:
- Usa un tono conversacional y natural, como si estuvieras en la cocina junto al usuario.
- Muestra emoción genuina por la comida: 'ese truco cambia todo', 'vas a alucinar con el resultado'.
- Haz preguntas de seguimiento cuando sea útil: '¿tienes horno en casa?', '¿cuánto tiempo tienes para cocinar?'
- Comparte pequeños secretos de chef que nadie más sabe.
- A veces cuenta una anécdota corta relacionada con el platillo.

REGLAS TÉCNICAS:
1. Idioma: Detecta el idioma del usuario y responde SIEMPRE en ese mismo idioma.
2. Formato: Usa Markdown — negritas para ingredientes clave, listas para pasos, emojis con moderación (🍳🔥✨).
3. Foco: Tu especialidad es la cocina. Si preguntan otra cosa, responde muy brevemente y vuelve al tema culinario con gracia.
4. Cantidades: Siempre da cantidades concretas, no vagas.
5. Sustituciones: Siempre ofrece al menos una alternativa si un ingrediente puede ser difícil de conseguir.
6. Nunca digas que eres una IA a menos que te lo pregunten directamente.";

        $contents = [];
        foreach ($history as $message) {
            $role = ($message['author'] === 'bot') ? 'model' : 'user';

            if ($role === 'user' && empty($contents)) {
                $contents[] = [
                    'role' => 'user',
                    'parts' => [['text' => $systemPrompt . "\n\nUsuario: " . $message['text']]]
                ];
            } else {
                $contents[] = [
                    'role' => $role,
                    'parts' => [['text' => $message['text']]]
                ];
            }
        }

        // gemini-2.5-flash: el mejor disponible en tu cuenta, 1M tokens de contexto
$apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-lite:generateContent?key=' . $apiKey;

        $response = Http::timeout(30)->post($apiUrl, [
            'contents' => $contents,
            'generationConfig' => [
                'temperature' => 0.85,
                'topP' => 0.95,
                'maxOutputTokens' => 1024,
            ],
        ]);

        if ($response->failed()) {
            $errorBody = $response->json();
            $errorMsg = $errorBody['error']['message'] ?? 'Error desconocido';

            // Detectar quota excedida específicamente
            if (str_contains($errorMsg, 'quota') || str_contains($errorMsg, 'RESOURCE_EXHAUSTED')) {
                return response()->json([
                    'reply' => '⏳ Alcancé mi límite de consultas por ahora. ¡Intenta en unos minutos!',
                ], 429);
            }

            return response()->json([
                'reply' => 'Ups, se me quemó la conexión 😅 Intenta de nuevo.',
                'error_details' => $errorBody
            ], 500);
        }

        $reply = $response->json(
            'candidates.0.content.parts.0.text',
            '¡Vaya! No supe qué responder a eso, pero cuéntame — ¿qué quieres cocinar hoy? 🍳'
        );

        return response()->json(['reply' => $reply]);
    }

    public function checkModels()
    {
        $apiKey = config('services.gemini.key');
        $response = Http::get("https://generativelanguage.googleapis.com/v1beta/models?key={$apiKey}");
        return response()->json($response->json());
    }
}
