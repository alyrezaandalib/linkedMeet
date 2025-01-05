<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\User\UploadAvatarRequest;
use Illuminate\Support\Facades\Storage;
use OpenApi\Attributes as OA;

class AvatarController extends Controller
{

    #[OA\Post(
        path: "/api/v1/user/avatar",
        description: "Uploads a new avatar for the authenticated user. If a previous avatar exists, it will be deleted and replaced.",
        summary: "Update user avatar",
        security: [["sanctum" => []]],
        requestBody: new OA\RequestBody(
            description: "Image file for the new avatar",
            required: true,
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    required: ["avatar"],
                    properties: [
                        new OA\Property(
                            property: "avatar",
                            description: "Image file (jpeg, png, jpg, gif, svg) with a maximum size of 2MB",
                            type: "string",
                            format: "binary"
                        ),
                    ]
                )
            )
        ),
        tags: ["User"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Avatar updated successfully!",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "message",
                            type: "string",
                            example: "Avatar updated successfully!"
                        ),
                        new OA\Property(
                            property: "avatar_url",
                            type: "string",
                            example: "http://example.com/storage/avatars/new-avatar.jpg"
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: "Unauthorized",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "message",
                            type: "string",
                            example: "Unauthenticated."
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
                                    property: "avatar",
                                    type: "array",
                                    items: new OA\Items(
                                        type: "string",
                                        example: "The avatar must be an image."
                                    )
                                ),
                            ],
                            type: "object"
                        ),
                    ]
                )
            ),
        ]
    )]
    public function upload(UploadAvatarRequest $request)
    {
        $user = $request->user();

        $currentAvatar = $user->getRawOriginal("avatar");
        if ($currentAvatar && Storage::disk('avatars')->exists($currentAvatar)) {
            Storage::disk('avatars')->delete($currentAvatar);
        }

        $avatarPath = $request->file('avatar')
            ->store('', 'avatars');

        $user->update([
            'avatar' => $avatarPath,
        ]);

        return response()->json([
            'message' => 'Avatar updated successfully!',
            'avatar_url' => $user->avatar,
        ], 200);
    }
}
