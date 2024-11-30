<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\VerifyEmailRequest;
use App\Models\User;
use Illuminate\Support\Carbon;
use OpenApi\Attributes as OA;

class EmailVerificationController extends Controller
{
    #[OA\Post(
        path: '/api/v1/auth/verify-email',
        operationId: 'verifyEmail',
        description: 'This endpoint verifies the user email using the verification code sent to their email address.',
        summary: 'Verify user email',
        requestBody: new OA\RequestBody(
            description: 'Verification data',
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'verification_code'],
                properties: [
                    new OA\Property(
                        property: 'email',
                        type: 'string',
                        format: 'email',
                        example: 'john.doe@example.com'
                    ),
                    new OA\Property(
                        property: 'verification_code',
                        type: 'string',
                        example: '123456'
                    ),
                ]
            )
        ),
        tags: ['Auth'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Email verified successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'Email verified successfully.'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 409,
                description: 'Email is already verified.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'Email is already verified.'
                        )
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation errors or invalid code',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'Invalid verification code.'
                        ),
                        new OA\Property(
                            property: 'errors',
                            type: 'object',
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'User not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'User not found.'
                        ),
                    ]
                )
            ),
        ]
    )]
    public function verifyEmail(VerifyEmailRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'User not found.',
            ], 404);
        }

        if ($user->email_verified_at) {
            return response()->json([
                'message' => 'Email is already verified.',
            ], 409);
        }

        if ($user->verification_code !== $request->verification_code) {
            return response()->json([
                'message' => 'Invalid verification code.',
            ], 422);
        }

        if (Carbon::now()->isAfter($user->verification_code_expires_at)) {
            return response()->json([
                'message' => 'Verification code has expired.',
            ], 422);
        }

        $user->email_verified_at = Carbon::now();
        $user->verification_code = null;
        $user->verification_code_expires_at = null;
        $user->save();

        return response()->json([
            'message' => 'Email verified successfully.',
        ], 200);
    }
}
