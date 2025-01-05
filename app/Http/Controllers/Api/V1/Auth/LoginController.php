<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use OpenApi\Attributes as OA;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{

    #[OA\Post(
        path: "/api/v1/auth/login",
        summary: "User Login",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: "email",
                        description: "The user's email address",
                        type: "string",
                        format: "email",
                        example: "user@example.com"
                    ),
                    new OA\Property(
                        property: "password",
                        description: "The user's password",
                        type: "string",
                        format: "password",
                        example: "password123"
                    ),
                ],
                type: "object"
            )
        ),
        tags: ["Auth"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Login successful",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "message",
                            type: "string",
                            example: "Login successful"
                        ),
                        new OA\Property(
                            property: "token",
                            type: "string",
                            example: "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
                        ),
                        new OA\Property(
                            property: "user",
                            properties: [
                                new OA\Property(
                                    property: "id",
                                    type: "integer",
                                    example: 1),
                                new OA\Property(
                                    property: "name",
                                    type: "string",
                                    example: "John Doe"),
                                new OA\Property(
                                    property: "email",
                                    type: "string",
                                    example: "john.doe@example.com"
                                ),
                                new OA\Property(
                                    property: "avatar",
                                    type: "string",
                                    example: "https://graph.facebook.com/me/"
                                ),
                            ],
                            type: "object"
                        ),
                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 401,
                description: "Invalid credentials",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "message",
                            type: "string",
                            example: "Invalid credentials"
                        ),
                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 403,
                description: 'Email not verified',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'Email not verified. Please verify your email.'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Validation error",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "message",
                            type: "string",
                            example: "The given data was invalid."
                        ),
                        new OA\Property(
                            property: "errors",
                            properties: [
                                new OA\Property(
                                    property: "email",
                                    type: "array",
                                    items: new OA\Items(type: "string", example: "The selected email is invalid.")
                                ),
                            ],
                            type: "object"
                        ),
                    ],
                    type: "object"
                )
            ),
        ]
    )]
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (is_null($user->email_verified_at)) {
            return response()->json([
                'message' => 'Email not verified. Please verify your email.',
            ], 403);
        }

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            "user" => new UserResource($user),
        ]);
    }
}
