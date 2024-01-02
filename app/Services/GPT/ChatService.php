<?php

namespace App\Services\GPT;

use App\Masks\GPT\ChatCollection;
use App\Masks\GPT\ChatMask;
use App\Masks\Promos\PromoMask;
use App\Repositories\GPT\ChatRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use OpenAI\Laravel\Facades\OpenAI;
use Orhanerday\OpenAi\OpenAi as OpenAiGPT;
use OpenAI\Resources\Chat;

class ChatService
{
    /**
     * @var Chat $openAIChat open AI Chat
     */
    protected $openAIChat = null;

    /**
     * Undocumented variable
     *
     * @var OpenAiGPT
     */
    protected $gpt = null;
    /**
     * Undocumented function
     *
     * @param ChatRepository $chatRepository
     * @param PromptService $promptService
     */
    public function __construct(protected ChatRepository $chatRepository, protected PromptService $promptService)
    {
        $this->openAIChat = OpenAI::chat();
    }

    public function send($messages = [])
    {
        $result = $this->openAIChat->create([
            'model' => 'gpt-4',
            'messages' => $messages,
        ]);
        // return $result;
        // dd($result);

        if ($result) {
            $choices = $result->choices;
            $first = $choices[0];
            $message = $first->message;
            $data = [
                'role' => $message->role,
                'content' => $message->content
            ];
            return $data;
        }
        return [];
    }
    public function sendMessages($messages)
    {
        $open_ai_key = getenv('OPENAI_API_KEY');
        $open_ai = new OpenAiGPT($open_ai_key);
        // try {
        //code...
        $chat = $open_ai->chat([
            'model' => 'gpt-4',
            'messages' => $messages,
            'temperature' => 1.0,
            'max_tokens' => 4000,
            'frequency_penalty' => 0,
            'presence_penalty' => 0,
        ]);

        $d = json_decode($chat);
        // dd($d);
        if ($d) {
            return ['role' => $d->choices[0]->message->role, 'content' => $d->choices[0]->message->content];
        }
        // } catch (\Throwable $th) {
        //     //throw $th;
        // }

        return null;
    }

    public function getChatDetail($user_id, $prompt_id = 0)
    {
        $detail = [
            'chat' => null,
            'inputs' => [],
            'prompt' => null,
            'messages' => [],
            'name' => 'chat'
        ];
        $params = ['user_id' => $user_id, 'prompt_id' => 0];
        if ($prompt_id) {
            $params['prompt_id'] = $prompt_id;
            if ($inputs = $this->promptService->getInputs($prompt_id)) {
                $detail['inputs'] = $inputs;
                $p = new PromoMask($this->promptService->getCurrentPrompt());
                $p->__lock();
                $detail['prompt'] = $p;
            }
        }
        $chat = $this->chatRepository->getOrCreateChat($params);
        if ($chat) {
            $detail['chat'] = $chat;
            if ($chat->messages && is_countable($chat->messages))
                $detail['messages'] = $chat->messages;
            return $detail;
        }
        return null;
    }


    public function getChatData($user_id, $chat_id = 0, $prompt_id = 0)
    {
        $detail = [
            'chat' => null,
            'inputs' => [],
            'prompt' => null,
            'messages' => [],
            'name' => 'chat'
        ];
        $hasChat = false;
        if ($chat_id && $chat = $this->chatRepository->getChat(['user_id' => $user_id, 'id' => $chat_id])) {
            $hasChat = true;
            $detail['chat'] = $chat;
            if ($chat->messages && is_countable($chat->messages))
                $detail['messages'] = $chat->messages;
            if ($chat->prompt_id) {
                $inputs = $this->promptService->getInputs($chat->prompt_id);
                if ($inputs) {
                    $detail['inputs'] = $inputs;
                }
                if($prompt = $this->promptService->getCurrentPrompt()){
                    $p = new PromoMask($prompt);
                    $p->__lock();
                    $detail['prompt'] = $p;
                    $detail['name'] = $p->name;
                }

            }
            return $detail;
        }
        $params = ['user_id' => $user_id, 'prompt_id' => 0];
        if ($prompt_id) {
            $params['prompt_id'] = $prompt_id;
            if ($inputs = $this->promptService->getInputs($prompt_id)) {
                $detail['inputs'] = $inputs;
                $p = new PromoMask($this->promptService->getCurrentPrompt());
                $p->__lock();
                $detail['prompt'] = $p;
            }
        }
        $chat = $this->chatRepository->getOrCreateChat($params);
        if ($chat) {
            $detail['chat'] = $chat;
            if ($chat->messages && is_countable($chat->messages))
                $detail['messages'] = $chat->messages;
            return $detail;
        }
        return null;
    }
    public function getOrCreateChat($user_id, $chat_id = 0, $prompt_id = 0): ChatMask
    {
        $params = [
            'user_id' => $user_id
        ];
        if ($chat_id) {
            $params['id'] = $chat_id;
        }
        elseif ($prompt_id) {
            $params['prompt_id'] = $prompt_id;
        }
        return $this->chatRepository->getOrCreateChat($params);
    }

    /**
     * get chat history
     *
     * @param int $user_id
     * @return ChatCollection<ChatMask>
     */
    public function getHistory($user_id)
    {
        return $this->chatRepository->mode('mask')->with('prompt')
            ->select('gpt_chats.*')->selectRaw("(
            SELECT gpt_chat_messages.updated_at
            FROM gpt_chat_messages
            WHERE gpt_chat_messages.chat_id = gpt_chats.id
            ORDER BY gpt_chat_messages.updated_at DESC
            LIMIT 1
        ) as last_sent")
            ->orderByRaw('last_sent DESC')
            ->getData(['user_id' => $user_id]);
    }
}
