<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'nombre' => ['required', 'string', 'max:50'],
            'apellidoPaterno' => ['required', 'string', 'max:50'],
            'apellidoMaterno' => ['nullable', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $normalize = static function (?string $value): ?string {
            if ($value === null) {
                return null;
            }

            $value = trim(preg_replace('/\s+/', ' ', $value));

            return $value === '' ? null : $value;
        };

        $email = strtolower(trim($data['email']));
        $role = $this->resolveRoleFromEmail($email);

        if ($role === null) {
            return back()->withInput($request->except('password', 'password_confirmation'))->withErrors([
                'email' => 'Usa tu correo institucional válido. Alumno: tic-320072@utnay.edu.mx | Tutor: nombre.apellido@utnay.edu.mx',
            ]);
        }

        $displayName = trim(implode(' ', array_filter([
            $normalize($data['nombre']),
            $normalize($data['apellidoPaterno']),
            $normalize($data['apellidoMaterno']),
        ])));

        $user = User::create([
            'name' => $displayName,
            'email' => $email,
            'role' => $role,
            'password' => Hash::make($data['password']),
        ]);

        if ($role === 'estudiante') {
            $matricula = $this->extractStudentMatriculaFromEmail($email);

            if ($matricula === null) {
                $user->delete();
                return back()->withInput($request->except('password', 'password_confirmation'))->withErrors([
                    'email' => 'No se pudo obtener la matrícula desde el correo institucional de alumno.',
                ]);
            }

            $student = Student::where('matricula', $matricula)->first();

            $nombre = $normalize($data['nombre']) ?? $displayName;
            $apellidoPaterno = $normalize($data['apellidoPaterno']);
            $apellidoMaterno = $normalize($data['apellidoMaterno']);

            if ($student) {
                if (!empty($student->user_id) && (int) $student->user_id !== (int) $user->id) {
                    $user->delete();
                    return back()->withInput($request->except('password', 'password_confirmation'))->withErrors([
                        'email' => 'Esta matrícula ya está vinculada a otra cuenta.',
                    ]);
                }

                $student->update([
                    'user_id' => $user->id,
                    'nombre' => $nombre,
                    'apellidoPaterno' => $apellidoPaterno,
                    'apellidoMaterno' => $apellidoMaterno,
                ]);
            } else {
                Student::create([
                    'matricula' => $matricula,
                    'user_id' => $user->id,
                    'nombre' => $nombre,
                    'idcarrera' => null,
                    'cuatrimestre' => null,
                    'apellidoPaterno' => $apellidoPaterno,
                    'apellidoMaterno' => $apellidoMaterno,
                ]);
            }
        }

        Auth::login($user);
        $request->session()->regenerate();

        if ($user->role === 'estudiante') {
            return redirect()->route('student.home')->with('status', 'Cuenta creada correctamente.');
        }

        return redirect()->route('dashboard')->with('status', 'Cuenta creada correctamente.');
    }

    private function resolveRoleFromEmail(string $email): ?string
    {
        $email = strtolower(trim($email));

        if (preg_match('/^[a-z]{2,10}-?\d{3,10}@utnay\.edu\.mx$/', $email)) {
            return 'estudiante';
        }

        if (preg_match('/^[a-z]+(?:\.[a-z]+)+@utnay\.edu\.mx$/', $email)) {
            return 'tutor';
        }

        return null;
    }

    private function extractStudentMatriculaFromEmail(string $email): ?string
    {
        if (!preg_match('/^([a-z]{2,10})-?(\d{3,10})@utnay\.edu\.mx$/', strtolower(trim($email)), $matches)) {
            return null;
        }

        return strtoupper($matches[1]) . '-' . $matches[2];
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withInput($request->only('email'))->withErrors([
                'email' => 'Credenciales inválidas.',
            ]);
        }

        $request->session()->regenerate();

        $user = $request->user();

        if ($user && $user->role === 'estudiante') {
            return redirect()->route('student.home')->with('status', 'Sesión iniciada.');
        }

        return redirect()->route('dashboard')->with('status', 'Sesión iniciada.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Sesión cerrada.');
    }
}
