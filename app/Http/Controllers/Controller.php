<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\PathItem(path: "/api")]
#[OA\Info(
    version: "1.0.0",
    description: "API documentation for LinkedMeet app",
    title: "LinkedMeet API",
)]
abstract class Controller
{
    //
}
