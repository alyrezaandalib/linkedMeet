<?php

namespace App\Http\Controllers\Api\V1\Chat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class UnreadMessagesCountController extends Controller
{
    #[OA\Get(
        path: '/api/v1/chat/unread-messages-count',
        description: 'Get the count of unread messages for the authenticated user.',
        summary: 'Get unread messages count',
        security: [['sanctum' => []]],
        tags: ['Chat'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Unread messages count retrieved successfully.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'unread_messages_count',
                            type: 'integer',
                            example: 5
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
        ]
    )]
    public function index(Request $request)
    {
        $user = $request->user();

        $unreadCount = \App\Models\Message::where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'unread_messages_count' => $unreadCount,
        ]);
    }
} 