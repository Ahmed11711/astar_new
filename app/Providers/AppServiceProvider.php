<?php

namespace App\Providers;

use App\Repositories\Attmpate\AttmpateRepository;
use App\Repositories\Attmpate\AttmpateRepositoryInterface;
use App\Repositories\ExamPaper\ExamPaperRepositoryInterface;
use App\Repositories\ExamPaper\ExamPaperRepository;

use App\Repositories\Packages\PackagesRepositoryInterface;
use App\Repositories\Packages\PackagesRepository;

use App\Repositories\FeaturePackage\FeaturePackageRepositoryInterface;
use App\Repositories\FeaturePackage\FeaturePackageRepository;



use App\Repositories\Feature\FeatureRepositoryInterface;
use App\Repositories\Feature\FeatureRepository;

use App\Repositories\paper\paperRepositoryInterface;
use App\Repositories\paper\paperRepository;

use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\User\UserRepository;

use App\Repositories\successStories\successStoriesRepositoryInterface;
use App\Repositories\successStories\successStoriesRepository;



use App\Repositories\StudentRegistrations\StudentRegistrationsRepositoryInterface;
use App\Repositories\StudentRegistrations\StudentRegistrationsRepository;

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
  public function register(): void
  {
    //
    $this->app->bind(schoolRepositoryInterface::class, schoolRepository::class);
    $this->app->bind(gradeRepositoryInterface::class, gradeRepository::class);
    $this->app->bind(SubjectRepositoryInterface::class, SubjectRepository::class);
    $this->app->bind(TopicRepositoryInterface::class, TopicRepository::class);
    $this->app->bind(SubtopicRepositoryInterface::class, SubtopicRepository::class);
    $this->app->bind(TeamRepositoryInterface::class, TeamRepository::class);
    $this->app->bind(BlogRepositoryInterface::class, BlogRepository::class);
    $this->app->bind(StudentRegistrationsRepositoryInterface::class, StudentRegistrationsRepository::class);
    $this->app->bind(successStoriesRepositoryInterface::class, successStoriesRepository::class);
    $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    $this->app->bind(paperRepositoryInterface::class, paperRepository::class);
    $this->app->bind(FeatureRepositoryInterface::class, FeatureRepository::class);
    $this->app->bind(FeaturePackageRepositoryInterface::class, FeaturePackageRepository::class);
    $this->app->bind(PackagesRepositoryInterface::class, PackagesRepository::class);
    $this->app->bind(ExamPaperRepositoryInterface::class, ExamPaperRepository::class);
    $this->app->bind(AttmpateRepositoryInterface::class, AttmpateRepository::class);
  }

  /**
   * Bootstrap any application services.
   */
  public function boot(): void
  {
    Model::unguard();
  }
}
