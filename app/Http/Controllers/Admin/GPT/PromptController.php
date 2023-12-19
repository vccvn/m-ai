<?php

namespace App\Http\Controllers\Admin\GPT;

use App\Http\Controllers\Admin\AdminController;

use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

use App\Repositories\GPT\PromptRepository;

class PromptController extends AdminController
{
    protected $module = 'gpt.prompts';

    protected $moduleName = 'Prompt';

    protected $flashMode = true;

    /**
     * repository chinh
     *
     * @var PromptRepository
     */
    public $repository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(PromptRepository $repository)
    {
        $this->repository = $repository;
        $this->init();
    }

    public function beforeGetListView($request, $data)
    {
        add_js_data('category_map_data', get_topic_map());
    }


}
