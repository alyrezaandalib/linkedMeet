<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\User\UpdateLocationRequest;
use OpenApi\Attributes as OA;

class LocationController extends Controller
{
    #[OA\Post(
        path: '/api/v1/user/location',
        description: 'Update the authenticated user\'s geographic location.',
        summary: 'Update user location',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: 'latitude',
                        description: 'Latitude of the location.',
                        type: 'number',
                        example: 35.6892
                    ),
                    new OA\Property(
                        property: 'longitude',
                        description: 'Longitude of the location.',
                        type: 'number',
                        example: 51.3890
                    ),
                ]
            )
        ),
        tags: ['User'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Location updated successfully.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'Location updated successfully.'
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
                                'latitude' => [
                                    'The latitude field is required.',
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
    public function update(UpdateLocationRequest $request)
    {
        $user = $request->user();

        $user->location()->updateOrCreate(
            [
                'user_id' => $user->id,
            ], [
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]
        );

        return response()
            ->json([
                'message' => 'Location updated successfully.',
            ]);
    }
}
