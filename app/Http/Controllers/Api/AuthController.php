<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuthToken;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /** POST /api/auth/register */
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'phone' => [
                'required', 'string', 'regex:/^08[0-9]{7,13}$/',
                Rule::unique('users', 'phone'),
            ],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'] ?? null,
            'password' => $data['password'], // auto-hash via cast
        ]);

        return response()->json([
            'data' => $this->presentUser($user),
            'token' => $this->issueToken($user),
        ], 201);
    }

    /** POST /api/auth/login — email atau no HP + password */
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'identifier' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $identifier = trim($data['identifier']);

        $user = User::where('phone', $identifier)
            ->orWhere('email', $identifier)
            ->first();

        if (! $user || ! $user->password || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'identifier' => 'Email/No. HP atau password salah.',
            ]);
        }

        return response()->json([
            'data' => $this->presentUser($user),
            'token' => $this->issueToken($user),
        ]);
    }

    /** POST /api/auth/logout */
    public function logout(Request $request): JsonResponse
    {
        $plain = $request->bearerToken();

        if ($plain) {
            AuthToken::where('token', hash('sha256', $plain))->delete();
        }

        return response()->json(['message' => 'Berhasil keluar.']);
    }

    /** GET /api/me — profil user yang sedang login */
    public function me(Request $request): JsonResponse
    {
        return response()->json(['data' => $this->presentUser($request->user())]);
    }

    private function issueToken(User $user): string
    {
        $plain = bin2hex(random_bytes(20));

        $user->tokens()->create([
            'token' => hash('sha256', $plain),
            'last_used_at' => now(),
        ]);

        return $plain;
    }

    private function presentUser(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'phone' => $user->phone,
            'email' => $user->email,
            'gender' => $user->gender,
        ];
    }
}
