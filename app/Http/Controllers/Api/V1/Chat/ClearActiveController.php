<?php

namespace App\Http\Controllers\Api\V1\Chat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class ClearActiveController extends Controller
{
    #[OA\Post(
        path: '/api/v1/chat/clear-active',
        description: 'Clear active chat status for messages from a specific sender.',
        summary: 'Clear active chat',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: 'sender_id',
                        description: 'ID of the sender whose messages should be marked as inactive.',
                        type: 'integer',
                        example: 7
                    ),
                ]
            )
        ),
        tags: ['Chat'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Active chat cleared successfully.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'Active chat cleared successfully.'
                        ),
                        new OA\Property(
                            property: 'cleared_count',
                            type: 'integer',
                            example: 3
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
                                'sender_id' => [
                                    'The sender id field is required.',
                                ],
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
        ]
    )]
    public function clearActive(Request $request)
    {
        $request->validate([
            'sender_id' => 'required|integer|exists:users,id',
        ]);

        $user = $request->user();

        // Get count of active messages from this sender before clearing
        $activeCount = \App\Models\Message::where('sender_id', $request->sender_id)
            ->where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->count();

        // Mark messages from this sender as read (clear active status)
        \App\Models\Message::where('sender_id', $request->sender_id)
            ->where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'message' => 'Active chat cleared successfully.',
            'cleared_count' => $activeCount,
        ]);
    }
} 