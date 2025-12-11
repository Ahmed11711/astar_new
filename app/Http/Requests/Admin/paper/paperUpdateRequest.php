<?php

namespace App\Http\Requests\Admin\paper;

use App\Http\Requests\BaseRequest\BaseRequest;

class paperUpdateRequest extends BaseRequest
{
 public function authorize(): bool
 {
  return true;
 }

 public function rules(): array
 {
  return [
   'name' => 'nullable|string',
   'type' => 'nullable|string',
  ];
 }
}
