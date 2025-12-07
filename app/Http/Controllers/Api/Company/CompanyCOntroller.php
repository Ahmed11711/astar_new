<?php

namespace App\Http\Controllers\Api\Company;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\setting;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class CompanyCOntroller extends Controller
{
    use ApiResponseTrait;
    public function index()
    {
        $company=Company::get();
        return $this->successResponse($company,'All Companies',200);

    }

    public function setting()
    {
        $settings=setting::get();
        return $this->successResponse($settings,'All settings',200);
    }
}
