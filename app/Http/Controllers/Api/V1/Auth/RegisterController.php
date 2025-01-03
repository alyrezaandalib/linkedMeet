<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\RegisterRequest;
use App\Mail\EmailVerification;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use OpenApi\Attributes as OA;

class RegisterController extends Controller
{
    #[OA\Post(
        path: '/api/v1/auth/register',
        operationId: 'registerUser',
        description: 'This endpoint allows a user to register by providing their name, email, and password. After registration, an email verification code will be sent to the user.',
        summary: 'Register a new user',
        requestBody: new OA\RequestBody(
            description: 'User registration data',
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'email', 'password'],
                properties: [
                    new OA\Property(
                        property: 'name',
                        description: 'Full name of the user',
                        type: 'string',
                        example: 'John Doe'
                    ),
                    new OA\Property(
                        property: 'email',
                        description: 'Email address of the user',
                        type: 'string',
                        format: 'email',
                        example: 'john.doe@example.com'
                    ),
                    new OA\Property(
                        property: 'password',
                        description: 'Password for the user account',
                        type: 'string',
                        example: 'password123'
                    ),
                ]
            )
        ),
        tags: ['Auth'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'User successfully registered',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'Registration successful. A verification code has been sent to your email.'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation errors',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'Validation failed'
                        ),
                        new OA\Property(
                            property: 'errors',
                            description: 'An object containing validation errors for each parameter',
                            type: 'object',
                        ),
                    ]
                )
            ),
        ]
    )]
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'verification_code' => rand(100000, 999999),
            'verification_code_expires_at' => now()->addMinutes(2),
        ]);

        $user->assignRole('User');

        Mail::to($user->email)->send(new EmailVerification($user));

        return response()->json([
            'message' => __('auth.register_success'),
        ], 201);
    }
}
