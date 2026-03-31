@extends('plantilla')

@section('titulo', 'Iniciar sesión - UTN Lockers')

@section('menu')

@endsection

@section('contenido')
    <div class="card" style="max-width: 480px; margin: 0 auto;">
        <h2>Iniciar sesión</h2>
        <p class="muted">Accede con tu cuenta de administrador, tutor o estudiante.</p>

        <form class="form" method="POST" action="{{ route('login.attempt') }}">
            @csrf

            @include('partials.form-field', [
                'label' => 'Correo',
                'name' => 'email',
                'type' => 'email',
                'value' => old('email'),
                'required' => true,
                'placeholder' => 'correo@dominio.com',
            ])

            @include('partials.form-field', [
                'label' => 'Contraseña',
                'name' => 'password',
                'type' => 'password',
                'required' => true,
            ])

            <div class="field remember-field">
                <label for="remember">Recordarme</label>
                <input type="checkbox" id="remember" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
            </div>

            <div class="actions">
                <button class="btn" type="submit">Entrar</button>
                <a class="btn secondary" href="{{ route('register') }}">Crear cuenta</a>
            </div>
        </form>
    </div>
@endsection
