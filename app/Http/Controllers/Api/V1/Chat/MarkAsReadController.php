<?php

namespace App\Http\Controllers\Api\V1\Chat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class MarkAsReadController extends Controller
{
    #[OA\Post(
        path: '/api/v1/chat/mark-as-read',
        description: 'Mark all unread messages as read for the authenticated user.',
        summary: 'Mark messages as read',
        security: [['sanctum' => []]],
        tags: ['Chat'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Messages marked as read successfully.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'Messages marked as read successfully.'
                        ),
                        new OA\Property(
                            property: 'marked_count',
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
    public function markAsRead(Request $request)
    {
        $user = $request->user();

        // Get count of unread messages before marking them as read
        $unreadCount = \App\Models\Message::where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->count();

        // Mark all unread messages as read
        \App\Models\Message::where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'message' => 'Messages marked as read successfully.',
            'marked_count' => $unreadCount,
        ]);
    }
} 