<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ProfileResource;
use App\Http\Resources\V1\UserResource;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class ProfileController extends Controller
{
    #[OA\Get(
        path: '/api/v1/user/profile',
        summary: 'Get user profile information',
        security: [['sanctum' => []]],
        tags: ['User'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'User profile information',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'id',
                            type: 'integer',
                            example: 1
                        ),
                        new OA\Property(
                            property: 'name',
                            type: 'string',
                            example: 'John Doe'
                        ),
                        new OA\Property(
                            property: 'email',
                            type: 'string',
                            example: 'johndoe@example.com'
                        ),
                        new OA\Property(
                            property: "avatar",
                            type: "string",
                            example: "https://graph.facebook.com/me/"
                        ),
                        new OA\Property(
                            property: 'job_title',
                            type: 'string',
                            example: 'Software Developer'
                        ),
                        new OA\Property(
                            property: 'industry',
                            type: 'string',
                            example: 'Technology'
                        ),
                        new OA\Property(
                            property: 'company_activity_types',
                            type: 'array',
                            items: new OA\Items(
                                type: 'string',
                                example: 'Research & Development'
                            )
                        ),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'Unauthorized'
                        ),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    public function show(Request $request)
    {
        $user = $request->user();

        return new UserResource($user);
    }


    #[OA\Get(
        path: '/api/v1/user/{id}/profile',
        summary: 'Get user profile by id',
        security: [['sanctum' => []]],
        tags: ['User'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'User profile information',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'id',
                            type: 'integer',
                            example: 1
                        ),
                        new OA\Property(
                            property: 'name',
                            type: 'string',
                            example: 'John Doe'
                        ),
                        new OA\Property(
                            property: 'email',
                            type: 'string',
                            example: 'johndoe@example.com'
                        ),
                        new OA\Property(
                            property: "avatar",
                            type: "string",
                            example: "https://graph.facebook.com/me/"
                        ),
                        new OA\Property(
                            property: 'job_title',
                            type: 'string',
                            example: 'Software Developer'
                        ),
                        new OA\Property(
                            property: 'industry',
                            type: 'string',
                            example: 'Technology'
                        ),
                    ],
                    type: 'object'
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
    public function showUserProfile($id)
    {
        try {
            $user = User::with('userDetails')->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'User not found.',
            ], 404);
        }

        return new ProfileResource($user);
    }
}
