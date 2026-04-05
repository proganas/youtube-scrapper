<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YouTube Scrapper</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to bottom right, #f8fafc, #eef2ff);
            min-height: 100vh;
        }

        .hero {
            padding: 60px 0 30px;
        }

        .hero-title {
            font-size: 2.2rem;
            font-weight: 800;
            color: #1e293b;
        }

        .hero-subtitle {
            color: #64748b;
            max-width: 720px;
            margin: 0 auto;
        }

        .form-card {
            border: none;
            border-radius: 24px;
            box-shadow: 0 20px 45px rgba(15, 23, 42, 0.08);
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(8px);
        }

        textarea {
            min-height: 180px;
            border-radius: 18px !important;
            border: 1px solid #dbeafe !important;
            padding: 18px !important;
            font-size: 15px;
        }

        textarea:focus {
            box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.15) !important;
            border-color: #60a5fa !important;
        }

        .btn-fetch {
            border-radius: 16px;
            padding: 13px 28px;
            font-weight: 700;
            font-size: 15px;
        }

        .playlist-card {
            border: none;
            border-radius: 22px;
            overflow: hidden;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
            transition: all 0.25s ease;
            background: #fff;
        }

        .playlist-card:hover {
            transform: translateY(-6px);
        }

        .playlist-card img {
            height: 220px;
            object-fit: cover;
        }

        .badge-category {
            background: #2563eb;
            border-radius: 999px;
            padding: 8px 14px;
            font-size: 12px;
            font-weight: 600;
        }

        .card-title {
            color: #0f172a;
            min-height: 56px;
        }

        .card-footer {
            background: white !important;
        }

        .empty-box {
            border-radius: 20px;
            padding: 30px;
        }
    </style>
</head>
<body>

<div class="container hero">
    <div class="text-center mb-5">
        <h1 class="hero-title">Discover Educational YouTube Playlists</h1>
        <p class="hero-subtitle mt-3">
            Enter categories like Programming, Marketing, or Business and let the app fetch relevant educational
            playlists using AI-generated search titles and YouTube Data API.
        </p>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-4 shadow-sm border-0">
            {{ session('success') }}
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning rounded-4 shadow-sm border-0 mb-4">
            {{ session('warning') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger rounded-4 shadow-sm border-0">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card form-card mb-5">
        <div class="card-body p-4 p-md-5">
            <form action="{{ route('playlists.fetch') }}" method="POST" id="fetchForm">
                @csrf
                <div class="mb-4">
                    <label class="form-label fw-bold mb-3">Enter Categories (one per line)</label>
                    <textarea name="categories" class="form-control" rows="5"
                              placeholder="Programming&#10;Marketing&#10;Business&#10;Graphic Design">{{ old('categories') }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-fetch d-flex align-items-center" id="fetchBtn">
                    <span id="btnText">Start Fetching</span>
                    <span id="btnSpinner" class="spinner-border spinner-border-sm ms-2 d-none" role="status" aria-hidden="true"></span>
                </button>
            </form>
        </div>
    </div>

    <div class="row g-4">
        @forelse($playlists as $playlist)
            <div class="col-md-6 col-lg-4">
                <div class="card playlist-card h-100">
                    @if($playlist->thumbnail)
                        <img src="{{ $playlist->thumbnail }}" class="card-img-top" alt="{{ $playlist->title }}">
                    @endif
                    <div class="card-body p-4">
                        <span class="badge badge-category mb-3">{{ $playlist->category }}</span>
                        <h5 class="card-title fw-bold">{{ $playlist->title }}</h5>
                        <p class="text-muted small mb-2">
                            <strong>Channel:</strong> {{ $playlist->channel_name }}
                        </p>
                        <p class="card-text text-muted small">
                            {{ \Illuminate\Support\Str::limit($playlist->description, 130) }}
                        </p>
                    </div>
                    <div class="card-footer border-0 p-4 pt-0">
                        <a href="https://www.youtube.com/playlist?list={{ $playlist->playlist_id }}" target="_blank"
                           class="btn btn-outline-primary w-100 rounded-4">
                            View Playlist
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info empty-box shadow-sm border-0">
                    No playlists found yet. Enter categories and click <strong>Start Fetching</strong>.
                </div>
            </div>
        @endforelse
    </div>
    <div class="mt-4">
        {{ $playlists->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
</div>

<script>
    document.getElementById('fetchForm').addEventListener('submit', function () {
        const btnText = document.getElementById('btnText');
        const btnSpinner = document.getElementById('btnSpinner');
        const btn = document.getElementById('fetchBtn');

        btn.disabled = true;
        btnText.innerText = 'Fetching...';
        btnSpinner.classList.remove('d-none');
    });
</script>

</body>
</html>
