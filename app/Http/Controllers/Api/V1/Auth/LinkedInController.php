<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\UserResource;
use App\Models\SocialNetwork;
use App\Models\User;
use App\Models\UserSocial;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use OpenApi\Attributes as OA;

class LinkedInController extends Controller
{
    #[OA\Get(
        path: "/api/v1/auth/linkedin",
        summary: "Redirect to LinkedIn for authentication",
        tags: ["Auth"],
        responses: [
            new OA\Response(
                response: 302,
                description: "Redirect to LinkedIn."
            ),
        ]
    )]
    public function redirectToLinkedIn()
    {
        return Socialite::driver('linkedin-openid')->stateless()->redirect();
    }

    #[OA\Get(
        path: "/api/v1/auth/linkedin/callback",
        summary: "Handle LinkedIn callback after authentication",
        tags: ["Auth"],
        parameters: [
            new OA\Parameter(
                name: "code",
                description: "Code",
                in: "query",
                required: true
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Login successful",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "message",
                            type: "string",
                            example: "Login successful."
                        ),
                        new OA\Property(
                            property: "token",
                            type: "string",
                            example: "your_generated_token_here"
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
                response: 500,
                description: "Unable to authenticate with LinkedIn.",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "error",
                            type: "string",
                            example: "Unable to authenticate with LinkedIn."
                        ),
                    ],
                    type: "object"
                )
            ),
        ]
    )]
    public function handleLinkedInCallback()
    {
        try {
            $linkedinUser = Socialite::driver('linkedin-openid')->stateless()->user();

            $user = User::where('email', $linkedinUser->getEmail())->first();

            if (!$user) {
                $user = User::create([
                    'name' => $linkedinUser->getName(),
                    'email' => $linkedinUser->getEmail(),
                    'password' => bcrypt(Str::random(8)),
                    'email_verified_at' => now(),
                    'avatar' => $linkedinUser->getAvatar(),
                ]);
            }

            $userSocial = UserSocial::firstOrNew([
                'user_id' => $user->id,
                'social_network_id' => SocialNetwork::LINKED_IN,
            ], [
                'social_id' => $linkedinUser->getId(),
                'access_token' => $linkedinUser->token,
            ]);

            $userSocial->save();

            $token = $user->createToken('LinkedInApp')->plainTextToken;

            return response()->json([
                'message' => 'Login successful.',
                'token' => $token,
                'user' => new UserResource($user),
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to authenticate with LinkedIn.'], 500);
        }
    }
}
