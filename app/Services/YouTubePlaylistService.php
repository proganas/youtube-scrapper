<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class YouTubePlaylistService
{
    public function searchPlaylists(string $query): array
    {
        $apiKey = env('YOUTUBE_API_KEY');

        $response = Http::timeout(60)->get('https://www.googleapis.com/youtube/v3/search', [
            'part' => 'snippet',
            'q' => $query,
            'type' => 'playlist',
            'maxResults' => 2,
            'key' => $apiKey,
        ]);

        if (!$response->successful()) {
            return [];
        }

        $items = $response->json('items', []);

        return collect($items)->map(function ($item) {
            return [
                'playlist_id' => $item['id']['playlistId'] ?? null,
                'title' => $item['snippet']['title'] ?? '',
                'description' => $item['snippet']['description'] ?? '',
                'thumbnail' => $item['snippet']['thumbnails']['high']['url']
                    ?? $item['snippet']['thumbnails']['medium']['url']
                        ?? $item['snippet']['thumbnails']['default']['url']
                        ?? null,
                'channel_name' => $item['snippet']['channelTitle'] ?? '',
            ];
        })->filter(fn($playlist) => !empty($playlist['playlist_id']))
            ->values()
            ->toArray();
    }
}
