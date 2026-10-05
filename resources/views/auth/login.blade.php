<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login — {{ config('app.name', 'DropZone Inventory') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen items-center justify-center bg-bg px-4 text-ink">
    <div class="w-full max-w-md">
        <div class="mb-8 text-center">
            <span class="dz-heading block text-5xl tracking-widest text-ink">DROPZONE</span>
            <span class="dz-overline mt-1 block text-primary">INVENTORY</span>
        </div>

        <div class="dz-card dz-glow-orange p-8">
            <h1 class="dz-heading text-3xl text-ink">Masuk</h1>
            <p class="mt-2 text-sm text-ink-2">Gunakan kredensial yang diberikan administrator.</p>

            <x-dz-alert tone="error" />

            <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-5">
                @csrf

                <div>
                    <label for="email" class="dz-label">Email</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="username"
                        class="dz-input"
                        @error('email') aria-invalid="true" @enderror
                    />
                    @error('email')
                        <p class="dz-error" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="dz-label">Password</label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        class="dz-input"
                        @error('password') aria-invalid="true" @enderror
                    />
                    @error('password')
                        <p class="dz-error" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <label class="flex items-center gap-3 text-sm text-ink-2">
                    <input type="checkbox" name="remember" class="h-5 w-5 accent-primary" />
                    Ingat saya
                </label>

                <button type="submit" class="dz-btn dz-btn-primary w-full" onclick="this.disabled = true; this.form.submit();">
                    Masuk
                </button>
            </form>
        </div>

        <p class="mt-6 text-center text-xs text-ink-3">
            DropZone Inventory &middot; Laravel {{ app()->version() }}
        </p>
    </div>
</body>
</html>
