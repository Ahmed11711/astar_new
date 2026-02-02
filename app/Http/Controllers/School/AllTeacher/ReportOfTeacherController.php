<?php

namespace App\Http\Controllers\School\AllTeacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\School\UpdateReportSchoolRequest;
use App\Models\Report;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class ReportOfTeacherController extends Controller
{

    use ApiResponseTrait;
    public function index(Request $request)
    {
        $userId = $request->user_id;
        $reports = Report::with('teacher:id,email')
            ->where('type', $userId)
            ->latest()
            ->get();
        return $this->successResponse($reports, "");
    }
    public function update(UpdateReportSchoolRequest $request, $id)
    {
        $userId = $request->user_id;
        $report = Report::where('type', $userId)->find($id);
        if (!$report) {
            return $this->errorResponse('Report not found', 404);
        }
        $report->status = $request->status;
        $report->response = $request->response;
        $report->save();
        return $this->successResponse($report, "Report status updated successfully");
    }
}
