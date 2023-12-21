<?php

namespace App\Http\Controllers\Web\AI;

use App\Http\Controllers\Web\WebController;

use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

use App\Repositories\GPT\ChatRepository;
use App\Repositories\GPT\MessageRepository;
use App\Repositories\GPT\PromptRepository;
use App\Repositories\GPT\TopicRepository;

/**
 * @property-read TopicRepository $topicRepository
 * @property-read PromptRepository $promptRepository
 * @property-read MessageRepository $topicRepository
 * @property-read ChatRepository $repository
 */
class ChatController extends WebController
{
    protected $module = 'chat';

    protected $moduleName = 'ChatGPT';

    protected $flashMode = true;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ChatRepository $repository, protected MessageRepository $messageRepository, protected TopicRepository $topicRepository, protected PromptRepository $promptRepository)
    {
        $this->repository = $repository;
        $this->init();
    }

    public function index(Request $request){
        $allPrompt = $this->promptRepository->mode('mask')->getData();
        $topics = $this->topicRepository->with('prompts')->getData();
        return $this->viewModule('index', ['allPrompt' => $allPrompt, 'topics'=>$topics]);
    }

    public function sendMessage(Request $request){
        extract($this->apiDefaultData);
        $chat = null;
        $user = $request->user();
        if($request->type == 'continue' && (!$request->id || !($chat = $this->repository->getUserChatDetail($user->id, $request->id))))
            $message = 'Dữ liệu không hợp lệ';
        elseif ($request->type != 'continue' && !($chat)) {
            # code...
        }
    }
}
