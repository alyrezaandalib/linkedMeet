<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\User\NearbyUsersRequest;
use App\Http\Resources\V1\NearbyUsersResource;
use App\Models\User;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class NearbyUsersController extends Controller
{
    #[OA\Get(
        path: "/api/v1/user/nearby-users",
        summary: "Get users within 500 meters of the authenticated user",
        security: [["sanctum" => []]],
        tags: ["User"],
        parameters: [
            new OA\QueryParameter(
                name: "job_title_ids[]",
                description: "Filter by job title IDs",
                in: "query",
                required: false,
                schema: new OA\Schema(
                    type: 'array',
                    items: new OA\Items(type: 'integer')
                ),
                style: "form",
                explode: true,
                allowReserved: true
            ),
            new OA\QueryParameter(
                name: "industry_ids[]",
                description: "Filter by industry IDs",
                in: "query",
                required: false,
                schema: new OA\Schema(
                    type: 'array',
                    items: new OA\Items(type: 'integer')
                ),
                style: "form",
                explode: true,
                allowReserved: true
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of nearby users",
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
                                        example: "John Doe"
                                    ),
                                    new OA\Property(
                                        property: "avatar",
                                        type: "string",
                                        example: "https://linkedmeet.com/storage/images/avatars/default.png"
                                    ),
                                    new OA\Property(
                                        property: "industry",
                                        type: "string",
                                        example: "Ltd"
                                    ),
                                    new OA\Property(
                                        property: "job_title",
                                        type: "string",
                                        example: "Agricultural Inspector"
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
                response: 400,
                description: 'User location not set.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            type: 'string',
                            example: 'User location not set.'
                        ),
                    ],
                    type: 'object'
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
                            example: "Unauthenticated."
                        ),
                    ],
                    type: "object"
                )
            ),
        ]
    )]
    public function index(NearbyUsersRequest $request)
    {
        $user = $request->user();

        if (!$user->location) {
            return response()->json([
                'message' => 'User location not set.',
            ], 400);
        }

        $jobTitleIds = $request->job_title_ids;
        $industryIds = $request->industry_ids;

        $distance = 500 / 1000;

        $latitude = $user->location->latitude;
        $longitude = $user->location->longitude;

        $earthRadius = 6371;

        $latDelta = $distance / $earthRadius;
        $lngDelta = $distance / ($earthRadius * cos(deg2rad($latitude)));

        $minLat = $latitude - $latDelta;
        $maxLat = $latitude + $latDelta;
        $minLng = $longitude - $lngDelta;
        $maxLng = $longitude + $lngDelta;

        $nearbyUsers = User::where('id', '!=', $user->id)
            ->with(['location', 'userDetails.industry', 'userDetails.jobTitle'])
            ->whereHas(
                'location',
                function ($query) use ($minLat, $maxLat, $minLng, $maxLng, $latitude, $longitude, $distance) {
                    $query->whereBetween('latitude', [$minLat, $maxLat])
                        ->whereBetween('longitude', [$minLng, $maxLng])
                        ->whereRaw("
                      (6371 * acos(
                          cos(radians(?)) *
                          cos(radians(latitude)) *
                          cos(radians(longitude) - radians(?)) +
                          sin(radians(?)) *
                          sin(radians(latitude))
                      )) < ?
                  ", [$latitude, $longitude, $latitude, $distance]);
                })
            ->when($jobTitleIds, function ($query, $jobTitleIds) {
                $query->whereHas('userDetails', function ($q) use ($jobTitleIds) {
                    $q->whereIn('job_title_id', $jobTitleIds);
                });
            })
            ->when($industryIds, function ($query, $industryIds) {
                $query->whereHas('userDetails', function ($q) use ($industryIds) {
                    $q->whereIn('industry_id', $industryIds);
                });
            })->get();


        return response()->json([
            'data' => NearbyUsersResource::collection($nearbyUsers),
        ]);
    }
}
