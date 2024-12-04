<?php

namespace App\Http\Controllers\Api\V1\App;

use App\Http\Controllers\Controller;
use App\Models\JobTitle;
use OpenApi\Attributes as OA;

class JobTitleController extends Controller
{

    #[OA\Get(
        path: "/api/v1/app/job-titles",
        summary: "Get list of job titles",
        security: [["sanctum" => []]],
        tags: ["App"],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of job titles",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: "data",
                            type: "array",
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(
                                        property: "id",
                                        type: "integer",
                                        example: 1
                                    ),
                                    new OA\Property(
                                        property: "name",
                                        type: "string",
                                        example: "Software Engineer"
                                    ),
                                ],
                                type: "object"
                            )
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
    public function index()
    {
        $jobTitles = JobTitle::all(['id', 'name']);
        return response()->json(['data' => $jobTitles]);
    }
}
