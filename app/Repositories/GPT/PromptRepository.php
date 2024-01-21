<?php

namespace App\Repositories\GPT;

use Gomee\Repositories\BaseRepository;
use App\Masks\GPT\PromptMask;
use App\Masks\GPT\PromptCollection;
use App\Models\GPTPrompt;
use App\Validators\GPT\PromptValidator;
use Illuminate\Http\Request;

/**
 * @method PromptCollection<PromptMask>|GPTPrompt[] filter(Request $request, array $args = []) lấy danh sách GPTPrompt được gán Mask
 * @method PromptCollection<PromptMask>|GPTPrompt[] getFilter(Request $request, array $args = []) lấy danh sách GPTPrompt được gán Mask
 * @method PromptCollection<PromptMask>|GPTPrompt[] getResults(Request $request, array $args = []) lấy danh sách GPTPrompt được gán Mask
 * @method PromptCollection<PromptMask>|GPTPrompt[] getData(array $args = []) lấy danh sách GPTPrompt được gán Mask
 * @method PromptCollection<PromptMask>|GPTPrompt[] get(array $args = []) lấy danh sách GPTPrompt
 * @method PromptCollection<PromptMask>|GPTPrompt[] getBy(string $column, mixed $value) lấy danh sách GPTPrompt
 * @method PromptMask|GPTPrompt getDetail(array $args = []) lấy GPTPrompt được gán Mask
 * @method PromptMask|GPTPrompt detail(array $args = []) lấy GPTPrompt được gán Mask
 * @method PromptMask|GPTPrompt find(integer $id) lấy GPTPrompt
 * @method PromptMask|GPTPrompt findBy(string $column, mixed $value) lấy GPTPrompt
 * @method PromptMask|GPTPrompt first(string $column, mixed $value) lấy GPTPrompt
 * @method GPTPrompt create(array $data = []) Thêm bản ghi
 * @method GPTPrompt update(integer $id, array $data = []) Cập nhật
 */
class PromptRepository extends BaseRepository
{
    /**
     * class chứ các phương thức để validate dử liệu
     * @var string $validatorClass
     */
    protected $validatorClass = PromptValidator::class;
    /**
     * tên class mặt nạ. Thường có tiền tố [tên thư mục] + \ vá hậu tố Mask
     *
     * @var string
     */
    protected $maskClass = PromptMask::class;

    /**
     * tên collection mặt nạ
     *
     * @var string
     */
    protected $maskCollectionClass = PromptCollection::class;


    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\GPTPrompt::class;
    }

    public function init()
    {
        $this->setJoinable([
            ['leftJoin', 'gpt_topics', 'gpt_topics.id', '=', 'gpt_prompts.topic_id'],
            ['leftJoin', 'gpt_prompt_topics', 'gpt_prompt_topics.topic_id', '=', 'gpt_prompts.topic_id'],
            ['join', 'gpt_topics as pt', 'gpt_prompt_topics.topic_id', '=', 'pt.id'],

        ]);
        $raw = [
            'topic_id', 'name', 'prompt'
        ];
        $columns = [
            'pt_name' => 'pt.name',
            'pt_slug' => 'pt.slug',
            'pt_keywords' => 'pt.keywords',
        ];
        $this->setSelectable(array_merge([
            'topic_name' => 'gpt_topics.name',
            'topic_keywords' => 'gpt_topics.keywords',
        ], ['gpt_prompts.*']));
        $this->setSearchable(array_merge($columns, [
            'name' => 'gpt_prompts.name',
            'keywords' => 'gpt_prompts.keywords'
        ]));
        foreach ($raw as $col) {
            $columns[$col] = 'gpt_prompts.' . $col;
        }
        $this->setSortable($columns);
        $this->searchRule([
            'pt.name' => [
                '{query}%',
                '% {query}%'
            ],
            'pt.slug' => [
                '%{query}%'
            ],
            'pt.keywords' => [
                '{query}%',
                '%, {query}%'
            ],
            'gpt_prompts.name' => [
                '{query}%',
                '% {query}%'
            ],
            'gpt_prompts.keywords' => [
                '{query}%',
                '%, {query}%'
            ]
        ])
        ->setGroupBy('gpt_prompts.id')
        ;
        $this->perPage = 50;
    }

    public function beforeSave($data, $id = null)
    {
        if (array_key_exists('name', $data)) $data['slug'] = str_slug($data['name']);
    }
}
