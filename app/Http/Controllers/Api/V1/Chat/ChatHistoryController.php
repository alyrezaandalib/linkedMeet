<?php

namespace App\Http\Controllers\Api\V1\Chat;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class ChatHistoryController extends Controller
{
    #[OA\Get(
        path: '/api/v1/chat/history',
        description: 'Get chat history between the authenticated user and a specific partner. Messages are returned in chronological order (oldest first) for proper chat display.',
        summary: 'Get chat history',
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(
                name: 'partner_id',
                description: 'ID of the chat partner',
                in: 'query',
                required: true,
                schema: new OA\Schema(type: 'integer', example: 6)
            ),
            new OA\Parameter(
                name: 'page',
                description: 'Page number for pagination. Page 1 returns the latest 10 messages, page 2 returns the next 10 messages, etc.',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer', example: 1)
            ),
        ],
        tags: ['Chat'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Chat history retrieved successfully.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'id', type: 'integer', example: 1),
                                    new OA\Property(property: 'sender_id', type: 'integer', example: 5),
                                    new OA\Property(property: 'receiver_id', type: 'integer', example: 6),
                                    new OA\Property(property: 'message', type: 'string', example: 'Hello!'),
                                    new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2024-01-01T12:00:00Z'),
                                    new OA\Property(property: 'read_at', type: 'string', format: 'date-time', example: '2024-01-01T12:05:00Z'),
                                ]
                            )
                        ),
                        new OA\Property(
                            property: 'meta',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'current_page', type: 'integer', example: 1),
                                new OA\Property(property: 'last_page', type: 'integer', example: 5),
                                new OA\Property(property: 'per_page', type: 'integer', example: 10),
                                new OA\Property(property: 'total', type: 'integer', example: 100),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'Unauthenticated.'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'The given data was invalid.'
                        ),
                        new OA\Property(
                            property: 'errors',
                            type: 'object',
                            example: [
                                'partner_id' => [
                                    'The partner id field is required.',
                                ],
                            ]
                        ),
                    ]
                )
            ),
        ]
    )]
    public function index(Request $request)
    {
        $request->validate([
            'partner_id' => 'required|integer|exists:users,id',
            'page' => 'nullable|integer|min:1',
        ]);

        $user = $request->user();
        $partnerId = $request->partner_id;
        $perPage = 10;

        $messages = Message::where(function ($query) use ($user, $partnerId) {
            $query->where('sender_id', $user->id)
                  ->where('receiver_id', $partnerId);
        })->orWhere(function ($query) use ($user, $partnerId) {
            $query->where('sender_id', $partnerId)
                  ->where('receiver_id', $user->id);
        })
        ->orderBy('created_at', 'desc')
        ->paginate($perPage);

        return response()->json([
            'data' => array_reverse($messages->items()),
            'meta' => [
                'current_page' => $messages->currentPage(),
                'last_page' => $messages->lastPage(),
                'per_page' => $messages->perPage(),
                'total' => $messages->total(),
                'total_pages' => $messages->lastPage(),
            ],
        ]);
    }
} 