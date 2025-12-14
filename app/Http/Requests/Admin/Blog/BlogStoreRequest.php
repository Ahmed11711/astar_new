<?php

namespace App\Http\Requests\Admin\Blog;

use App\Http\Requests\BaseRequest\BaseRequest;

class BlogStoreRequest extends BaseRequest
{
 public function authorize(): bool
 {
  return true;
 }

 public function rules(): array
 {
  return [
   'title' => 'required|string|max:255',
   'slug' => 'required|string|max:255|unique:blogs,slug',
   'content' => 'required|string',
   'img' => 'nullable|file',
   'author_id' => 'nullable|integer',
   'is_published' => 'required|integer',
  ];
 }
}
