<?php
namespace App\Services\GPT;

use App\Models\GPTPrompt;
use App\Repositories\GPT\CriteriaRepository;
use App\Repositories\GPT\PromptRepository;
use App\Repositories\GPT\TopicRepository;
use Gomee\Html\Dom\HtmlDomParser;
use Illuminate\Http\Request;

class TopicService{
    protected static $activeTopicID = null;
    protected static $activeTopic = null;
    public function __construct(private TopicRepository $topicRepository){}
    public function setActiveTopicID($id){
        self::$activeTopicID = $id;
    }
    public function getActiveTopicID(){
        return self::$activeTopicID;
    }
    public static function activeTopic($id){
        self::$activeTopicID = $id;
    }

    public function getActiveTopic(){
        return self::$activeTopic;
    }

    public function getAndActive($id){
        return (static::$activeTopic = $this->topicRepository->withCount('children')->with('children')->first(['id'=>$id]))?static::$activeTopic:null;
    }

    public function __call($name, $arguments)
    {
        return $this->topicRepository->{$name}(...$arguments);
    }
}
