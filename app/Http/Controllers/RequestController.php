<?php

namespace App\Http\Controllers;

use App\Models\Request;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Mail;

class RequestController extends Controller
{
    public function create(HttpRequest $request)
    {
        // Проверяем входящие данные для создания заявки
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'message' => 'required|string',
        ]);

        // Создаем новую заявку
        $requestModel = Request::create([
            'name' => $request->name,
            'email' => $request->email,
            'message' => $request->message,
            'status' => 'Active',
        ]);

        // Отправляем уведомление пользователю по электронной почте
        $this->sendResponseEmail($requestModel);

        return response()->json(['message' => 'Заявка успешно создана'], 201);
    }
}
