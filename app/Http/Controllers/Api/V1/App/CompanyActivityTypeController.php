<?php

namespace App\Http\Controllers\Api\V1\App;

use App\Http\Controllers\Controller;
use App\Models\CompanyActivityType;
use OpenApi\Attributes as OA;

class CompanyActivityTypeController extends Controller
{

    #[OA\Get(
        path: '/api/v1/app/company-activity-types',
        summary: 'Get all company activity types',
        security: [['sanctum' => []]],
        tags: ['App'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of company activity types',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'id', type: 'integer', example: 1),
                                    new OA\Property(property: 'name', type: 'string', example: 'Technology'),
                                ],
                                type: 'object'
                            )
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorized',
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
        ],
    )]
    public function index()
    {
        $activityTypes = CompanyActivityType::all(['id', 'name']);

        return response()->json([
            'data' => $activityTypes,
        ]);
    }


}
