<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\User\CompanyActivityTypeStoreRequest;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class UserCompanyActivityTypeController extends Controller
{

    #[OA\Get(
        path: '/api/v1/user/company-activity-types',
        summary: 'Get company activity types selected by the authenticated user',
        security: [['sanctum' => []]],
        tags: ['User'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of selected company activity types',
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
        ]
    )]
    public function index(Request $request)
    {
        $user = $request->user();

        $selectedActivityTypes = $user->companyActivityTypes()
            ->get(['id', 'name'])
            ->makeHidden('pivot');

        return response()->json([
            'data' => $selectedActivityTypes,
        ]);
    }

    #[OA\Put(
        path: '/api/v1/user/company-activity-types',
        summary: 'Update company activity types for the authenticated user',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: 'activity_type_ids',
                        description: 'Array of company activity type IDs',
                        type: 'array',
                        items: new OA\Items(type: 'integer')
                    ),
                ],
                type: 'object',
                example: ['activity_type_ids' => [1, 2, 3]]
            )
        ),
        tags: ['User'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successfully updated company activity types',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'Company activity types updated successfully.'
                        ),
                    ],
                    type: 'object'
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
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Validation error.'),
                        new OA\Property(
                            property: "errors",
                            properties: [
                                new OA\Property(
                                    property: "field",
                                    type: "string",
                                    example: ""
                                ),
                            ],
                            type: "object"
                        ),
                    ],
                    type: 'object'
                )
            ),
        ]
    )]
    public function store(CompanyActivityTypeStoreRequest $request)
    {
        $user = $request->user();

        $user->companyActivityTypes()->sync($request->activity_type_ids);

        return response()->json([
            'message' => 'Company activity types updated successfully.',
        ]);
    }
}
