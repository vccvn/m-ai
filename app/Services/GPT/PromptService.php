<?php
namespace App\Services\GPT;

use App\Models\GPTPrompt;
use App\Repositories\GPT\CriteriaRepository;
use App\Repositories\GPT\PromptRepository;
use Gomee\Html\Dom\HtmlDomParser;
use Illuminate\Http\Request;

class PromptService{
    protected $data = [
        'criteria' => [],
        'map' => [],
        'inputs' => [],
        'text' => ''
    ];
    /**
     * prompt
     *
     * @var GPTPrompt
     */
    protected $prompt = null;
    public function __construct(protected CriteriaRepository $criteriaRepository, protected PromptRepository $promptRepository){}

    /**
     * lay prompt hiện tại
     *
     * @return GPTPrompt
     */
    public function getCurrentPrompt() {
        return $this->prompt;
    }
    public function analyticHtmlPrompt($content){
        //
        $expression = '/<span\s[^>]*\s*role=\"criteria\"[^>]*\s*data-id=\"([^\"]+)\"[^>]*>([^<]+)*<\/span>/';
        preg_match_all($expression, $content, $matches);
        $t = count($matches[0]);
        for ($i=0; $i < $t; $i++) {
            $s = $matches[0][$i];
            $id = $matches[1][$i];
            if($criteria = $this->criteriaRepository->find($id)){
                $mark = "[@criteria:{$criteria->id}]";
                $this->data['criteria'][] = $criteria->id;
                $this->data['map'][$criteria->name] = $mark;
                $this->data['inputs'][$criteria->id] = [];
                $content = str_replace($s, $mark, $content);
            }else{
                $mark = "";
                // $this->data['criteria'][] = $criteria->id;
                // $this->data['map'][$criteria->name] = $mark;
                // $this->data['inputs'][$criteria->id] = [];
                $content = str_replace($s, $mark, $content);
            }
        }
        $content = str_replace("/p><p", "/p>\r\n<p", $content);
        $content = str_replace(["<br>", "<br />"], "\r\n", $content);
        $this->data['text'] = html_entity_decode(strip_tags($content));
        $d = $this->data;
        $this->data = [
            'criteria' => [],
            'map' => [],
            'inputs' => [],
            'text' => ''
        ];
        return $d;
    }

    /**
     * lấy prompt
     *
     * @param int $id
     * @return GPTPrompt|null
     */
    public function getPrompt($id = null) {
        return ($id && $this->prompt = $this->promptRepository->find($id))?$this->prompt:null;
    }

    public function getInputs($id = null){
        if(!($prompt = $this->getPrompt($id))) return [];
        $config = array_merge([
            'criteria' => [],
            'map' => [],
            'inputs' => [],
            'text' => ''
        ], $prompt->getConfigData());

        $inputs = [];
        if($config['criteria'] && count($list = $this->criteriaRepository->get(['id' => $config['criteria']]))){
            foreach ($list as $item) {
                $config['inputs'][$item->id] = $item->toArray();
            }
            foreach ($config['inputs'] as $input) {
                if($input && $input['type']??null){
                    $inputs[] = $input;
                }
            }
        }

        return $inputs;
    }

    public function getPromptDataFilled(Request $request) {
        $__mess = str_replace("/div><div", "/div>\r\n<div", $request->message);
        $__mess = str_replace("/p><p", "/p>\r\n<p", $__mess);
        $__mess = str_replace("<br>", "\r\n", $__mess);
        $__mess = str_replace("<br/>", "\r\n", $__mess);
        $__mess = str_replace("<br />", "\r\n", $__mess);

        if(!($prompt = $this->getPrompt($request->prompt_id))){
            return ['content' => $__mess, 'message' => $request->message];
        }

        $content = $prompt->prompt_config;
        $config = $prompt->getConfigData();
        $message = "<h4>{$prompt->name}:</h4>\r\n";
        $config = array_merge([
            'criteria' => [],
            'map' => [],
            'inputs' => [],
            'text' => ''
        ], (array) $config);
        if($config['criteria'] && count($list = $this->criteriaRepository->get(['id' => $config['criteria']]))){
            foreach ($list as $item) {
                $val = $request->input("criteria.".$item->name);
                $mark = "[@criteria:{$item->id}]";
                $content = str_replace($mark, $val, $content);
                $message .= "<div class=\"criteria-info\"><span class=\"criteria-tag\">{$item->label}: </span> {$val}</div>\r\n";
            }

        }
        $message_label = $prompt->message_label??'Thông tin liên quan';
        $message .= "<div class=\"message-info\">{$message_label}</div>\r\n";
        $message .= "<div class=\"message-detail\">{$request->message}</div>\r\n";


        if(preg_match('/\[\]/', $content)){
            $content = str_replace('[]', $__mess, $content);
        }else{
            $content = $content.($__mess? ((substr($content, -1) == '.')? '.':'' ) . ' ' . $__mess : '');
        }
        $content = html_entity_decode($content);
        return compact('content', 'message');
    }
}
