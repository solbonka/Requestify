<?php

namespace App\Http\Controllers;

use App\Http\Requests\SignInRequest;
use App\Http\Requests\SignUpRequest;
use App\Jobs\SendVerificationEmailJob;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function postSignUp(SignUpRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
        SendVerificationEmailJob::dispatch($user)->onQueue('default');
        Auth::login($user);
        $result['token'] = $user->createToken('MyApp')->accessToken;
        $result['name'] = $user->name;

        return response()->json(
            [
                'success' => true,
                'data' => $result,
                'message' => 'User register successfully.',
            ]
        );
    }

    public function postSignIn(SignInRequest $request): JsonResponse
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $result['token'] = $user->createToken('MyApp')->accessToken;
            $result['name'] = $user->name;

            return response()->json(
                [
                    'success' => true,
                    'data' => $result,
                    'message' => 'User login successfully.',
                ]
            );
        } else {
            return response()->json(
                [
                    'success' => false,
                    'error' => 'Wrong email or password',
                    'message' => 'Unauthorised.',
                ],
                401
            );
        }
    }

    public function logout(): JsonResponse
    {
        $token = Auth::user()->token();
        $token->revoke();

        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }
}
