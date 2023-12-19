<?php

namespace App\Http\Controllers\Admin\GPT;

use App\Http\Controllers\Admin\AdminController;

use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

use App\Repositories\GPT\TopicRepository;

class TopicController extends AdminController
{
    protected $module = 'gpt.topics';

    protected $moduleName = 'Chủ đề';

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


    public function beforeGetListView($request, $data)
    {
        add_js_data('category_map_data', [
            'map' => get_topic_map(),
            'default_parent' => 'Không'
        ]);
    }


}
