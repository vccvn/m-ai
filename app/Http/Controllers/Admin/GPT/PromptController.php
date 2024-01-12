<?php

namespace App\Http\Controllers\Admin\GPT;

use App\Http\Controllers\Admin\AdminController;

use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

use App\Repositories\GPT\PromptRepository;
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
    public function __construct(PromptRepository $repository, PromptService $promptService, protected TopicService $topicService)
    {
        $this->repository = $repository;
        $this->promptService = $promptService;
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
}
