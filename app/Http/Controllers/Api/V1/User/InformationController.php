<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\User\UpdateInformationRequest;
use OpenApi\Attributes as OA;

class InformationController extends Controller
{
    #[OA\Patch(
        path: "/api/v1/user/information",
        description: "Update the logged-in user's job title and industry information.",
        summary: "Update User Information",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["job_title_id", "industry_id"],
                properties: [
                    new OA\Property(
                        property: "job_title_id",
                        description: "The ID of the job title.",
                        type: "integer",
                        example: 3
                    ),
                    new OA\Property(
                        property: "industry_id",
                        description: "The ID of the industry.",
                        type: "integer",
                        example: 5
                    ),
                ],
                type: "object"
            )
        ),
        tags: ["User"],
        responses: [
            new OA\Response(
                response: 200,
                description: "User information updated successfully.",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "message",
                            type: "string",
                            example: "User information updated successfully."
                        ),
                    ],
                    type: "object"
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error or incorrect current password.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'The industry id field is required.'
                        ),
                        new OA\Property(
                            property: "errors",
                            properties: [
                                new OA\Property(
                                    property: "industry_id",
                                    type: "array",
                                    items: new OA\Items(
                                        type: "string",
                                        example: "The industry id field is required."
                                    )
                                ),
                            ],
                            type: "object"
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
    public function update(UpdateInformationRequest $request)
    {
        $user = $request->user();

        $user->userDetails()->update([
            'job_title_id' => $request->job_title_id,
            'industry_id' => $request->industry_id,
        ]);

        return response()->json([
            'message' => 'User information updated successfully.',
        ]);
    }
}
