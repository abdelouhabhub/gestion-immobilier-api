<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

/**
 * @group Authentication
 *
 * APIs for user registration, login, and logout with JWT token management
 */
class AuthController extends Controller
{
    /**
     * Register a new user
     *
     * Create a new user account with role-based access control
     *
     * @bodyParam name string required Full name of the user. Example: Ahmed Benali
     * @bodyParam email string required Email address. Example: ahmed.benali@example.com
     * @bodyParam password string required Password (minimum 8 characters). Example: SecurePass123
     * @bodyParam password_confirmation string required Password confirmation. Example: SecurePass123
     * @bodyParam role string required User role (admin, agent, guest). Example: agent
     *
     * @response 201 {
     *   "success": true,
     *   "message": "Account created successfully. Welcome!",
     *   "data": {
     *     "user": {
     *       "id": 1,
     *       "name": "Ahmed Benali",
     *       "email": "ahmed.benali@example.com",
     *       "role": "agent",
     *       "created_at": "2025-11-11T18:00:00.000000Z"
     *     },
     *     "access_token": "1|abc123...",
     *     "token_type": "Bearer",
     *     "expires_in": null
     *   }
     * }
     *
     * @response 422 {
     *   "message": "The given data was invalid.",
     *   "errors": {
     *     "email": ["The email has already been taken."]
     *   }
     * }
     */
    public function register(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|min:3',
                'email' => 'required|string|email|max:255|unique:users|regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                'password' => 'required|string|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
                'role' => 'required|in:admin,agent,guest'
            ], [
                'name.required' => 'Full name is required',
                'name.min' => 'Name must be at least 3 characters',
                'email.required' => 'Email address is required',
                'email.unique' => 'This email is already registered',
                'email.regex' => 'Please provide a valid email address',
                'password.required' => 'Password is required',
                'password.min' => 'Password must be at least 8 characters',
                'password.confirmed' => 'Password confirmation does not match',
                'password.regex' => 'Password must contain at least one uppercase, one lowercase, and one digit',
                'role.required' => 'User role is required',
                'role.in' => 'Role must be admin, agent, or guest'
            ]);

            // Create user
            $user = User::create([
                'name' => $validated['name'],
                'email' => strtolower($validated['email']),
                'password' => Hash::make($validated['password']),
                'role' => $validated['role']
            ]);

            // Generate token with abilities based on role
            $abilities = $this->getAbilitiesForRole($validated['role']);
            $token = $user->createToken('auth_token', $abilities)->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Account created successfully. Welcome!',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role,
                        'created_at' => $user->created_at
                    ],
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                    'expires_in' => null
                ]
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during registration',
                'error' => config('app.debug') ? $e->getMessage() : 'Please try again later'
            ], 500);
        }
    }

    /**
     * Login user
     *
     * Authenticate user and return access token with rate limiting (5 attempts per minute)
     *
     * @bodyParam email string required Email address. Example: agent@digitup.com
     * @bodyParam password string required Password. Example: password
     * @bodyParam remember boolean optional Remember me for extended session. Example: true
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Login successful. Welcome back!",
     *   "data": {
     *     "user": {
     *       "id": 2,
     *       "name": "Agent Immobilier",
     *       "email": "agent@digitup.com",
     *       "role": "agent"
     *     },
     *     "access_token": "2|xyz789...",
     *     "token_type": "Bearer",
     *     "expires_in": null
     *   }
     * }
     *
     * @response 401 {
     *   "success": false,
     *   "message": "Invalid credentials. Please check your email and password."
     * }
     *
     * @response 429 {
     *   "success": false,
     *   "message": "Too many login attempts. Please try again in 60 seconds."
     * }
     */
    public function login(Request $request): JsonResponse
    {
        // Rate limiting: 5 attempts per minute per email
        $key = Str::lower($request->input('email')) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            return response()->json([
                'success' => false,
                'message' => "Too many login attempts. Please try again in {$seconds} seconds.",
                'retry_after' => $seconds
            ], 429);
        }

        try {
            $validated = $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
                'remember' => 'boolean'
            ]);

            $user = User::where('email', strtolower($validated['email']))->first();

            if (!$user || !Hash::check($validated['password'], $user->password)) {
                RateLimiter::hit($key, 60); // Increment failed attempts

                return response()->json([
                    'success' => false,
                    'message' => 'Invalid credentials. Please check your email and password.'
                ], 401);
            }

            // Clear rate limiter on successful login
            RateLimiter::clear($key);

            // Revoke all previous tokens for security
            $user->tokens()->delete();

            // Generate new token with abilities
            $abilities = $this->getAbilitiesForRole($user->role);
            $tokenName = 'auth_token_' . now()->timestamp;
            $token = $user->createToken($tokenName, $abilities)->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login successful. Welcome back!',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role
                    ],
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                    'expires_in' => null
                ]
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during login',
                'error' => config('app.debug') ? $e->getMessage() : 'Please try again later'
            ], 500);
        }
    }

    /**
     * Logout user
     *
     * Revoke the current access token
     *
     * @authenticated
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Logout successful. See you soon!"
     * }
     *
     * @response 401 {
     *   "message": "Unauthenticated."
     * }
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            // Revoke current token
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Logout successful. See you soon!'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during logout',
                'error' => config('app.debug') ? $e->getMessage() : 'Please try again later'
            ], 500);
        }
    }

    /**
     * Get current authenticated user
     *
     * Retrieve the profile of the currently authenticated user
     *
     * @authenticated
     *
     * @response 200 {
     *   "success": true,
     *   "data": {
     *     "id": 2,
     *     "name": "Agent Immobilier",
     *     "email": "agent@digitup.com",
     *     "role": "agent",
     *     "created_at": "2025-11-11T18:00:00.000000Z",
     *     "updated_at": "2025-11-11T18:00:00.000000Z"
     *   }
     * }
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $request->user()
        ], 200);
    }

    /**
     * Refresh token
     *
     * Revoke current token and issue a new one
     *
     * @authenticated
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Token refreshed successfully",
     *   "data": {
     *     "access_token": "3|new_token...",
     *     "token_type": "Bearer"
     *   }
     * }
     */
    public function refresh(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            // Revoke current token
            $request->user()->currentAccessToken()->delete();

            // Generate new token
            $abilities = $this->getAbilitiesForRole($user->role);
            $tokenName = 'auth_token_' . now()->timestamp;
            $token = $user->createToken($tokenName, $abilities)->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Token refreshed successfully',
                'data' => [
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                    'expires_in' => null
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to refresh token',
                'error' => config('app.debug') ? $e->getMessage() : 'Please try again later'
            ], 500);
        }
    }

    /**
     * Get token abilities based on user role
     *
     * @param string $role
     * @return array
     */
    private function getAbilitiesForRole(string $role): array
    {
        return match($role) {
            'admin' => ['*'], // All abilities
            'agent' => ['create-property', 'update-own-property', 'delete-own-property', 'view-property'],
            'guest' => ['view-property'],
            default => []
        };
    }
}
