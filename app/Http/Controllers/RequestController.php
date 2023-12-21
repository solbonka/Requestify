<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateRequestRequest;
use App\Http\Requests\GetRequestsRequest;
use App\Http\Resources\RequestResource;
use App\Models\Request;
use App\Services\RequestService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Auth;
use Exception;
use OpenApi\Attributes as OA;

class RequestController extends Controller
{
    private RequestService $requestService;

    public function __construct(RequestService $requestService)
    {
        $this->requestService = $requestService;
    }

    #[OA\Post(
        path: '/api/requests',
        summary: 'Создание заявки',
        security: [
            ['bearerAuth' => []],
        ],
        requestBody: new OA\RequestBody(
            content: new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(
                    required: ['message'],
                    properties: [
                        new OA\Property(property: 'message', type: 'string'),
                    ]
                ),
            ),
        ),
        tags: ['Requests'],
        responses: [
            new OA\Response(
                response: 201,
                description: "Заявка успешно создана",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', properties: []),
                        new OA\Property(property: 'message', type: 'string'),
                    ],
                ),
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorised',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string'),
                    ],
                ),
            ),
            new OA\Response(
                response: 422,
                description: 'The message field is required.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string'),
                        new OA\Property(property: 'errors', properties: []),
                    ],
                ),
            ),
        ],
    )]
    public function create(CreateRequestRequest $request): JsonResponse
    {
        $user = Auth::user();

        $requestModel = Request::create([
            'name' => $user->name,
            'email' => $user->email,
            'message' => $request->message,
            'status' => 'Active',
            'user_id' => $user->id,
        ]);

        return response()->json([
            'data' => $requestModel->toArray(),
            'message' => 'Заявка успешно создана',
        ], 201);
    }

    #[OA\Post(
        path: '/api/requests/filtered',
        summary: 'Получение заявок по статусу и дате',
        security: [
            ['bearerAuth' => []],
        ],
        requestBody: new OA\RequestBody(
            content: new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: 'status', type: 'string'),
                        new OA\Property(property: 'start_date', type: 'string', format: 'date'),
                        new OA\Property(property: 'end_date', type: 'string', format: 'date'),
                    ]
                ),
            ),
        ),
        tags: ['Requests'],
        responses: [
            new OA\Response(
                response: 200,
                description: "Requests retrieved successfully.",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', properties: []),
                        new OA\Property(property: 'message', type: 'string'),
                    ],
                ),
            ),
            new OA\Response(
                response: 400,
                description: 'You\'re not admin',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'bool', example: false),
                        new OA\Property(property: 'message', type: 'string'),
                    ],
                ),
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorised',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string'),
                    ],
                ),
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string'),
                        new OA\Property(property: 'errors', properties: []),
                    ],
                ),
            ),
            new OA\Response(
                response: 404,
                description: 'No requests found.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string'),
                    ],
                ),
            ),
            new OA\Response(
                response: 500,
                description: 'Ошибка сервера',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string'),
                    ],
                ),
            ),
        ],
    )]
    public function getByStatusAndDate(GetRequestsRequest $request): JsonResponse
    {
        try {
            $status = $request->status;
            $startDate = $request->start_date;
            $endDate = $request->end_date;
            $requests = Request::filterByStatus($status)->filterByDate($startDate, $endDate)->get();
            $result = RequestResource::collection($requests);

            if ($result->count()) {
                return response()->json([
                    'data' => $result,
                    'message' => 'Requests retrieved successfully.',
                ]);
            } else {
                throw new ModelNotFoundException();
            }
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'message' => 'No requests found.',
            ], 404);
        } catch (Exception $exception) {
            return response()->json([
                'message' => 'An error occurred while retrieving requests.',
            ], 500);
        }
    }

    #[OA\Get(
        path: '/api/requests',
        summary: 'Получение активных заявок',
        security: [
            ['bearerAuth' => []],
        ],
        tags: ['Requests'],
        responses: [
            new OA\Response(
                response: 200,
                description: "Requests retrieved successfully.",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', properties: []),
                        new OA\Property(property: 'message', type: 'string'),
                    ],
                ),
            ),
            new OA\Response(
                response: 400,
                description: 'You\'re not admin',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'bool', example: false),
                        new OA\Property(property: 'message', type: 'string'),
                    ],
                ),
            ),
            new OA\Response(
                response: 401,
                description: 'Unauthorised',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string'),
                    ],
                ),
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string'),
                        new OA\Property(property: 'errors', properties: []),
                    ],
                ),
            ),
            new OA\Response(
                response: 404,
                description: 'Заявки не найдены',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string'),
                    ],
                ),
            ),
            new OA\Response(
                response: 500,
                description: 'Ошибка сервера',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string'),
                    ],
                ),
            ),
        ],
    )]
    public function getActive(): JsonResponse
    {
        try {
            $requests = Request::filterByStatus('Active')->get();
            $result = RequestResource::collection($requests);

            if ($result->count()) {
                return response()->json([
                    'data' => $result,
                    'message' => 'Requests retrieved successfully.',
                ]);
            } else {
                throw new ModelNotFoundException();
            }
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'message' => 'No requests found.',
            ], 404);
        } catch (Exception $exception) {
            return response()->json([
                'message' => 'An error occurred while retrieving requests.',
            ], 500);
        }
    }

    #[OA\Put(
        path: '/api/requests/{id}',
        summary: 'Разрешение заявки',
        security: [
            ['bearerAuth' => []],
        ],
        requestBody: new OA\RequestBody(
            content: new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(
                    required: ['comment'],
                    properties: [
                        new OA\Property(property: 'comment', type: 'string'),
                    ],
                ),
            ),
        ),
        tags: ['Requests'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Заявка разрешена успешно',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', properties: []),
                        new OA\Property(property: 'message', type: 'string'),
                    ],
                ),
            ),
            new OA\Response(
                response: 401,
                description: 'Неавторизован',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string'),
                    ],
                ),
            ),
            new OA\Response(
                response: 404,
                description: 'Заявка не найдена',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string'),
                    ],
                ),
            ),
            new OA\Response(
                response: 422,
                description: 'Некорректный комментарий',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string'),
                        new OA\Property(property: 'errors', properties: []),
                    ],
                ),
            ),
        ],
    )]
    public function resolve(HttpRequest $request, $id): JsonResponse
    {
        try {
            $adminId = Auth::user()->id;
            $request->validate([
                'comment' => 'required|string',
            ]);
            $requestModel = Request::findOrFail($id);
            $comment = $request->comment;
            $result = $this->requestService->resolve($requestModel, $adminId, $comment);

            return response()->json($result);
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'message' => 'Заявка не найдена.',
            ], 404);
        } catch (Exception $exception) {
            return response()->json([
                'message' => 'Произошла ошибка при разрешении заявки.',
            ], 500);
        }
    }

    #[OA\Delete(
        path: '/api/requests',
        summary: 'Удаление заявки',
        security: [
            ['bearerAuth' => []],
        ],
        requestBody: new OA\RequestBody(
            content: new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(
                    required: ['id'],
                    properties: [
                        new OA\Property(property: 'id', type: 'integer'),
                    ],
                ),
            ),
        ),
        tags: ['Requests'],
        responses: [
            new OA\Response(
                response: 204,
                description: 'Заявка успешно удалена',
            ),
            new OA\Response(
                response: 401,
                description: 'Неавторизован',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string'),
                    ],
                ),
            ),
            new OA\Response(
                response: 404,
                description: 'Заявка не найдена',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string'),
                    ],
                ),
            ),
            new OA\Response(
                response: 422,
                description: 'Некорректный идентификатор заявки',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string'),
                        new OA\Property(property: 'errors', properties: []),
                    ],
                ),
            ),
        ],
    )]
    public function delete(HttpRequest $request): JsonResponse
    {
        try {
            $request->validate([
                'id' => 'required|int',
            ]);

            $result = Request::where('user_id', auth()->id())->findOrFail($request->id);
            $result->delete();

            return response()->json([
                'message' => 'Заявка успешно удалена.',
            ], 204);
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'message' => 'Заявка не найдена.',
            ], 404);
        } catch (Exception $exception) {
            return response()->json([
                'message' => 'Произошла ошибка при удалении заявки.',
            ], 500);
        }
    }
}
