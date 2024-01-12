<?php

namespace App\Http\Controllers\Admin\GPT;

use App\Http\Controllers\Admin\AdminController;

use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

use App\Repositories\GPT\PromptRepository;
use App\Services\GPT\PromptService;
use App\Services\GPT\TopicService;

/**
 * @property-read PromptService $promptService
 */
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
    public function __construct(PromptRepository $repository, PromptService $promptService, protected TopicService $topicService)
    {
        $this->repository = $repository;
        $this->promptService = $promptService;
        $this->init();
        $this->activeMenu('gpt');
    }

    public function beforeGetListData($request)
    {
        if($topic = $this->topicService->getActiveTopic()){
            $this->repository->where('topic_id', $topic->id);
        }
    }

    public function beforeGetListView($request, $data)
    {
        add_js_data('category_map_data', [
            'map' => get_topic_map(),
            'default_parent' => 'Không'
        ]);
    }

    /**
     * xu ly truoc khi luu
     *
     * @param Request $request
     * @param Arr $data
     * @param GPTPrompt $old
     * @return mixed
     */
    public function beforeSave($request, $data, $old = null)
    {
        $c = $this->promptService->analyticHtmlPrompt($data->prompt);
        $data->config = $c;
        $data->prompt_config = $c['text']??'';
    }

    public function getImportForm(Request $request) {
        return $this->viewModule('import');
    }
}
