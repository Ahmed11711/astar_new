<?php

namespace App\Http\Controllers\Admin\Blog;

use App\Repositories\Blog\BlogRepositoryInterface;
use App\Http\Controllers\BaseController\BaseController;
use App\Http\Requests\Admin\Blog\BlogStoreRequest;
use App\Http\Requests\Admin\Blog\BlogUpdateRequest;
use App\Http\Resources\Admin\Blog\BlogResource;

class BlogController extends BaseController
{
    public function __construct(BlogRepositoryInterface $repository)
    {
        parent::__construct();

        $this->initService(
            repository: $repository,
            collectionName: 'Blog',
            fileFields: ['img']
        );

        $this->storeRequestClass = BlogStoreRequest::class;
        $this->updateRequestClass = BlogUpdateRequest::class;
        $this->resourceClass = BlogResource::class;
    }
}
