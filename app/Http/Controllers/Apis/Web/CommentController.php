<?php

namespace App\Http\Controllers\Apis\Web;

use App\Http\Controllers\Apis\ApiController;


use App\Repositories\Comments\CommentRepository;

class CommentController extends ApiController
{
    protected $module = 'comments';

    protected $moduleName = 'Comment';

    protected $flashMode = true;

    /**
     * repository chinh
     *
     * @var CommentRepository
     */
    public $repository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(CommentRepository $repository)
    {
        $this->repository = $repository;
        $this->repository->mode('mask');
        $this->init();
    }



}
