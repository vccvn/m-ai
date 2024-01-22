<?php

namespace App\Http\Controllers\Web\AI;

use App\Http\Controllers\Web\WebController;

use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

use App\Repositories\GPT\TopicRepository;

class TopicController extends WebController
{
    protected $module = 'ai.topics';

    protected $moduleName = 'Chuyen de';

    protected $flashMode = true;

    /**
     * repository chinh
     *
     * @var TopicRepository
     */
    public $repository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(TopicRepository $repository)
    {
        $this->repository = $repository;
        $this->init();
    }

    public function getIndex(Request $request)
    {
        $topics = $this->repository->mode('mask')->getData(['parent_id' => 0]);
        $topic = null;
        $this->breadcrumb->add('Chuyên gia AI');
        return $this->viewModule('index', ['topic' => $topic, 'topics' => $topics]);
    }

    public function getTopic(Request $request)
    {
        extract($this->apiDefaultData);
        if(!$request->id)
            $message = 'Thiếu thông tin topic';
        elseif(!($data = $this->repository->buildWithData()->mode('mask')->detail(['id' => $request->id])))
            $message = 'Chủ đề không tồn tại';
        else
            $status = true;
        return $this->json(compact(...$this->apiSystemVars));
    }

}
