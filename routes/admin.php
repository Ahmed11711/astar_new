<?php

use App\Http\Controllers\Admin\Auth\AuthController;
use App\Http\Controllers\Admin\Blog\BlogController;
use App\Http\Controllers\Admin\Company\CompanyController;
use App\Http\Controllers\Admin\ExamPaper\ExamPaperController;
use App\Http\Controllers\Admin\ExamPaper\UpdateExamPaperController;
use App\Http\Controllers\Admin\Feature\FeatureController;
use App\Http\Controllers\Admin\FeaturePackage\FeaturePackageController;
use App\Http\Controllers\Admin\grade\gradeController;
use App\Http\Controllers\Admin\notifications\notificationsController;
use App\Http\Controllers\Admin\Packages\PackagesController;
use App\Http\Controllers\Admin\paper\paperController;
use App\Http\Controllers\Admin\school\schoolController;
use App\Http\Controllers\Admin\setting\settingController;
use App\Http\Controllers\Admin\StudentRegistrations\StudentRegistrationsController;
use App\Http\Controllers\Admin\Subject\SubjectController;
use App\Http\Controllers\Admin\Subtopic\SubtopicController;
use App\Http\Controllers\Admin\successStories\successStoriesController;
use App\Http\Controllers\Admin\Team\TeamController;
use App\Http\Controllers\Admin\Topic\TopicController;
use App\Http\Controllers\Admin\trusted\trustedController;
use App\Http\Controllers\Admin\User\UserController;
use App\Http\Controllers\Admin\withdraw\withdrawController;
use App\Http\Controllers\DataEntry\DataEntryController;
use App\Http\Controllers\exampleController;
use App\Http\Middleware\CheckJwtTokenByAdmin;
use App\Models\User;
use Illuminate\Support\Facades\Route;


// Route::prefix('admin/v1')->middleware(CheckJwtTokenByAdmin::class)->group(function () {});

 
Route::prefix('v1')->group(function () {

 Route::apiResource('schools', schoolController::class)->names('school');
 Route::apiResource('grades', gradeController::class)->names('grade');
 Route::apiResource('subjects', SubjectController::class)->names('subject');
 Route::apiResource('topics', TopicController::class)->names('topic');
 Route::apiResource('subtopics', SubtopicController::class)->names('subtopic');
 Route::apiResource('teams', TeamController::class)->names('team');
 Route::apiResource('blogs', BlogController::class)->names('blog');
 Route::apiResource('student_registrations', StudentRegistrationsController::class)->names('student_registrations');
 Route::apiResource('trusteds', trustedController::class)->names('trusted');
 Route::apiResource('success-stories', successStoriesController::class)->names('success_stories');
 Route::apiResource('users', UserController::class)->names('user');
 // Route::get('examss', [DataEntryController::class, 'index']);
 Route::apiResource('papers', paperController::class)->names('paper');
 Route::apiResource('features', FeatureController::class)->names('feature');
 Route::apiResource('feature_packages', FeaturePackageController::class)->names('feature_package');
 Route::apiResource('packages', PackagesController::class)->names('packages');
 Route::apiResource('exams', ExamPaperController::class)->names('exam_paper')->except(['store', 'show']);
 Route::post('exams', [UpdateExamPaperController::class, 'store']);
 Route::get('exams/{id}', [UpdateExamPaperController::class, 'show']);
});
