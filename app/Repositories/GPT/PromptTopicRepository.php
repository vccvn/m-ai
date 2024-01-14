<?php

namespace App\Repositories\GPT;

use Gomee\Repositories\BaseRepository;
use App\Masks\GPT\PromptTopicMask;
use App\Masks\GPT\PromptTopicCollection;
use App\Models\GPTPromptTopic;
use App\Validators\GPT\PromptTopicValidator;
use Illuminate\Http\Request;

/**
 * @method PromptTopicCollection<PromptTopicMask>|GPTPromptTopic[] filter(Request $request, array $args = []) lấy danh sách GPTPromptTopic được gán Mask
 * @method PromptTopicCollection<PromptTopicMask>|GPTPromptTopic[] getFilter(Request $request, array $args = []) lấy danh sách GPTPromptTopic được gán Mask
 * @method PromptTopicCollection<PromptTopicMask>|GPTPromptTopic[] getResults(Request $request, array $args = []) lấy danh sách GPTPromptTopic được gán Mask
 * @method PromptTopicCollection<PromptTopicMask>|GPTPromptTopic[] getData(array $args = []) lấy danh sách GPTPromptTopic được gán Mask
 * @method PromptTopicCollection<PromptTopicMask>|GPTPromptTopic[] get(array $args = []) lấy danh sách GPTPromptTopic
 * @method PromptTopicCollection<PromptTopicMask>|GPTPromptTopic[] getBy(string $column, mixed $value) lấy danh sách GPTPromptTopic
 * @method PromptTopicMask|GPTPromptTopic getDetail(array $args = []) lấy GPTPromptTopic được gán Mask
 * @method PromptTopicMask|GPTPromptTopic detail(array $args = []) lấy GPTPromptTopic được gán Mask
 * @method PromptTopicMask|GPTPromptTopic find(integer $id) lấy GPTPromptTopic
 * @method PromptTopicMask|GPTPromptTopic findBy(string $column, mixed $value) lấy GPTPromptTopic
 * @method PromptTopicMask|GPTPromptTopic first(string $column, mixed $value) lấy GPTPromptTopic
 * @method GPTPromptTopic create(array $data = []) Thêm bản ghi
 * @method GPTPromptTopic update(integer $id, array $data = []) Cập nhật
 */
class PromptTopicRepository extends BaseRepository
{

    /**
     * tên class mặt nạ. Thường có tiền tố [tên thư mục] + \ vá hậu tố Mask
     *
     * @var string
     */
    protected $maskClass = PromptTopicMask::class;

    /**
     * tên collection mặt nạ
     *
     * @var string
     */
    protected $maskCollectionClass = PromptTopicCollection::class;


    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\GPTPromptTopic::class;
    }


    /**
     * cập nhật danh sách topic
     * @param int $ref_id
     * @param array $topic_id_list
     * @return void
     */
    public function updatePromptTopics(int $prompt_id, array $topic_id_list = [])
    {

        $ignore = [];
        $addedData = [];
        if(count($categoris = $this->get(compact('prompt_id')))){
            foreach ($categoris as $topic) {
                // nếu topic nằm trong số id them thì bỏ qua
                if(!in_array($topic->topic_id, $topic_id_list)) $topic->delete();
                // nếu ko thì xóa
                else $ignore[] = $topic->topic_id;
            }
        }
        if(count($topic_id_list)){
            foreach ($topic_id_list as $topic_id) {
                if($topic_id &&!in_array($topic_id, $ignore)){
                    // nếu ko nằm trong danh sách bỏ qua thì ta thêm mới
                    $addedData[] = $this->save(compact('prompt_id', 'topic_id'));
                }
            }
        }
        return $addedData;
    }


}
