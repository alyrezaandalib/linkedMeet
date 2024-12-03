<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\PathItem(path: "/api")]
#[OA\Info(
    version: "1.2.5",
    description: "API documentation for LinkedMeet app",
    title: "LinkedMeet API",
)]
#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'http',
    bearerFormat: 'JWT',
    scheme: 'bearer'
)]
abstract class Controller
{
    //
}
