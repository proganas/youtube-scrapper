<?php

namespace App\Http\Controllers;

use App\Models\Playlist;
use App\Services\AiCourseTitleService;
use App\Services\YouTubePlaylistService;
use Illuminate\Http\Request;

class PlaylistController extends Controller
{
    protected $aiService;
    protected $youtubeService;

    public function __construct(
        AiCourseTitleService $aiService,
        YouTubePlaylistService $youtubeService
    ) {
        $this->aiService = $aiService;
        $this->youtubeService = $youtubeService;
    }

    public function index()
    {
        $playlists = Playlist::latest()->paginate(9);

        return view('playlists.index', compact('playlists'));
    }

    public function fetch(Request $request)
    {
        $request->validate([
            'categories' => 'required|string',
        ]);

        $categories = array_filter(array_map('trim', explode("\n", $request->categories)));

        foreach ($categories as $category) {
            $titles = $this->aiService->generateTitles($category);

            foreach ($titles as $title) {
                $playlists = $this->youtubeService->searchPlaylists($title);

                foreach ($playlists as $playlist) {
                    Playlist::updateOrCreate(
                        ['playlist_id' => $playlist['playlist_id']],
                        [
                            'title' => $playlist['title'],
                            'description' => $playlist['description'],
                            'thumbnail' => $playlist['thumbnail'],
                            'channel_name' => $playlist['channel_name'],
                            'category' => $category,
                        ]
                    );
                }
            }
        }

        return redirect()->route('playlists.index')->with('success', 'Fetching completed successfully.');
    }
}
