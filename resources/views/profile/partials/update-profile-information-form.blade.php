<form method="POST" action="{{ route('profile.update') }}">
    @csrf
    @method('PATCH')

    <div class="form-group">
        <label>Nama</label>
        <input type="text" name="name" value="{{ old('name', $user->name) }}" required autofocus class="form-control @error('name') is-invalid @enderror">
        @error('name')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label>Username (opsional, untuk login alternatif)</label>
        <input type="text" name="username" value="{{ old('username', $user->username) }}" class="form-control @error('username') is-invalid @enderror">
        @error('username')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="form-control @error('email') is-invalid @enderror">
        @error('email')
            <span class="invalid-feedback">{{ $message }}</span>
        @enderror

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="mt-2">
                <p class="text-muted small">
                    Email Anda belum terverifikasi.
                    <button form="send-verification" class="btn btn-link p-0 align-baseline">Klik di sini untuk kirim ulang email verifikasi.</button>
                </p>
                @if (session('status') === 'verification-link-sent')
                    <p class="text-success small font-weight-bold">Link verifikasi baru sudah dikirim ke email Anda.</p>
                @endif
            </div>
        @endif
    </div>

    <button type="submit" class="btn btn-primary">Simpan</button>
</form>

@if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
    <form id="send-verification" method="POST" action="{{ route('verification.send') }}">
        @csrf
    </form>
@endif