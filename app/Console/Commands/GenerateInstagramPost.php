<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class GenerateInstagramPost extends Command
{
    protected $signature = 'app:generate-instagram-post';
    protected $description = 'Genera imagen + caption para Instagram usando Gemini y manda por mail';

    private string $imageStyleBase = 'Professional sports marketing image for Instagram. Moody but not too dark atmosphere, dramatic cinematic lighting with green and blue-teal tones. Some areas can be lighter to show the sports equipment clearly. Shallow depth of field. Subtle dark gradient at the top for text readability. The text "tucancha.com.ar" appears small in the bottom right corner in white. Vertical 4:5 portrait aspect ratio. Premium sports brand aesthetic, magazine-quality photography. No people visible. Text must use Sora Bold font style, all lowercase except first letter of sentences. The highlighted keyword must be in vivid medium green (similar to emerald or TuCancha brand green, NOT dark green, NOT lime, NOT neon). Other text is white. NEVER write hex color codes or hashtag symbols in the image.';

    private array $themes = [
        [
            'topic' => 'tip de reserva',
            'image_scene' => 'A worn white soccer ball in sharp focus in the foreground resting on dark green synthetic turf at night. Two more soccer balls blurred in the background. LED floodlights creating dramatic rim lighting on the ball.',
            'phrase_prompt' => 'Genera una frase publicitaria corta y contundente (maximo 6 palabras) sobre reservar canchas online o no perder tu turno. Puede ser de cualquier deporte: futbol, padel, tenis, basquet. Estilo directo, argentino. Todo en minuscula excepto la primera letra. Tambien genera un subtitulo de maximo 8 palabras que complemente la frase. Responde SOLO en este formato exacto sin nada mas:\nFRASE: [la frase]\nPALABRA_VERDE: [una palabra clave de la frase para resaltar en verde]\nSUBTITULO: [el subtitulo]',
            'caption_prompt' => 'Escribi un tip corto para jugadores de futbol amateur sobre como reservar cancha de forma mas inteligente (horarios baratos, reservar con anticipacion, etc). Tono argentino informal con voseo. Maximo 150 palabras. Sin emojis. No uses comillas.',
        ],
        [
            'topic' => 'motivacion deportiva',
            'image_scene' => 'Multiple soccer balls scattered on dark green synthetic turf at night, one ball in sharp focus close to camera, others softly blurred behind. A smartphone lying on the turf between the balls. Stadium floodlights creating green-tinted atmosphere.',
            'phrase_prompt' => 'Genera una frase publicitaria corta y contundente (maximo 6 palabras) sobre la pasion por el futbol amateur, juntarse a jugar con amigos, o no dejar de jugar. Estilo directo, argentino. Tambien genera un subtitulo de maximo 8 palabras. Responde SOLO en este formato exacto sin nada mas:\nFRASE: [la frase]\nPALABRA_VERDE: [una palabra clave de la frase para resaltar en verde]\nSUBTITULO: [el subtitulo]',
            'caption_prompt' => 'Escribi un mensaje motivacional corto para jugadores de futbol amateur argentinos. Algo sobre la importancia de juntarse a jugar, el valor de la amistad en la cancha, o por que el futbol con amigos es lo mejor. Tono argentino informal con voseo. Maximo 100 palabras. Sin emojis. No uses comillas.',
        ],
        [
            'topic' => 'padel tips',
            'image_scene' => 'A padel racket lying diagonally on dark blue padel court surface, a yellow padel ball nearby, white court line visible. Dramatic overhead lighting creating strong shadows. Deep blue and teal color palette.',
            'phrase_prompt' => 'Genera una frase publicitaria corta y contundente (maximo 6 palabras) sobre padel, mejorar tu juego, o reservar cancha de padel. Estilo directo, argentino. Tambien genera un subtitulo de maximo 8 palabras. Responde SOLO en este formato exacto sin nada mas:\nFRASE: [la frase]\nPALABRA_VERDE: [una palabra clave de la frase para resaltar en verde]\nSUBTITULO: [el subtitulo]',
            'caption_prompt' => 'Escribi un tip corto sobre padel para jugadores principiantes o intermedios en Argentina. Puede ser sobre tecnica, estrategia, como elegir categoria, o cuando jugar. Tono argentino informal con voseo. Maximo 150 palabras. Sin emojis. No uses comillas.',
        ],
        [
            'topic' => 'falta uno',
            'image_scene' => 'Close-up of colorful sport bibs (pecheras) in green and orange draped over a metal fence, with a blurry synthetic soccer field and floodlights in the background at night. Dark moody atmosphere with green tint.',
            'phrase_prompt' => 'Genera una frase publicitaria corta y contundente (maximo 6 palabras) sobre el problema de que siempre falta un jugador para completar el equipo, o sobre encontrar jugadores. Estilo directo, argentino, puede tener humor. Tambien genera un subtitulo de maximo 8 palabras. Responde SOLO en este formato exacto sin nada mas:\nFRASE: [la frase]\nPALABRA_VERDE: [una palabra clave de la frase para resaltar en verde]\nSUBTITULO: [el subtitulo]',
            'caption_prompt' => 'Escribi un post corto sobre el problema de siempre faltar uno para completar el equipo de futbol. Menciona que TuCancha tiene una funcion llamada Falta Uno donde publicas tu partido y se suman jugadores de la zona. Tono argentino informal con voseo, con humor. Maximo 120 palabras. Sin emojis. No uses comillas.',
        ],
        [
            'topic' => 'tenis',
            'image_scene' => 'A tennis racket lying on dark green-blue court surface with three yellow tennis balls scattered around it. Dramatic low-angle lighting, shallow depth of field. Dark teal and green color palette.',
            'phrase_prompt' => 'Genera una frase publicitaria corta y contundente (maximo 6 palabras) sobre tenis amateur, mejorar tu juego, o reservar cancha de tenis. Estilo directo, argentino. Tambien genera un subtitulo de maximo 8 palabras. Responde SOLO en este formato exacto sin nada mas:\nFRASE: [la frase]\nPALABRA_VERDE: [una palabra clave de la frase para resaltar en verde]\nSUBTITULO: [el subtitulo]',
            'caption_prompt' => 'Escribi un tip o reflexion corta sobre tenis amateur en Argentina. Puede ser sobre mejorar el juego, encontrar rivales de tu nivel, o la importancia de jugar seguido. Tono argentino informal con voseo. Maximo 120 palabras. Sin emojis. No uses comillas.',
        ],
        [
            'topic' => 'gestion de complejo',
            'image_scene' => 'A dark synthetic soccer field at night shot from ground level, multiple soccer balls in the foreground and background at different depths of field. Green LED floodlights illuminating the scene from above. Moody cinematic atmosphere.',
            'phrase_prompt' => 'Genera una frase publicitaria corta y contundente (maximo 6 palabras) dirigida a duenos de complejos deportivos sobre la importancia de tener un sistema digital, dejar de perder reservas, o modernizar la gestion. Estilo directo, argentino. Tambien genera un subtitulo de maximo 8 palabras. Responde SOLO en este formato exacto sin nada mas:\nFRASE: [la frase]\nPALABRA_VERDE: [una palabra clave de la frase para resaltar en verde]\nSUBTITULO: [el subtitulo]',
            'caption_prompt' => 'Escribi un post corto dirigido a duenos de complejos deportivos sobre la importancia de tener un sistema de reservas online. Menciona beneficios como cobros automaticos, agenda digital, y visibilidad. Tono profesional pero cercano, argentino con voseo. Maximo 130 palabras. Sin emojis. No uses comillas.',
        ],
        [
            'topic' => 'reserva online',
            'image_scene' => 'A soccer ball and a smartphone side by side on dark synthetic turf at night, the phone screen glowing faintly. Dramatic green-tinted stadium lighting from above. Shallow depth of field with the ball in sharp focus.',
            'phrase_prompt' => 'Genera una frase publicitaria corta y contundente (maximo 6 palabras) sobre lo facil que deberia ser reservar una cancha, o sobre dejar de depender de WhatsApp y llamadas. Estilo directo, argentino. Tambien genera un subtitulo de maximo 8 palabras. Responde SOLO en este formato exacto sin nada mas:\nFRASE: [la frase]\nPALABRA_VERDE: [una palabra clave de la frase para resaltar en verde]\nSUBTITULO: [el subtitulo]',
            'caption_prompt' => 'Escribi un post corto sobre lo facil que es reservar una cancha online vs la forma tradicional (llamar, mandar WhatsApp, esperar respuesta). Menciona TuCancha como solucion. Tono argentino informal con voseo. Maximo 120 palabras. Sin emojis. No uses comillas.',
        ],
    ];

    public function handle(): void
    {
        $apiKey = config('services.gemini.api_key');
        if (!$apiKey) {
            $this->error('GEMINI_API_KEY no configurada.');
            return;
        }

        // Elegir tema aleatorio
        $theme = $this->themes[array_rand($this->themes)];
        $this->info("Tema: {$theme['topic']}");

        // 1. Generar frase para la imagen
        $this->info('Generando frase...');
        $phraseRaw = $this->generateCaption($apiKey, $theme['phrase_prompt']);
        if (!$phraseRaw) {
            $this->error('Error generando frase.');
            return;
        }

        // Parsear frase, palabra verde y subtitulo
        $frase = '';
        $palabraVerde = '';
        $subtitulo = '';
        foreach (explode("\n", $phraseRaw) as $line) {
            $line = trim($line);
            if (str_starts_with($line, 'FRASE:')) $frase = trim(str_replace('FRASE:', '', $line));
            if (str_starts_with($line, 'PALABRA_VERDE:')) $palabraVerde = trim(str_replace('PALABRA_VERDE:', '', $line));
            if (str_starts_with($line, 'SUBTITULO:')) $subtitulo = trim(str_replace('SUBTITULO:', '', $line));
        }

        if (!$frase) {
            $this->error('No se pudo parsear la frase: ' . $phraseRaw);
            return;
        }

        $this->info("Frase: {$frase} (verde: {$palabraVerde})");
        $this->info("Subtitulo: {$subtitulo}");

        // 2. Generar caption con Gemini
        $this->info('Generando caption...');
        $caption = $this->generateCaption($apiKey, $theme['caption_prompt']);
        if (!$caption) {
            $this->error('Error generando caption.');
            return;
        }

        $caption .= "\n\nReserva tu cancha en tucancha.com.ar\n\n#TuCancha #DeportesArgentina #FutbolArgentina #Padel #Tenis #CanchasDeFutbol #FutbolAmateur #ReservasOnline";

        // 3. Generar imagen con la frase inyectada
        $this->info('Generando imagen...');
        $textOverlay = 'Medium-sized bold white text in the upper left area, all lowercase except first letter, reading "' . $frase . '" with the word "' . $palabraVerde . '" in vivid medium green color (the green used by TuCancha brand, similar to emerald green). Smaller gray italic text below reading: "' . $subtitulo . '". The text should be proportional to the image, not too large, not too small. Clean and elegant typography. Do NOT write any hex color codes or hashtags in the image.';
        $fullImagePrompt = $this->imageStyleBase . ' ' . $theme['image_scene'] . ' ' . $textOverlay;
        $imagePath = $this->generateImage($apiKey, $fullImagePrompt);
        if (!$imagePath) {
            $this->error('Error generando imagen.');
            return;
        }

        // 3. Guardar en Google Sheets via n8n webhook
        $webhookUrl = config('services.n8n.instagram_webhook');
        if ($webhookUrl) {
            try {
                Http::withOptions(['verify' => app()->isProduction()])->timeout(10)->post($webhookUrl, [
                    'fecha' => now()->format('d/m/Y H:i'),
                    'tema' => $theme['topic'],
                    'frase' => $frase,
                    'subtitulo' => $subtitulo,
                    'caption' => $caption,
                    'imagen_url' => url('/storage/' . $imagePath),
                ]);
                $this->info('Guardado en Google Sheets.');
            } catch (\Throwable $e) {
                $this->warn('No se pudo guardar en Sheets: ' . $e->getMessage());
            }
        }

        $this->info('Post de Instagram generado y enviado por mail.');
    }

    private function generateCaption(string $apiKey, string $prompt): ?string
    {
        try {
            $response = Http::withOptions(['verify' => app()->isProduction()])->timeout(30)->post(
                "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}",
                [
                    'contents' => [
                        ['parts' => [['text' => $prompt]]]
                    ],
                ]
            );

            $data = $response->json();
            return $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
        } catch (\Throwable $e) {
            $this->error('Gemini caption error: ' . $e->getMessage());
            return null;
        }
    }

    private function generateImage(string $apiKey, string $prompt): ?string
    {
        try {
            $response = Http::withOptions(['verify' => app()->isProduction()])->timeout(60)->post(
                "https://generativelanguage.googleapis.com/v1beta/models/imagen-4.0-generate-001:predict?key={$apiKey}",
                [
                    'instances' => [
                        ['prompt' => $prompt]
                    ],
                    'parameters' => [
                        'sampleCount' => 1,
                        'aspectRatio' => '3:4',
                    ],
                ]
            );

            $data = $response->json();

            if (isset($data['predictions'][0]['bytesBase64Encoded'])) {
                $imageData = base64_decode($data['predictions'][0]['bytesBase64Encoded']);
                $filename = 'instagram/ig-' . date('Y-m-d-His') . '.png';
                Storage::disk('public')->put($filename, $imageData);
                return $filename;
            }

            // Fallback: probar con Gemini 2.5 Flash que también genera imágenes
            $response2 = Http::withOptions(['verify' => app()->isProduction()])->timeout(60)->post(
                "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}",
                [
                    'contents' => [
                        ['parts' => [['text' => "Generate an image: {$prompt}"]]]
                    ],
                    'generationConfig' => [
                        'responseModalities' => ['IMAGE', 'TEXT'],
                    ],
                ]
            );

            $data2 = $response2->json();
            $parts = $data2['candidates'][0]['content']['parts'] ?? [];
            foreach ($parts as $part) {
                if (isset($part['inlineData'])) {
                    $imageData = base64_decode($part['inlineData']['data']);
                    $filename = 'instagram/ig-' . date('Y-m-d-His') . '.png';
                    Storage::disk('public')->put($filename, $imageData);
                    return $filename;
                }
            }

            $this->error('No image data in response: ' . json_encode($data));
            return null;
        } catch (\Throwable $e) {
            $this->error('Gemini image error: ' . $e->getMessage());
            return null;
        }
    }
}
