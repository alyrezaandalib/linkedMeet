<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\RegisterRequest;
use App\Http\Requests\Api\V1\Auth\ResendVerificationCodeRequest;
use App\Mail\EmailVerification;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use OpenApi\Attributes as OA;

class AuthController extends Controller
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

        Mail::to($user->email)->send(new EmailVerification($user));

        return response()->json([
            'message' => __('auth.register_success'),
        ], 201);
    }

    #[OA\Post(
        path: "/api/v1/auth/resend-verification-code",
        description: "Resend a 6-digit email verification code. Ensures the code is not resent if the previous one is still valid.",
        summary: "Resend Email Verification Code",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email"],
                properties: [
                    new OA\Property(
                        property: "email",
                        description: "The email address of the user.",
                        type: "string",
                        format: "email"
                    ),
                ],
                type: "object"
            )
        ),
        tags: ["Auth"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Verification code resent successfully.",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "message",
                            type: "string",
                            example: "Verification code resent successfully."
                        ),
                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 400,
                description: "Email is already verified.",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "message",
                            type: "string",
                            example: "Email is already verified."
                        ),
                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 429,
                description: "Too many requests.",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "message",
                            type: "string",
                            example: "A verification code was recently sent. Please wait 1 minute before requesting a new code."
                        ),
                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 422,
                description: "Validation error response.",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "message",
                            type: "string",
                            example: "The given data was invalid."
                        ),
                        new OA\Property(
                            property: "errors",
                            type: "object",
                        ),
                    ],
                    type: "object"
                )
            ),
        ]
    )]
    public function resendVerificationCode(ResendVerificationCodeRequest $request)
    {

        $user = User::where('email', $request->email)->first();

        if ($user->email_verified_at) {
            return response()->json(['message' => 'Email is already verified.'], 400);
        }

        if ($user->verification_code_expires_at && $user->verification_code_expires_at->isFuture()) {
            $remainingTime = $user->verification_code_expires_at->diffForHumans(now(), true);
            return response()->json([
                'message' => "A verification code was recently sent. Please wait $remainingTime before requesting a new code.",
            ], 429);
        }

        $user->verification_code = rand(100000, 999999);
        $user->verification_code_expires_at = now()->addMinutes(2);
        $user->save();

        Mail::to($user->email)->send(new EmailVerification($user));

        return response()->json(['message' => 'Verification code resent successfully.'], 200);
    }
}
