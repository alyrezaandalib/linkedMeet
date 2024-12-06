<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\User\UpdateJobTitleRequest;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class JobTitleController extends Controller
{


    #[OA\Patch(
        path: "/api/v1/user/job-title",
        summary: "Update the job title for the authenticated user",
        security: [["sanctum" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: "job_title_id",
                        description: "The ID of the job title",
                        type: "integer",
                        example: 1
                    ),
                ],
                type: "object"
            )
        ),
        tags: ["User"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Successfully updated the job title",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "message",
                            type: "string",
                            example: "Job title updated successfully."
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
                            example: "Validation error."
                        ),
                        new OA\Property(
                            property: "errors",
                            type: "object"
                        ),
                    ],
                    type: "object"
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
                            example: "Unauthorized"
                        ),
                    ],
                    type: "object"
                )
            ),
        ]
    )]
    public function update(UpdateJobTitleRequest $request)
    {
        $user = $request->user();

        $user->userDetails()->update([
            'job_title_id' => $request->job_title_id,
        ]);

        return response()->json([
            'message' => 'Job title updated successfully.',
        ]);
    }

}
