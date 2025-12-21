<?php

namespace App\Http\Requests\Student\Ai;

use App\Http\Requests\BaseRequest\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class AiChateRequest extends BaseRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'role'      => 'required|in:user,assistant',
            'title'     => 'nullable|string',
            'content'   => 'required|string',
            'parent_id' => 'nullable|exists:chat_ais,id',
        ];
    }
}
