<?php

namespace App\Http\Requests\Admin\Packages;

use App\Http\Requests\BaseRequest\BaseRequest;
use Illuminate\Validation\Rule;

enum AssignTypeEnum: string
{
 case SYSTEM = 'system';
 case SCHOOL = 'school';
 case TEACHER = 'teacher';

 public static function values(): array
 {
  return array_map(fn($case) => $case->value, self::cases());
 }
}

class PackagesStoreRequest extends BaseRequest
{
 public function authorize(): bool
 {
  return true;
 }

 public function rules(): array
 {
  return [

   'name'           => 'required|string|max:255',
   'description'    => 'nullable|string',
   'price'          => 'required|numeric',
   'duration_days'  => 'required|integer',

   // assign_type using enum directly
   'assign_type' => [
    'required',
    Rule::in(AssignTypeEnum::values())
   ],

   'assignable_id' => [
    Rule::requiredIf(fn() => in_array($this->assign_type, [
     AssignTypeEnum::SCHOOL->value,
     AssignTypeEnum::TEACHER->value,
    ])),

    Rule::excludeIf(fn() => $this->assign_type === AssignTypeEnum::SYSTEM->value),

    // Rule::exists('users', 'id'),
   ],
  ];
 }
}
