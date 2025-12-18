<?php

namespace App\Http\Requests\Student\Attmpate;

use App\Http\Controllers\BaseController\BaseController;
use App\Http\Requests\BaseRequest\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class CreateAttmpateRequest extends BaseRequest
{

    public function rules(): array
    {
        return [
            'exam_id' => ['required', 'exists:exam_papers,id'],
            'paper_id' => ['required', 'exists:papers,id'],
        ];
    }
}
