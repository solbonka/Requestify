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

class RequestController extends Controller
{
    private RequestService $requestService;

    public function __construct(RequestService $requestService)
    {
        $this->requestService = $requestService;
    }
    public function create(CreateRequestRequest $request)
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
            'message' => 'Заявка успешно создана'
        ], 201);
    }

    public function getAll(GetRequestsRequest $request): JsonResponse
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
                    'message' => 'Requests retrieved successfully.'
                ]);
            } else {
                throw new ModelNotFoundException();
            }
        } catch (ModelNotFoundException $exception) {
            return response()->json([
                'message' => 'No requests found.'
            ], 404);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'An error occurred while retrieving requests.'
            ], 500);
        }
    }

    public function resolve(HttpRequest $request, $id)
    {
        $request->validate([
            'comment' => 'required|string',
        ]);
        $requestModel = Request::findOrFail($id);
        $comment = $request->comment;
        $message = $this->requestService->resolve($requestModel, $comment);

        return response()->json([
            'data' => $requestModel->toArray(),
            'message' => $message,
        ]);
    }
}
