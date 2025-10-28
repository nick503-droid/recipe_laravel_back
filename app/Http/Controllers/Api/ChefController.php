<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChefController extends Controller
{
    public function handle(Request $request)
    {
        // 1. AHORA RECIBIMOS UN HISTORIAL COMPLETO
        $history = $request->input('history', []);
        $apiKey = config('services.gemini.key');
        
        if (!$apiKey) {
            return response()->json(['reply' => 'Error: La API Key de Gemini no está configurada.'], 500);
        }

        // El System Prompt define la personalidad y las reglas generales
        $systemPrompt = "
        Eres 'SaborIA', un chef experto y amigable.
        TUS REGLAS:
        1. Idioma: Detecta el idioma del usuario y responde SIEMPRE en ese mismo idioma.
        2. Formato: Usa formato Markdown para tus respuestas.
        3. Foco Principal: Tu especialidad es la cocina. Prioriza hablar de recetas, ingredientes y técnicas culinarias.
        4. Flexibilidad: Si el usuario pregunta sobre algo que no es cocina, puedes responder brevemente, pero siempre intenta reorientar la conversación amablemente hacia la comida.";
        
        // 2. CONVERTIMOS EL HISTORIAL DE VUE AL FORMATO DE GEMINI
        $contents = [];
        foreach ($history as $message) {
            // Mapeamos 'bot' a 'model' y 'user' a 'user'
            $role = ($message['author'] === 'bot') ? 'model' : 'user';
            
            // La primera vez que habla el usuario, le añadimos el System Prompt
            if ($role === 'user' && empty($contents)) {
                 $contents[] = ['role' => 'user', 'parts' => [['text' => $systemPrompt . "\n\nUsuario: " . $message['text']]]];
            } else {
                 $contents[] = ['role' => $role, 'parts' => [['text' => $message['text']]]];
            }
        }
        
        $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-001:generateContent?key=' . $apiKey;

        // 3. ENVIAMOS EL HISTORIAL FORMATEADO A GEMINI
        $response = Http::post($apiUrl, [
            'contents' => $contents,
        ]);

        if ($response->failed()) {
            return response()->json(['reply' => 'Error de Gemini.', 'error_details' => $response->json()], 500);
        }
        
        $reply = $response->json('candidates.0.content.parts.0.text', 'No supe qué decir a eso, ¡pero hablemos de comida!');

        return response()->json(['reply' => $reply]);
    }
}