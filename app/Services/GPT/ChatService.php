<?php
namespace App\Services\GPT;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use OpenAI\Laravel\Facades\OpenAI;

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
        return Arr::get($result, 'choices.0.message');

    }
}
