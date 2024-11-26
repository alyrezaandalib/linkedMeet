<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\AppVersion\CheckVersionRequest;
use App\Models\AppVersion;
use OpenApi\Attributes as OA;

class AppVersionController extends Controller
{
    #[OA\Get(
        path: '/api/v1/app/check-version',
        operationId: 'checkVersion',
        description: 'Checks the current app version against the latest version and returns whether an update is available, and if so, whether it\'s mandatory.',
        summary: 'Check for app version and update status',
        tags: ['App'],
        parameters: [
            new OA\Parameter(
                name: 'current_version',
                description: 'The current version of the app',
                in: 'query',
                required: true,
                schema: new OA\Schema(type: 'string', example: '1.1.3')
            ),
            new OA\Parameter(
                name: 'platform',
                description: 'The platform of the app (android, ios, or web)',
                in: 'query',
                required: true,
                schema: new OA\Schema(type: 'string', enum: ['android', 'ios', 'web'], example: 'android')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'App version information and update status',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'current_version',
                            description: 'Current version of the app',
                            type: 'string',
                            example: '1.1.3'
                        ),
                        new OA\Property(
                            property: 'latest_version',
                            description: 'Latest version of the app',
                            type: 'string',
                            example: '1.2.0'
                        ),
                        new OA\Property(
                            property: 'is_mandatory',
                            description: 'Indicates if the update is mandatory',
                            type: 'boolean',
                            example: true
                        ),
                        new OA\Property(
                            property: 'message',
                            description: 'Message to the user regarding the update status',
                            type: 'string',
                            example: 'A new version is available. Do you want to update?'
                        ),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Platform not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Platform not found.'),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error - The request parameters are not valid',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'message',
                            description: 'A general error message',
                            type: 'string',
                            example: 'The selected platform is invalid. (and 1 more error)'),
                        new OA\Property(
                            property: 'errors',
                            description: 'An object containing validation errors for each parameter',
                            type: 'object',
                        ),
                    ]
                )
            ),
        ]
    )]
    public function checkVersion(CheckVersionRequest $request)
    {
        $currentVersion = $request->current_version;

        $latestVersion = AppVersion::where('platform', $request->platform)
            ->orderByDesc('created_at')
            ->first();

        if (!$latestVersion) {
            return response()->json([
                'message' => _('Platform not found.'),
            ], 404);
        }

        $isUpdateRequired = version_compare($currentVersion, $latestVersion->version, '<');

        return response()->json([
            'current_version' => $currentVersion,
            'latest_version' => $latestVersion->version,
            'is_mandatory' => $latestVersion->is_mandatory,
            'message' => $isUpdateRequired
                ? ($latestVersion->is_mandatory
                    ? _('A new version is mandatory. Please update your app.')
                    : _('A new version is available. Do you want to update?'))
                : _('You are using the latest version.'),
        ]);
    }

}
