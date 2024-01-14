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
        if (!($chat = $this->chatService->getOrCreateChat($user->id, $request->id, $request->prompt_id ?? 0)))
            $message = 'Dữ liệu không hợp lệ';
        elseif ($request->prompt_id && !($prompt = $this->promptRepository->find($request->prompt_id))) {
            $message = 'Prompt không tồn tại';
        } elseif (!($messageData = $this->promptService->getPromptDataFilled($request))) {
            $message = 'Dữ liệu không hợp lệ';
        } else {
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


            $messages = $chat->toGPT();
            $messages[] = $cm;

            // return $this->json($messages);
            $data = $this->chatService->sendMessages($messages);
            if (!$data) {
                if ($this->chatService->getErrorCode() == 'context_length_exceeded') {
                    $chat = $this->chatService->createChat($user->id, $request->prompt_id);
                    // $userMessage = $this->messageRepository->create($cmLog);
                    $messages = $chat->toGPT();
                    $messages[] = $cm;

                    // return $this->json($messages);
                    $data = $this->chatService->sendMessages($messages);
                }
            }
            if (!$data || !$data['content']) {
                $message = $this->chatService->getErrorMessage();
                return $this->json(compact(...$this->apiSystemVars));
            }
            $userMessage = $this->messageRepository->create($cmLog);
            $content = $data['content'];
            if (strip_tags($data['content']) == $data['content']) {
                $contentArrays = nl2array($content, false);
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
            } else {
                $data['message'] = preg_replace('/(<html[^>]>.*<body>|<\/body>|</html>)/i', '', $data['content']);
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



    public function chatBox(Request $request)
    {
        $history = $this->chatService->getHistory($user_id = $request->user()->id);
        $chatData = $this->chatService->getChatDetail($user_id, $request->p ?? ($request->prompt_id ?? ($request->pid ?? 0)));
        $page_title = $chatData['name'] ?? 'Chat';
        return $this->viewModule('box', ['history' => $history, 'data' => $chatData, 'page_title' => $page_title]);
    }

    public function getChatData(Request $request)
    {

        extract($this->apiDefaultData);
        $user_id = $request->user()->id;
        if ($chatData = $this->chatService->getChatData($user_id, $request->id ?? ($request->chat_id ?? ($request->cid ?? 0)), $request->p ?? ($request->prompt_id ?? ($request->pid ?? 0)))) {
            $status = true;
            $data = $chatData;
        }

        return $this->json(compact(...$this->apiSystemVars));
    }
}
