@extends('layout.app')

@section('title', 'Masuk - '.config('app.name'))

@section('content')
    <div class="login-shell">
        <section class="login-visual">
            <div class="login-visual-content">
                <div class="login-brand"><img class="company-logo login-company-logo" src="{{ asset('images/sebro-logo.jpeg') }}" alt="Logo PT. Sebro Precision Engineering" width="72" height="78"><span>{{ config('app.name') }}</span></div>
                <h1>Pantau progres dan dokumen project dalam satu tempat.</h1>
                <p>Timeline rencana dan realisasi membantu tim serta klien memahami perkembangan pekerjaan dengan jelas.</p>
                <div class="login-points">
                    <span><i class="bi bi-check2-circle"></i> Timeline rencana dan realisasi</span>
                    <span><i class="bi bi-check2-circle"></i> Dokumen tersusun per tahapan</span>
                    <span><i class="bi bi-check2-circle"></i> Komunikasi langsung dengan klien</span>
                </div>
            </div>
        </section>
        <section class="login-panel">
            <form class="login-card" method="POST" action="{{ route('login.store') }}">
                @csrf
                <div class="login-mobile-brand"><img class="company-logo" src="{{ asset('images/sebro-logo.jpeg') }}" alt="Logo PT. Sebro Precision Engineering" width="56" height="61"><strong>{{ config('app.name') }}</strong></div>
                <div class="login-heading"><span class="eyebrow">Selamat datang</span><h2>Masuk ke akun Anda</h2><p>Gunakan email dan kata sandi yang diberikan Admin.</p></div>
                <div class="form-group">
                    <label class="form-label" for="email">Email</label>
                    <div class="input-with-icon"><i class="bi bi-envelope"></i><input id="email" type="email" name="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" value="{{ old('email') }}" required autofocus></div>
                    @error('email')<div class="invalid-feedback visible">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="password">Kata sandi</label>
                    <div class="input-with-icon"><i class="bi bi-lock"></i><input id="password" type="password" name="password" class="form-control" required></div>
                </div>
                <label class="form-check login-remember"><input type="checkbox" name="remember" value="1" class="check-input"><span class="check-label">Ingat saya</span></label>
                <button class="btn btn-primary login-submit" type="submit">Masuk <i class="bi bi-arrow-right"></i></button>
            </form>
        </section>
    </div>
@endsection
