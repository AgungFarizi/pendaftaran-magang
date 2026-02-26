<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
    /**
     * Kode akses yang valid untuk setiap role
     * Dalam implementasi nyata, simpan di database atau environment variables
     */
    private const VALID_ACCESS_CODES = [
        'operator' => ['OP2024TELLINTER', 'OPERATOR-SECRET-001'],
        'pembimbing' => ['PL2024TELLINTER', 'PEMBIMBING-SECRET-001'],
        'manager' => ['MGR-SUPER-ADMIN-2024', 'MANAGER-MASTER-KEY'],
        'manager_dept' => ['MGRDEPT-SUPER-ADMIN-2024', 'MANAGERDEPT-MASTER-KEY'],
    ];

    /**
     * Show registration form
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle registration
     */
    public function register(Request $request)
    {
        $role = $request->input('role', 'mahasiswa');
        
        // Validasi role yang diperbolehkan
        $allowedRoles = ['mahasiswa', 'operator', 'pembimbing', 'manager', 'manager_dept'];
        if (!in_array($role, $allowedRoles)) {
            throw ValidationException::withMessages([
                'role' => 'Role tidak valid.',
            ]);
        }

        // Validasi kode akses untuk role admin
        if ($role !== 'mahasiswa') {
            $this->validateAccessCode($request, $role);
        }

        // Validasi umum
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'terms' => ['required', 'accepted'],
        ];

        $messages = [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 8 karakter.',
            'terms.required' => 'Anda harus menyetujui syarat dan ketentuan.',
            'terms.accepted' => 'Anda harus menyetujui syarat dan ketentuan.',
        ];

        // Validasi tambahan berdasarkan role
        if ($role === 'mahasiswa') {
            $rules = array_merge($rules, [
                'student_id' => ['required', 'string', 'max:50'],
                'university' => ['required', 'string', 'max:255'],
                'major' => ['required', 'string', 'max:255'],
                'phone' => ['required', 'string', 'max:20'],
            ]);
            $messages = array_merge($messages, [
                'student_id.required' => 'NIM wajib diisi.',
                'university.required' => 'Universitas wajib diisi.',
                'major.required' => 'Jurusan/Program studi wajib diisi.',
                'phone.required' => 'No. Telepon wajib diisi.',
            ]);
        }

        if ($role === 'pembimbing' || $role === 'manager_dept') {
            $rules['department'] = ['required', 'string', 'max:255'];
            $messages['department.required'] = 'Divisi/Departemen wajib dipilih.';
        }

        $validated = $request->validate($rules, $messages);

        // Buat user berdasarkan role
        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $role,
        ];

        // Data tambahan berdasarkan role
        if ($role === 'mahasiswa') {
            $userData['student_id'] = $validated['student_id'];
            $userData['university'] = $validated['university'];
            $userData['major'] = $validated['major'];
            $userData['phone'] = $validated['phone'];
        }

        if (in_array($role, ['pembimbing', 'manager_dept', 'operator', 'manager'])) {
            $userData['department'] = $validated['department'] ?? 'General';
            $userData['is_verified'] = true; // Admin sudah terverifikasi via kode akses
        }

        $user = User::create($userData);

        // Log aktivitas registrasi untuk audit (opsional)
        activity()
            ->causedBy($user)
            ->performedOn($user)
            ->withProperties([
                'role' => $role,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log('User registered');

        Auth::login($user);

        $dashboardRoute = $this->getDashboardRoute($role);
        
        return redirect($dashboardRoute)->with('success', 'Registrasi berhasil! Selamat datang di Tellinter.');
    }

    /**
     * Validasi kode akses untuk role admin
     */
    private function validateAccessCode(Request $request, string $role): void
    {
        $accessCode = $request->input('access_code', '');
        $validCodes = self::VALID_ACCESS_CODES[$role] ?? [];

        if (empty($accessCode)) {
            throw ValidationException::withMessages([
                'access_code' => 'Kode akses wajib diisi untuk role ' . $this->getRoleLabel($role) . '.',
            ]);
        }

        if (!in_array(trim($accessCode), $validCodes)) {
            // Log percobaan akses tidak valid untuk keamanan
            \Log::warning('Invalid access code attempt', [
                'role' => $role,
                'email' => $request->input('email'),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            throw ValidationException::withMessages([
                'access_code' => 'Kode akses tidak valid. Silakan hubungi administrator.',
            ]);
        }
    }

    /**
     * Get role label
     */
    private function getRoleLabel(string $role): string
    {
        return match($role) {
            'operator' => 'Operator',
            'pembimbing' => 'Pembimbing Lapang',
            'manager' => 'Manager',
            'manager_dept' => 'Manager Departemen',
            default => 'Mahasiswa',
        };
    }

    /**
     * Get dashboard route based on role
     */
    private function getDashboardRoute(string $role): string
    {
        return match($role) {
            'manager' => '/manager/dashboard',
            'manager_dept' => '/manager-dept/dashboard',
            'operator' => '/operator/dashboard',
            'pembimbing' => '/pembimbing/dashboard',
            default => '/mahasiswa/dashboard',
        };
    }
}
