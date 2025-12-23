<?php

namespace App\Http\Service\Admin;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardStatsService
{
    public function get()
    {
        return [
            'users_by_role' => $this->usersByRole(),
            'students_by_type'     => $this->studentsByType(),

            'students_per_subject' => $this->studentsPerSubject(),
            'students_per_package' => $this->studentsPerPackage(),
            'students_per_grade' => $this->studentsPerGrade(),

        ];
    }

    protected function usersByRole()
    {
        return User::select('role', DB::raw('COUNT(*) as total'))
            ->groupBy('role')
            ->pluck('total', 'role');
    }
    protected function studentsByType()
    {
        return User::where('role', 'student')
            ->select('student_type', DB::raw('COUNT(*) as total'))
            ->groupBy('student_type')
            ->pluck('total', 'student_type');
    }


    protected function studentsPerSubject()
    {
        return DB::table('student_subject as ss')
            ->join('subjects as s', 's.id', '=', 'ss.subject_id')
            ->select('s.name', DB::raw('COUNT(DISTINCT ss.student_id) as total_students'))
            ->groupBy('s.id', 's.name')
            ->get();
    }

    protected function studentsPerPackage()
    {
        return DB::table('student_packages as sp')
            ->join('packages as p', 'p.id', '=', 'sp.package_id')
            ->where('sp.active', 1)
            ->select('p.name', DB::raw('COUNT(DISTINCT sp.student_id) as total_students'))
            ->groupBy('p.id', 'p.name')
            ->get();
    }

    protected function studentsPerGrade()
    {
        return DB::table('user_grades as ug')
            ->join('users as u', 'u.id', '=', 'ug.user_id')
            ->join('grades as g', 'g.id', '=', 'ug.grade_id')
            ->where('u.role', 'student')
            ->select('g.name as grade', DB::raw('COUNT(*) as total_students'))
            ->groupBy('g.id', 'g.name')
            ->get();
    }
}
