<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>

    <!-- ✅ Bootstrap CDN (no vite) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f5f7fb;
        }

        .login-card {
            border-radius: 18px;
        }
    </style>
</head>

<body>

    <div class="container py-5" style="max-width: 430px;">
        <div class="card shadow-sm border-0 login-card">
            <div class="card-body p-4 p-md-5">
                <h3 class="fw-bold mb-1">Admin Login</h3>
                <p class="text-muted mb-4">Login to manage the system</p>

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.post') }}">
                    @csrf

                    <div class="mb-3">
    <label class="form-label fw-semibold">Username or Email</label>
    <input type="text" name="login" class="form-control" value="{{ old('login') }}" required autofocus>
</div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>
                    </div>

                    <button class="btn btn-dark w-100 py-2">Login</button>
                </form>
            </div>
        </div>
    </div>

</body>

</html>
