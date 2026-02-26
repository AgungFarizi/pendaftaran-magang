<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    // ==================== OPERATOR MANAGEMENT (Manager) ====================

    public function operatorIndex(Request $request)
    {
        $query = User::where('role', 'operator');

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $operators = $query->latest()->paginate(10);

        return view('manager.operators.index', compact('operators'));
    }

    public function operatorCreate()
    {
        return view('manager.operators.create');
    }

    public function operatorStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:20',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'operator',
            'phone' => $request->phone,
        ]);

        return redirect()->route('manager.operators.index')
            ->with('success', 'Operator berhasil ditambahkan!');
    }

    public function operatorEdit(User $user)
    {
        if ($user->role !== 'operator') {
            abort(404);
        }

        return view('manager.operators.edit', compact('user'));
    }

    public function operatorUpdate(Request $request, User $user)
    {
        if ($user->role !== 'operator') {
            abort(404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => 'required|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return redirect()->route('manager.operators.index')
            ->with('success', 'Operator berhasil diperbarui!');
    }

    public function operatorDestroy(User $user)
    {
        if ($user->role !== 'operator') {
            abort(404);
        }

        $user->delete();

        return redirect()->route('manager.operators.index')
            ->with('success', 'Operator berhasil dihapus!');
    }

    // ==================== PEMBIMBING MANAGEMENT (Manager Dept) ====================

    public function pembimbingIndex(Request $request)
    {
        $user = Auth::user();
        $query = User::where('role', 'pembimbing');

        // Manager Dept can only see pembimbing in their department
        if ($user->role === 'manager_dept') {
            $query->where('department', $user->department);
        }

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $pembimbings = $query->latest()->paginate(10);

        return view('manager.pembimbing.index', compact('pembimbings'));
    }

    public function pembimbingCreate()
    {
        $departments = [
            'IT' => 'Information Technology',
            'HR' => 'Human Resources',
            'Finance' => 'Finance & Accounting',
            'Marketing' => 'Marketing',
            'Operations' => 'Operations',
            'RnD' => 'Research & Development',
        ];

        return view('manager.pembimbing.create', compact('departments'));
    }

    public function pembimbingStore(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:20',
            'department' => 'required|string|max:100',
            'position' => 'nullable|string|max:100',
        ]);

        // Manager Dept can only create pembimbing in their department
        $department = $request->department;
        if ($user->role === 'manager_dept') {
            $department = $user->department;
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'pembimbing',
            'phone' => $request->phone,
            'department' => $department,
            'position' => $request->position,
        ]);

        return redirect()->route('manager.pembimbing.index')
            ->with('success', 'Pembimbing Lapang berhasil ditambahkan!');
    }

    public function pembimbingEdit(User $user)
    {
        if ($user->role !== 'pembimbing') {
            abort(404);
        }

        $currentUser = Auth::user();
        if ($currentUser->role === 'manager_dept' && $user->department !== $currentUser->department) {
            abort(403);
        }

        $departments = [
            'IT' => 'Information Technology',
            'HR' => 'Human Resources',
            'Finance' => 'Finance & Accounting',
            'Marketing' => 'Marketing',
            'Operations' => 'Operations',
            'RnD' => 'Research & Development',
        ];

        return view('manager.pembimbing.edit', compact('user', 'departments'));
    }

    public function pembimbingUpdate(Request $request, User $user)
    {
        if ($user->role !== 'pembimbing') {
            abort(404);
        }

        $currentUser = Auth::user();
        if ($currentUser->role === 'manager_dept' && $user->department !== $currentUser->department) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => 'required|string|max:20',
            'department' => 'required|string|max:100',
            'position' => 'nullable|string|max:100',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'position' => $request->position,
        ];

        // Only manager can change department
        if ($currentUser->role === 'manager') {
            $updateData['department'] = $request->department;
        }

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return redirect()->route('manager.pembimbing.index')
            ->with('success', 'Pembimbing Lapang berhasil diperbarui!');
    }

    public function pembimbingDestroy(User $user)
    {
        if ($user->role !== 'pembimbing') {
            abort(404);
        }

        $currentUser = Auth::user();
        if ($currentUser->role === 'manager_dept' && $user->department !== $currentUser->department) {
            abort(403);
        }

        $user->delete();

        return redirect()->route('manager.pembimbing.index')
            ->with('success', 'Pembimbing Lapang berhasil dihapus!');
    }

    // ==================== MAHASISWA LIST (Operator) ====================

    public function mahasiswaIndex(Request $request)
    {
        $query = User::where('role', 'mahasiswa')
            ->with(['proposals' => function($q) {
                $q->latest();
            }]);

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('student_id', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->university) {
            $query->where('university', $request->university);
        }

        $mahasiswas = $query->latest()->paginate(15);

        $universities = User::where('role', 'mahasiswa')
            ->distinct()
            ->pluck('university')
            ->filter();

        return view('operator.mahasiswa.index', compact('mahasiswas', 'universities'));
    }

    public function mahasiswaShow(User $user)
    {
        if ($user->role !== 'mahasiswa') {
            abort(404);
        }

        $user->load(['proposals.members', 'attendanceLogs', 'dailyLogs']);

        return view('operator.mahasiswa.show', compact('user'));
    }
}
