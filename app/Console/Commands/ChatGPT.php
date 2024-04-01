<?php

namespace App\Console\Commands;

use App\Services\GPT\ChatService;
use GeminiAPI\Laravel\Facades\Gemini;
use Illuminate\Console\Command;

class ChatGPT extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chatgpt:test {message=:Noi dung chat}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test chat GPT';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {


        $chatGPT = app(ChatService::class);

        $data = $chatGPT->sendMessages([
            ['role' => 'user', 'content' => "i want to be supper man"],
            ['role' => 'model', 'content' => "Perhaps you mean you want to be like \"Superman\", the popular comic book hero. Here are few tips:

            // 1. Develop Moral Character: Superman is known for his strong moral compass. He always tries to do what is right, no matter the situation.

            // 2. Help Others: Superman is always helping people, whether it's saving them from a burning building or stopping a runaway train. In real life, this might translate to doing volunteer work or assisting someone who is struggling.

            // 3. Get Physically Fit: While you may not be able to fly or have super strength, you can still make sure your body is healthy and strong. Regular exercise and a balanced diet are key.

            // 4. Have a Fearless Attitude: Superman doesn't back down from challenges. Adopt a similar fearless attitude where you don't allow your fears to stop you from pursuing your goals.

            // 5. Stand For Justice: Superman is all about justice. In your life, make sure that you stand up for what is right and fight against what is wrong.

            // Remember that Superman is just a character, but the attributes he represents, such as strength, courage, kindness, and resilience, are within everyone's reach."],
            ['role' => 'user', 'content' => $this->argument('message')??'Tôi muốn làm siêu nhân']
        ], 'gemini');
        if(!$data){
            dd($chatGPT->getErrorMessage());
        }
        dd($data);

        $content = $data['content'];
        $contentArrays = explode("
",$content);
        $data['content'] = implode("
", array_map(function($ln){
            $i = 0;
            $s = '';
            $l = strlen($ln);
            for ($j=0; $j < $l; $j++) {
                # code...
                $c = substr($ln, $j, 1);
                if($c != ' '){
                    $s.= substr($ln, $j);
                    return $s;
                }else{
                    $s.="&nbsp;";
                }
            }
            return $ln;
        }, $contentArrays));
        dd($data);
        return Command::SUCCESS;

    }
}
