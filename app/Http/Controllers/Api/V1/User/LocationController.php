<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\User\UpdateLocationRequest;
use OpenApi\Attributes as OA;
use Illuminate\Http\Request;

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

    #[OA\Patch(
        path: '/api/v1/user/gps-status',
        description: 'Update the authenticated user\'s GPS status (on/off).',
        summary: 'Update user GPS status',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: 'is_gps_enabled',
                        description: 'Whether GPS is enabled (true/false).',
                        type: 'boolean',
                        example: true
                    ),
                ]
            )
        ),
        tags: ['User'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'GPS status updated successfully.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'GPS status updated successfully.'
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
                                'is_gps_enabled' => [
                                    'The is_gps_enabled field is required.'
                                ]
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
    public function updateGpsStatus(Request $request)
    {
        $request->validate([
            'is_gps_enabled' => 'required|boolean',
        ]);
        $user = $request->user();
        $user->location()->updateOrCreate(
            [ 'user_id' => $user->id ],
            [ 'is_gps_enabled' => $request->is_gps_enabled ]
        );
        return response()->json(['message' => 'GPS status updated successfully.']);
    }

    #[OA\Get(
        path: '/api/v1/user/gps-status',
        description: 'Get the authenticated user\'s GPS status (on/off).',
        summary: 'Get user GPS status',
        security: [['sanctum' => []]],
        tags: ['User'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Current GPS status.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'is_gps_enabled',
                            type: 'boolean',
                            example: true
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
    public function getGpsStatus(Request $request)
    {
        $user = $request->user();
        $location = $user->location;
        return response()->json([
            'is_gps_enabled' => $location ? (bool)$location->is_gps_enabled : false
        ]);
    }
}
