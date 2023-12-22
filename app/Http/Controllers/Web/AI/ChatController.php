<?php

namespace App\Http\Controllers\Web\AI;

use App\Http\Controllers\Web\WebController;

use Illuminate\Http\Request;
use Gomee\Helpers\Arr;

use App\Repositories\GPT\ChatRepository;
use App\Repositories\GPT\MessageRepository;
use App\Repositories\GPT\PromptRepository;
use App\Repositories\GPT\TopicRepository;
use App\Services\GPT\ChatService;

/**
 * @property-read TopicRepository $topicRepository
 * @property-read PromptRepository $promptRepository
 * @property-read MessageRepository $topicRepository
 * @property-read ChatRepository $repository
 * @property-read ChatService $chatService
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
    public function __construct(ChatRepository $repository, protected MessageRepository $messageRepository, protected TopicRepository $topicRepository, protected PromptRepository $promptRepository, protected ChatService $chatService)
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
        $prompt = null;
        if($request->type == 'continue' && (!$request->id || !($chat = $this->repository->getUserChatDetail($user->id, $request->id))))
            $message = 'Dữ liệu không hợp lệ';
        elseif ($request->type != 'continue' && !($chat = $this->repository->createChatDetail($user->id, $request->prompt_id??0))) {
            $message = 'Không thể tạo doạn chat';
        }
        elseif($request->type != 'continue' && $request->prompt_id && !($prompt = $this->promptRepository->find($request->prompt_id))){
            $message = 'Prompt không tồn tại';
        }else{
            $__mess = str_replace("/div><div", "/div>\n<div", $request->message);
            $__mess = str_replace("/p><p", "/p>\n<p", $__mess);
            $__mess = str_replace("<br>", "\n", $__mess);
            $__mess = str_replace("<br/>", "\n", $__mess);
            $__mess = str_replace("<br />", "\n", $__mess);
            $cm = [
                'role' => 'user',
                'content' => $__mess
            ];
            $cmLog = [
                'chat_id' => $chat->id,
                'role' => 'user',
                'content' => $__mess,
                'message' => $__mess
            ];

            if($prompt){
                $p = $prompt->prompt;
                $c = str_replace('[]', $__mess, $p);
                if($c == $p){
                    $a = trim($p);
                    $cn = $a. ((substr($a, -1) == '.')? '.':'' ) . ' ' . $__mess;
                    $cm['content'] = $cn;
                    $cmLog['content'] = $cn;
                }
            }
            $userMessage = $this->messageRepository->create($cmLog);
            $messages = $chat->toGPT();
            $messages[] = $cm;
            // dd($messages);
            $data = $this->chatService->send($messages);
            $data['message'] = $data['content'];
            $data['chat_id'] = $chat->id;
            $assistantMessage = $this->messageRepository->create($data);
            $status = true;
        }
        return $this->json(compact(...$this->apiSystemVars));
    }
}
