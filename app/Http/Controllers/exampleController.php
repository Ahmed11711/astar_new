<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExamPaperRequest;
use App\Http\Service\ExamPaperService;
use Illuminate\Http\Request;

class exampleController extends Controller
{

 public function store(ExamPaperRequest $request, ExamPaperService $service)
 {
  $paper = $service->createExamPaperWithQuestions($request->validated());

  return response()->json([
   'success' => true,
   'data' => $paper
  ], 201);
 }
}
