<?php

namespace App\Http\Controllers\Api\V1\Chat;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Chat\SendMessageRequest;
use App\Models\Message;
use OpenApi\Attributes as OA;

class SendMessageController extends Controller
{

    #[OA\Post(
        path: "/api/v1/chat/send-message",
        description: "Send a chat message to a specific user.",
        summary: "Send Chat Message",
        security: [["sanctum" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["receiver_id", "message"],
                properties: [
                    new OA\Property(
                        property: "receiver_id",
                        description: "The ID of the user receiving the message.",
                        type: "integer",
                        example: 123
                    ),
                    new OA\Property(
                        property: "message",
                        description: "The content of the message. Maximum length is 1000 characters.",
                        type: "string",
                        maxLength: 1000,
                        example: "Hello, how are you?"
                    ),
                ],
                type: "object"
            )
        ),
        tags: ["Chat"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Message sent successfully.",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "message",
                            type: "string",
                            example: "Message sent successfully."
                        ),
                    ],
                    type: "object"
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
                            example: "The message field is required."
                        ),
                        new OA\Property(
                            property: "errors",
                            properties: [
                                new OA\Property(
                                    property: "message",
                                    type: "array",
                                    items: new OA\Items(
                                        type: "string",
                                        example: "The message field is required."
                                    )
                                ),
                            ],
                            type: "object"
                        ),
                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 401,
                description: "Unauthorized. User must be authenticated.",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "message",
                            type: "string",
                            example: "Unauthenticated."
                        ),
                    ],
                    type: "object"
                )
            ),
        ]
    )]
    public function sendMessage(SendMessageRequest $request)
    {
        Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        broadcast(
            new MessageSent(
                auth()->id(),
                $request->receiver_id,
                $request->message
            )
        )->toOthers();

        return response()
            ->json(['message' => 'Message sent successfully']);
    }
}
