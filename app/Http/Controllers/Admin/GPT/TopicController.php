<?php

namespace App\Http\Controllers\Admin\GPT;

use App\Http\Controllers\Admin\AdminController;

use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

use App\Repositories\GPT\TopicRepository;
use App\Services\GPT\TopicService;

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
    public function __construct(TopicRepository $repository, protected TopicService $topicService)
    {
        $this->repository = $repository;
        $this->init();
        $this->activeMenu('gpt');
    }

    public function beforeGetListData($request)
    {
        if($topic = $this->topicService->getActiveTopic()){
            $this->repository->where('parent_id', $topic->id);
        }
    }

    public function beforeGetListView($request, $data)
    {
        add_js_data('category_map_data', [
            'map' => get_topic_map(),
            'default_parent' => 'Không'
        ]);
        if($topic = $this->topicService->getActiveTopic()){
            $data->list_title = $topic->name;
        }


    }

    /**
     * can thiệp trước khi luu
     * @param Request $request
     * @param Arr $data dũ liệu đã được validate
     * @return void
     */
    protected function beforeSave(Request $request, $data)
    {
        $data->slug = $this->repository->getSlug(
            $request->slug ?? $request->name,
            $request->id
        );
    }

    public function getDetail(Request $request, $id = null){

        $id = $id?$id:$request->id;
        if(!$id || !($parent = $this->topicService->getAndActive($id))){

            return $this->showError($request, 404, 'Không tìm thấy chuyên mục');
        }
        if($parent->children_count > 0){
            return $this->getList($request);
        }
        /**
         * @var PromptController
         */
        $promptController = app(PromptController::class);

        return $promptController->getList($request);

    }


}
