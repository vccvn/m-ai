<?php
namespace App\Services\GPT;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use OpenAI\Laravel\Facades\OpenAI;
use Orhanerday\OpenAi\OpenAi as OpenAiGPT;

class ChatService{
    public function __construct()
    {

    }

    public function send($messages = []){
        $result = OpenAI::chat()->create([
            'model' => 'gpt-4',
            'messages' => $messages,
        ]);
        // return $result;
        // dd($result);
        if($result){
            $choices = $result->choices;
            $first = $choices[0];
            $message= $first->message;
            $data = [
                'role' => $message->role,
                'content' => $message->content
            ];
            return $data;
        }
        return Arr::get($result, 'choices.0.message');

    }
    public function sendMessages($messages){
        $open_ai_key = getenv('OPENAI_API_KEY');
        $open_ai = new OpenAiGPT($open_ai_key);

        $chat = $open_ai->chat([
           'model' => 'gpt-4',
           'messages' => $messages,
           'temperature' => 1.0,
           'max_tokens' => 4000,
           'frequency_penalty' => 0,
           'presence_penalty' => 0,
        ]);
        $d = json_decode($chat);
        if($d){
            return ['role' => $d->choices[0]->message->role, 'content' => $d->choices[0]->message->content];
        }
        return null;
    }
}
