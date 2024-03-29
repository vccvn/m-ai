<?php

namespace App\Services\GPT;

use App\Models\GPTPrompt;
use App\Repositories\GPT\CriteriaRepository;
use App\Repositories\GPT\PromptRepository;
use App\Repositories\GPT\PromptTopicRepository;
use App\Repositories\GPT\TopicRepository;
use App\Tools\PromptImporter;
use Gomee\Html\Dom\HtmlDomParser;
use Illuminate\Http\Request;

class PromptService
{
    protected $data = [
        'criteria' => [],
        'map' => [],
        'inputs' => [],
        'text' => ''
    ];

    protected $errorMessage = '';
    protected $criteriaListById = [];
    protected $criteriaListByName = [];

    protected $topicMap = [];
    /**
     * prompt
     *
     * @var GPTPrompt
     */
    protected $prompt = null;
    public function __construct(
        protected CriteriaRepository $criteriaRepository,
        protected PromptRepository $promptRepository,
        protected TopicRepository $topicRepository,
        protected PromptTopicRepository $promptTopicRepository
        // protected ChatService $chatService
    ) {
    }

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    public function getTopicMap($topic_id)
    {
        if (array_key_exists($topic_id, $this->topicMap))
            return $this->topicMap[$topic_id];
        $this->topicMap[$topic_id] = ($topic = $this->topicRepository->find($topic_id)) ? $topic->getMap() : [];
        return $this->topicMap[$topic_id];
    }

    /**
     * lay prompt hiện tại
     *
     * @return GPTPrompt
     */
    public function getCurrentPrompt()
    {
        return $this->prompt;
    }
    public function analyticHtmlPrompt($content)
    {
        //
        $expression = '/<span\s[^>]*\s*role=\"criteria\"[^>]*\s*data-id=\"([^\"]+)\"[^>]*>([^<]+)*<\/span>/';
        $content2 = $content;

        $criterialMap = [];


        preg_match_all($expression, $content, $matches);
        $t = count($matches[0]);
        for ($i = 0; $i < $t; $i++) {
            $s = $matches[0][$i];
            $mark = '<criteria key="' . $i . '">' . $i . '</criteria>';
            $criterialMap[$mark] = $s;
            $content2 = str_replace($s, $mark, $content2);
        }

        $content2 = $this->checkCriteria($content2);


        if ($criterialMap) {
            foreach ($criterialMap as $key => $value) {
                $content2 = str_replace($key, $value, $content2);
            }
        }
        $content = $content2;

        $display_content = $content;

        preg_match_all($expression, $content, $matches);
        $t = count($matches[0]);
        for ($i = 0; $i < $t; $i++) {
            $s = $matches[0][$i];
            $id = $matches[1][$i];
            if ($criteria = $this->getCriterialById($id)) {
                // dump($criteria->toArray());
                $mark = "[@criteria:{$criteria->id}]";
                $this->data['criteria'][] = $criteria->id;
                $this->data['map'][$criteria->name] = $mark;
                $this->data['inputs'][$criteria->id] = [];
                $content = str_replace($s, $mark, $content);
            } else {
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
        $this->data['display_content'] = $display_content;
        $d = $this->data;
        $this->data = [
            'criteria' => [],
            'map' => [],
            'inputs' => [],
            'text' => '',
            'display_content' => ''
        ];
        return $d;
    }



    /**
     * lấy prompt
     *
     * @param int $id
     * @return GPTPrompt|null
     */
    public function getPrompt($id = null)
    {
        return ($id && $this->prompt = $this->promptRepository->find($id)) ? $this->prompt : null;
    }

    public function getInputs($id = null)
    {
        if (is_object($id) && is_a($id, GPTPrompt::class))
            $prompt = $id;
        elseif (!($prompt = $this->getPrompt($id))) return [];
        $config = array_merge([
            'criteria' => [],
            'map' => [],
            'inputs' => [],
            'text' => ''
        ], $prompt->getConfigData());

        $inputs = [];
        if ($config['criteria'] && count($list = $this->criteriaRepository->get(['id' => $config['criteria']]))) {
            foreach ($list as $item) {
                $config['inputs'][$item->id] = $item->toArray();
            }
            foreach ($config['inputs'] as $input) {
                if ($input && $input['type'] ?? null) {
                    $inputs[] = $input;
                }
            }
        }

        return $inputs;
    }

    public function updatePromptTopicMap($prompt_id, $topic_id)
    {
        return $this->promptTopicRepository->updatePromptTopics($prompt_id, $this->getTopicMap($topic_id));
    }

    public function getPromptDataFilled(Request $request)
    {
        $__mess = str_replace("/div><div", "/div>\r\n<div", $request->message);
        $__mess = str_replace("/p><p", "/p>\r\n<p", $__mess);
        $__mess = str_replace("<br>", "\r\n", $__mess);
        $__mess = str_replace("<br/>", "\r\n", $__mess);
        $__mess = str_replace("<br />", "\r\n", $__mess);
        $__mess = trim(strip_tags($__mess));
        $messageContent = $request->message;
        $messageContent = preg_replace('/\sstyle=\"[^\"]\"/i', '', $messageContent);
        if (!($prompt = $this->getPrompt($request->prompt_id))) {

            return ['content' => $__mess, 'message' => $messageContent];
        }


        $content = $prompt->prompt_config;
        $config = $prompt->getConfigData();
        $config = array_merge([
            'criteria' => [],
            'map' => [],
            'inputs' => [],
            'text' => ''
        ], (array) $config);

        if ($config['criteria'] && $request->use_criteria != 1) {
            return ['content' => $__mess, 'message' => $messageContent];
        }

        // $message = "<h4>{$prompt->name}:</h4>\r\n";
        $message = "";

        if ($config['criteria'] && count($list = $this->criteriaRepository->get(['id' => $config['criteria']]))) {
            foreach ($list as $item) {
                $val = $request->input("criteria." . $item->name);
                $mark = "[@criteria:{$item->id}]";
                $content = str_replace($mark, $val, $content);
                $message .= "<div class=\"criteria-info\"><span class=\"criteria-tag\">{$item->label}: </span> {$val}</div>\r\n";
            }
            $message_label = $prompt->message_label ?? 'Thông tin liên quan';
            $message .= "<div class=\"message-info\">{$message_label}</div>\r\n";
        }
        $message .= "<div class=\"message-detail\">{$messageContent}</div>\r\n";


        if (preg_match('/\[\]/', $content)) {
            $content = str_replace('[]', $__mess, $content);
        } else {
            $content = $content . ($__mess ? ((substr($content, -1) == '.') ? '.' : '') . ' ' . $__mess : '');
        }
        $content = html_entity_decode($content);
        return compact('content', 'message');
    }

    public function importFromExcelFile($file, $topic_id = 0, $user_id = 0)
    {
        $this->errorMessage = '';
        $importer = new PromptImporter($file);
        $list = $importer->getSheetData();
        if (!count($list)) {
            $this->errorMessage = 'Không có dữ liệu';
            return false;
        }

        $topicIDS = [$topic_id];
        $checkList = [];
        $success = 0;
        $failed = 0;
        $promptNeedToCreates = [];
        // lọc dữ liệu đầu vào
        foreach ($list as $promptData) {
            $text = $promptData['prompt'] ?? null;
            // $text = html_entity_decode($text);
            $name = $promptData['name'] ?? null;
            if (!($text = trim($text)) || !$name) {
                $failed++;
                continue;
            }
            $promptData['prompt'] = $text;
            $topid = $promptData['topic_id'] ?? null;
            if ($topid) {
                if (!in_array($topid, $checkList))
                    $checkList[] = $topid;
            } else {
                $promptData['topic_id'] = $topic_id;
            }
            $promptData['user_id'] = $user_id;
            $promptNeedToCreates[] = $promptData;
        }
        if (count($promptNeedToCreates) == 0) {
            $this->errorMessage = 'Không có dữ liệu hoặc dữ liệu không hợp lệ';
            return false;
        }

        // kiểm tra topic
        if (count($topicIDS) && count($topics = $this->topicRepository->select('id')->get(['id' => $checkList]))) {
            foreach ($topics as $topic) {
                $topicIDS[] = $topic->id;
            }
        }

        // dd($promptNeedToCreates);
        foreach ($promptNeedToCreates as $p) {
            if (!in_array($p['topic_id'], $topicIDS))
                $p['topic_id'] = $topic_id;
            $create = $this->createPrompt($p);
            if ($create) {
                $success++;
                $this->promptTopicRepository->updatePromptTopics($create->id, $this->getTopicMap($create->topic_id));
            }
        }
        return ['success' => $success, 'failed' => $failed];
    }

    /**
     * create prompt
     *
     * @param array $data
     * @return GPTPrompt
     */
    public function createPrompt($data)
    {
        $data['prompt'] = $this->checkCriteria($data['prompt']);
        $c = $this->analyticHtmlPrompt($data['prompt']);
        $data['config'] = $c;
        $data['prompt_config'] = $c['text'] ?? '';
        return $this->promptRepository->create($data);
    }


    public function checkCriteria($prompt, $type = 1, $promptSecret = null)
    {
        $expression = $type == 1 ? '/\[([^\]]+)\]/i' : '/\{([^\}]+)\}/i';
        preg_match_all($expression, $prompt, $matches);
        if (!$promptSecret) $promptSecret = $prompt;
        if ($t = count($matches[1]))
            foreach ($matches[1] as $i => $key) {
                $key = html_entity_decode($key);
                $label = ucfirst($key);
                $name = $key;
                if (!$name) continue;
                if (in_array($s = str_slug($name), ['chi-tiet', 'mo-ta-chi-tiet', 'thong-tin-chi-tiet']) && $i == $t - 1) {
                    $promptSecret = str_replace($matches[0][$i], '[]', $promptSecret);
                    continue;
                }
                if (count($p = explode(':', $key)) > 1) {
                    $name = array_shift($p);
                    $label = implode(':', $p);
                }
                if (!($criteria = $this->getCriterialByName($name)))
                    $criteria = $this->addCriteria($name, $label);
                if (!$criteria)
                    continue;
                $promptSecret = str_replace($matches[0][$i], '<span class="mceNonEditable criteria-tag" role="criteria" data-id="' . $criteria->id . '">[' . $criteria->label . ']</span>', $promptSecret);
            }
        return $type == 2 ? $promptSecret : $this->checkCriteria($prompt, 2, $promptSecret);
    }

    protected function getCriterialById($id)
    {
        if (array_key_exists($id, $this->criteriaListById))
            return $this->criteriaListById[$id];
        if (!($criteria = $this->criteriaRepository->find($id)))
            return null;
        $this->criteriaListById[$criteria->id] = $criteria;
        $this->criteriaListByName[$criteria->name] = $criteria;
        return $criteria;
    }
    protected function getCriterialByName($name)
    {
        $name = strtoupper(str_slug($name, '_'));
        if (array_key_exists($name, $this->criteriaListByName))
            return $this->criteriaListByName[$name];
        if (!($criteria = $this->criteriaRepository->first(['name' => $name])))
            return null;
        $this->criteriaListById[$criteria->id] = $criteria;
        $this->criteriaListByName[$criteria->name] = $criteria;
        return $criteria;
    }

    public function addCriteria($name, $label = null)
    {
        if (!($criteria = $this->getCriterialByName($name))) {
            if (!$label)
                $label = ucwords(str_replace(['_', '-'], [' ', ' '], $name));
            $name = $name = strtoupper(str_slug($name, '_'));
            $criteria = $this->criteriaRepository->create(['name' => $name, 'label' => $label, 'type' => 'text']);
            $this->criteriaListById[$criteria->id] = $criteria;
            $this->criteriaListByName[$criteria->name] = $criteria;
        }
        return $criteria;
    }


    public function updateAllPrompt($isPrint = false)
    {
        // dd($isPrint);
        if ($isPrint) echo "Update prompts...\n";
        $this->promptRepository->chunkById(50, function ($prompts) use ($isPrint) {
            foreach ($prompts as $prompt) {
                $prompt->description = str_replace(['Prompt', 'prompt'], ['Công cụ', 'Công cụ'], $prompt->description);
                $prompt->description = preg_replace('/Chat\s*GPT/i', 'M.AI', $prompt->description);
                $prompt->save();
                if ($isPrint) {
                    echo "Updating " . $prompt->name . "\n";
                }
                continue;
                $content = $prompt->prompt;
                $raw = $content;
                preg_match_all('/<\/*(table|tr|td|tbody|colgroup)+[^>]*>/i', $content, $matches);
                // print_r($matches);
                if (count($matches[0])) {
                    $content = preg_replace('/<\/*(table|tr|td|tbody|colgroup)+[^>]*>/i', '', $content);
                }
                // if($p != $prompt->prompt) echo $p . "\n";
                $c = $this->analyticHtmlPrompt($content);
                if ($c) {
                    $display_content = $c['display_content'] ?? '';

                    if ($display_content) {
                        $prompt->prompt = $display_content;
                        $content = $display_content;
                    }
                    if ($text = $c['text'] ?? '') {
                        $prompt->prompt_config = $text;
                    }
                    $prompt->config = $c;
                    $prompt->description = str_replace(['Prompt', 'prompt'], ['Công cụ', 'Công cụ'], $prompt->description);
                    $prompt->description = preg_replace('/Chat\s*GPT/i', 'M.AI', $prompt->description);
                    $prompt->save();

                    if ($isPrint) {
                        echo "Updating " . $prompt->name . "\n";
                    }
                }
                // $prompt->slug = str_slug($prompt->name);
                // $prompt->save();
                $this->updatePromptTopicMap($prompt->id, $prompt->topic_id);
            }
        });
    }

    public function analyticPrompt($prompt)
    {
        try {
            $promptChat = implode("\n", [
                'phân tích prompt và trả về dử liệu định dạng json với 4 key là name, description, placeholder, suggest. ',
                'cụ thể như sau:',
                'name: là tên prompt khoảng 10 từ dựa trên nội dung và tác dụng của prompt,',
                'description: là phần mô tả và giải thích tác dụng của prompt một cách xúc tích dễ hiểu không được vượt quá 36 từ,',
                'placeholder: là gợi ý nhập thông tin cho prompt trong ô chat không vượt quá 10 từ, ví dụ: bạn có thể nhập thông tin bổ sung. ',
                'suggest: là gợi ý tối ưu hơn cho prompt vừa phân tích.',
                'Và tôi chỉ cần kết quả trả về là json không thừa không thiếu và nếu là ngôn ngữ nào không phải tiếng Việt thì hãy dịch sang tiếng Việt. và cần tránh dùng từ "prompt", trong một số trường hợp có thể thay bằng từ "công cụ".',
                'Nội dung prompt cần phân tích như sau: "[]"'
            ]);
            $content = str_replace('[]', $prompt, $promptChat);
            $d = app(ChatService::class)->sendMessages([
                ['role' => 'system', 'content' => 'Tất cả kết quả trả về nếu có các từ như "ChatGPT", "chat gpt", "Chat GPT" hoặc các từ tương tự thì hãy thay bằng từ "Chuyên gia AI" hoặc "M.Ai". '],
                ['role' => 'user', 'content' => $content]
            ]);

            if ($d && array_key_exists('content', $d)) {
                $a = json_decode($d['content'], true);
                if ($a && array_key_exists('name', $a)) {
                    $d['prompt'] = $prompt;
                    return $a;
                }
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
        return null;
    }

    public function analyticPromptList($prompt_texts)
    {
        $list = nl2array($prompt_texts, false);
        $i = -1;
        $inPrompt = false;
        $promptList = [];
        foreach ($list as $line) {
            $a = trim($line);
            if ($line != "") {
                if (!$inPrompt) {
                    $i++;
                    $promptList[$i] = [];
                    $inPrompt = true;
                }
                $promptList[$i][] = $line;
            } elseif ($inPrompt) {
                $inPrompt = false;
            }
        }
        // header("Content-Type: application/json");
        // die(json_encode($promptList));
        if (count($promptList)) {
            $data = [];
            foreach ($promptList as $key => $promptData) {
                $p = implode('<br>', $promptData);
                try {
                    $d = $this->analyticPrompt($p);
                    if ($d) {
                        $d['prompt'] = implode("\n", $promptData);
                        $data[] = $d;
                    }
                } catch (\Throwable $th) {
                    //throw $th;
                }
            }
            return $data;
        }
        return null;
    }
}
