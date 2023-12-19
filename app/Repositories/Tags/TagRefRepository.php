<?php

namespace App\Repositories\Tags;
use Gomee\Repositories\BaseRepository;
class TagRefRepository extends BaseRepository
{
    /**
     * class chứ các phương thức để validate dử liệu
     * @var string $validatorClass
     */
    protected $validatorClass = 'App\Validators\Tags\TagRefValidator';

    /**
     * TagRepository
     *
     * @var TagRepository $tagRepository
     */
    protected $tagRepository;
    /**
     * get model
     * @return string
     */
    public function getModel()
    {
        return \App\Models\TagRef::class;
    }

    public function init()
    {
        $this->tagRepository = app(TagRepository::class);
    }



    /**
     * lấy các tag của một ref nào đó
     * @param string $ref
     * @param int $ref_id
     * @return array
     */
    public function getTagRefs($ref = 'post', $ref_id = 0)
    {
        $data = [];
        if($ref_id && $tags = $this->getBy(compact('ref', 'ref_id'))){
            foreach ($tags as $tag) {
                $data[] = $tag->tag_id;
            }
        }
        return $data;
    }

    /**
     * cập nhật danh sách tag
     * @param string $ref
     * @param string $ref_id
     * @param array $tag_id_list
     * @return void
     */
    public function updateTagRef(string $ref = 'post', $ref_id, array $tag_id_list = [])
    {

        $ingore = [];
        $addedData = [];
        if(count($tags = $this->get(compact('ref', 'ref_id')))){
            foreach ($tags as $tagged) {
                // nếu tag nằm trong số id them thì bỏ qua
                if(!in_array($tagged->tag_id, $tag_id_list)) {
                    if($tag = $this->tagRepository->find($tagged->tag_id)){
                        $tag->tagged_count--;
                        $tag->save();
                    }

                    $tagged->delete();
                }
                // nếu ko thì xóa
                else $ingore[] = $tagged->tag_id;
            }
        }
        if(count($tag_id_list)){
            foreach ($tag_id_list as $tag_id) {
                if(!in_array($tag_id, $ingore)){
                    // nếu ko nằm trong danh sách bỏ qua thì ta thêm mới
                    $addedData[] = $this->save(compact('ref','ref_id', 'tag_id'));
                    if($tag = $this->tagRepository->find($tag_id)){
                        $tag->tagged_count++;
                        $tag->save();
                    }

                }
            }
        }
        return $ingore;
    }
}
