<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $role = Role::tryFrom((string) $request->query('role'));
        $classId = $request->integer('school_class_id') ?: null;

        return view('admin.users.index', [
            'users' => User::query()
                ->with('schoolClass')
                ->withCount('submissions')
                ->when($search !== '', fn (Builder $query) => $query->where(fn (Builder $inner) => $inner
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")))
                ->when($role, fn (Builder $query, Role $role) => $query->where('role', $role))
                ->when($classId, fn (Builder $query, int $classId) => $query->where('school_class_id', $classId))
                ->orderByRaw("CASE role WHEN 'super_admin' THEN 0 WHEN 'admin' THEN 1 ELSE 2 END")
                ->orderBy('name')
                ->paginate(25)
                ->withQueryString(),
            'classes' => SchoolClass::ordered()->get(),
            'roles' => Role::cases(),
            'filters' => ['search' => $search, 'role' => $role?->value, 'school_class_id' => $classId],
            'maxSubmissions' => config('kaih.max_submissions'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->isSuperAdmin(), 403, 'Hanya Super Admin yang dapat menambah pengguna.');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::enum(Role::class)],
            'school_class_id' => ['nullable', Rule::exists('school_classes', 'id')],
        ]);

        $user = new User($validated);
        $user->email_verified_at = now();
        $user->save();

        return back()->with('status', 'Pengguna '.$validated['email'].' berhasil ditambahkan.');
    }

    public function edit(Request $request, User $user): View
    {
        abort_unless($request->user()->isSuperAdmin(), 403, 'Hanya Super Admin yang dapat mengedit data pengguna.');

        return view('admin.users.edit', [
            'user' => $user,
            'classes' => SchoolClass::ordered()->get(),
            'roles' => Role::cases(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $actor = $request->user();

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['sometimes', Rule::enum(Role::class)],
            'school_class_id' => ['nullable', Rule::exists('school_classes', 'id')],
        ]);

        $changesAccountData = array_key_exists('name', $validated)
            || array_key_exists('email', $validated)
            || ! empty($validated['password']);

        abort_if($changesAccountData && ! $actor->isSuperAdmin(), 403, 'Hanya Super Admin yang dapat mengubah data akun.');

        if (array_key_exists('name', $validated)) {
            $user->name = $validated['name'];
        }

        if (array_key_exists('email', $validated)) {
            $user->email = $validated['email'];

            if ($user->isDirty('email')) {
                $user->email_verified_at = now();
            }
        }

        if (! empty($validated['password'])) {
            $user->password = $validated['password'];
        }

        if (array_key_exists('role', $validated)) {
            $newRole = Role::from($validated['role']);

            abort_unless($actor->isSuperAdmin(), 403, 'Hanya Super Admin yang dapat mengubah role.');

            if ($user->is($actor) && $newRole !== Role::SuperAdmin) {
                return back()->with('error', 'Kamu tidak dapat menurunkan role akunmu sendiri.');
            }

            $user->role = $newRole;
        }

        if (array_key_exists('school_class_id', $validated)) {
            abort_if($user->isSuperAdmin() && ! $actor->isSuperAdmin(), 403);
            $user->school_class_id = $validated['school_class_id'];
        }

        $user->save();

        return back()->with('status', 'Data '.$user->name.' berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        abort_unless($request->user()->isSuperAdmin(), 403);

        if ($user->is($request->user())) {
            return back()->with('error', 'Kamu tidak dapat menghapus akunmu sendiri.');
        }

        $user->delete();

        return back()->with('status', 'Akun '.$user->email.' beserta jawabannya telah dihapus.');
    }
}
