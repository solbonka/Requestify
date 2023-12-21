<?php

namespace App\Http\Controllers\Swagger;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'My API',
)]
#[OA\PathItem(path: '/api/docs')]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    bearerFormat: 'JWT',
    scheme: 'bearer',
)]
class SwaggerController extends Controller
{
    public function index(): Factory|View|Application
    {
        $swaggerJsonFile = 'swagger.json';

        return view('swagger', compact('swaggerJsonFile'));
    }
}
