<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AiCourseTitleService
{
    public function generateTitles(string $category): array
    {
        $apiKey = env('OPENAI_API_KEY');

        if (!$apiKey) {
            return $this->fallbackTitles($category);
        }

        $prompt = "Generate 10 educational YouTube course titles for the category: {$category}.
Return only a plain list, one title per line, without numbering or explanations.";

        try {
            $response = Http::withToken($apiKey)
                ->timeout(60)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You generate clean educational course titles for YouTube playlist discovery.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'temperature' => 0.7,
                ]);

            if (!$response->successful()) {
                return $this->fallbackTitles($category);
            }

            $content = $response->json('choices.0.message.content', '');

            $titles = array_filter(array_map(function ($line) {
                $line = trim(preg_replace('/^\d+[\).\-\s]*/', '', $line));
                $line = trim($line, "\"'•- ");
                return $line;
            }, explode("\n", $content)));

            if (empty($titles)) {
                return $this->fallbackTitles($category);
            }

            return array_slice($titles, 0, 10);
        } catch (\Exception $e) {
            return $this->fallbackTitles($category);
        }
    }

    private function fallbackTitles(string $category): array
    {
        session()->flash('warning', "AI service unavailable for '{$category}', showing fallback titles.");
        return [
            "{$category} Full Course",
            "{$category} for Beginners",
            "Complete {$category} Course",
            "{$category} Masterclass",
            "Learn {$category} Step by Step",
            "{$category} Bootcamp",
            "{$category} Fundamentals",
            "Advanced {$category} Course",
            "{$category} Training Program",
            "{$category} Crash Course",
        ];
    }
}
