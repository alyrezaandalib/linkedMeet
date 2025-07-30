<?php

namespace App\Http\Controllers\Api\V1\App;

use App\Http\Controllers\Controller;
use App\Models\Industry;
use OpenApi\Attributes as OA;

class IndustryController extends Controller
{

    #[OA\Get(
        path: "/api/v1/app/industries",
        summary: "Get list of industries",
        security: [["sanctum" => []]],
        tags: ["App"],
        parameters: [
            new OA\Parameter(
                name: "q",
                in: "query",
                required: false,
                description: "Search industries by name",
                schema: new OA\Schema(type: "string", example: "tech")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of industries",
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
                                        example: "Technology"
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
        $query = request('q');
        $industries = Industry::query()
            ->when($query, function($q) use ($query) {
                $q->where('name', 'like', "%$query%");
            })
            ->get(['id', 'name']);
        return response()->json([
            'data' => $industries
        ]);
    }
}
