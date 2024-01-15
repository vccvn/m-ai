<?php

namespace App\Http\Controllers\Admin\GPT;

use App\Http\Controllers\Admin\AdminController;
use App\Models\GPTPrompt;
use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

use App\Repositories\GPT\PromptRepository;
use App\Services\GPT\ChatService;
use App\Services\GPT\PromptService;
use App\Services\GPT\TopicService;
use App\Validators\GPT\PromptImportValidator;

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
    public function __construct(PromptRepository $repository, protected PromptService $promptService, protected TopicService $topicService, protected ChatService $chatService)
    {
        $this->repository = $repository;
        $this->init();
        $this->activeMenu('gpt');
    }

    public function beforeGetListData($request)
    {
        if ($topic = $this->topicService->getActiveTopic()) {
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
        $data->prompt_config = $c['text'] ?? '';
    }

    /**
     * hanh dong sau khi luu thanh cong
     *
     * @param Request $request
     * @param GPTPrompt $result
     * @return void
     */
    public function afterSave($request, $result)
    {
        $this->promptService->updatePromptTopicMap($result->id, $result->topic_id);
    }

    public function getImportForm(Request $request)
    {
        return $this->viewModule('import');
    }

    public function import(Request $request)
    {
        $validator = $this->repository->validator($request, PromptImportValidator::class);
        $back = redirect()->back()->withInput();
        if (!$validator->success())
            return $back->withErrors($validator->errors())->with('error', 'Thông tin nhập liệu không hợp lệ');
        if (!($file = $this->uploadFile($request, 'import_file', null, storage_path('data/prompts'))))
            return $back->with('error', 'Không thể tải lên file nhập liệu');
        if (!($importData = $this->promptService->importFromExcelFile($file->filepath, $request->topic_id)))
            return $back->with('error', $this->promptService->getErrorMessage());
        return $back->with('success', 'Đã thêm thành công ' . $importData['success'] . ' prompt!');
    }

    public function getQuickAddForm(Request $request){
        return $this->viewModule('quick-add');
    }

    public function quickAdd(Request $request){
        extract($this->apiDefaultData);

        if(!($promptContent = trim($request->prompt)))
            $message = 'Nội dung prompt không được bỏ trống';
        elseif(!$request->topic_id || !($topic = $this->topicService->first(['id' => $request->topic_id])))
            $message = 'Chủ đề không hợp lệ';
        return $this->json(compact(...$this->apiSystemVars));
    }
}
