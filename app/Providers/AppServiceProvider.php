<?php

namespace App\Providers;

use App\Repositories\Blog\BlogRepositoryInterface;
use App\Repositories\Blog\BlogRepository;

use App\Repositories\Team\TeamRepositoryInterface;
use App\Repositories\Team\TeamRepository;

use App\Repositories\Subtopic\SubtopicRepositoryInterface;
use App\Repositories\Subtopic\SubtopicRepository;

use App\Repositories\Topic\TopicRepositoryInterface;
use App\Repositories\Topic\TopicRepository;

use App\Repositories\Subject\SubjectRepositoryInterface;
use App\Repositories\Subject\SubjectRepository;

use App\Repositories\grade\gradeRepositoryInterface;
use App\Repositories\grade\gradeRepository;

use App\Repositories\school\schoolRepositoryInterface;
use App\Repositories\school\schoolRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void {
//
        $this->app->bind(schoolRepositoryInterface::class, schoolRepository::class);
        $this->app->bind(gradeRepositoryInterface::class, gradeRepository::class);
        $this->app->bind(SubjectRepositoryInterface::class, SubjectRepository::class);
        $this->app->bind(TopicRepositoryInterface::class, TopicRepository::class);
        $this->app->bind(SubtopicRepositoryInterface::class, SubtopicRepository::class);
        $this->app->bind(TeamRepositoryInterface::class, TeamRepository::class);
        $this->app->bind(BlogRepositoryInterface::class, BlogRepository::class);
}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::unguard();
    }
}
