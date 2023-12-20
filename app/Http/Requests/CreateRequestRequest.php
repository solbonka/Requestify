<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateRequestRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Можно настроить проверку авторизации здесь, если необходимо
    }

    public function rules()
    {
        return [
            'message' => 'required|string',
        ];
    }
}
