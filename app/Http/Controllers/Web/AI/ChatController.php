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
use App\Services\GPT\PromptService;

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
    public function __construct(
        ChatRepository $repository,
        protected MessageRepository $messageRepository,
        protected TopicRepository $topicRepository,
        protected PromptRepository $promptRepository,
        protected ChatService $chatService,
        protected PromptService $promptService
    ) {
        $this->repository = $repository;
        $this->init();
    }

    public function index(Request $request)
    {
        $allPrompt = $this->promptRepository->mode('mask')->getData();
        $topics = $this->topicRepository->with('prompts')->getData();
        return $this->viewModule('index', ['allPrompt' => $allPrompt, 'topics' => $topics]);
    }


    public function sendMessage(Request $request)
    {
        extract($this->apiDefaultData);
        $chat = null;
        $user = $request->user();
        $prompt = null;
        if ($request->type == 'continue' && (!$request->id || !($chat = $this->repository->getUserChatDetail($user->id, $request->id))))
            $message = 'Dữ liệu không hợp lệ';
        elseif ($request->type != 'continue' && !($chat = $this->repository->createChatDetail($user->id, $request->prompt_id ?? 0))) {
            $message = 'Không thể tạo doạn chat';
        } elseif ($request->type != 'continue' && $request->prompt_id && !($prompt = $this->promptRepository->find($request->prompt_id))) {
            $message = 'Prompt không tồn tại';
        }
        elseif (!($messageData = $this->promptService->getPromptDataFilled($request))) {
            $message = 'Dữ liệu không hợp lệ';
        }
        else {
            // return $this->json($messageData);
            $cm = [
                'role' => 'user',
                'content' => $messageData['content'],
                // 'message' => $messageData['message']
            ];
            $cmLog = [
                'chat_id' => $chat->id,
                'role' => 'user',
                'content' => $messageData['content'],
                'message' => $messageData['message']
            ];

            $userMessage = $this->messageRepository->create($cmLog);
            $messages = $chat->toGPT();
            $messages[] = $cm;

            // dd($messages);

            // return $this->json($messages);
            $data = $this->chatService->sendMessages($messages);
            $content = $data['content'];
            if(strip_tags($data['content']) == $data['content']){
               $contentArrays = explode("
", $content);
                $data['message'] = implode("<br>", array_map(function ($ln) {
                    $i = 0;
                    $s = '';
                    $l = strlen($ln);
                    for ($j = 0; $j < $l; $j++) {
                        # code...
                        $c = substr($ln, $j, 1);
                        if ($c != ' ') {
                            $s .= substr($ln, $j);
                            return $s;
                        } else {
                            $s .= "&nbsp;";
                        }
                    }
                    return $ln;
                }, $contentArrays));

            }
            // $content = $data['content'];
            $data['chat_id'] = $chat->id;
            $assistantMessage = $this->messageRepository->create($data);
            $status = true;
        }
        return $this->json(compact(...$this->apiSystemVars));
    }


    public function getPromptInputs(Request $request)
    {
        extract($this->apiDefaultData);
        $data = [];
        $data['inputs'] = $this->promptService->getInputs($request->prompt_id);
        if ($data['prompt'] = $this->promptService->getCurrentPrompt())
            $status = true;

        return $this->json(compact(...$this->apiSystemVars));
    }
}
