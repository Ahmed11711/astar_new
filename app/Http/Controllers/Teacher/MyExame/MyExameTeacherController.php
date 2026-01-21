<?php

namespace App\Http\Controllers\Teacher\MyExame;

use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\StudentQuizeRequest;
use App\Models\ExamPaper;
use Illuminate\Http\Request;

class MyExameTeacherController extends Controller
{
    public function index(Request $request) {}

    public function show(StudentQuizeRequest $request) {}
}
