<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\ForgotPasswordRequest;
use App\Http\Requests\Api\V1\Auth\ResetForgottenPasswordRequest;
use App\Mail\EmailVerification;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use OpenApi\Attributes as OA;

class ForgotPasswordController extends Controller
{
    #[OA\Post(
        path: '/api/v1/auth/forgot-password',
        summary: 'Send password reset code to email',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'user@example.com'),
                ]
            )
        ),
        tags: ['Auth'],
        responses: [
            new OA\Response(response: 200, description: 'Code sent'),
            new OA\Response(response: 404, description: 'User not found'),
        ]
    )]
    public function sendCode(ForgotPasswordRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }
        // Prevent spamming
        if ($user->verification_code_expires_at && now()->isBefore($user->verification_code_expires_at)) {
            $remaining = $user->verification_code_expires_at->diffForHumans(now(), true);
            return response()->json(['message' => "A code was recently sent. Please wait $remaining before requesting a new code."], 429);
        }
        $user->verification_code = rand(100000, 999999);
        $user->verification_code_expires_at = now()->addMinutes(2);
        $user->save();
        Mail::to($user->email)->send(new EmailVerification($user));
        return response()->json(['message' => 'Verification code sent to your email.']);
    }

    #[OA\Post(
        path: '/api/v1/auth/reset-forgotten-password',
        summary: 'Verify code and set new password',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'verification_code', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'user@example.com'),
                    new OA\Property(property: 'verification_code', type: 'string', example: '123456'),
                    new OA\Property(property: 'password', type: 'string', example: 'newPassword123'),
                ]
            )
        ),
        tags: ['Auth'],
        responses: [
            new OA\Response(response: 200, description: 'Password reset successful'),
            new OA\Response(response: 404, description: 'User not found'),
            new OA\Response(response: 422, description: 'Invalid or expired code'),
        ]
    )]
    public function reset(ResetForgottenPasswordRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }
        if ($user->verification_code !== $request->verification_code) {
            return response()->json(['message' => 'Invalid verification code.'], 422);
        }
        if (Carbon::now()->isAfter($user->verification_code_expires_at)) {
            return response()->json(['message' => 'Verification code has expired.'], 422);
        }
        $user->password = Hash::make($request->password);
        $user->verification_code = null;
        $user->verification_code_expires_at = null;
        $user->save();
        return response()->json(['message' => 'Password reset successfully.']);
    }
} 