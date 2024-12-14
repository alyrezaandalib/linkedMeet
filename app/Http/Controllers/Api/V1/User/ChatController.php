<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\UserChatResource;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class ChatController extends Controller
{

    #[OA\Get(
        path: '/api/v1/user/chats',
        description: 'Returns a list of users with whom the authenticated user has had a chat, including the last message time.',
        summary: 'Retrieve the list of users the authenticated user has chatted with.',
        security: [['sanctum' => []]],
        tags: ['Chat'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful Response',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
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
                                property: 'avatar',
                                type: 'string',
                                example: 'https://example.com/avatar.jpg'
                            ),
                            new OA\Property(
                                property: 'started_at',
                                type: 'string',
                                format: 'date-time',
                                example: '2024-12-15T12:30:00Z'
                            ),
                            new OA\Property(
                                property: 'last_message_at',
                                type: 'string',
                                format: 'date-time',
                                example: '2024-12-15T12:30:00Z'
                            ),
                        ],
                        type: 'object'
                    )
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthenticated',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'Unauthenticated.'
                        ),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    public function index(Request $request)
    {
        $user = $request->user();

        $chatUsers = $user->chats()
            ->with(['partner' => function ($query) {
                $query->select('id', 'name', 'avatar');
            }])
            ->get();

        return response()
            ->json([
                'data' => UserChatResource::collection($chatUsers),
            ]);
    }
}
