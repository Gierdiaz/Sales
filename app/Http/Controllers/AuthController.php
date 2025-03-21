<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(AuthRequest $request)
    {
        Log::info('===================== Iniciando registro de usuário =====================', ['email' => $request->email]);

        $validatedData = $request->validated();

        try {
            $user = User::create([
                'name'     => $validatedData['name'],
                'email'    => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
            ]);

            Log::info('===================== Usuário registrado com sucesso.', ['user_id' => $user->id]);

            return response()->json(['message' => 'User registered successfully'], 201);
        } catch (\Exception $e) {
            Log::error('Erro ao registrar usuário.', ['error' => $e->getMessage()]);

            return response()->json(['error' => 'Erro ao registrar usuário.'], 500);
        }
    }

    public function login(AuthRequest $request)
    {
        Log::info('===================== Tentativa de login ====================', ['email' => $request->email]);

        $validatedData = $request->validated();

        $user = User::where('email', $validatedData['email'])->first();

        if (!$user || !Hash::check($validatedData['password'], $user->password)) {
            Log::warning('Falha no login. Credenciais incorretas.', ['email' => $request->email]);

            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        Log::info('Usuário autenticado com sucesso.', ['user_id' => $user->id]);

        return response()->json([
            'access_token' => $token,
            'token_type'   => 'Bearer',
        ]);
    }

    public function logout(Request $request)
    {
        Log::info('===================== Tentativa de logout  =====================', ['user_id' => $request->user()->id ?? null]);

        $token = $request->user()->currentAccessToken();

        if ($token) {
            $token->delete();
            Log::info('Usuário deslogado com sucesso.', ['user_id' => $request->user()->id]);
        } else {
            Log::warning('Tentativa de logout sem token válido.', ['user_id' => $request->user()->id ?? null]);
        }

        return response()->json(['message' => 'Logged out successfully'], 200);
    }
}
