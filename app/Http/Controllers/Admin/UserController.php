<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorizeAdministrator($request);

        return response()->json(['users' => User::query()->orderBy('name')->get(), 'currentUserId' => $request->user()->id]);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorizeAdministrator($request);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in(['administrator', 'editor'])],
            'password' => ['required', 'string', 'min:8'],
            'is_active' => ['boolean'],
        ]);

        return response()->json(['message' => 'Administrator account created.', 'user' => User::create($data)], 201);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $this->authorizeAdministrator($request);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', Rule::in(['administrator', 'editor'])],
            'password' => ['nullable', 'string', 'min:8'],
            'is_active' => ['boolean'],
        ]);

        if ($request->user()->is($user) && (! $request->boolean('is_active') || $data['role'] !== 'administrator')) {
            return response()->json(['message' => 'You cannot deactivate or remove administrator access from your own account.'], 422);
        }
        if (empty($data['password'])) {
            unset($data['password']);
        }
        $user->update($data);

        return response()->json(['message' => 'Administrator account updated.', 'user' => $user->fresh()]);
    }

    public function destroy(Request $request, User $user): JsonResponse
    {
        $this->authorizeAdministrator($request);
        if ($request->user()->is($user)) {
            return response()->json(['message' => 'You cannot delete your own account.'], 422);
        }
        $user->delete();

        return response()->json(['message' => 'Administrator account deleted.']);
    }

    private function authorizeAdministrator(Request $request): void
    {
        abort_unless($request->user()?->role === 'administrator', 403, 'Only administrators can manage user accounts.');
    }
}
