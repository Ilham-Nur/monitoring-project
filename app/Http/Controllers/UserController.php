<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', User::class);

        $users = User::query()
            ->when($request->filled('search'), fn ($query) => $query->where(function ($searchQuery) use ($request): void {
                $searchQuery->where('name', 'like', '%'.$request->string('search').'%')
                    ->orWhere('email', 'like', '%'.$request->string('search').'%')
                    ->orWhere('company_name', 'like', '%'.$request->string('search').'%');
            }))
            ->when($request->filled('role'), fn ($query) => $query->where('role', $request->string('role')))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('user.index', compact('users'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->safe()->only(['name', 'email', 'role', 'company_name', 'password']);
        $data['company_name'] = $data['role'] === 'client' ? $data['company_name'] : null;
        $data['is_active'] = $request->boolean('is_active', true);
        User::create($data);

        return back()->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->safe()->only(['name', 'email', 'role', 'company_name']);
        $data['company_name'] = $data['role'] === 'client' ? $data['company_name'] : null;
        $data['is_active'] = $request->boolean('is_active');

        if ($request->filled('password')) {
            $data['password'] = $request->string('password')->toString();
        }

        $user->update($data);

        return back()->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        if ($user->clientProjects()->exists() || $user->assignedProjects()->exists()) {
            return back()->with('error', 'Pengguna masih terhubung dengan project dan tidak dapat dihapus.');
        }

        $user->delete();

        return back()->with('success', 'Pengguna berhasil dihapus.');
    }
}
