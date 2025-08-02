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
                                property: 'job_title',
                                type: 'string',
                                example: 'Software Engineer'
                            ),
                            new OA\Property(
                                property: 'industry',
                                type: 'string',
                                example: 'Technology'
                            ),
                            new OA\Property(
                                property: 'started_at',
                                type: 'string',
                                format: 'date-time',
                                example: '2024-12-15T10:00:00Z'
                            ),
                            new OA\Property(
                                property: 'last_message_at',
                                type: 'string',
                                format: 'date-time',
                                example: '2024-12-15T12:30:00Z'
                            ),
                            new OA\Property(
                                property: 'message',
                                type: 'string',
                                example: 'Hello! How are you?'
                            ),
                            new OA\Property(
                                property: 'has_new_messages',
                                type: 'integer',
                                example: 3
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

        // Get unique chat partners from messages table
        $chatPartners = \App\Models\Message::where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->selectRaw('
                CASE 
                    WHEN sender_id = ? THEN receiver_id 
                    ELSE sender_id 
                END as partner_id,
                MIN(created_at) as started_at,
                MAX(created_at) as last_message_at,
                SUBSTRING_INDEX(GROUP_CONCAT(message ORDER BY created_at DESC), ",", 1) as message
            ', [$user->id])
            ->groupBy('partner_id')
            ->orderBy('last_message_at', 'desc')
            ->get();

        // Get user details for each partner
        $chatUsers = [];
        foreach ($chatPartners as $partner) {
            $partnerUser = \App\Models\User::with(['userDetails.jobTitle', 'userDetails.industry'])
                ->select('id', 'name', 'avatar')
                ->find($partner->partner_id);
            
            if ($partnerUser) {
                // Count unread messages from this partner
                $unreadCount = \App\Models\Message::where('sender_id', $partner->partner_id)
                    ->where('receiver_id', $user->id)
                    ->whereNull('read_at')
                    ->count();

                $chatUsers[] = [
                    'id' => $partnerUser->id,
                    'name' => $partnerUser->name,
                    'avatar' => $partnerUser->avatar,
                    'job_title' => $partnerUser->userDetails?->jobTitle?->name ?? null,
                    'industry' => $partnerUser->userDetails?->industry?->name ?? null,
                    'started_at' => $partner->started_at,
                    'last_message_at' => $partner->last_message_at,
                    'message' => $partner->message,
                    'has_new_messages' => $unreadCount,
                ];
            }
        }

        return response()
            ->json([
                'data' => $chatUsers,
            ]);
    }
}
