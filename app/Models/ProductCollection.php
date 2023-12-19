<?php

namespace App\Models;
use Gomee\Models\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * ProductCollection class
 *
 * @property string $name Name
 * @property string $slug Slug
 * @property string $description Description
 * @property integer $category_id Category Id
 * @property string $sorttype SortType
 * @property string $keywords Keywords
 */
class ProductCollection extends Model
{
    const REF_KEY = 'product-collection';
    public $table = 'product_collections';
    public $fillable = ['name', 'slug', 'description', 'category_id', 'sorttype', 'keywords'];


    /**
     * ket noi voi bang user_meta
     * @return queryBuilder
     */
    public function metadatas()
    {
        return $this->hasMany(Metadata::class, 'ref_id', 'id')->where('ref', self::REF_KEY);
    }


    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
    public function categoryRefs()
    {
        return $this->hasMany(CategoryRef::class, 'ref_id', 'id')->where('ref', self::REF_KEY);
    }



    public function categories()
    {
        return $this->categoryRefs()->join('categories', 'categories.id', '=', 'category_refs.category_id')->select('category_refs.category_id', 'category_refs.ref_id', 'categories.*');
    }

    public function collectionTags()
    {
        return $this->hasMany(TagRef::class, 'ref_id', 'id')->where('tag_refs.ref', self::REF_KEY);
    }

    // public function tags()
    // {
    //     return $this->collectionTags()->join('tags', 'tag_refs.tag_id', '=', 'tags.id')->select('tags.name', 'tags.keyword', 'tags.slug');

    // }

    public function tags()
    {
        return $this->collectionTags()->join('tags', 'tag_refs.tag_id', '=', 'tags.id')->select('tags.name', 'tags.keyword', 'tags.slug');
    }


    public function collectionLabels()
    {
        return $this->hasMany(ProductLabelRef::class, 'ref_id', 'id')->where('ref', self::REF_KEY);
    }

    public function labels(): BelongsToMany
    {
        return $this->collectionLabels()
            ->join('product_labels', 'product_labels.id', '=', 'product_label_refs.label_id')
            ->select('product_labels.*', 'product_label_refs.ref_id', 'product_label_refs.label_id');
    }

    /**
     * Get the featureRef associated with the StyleSet
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function featureRef(): HasOne
    {
        return $this->hasOne(FileRef::class, 'ref_id', 'id')->where('ref', self::REF_KEY);
    }




    public function beforeDelete()
    {
        $this->collectionTags()->delete();
        $this->categoryRefs()->delete();
        $this->collectionLabels()->delete();
    }

    public function getSortTypeLabel()
    {
        return array_key_exists($this->sorttype, $so = get_product_sortby_options()) ? $so[$this->sorttype]: "Mặc định";
    }

    public function getTagNames()
    {
        $names = [];

        if($this->tags  && count($this->tags)){
            foreach ($this->tags as $tag) {
                $names[] = htmlentities($tag->name);
            }
        }
        return implode(', ', $names);
    }
    public function getLabelIDs()
    {
        $data = [];
        if($this->collectionLabels && count($this->collectionLabels)){
            foreach ($this->collectionLabels as $key => $value) {
                $data[] = $value->label_id;
            }
        }
        return $data;
    }
    public function getTagIDs()
    {
        $data = [];
        if($this->collectionTags && count($this->collectionTags)){
            foreach ($this->collectionTags as $key => $value) {
                $data[] = $value->tag_id;
            }
        }
        return $data;
    }
    public function getCateIDs()
    {
        $data = [];
        if($this->categories && count($this->categories)){
            foreach ($this->categories as $key => $value) {
                $data[] = $value->id;
            }
        }
        return $data;
    }


    public function getProductParams()
    {
        $labels = $this->getLabelIDs();
        $cates = $this->getCateIDs();
        $tags = $this->getTagIDs();
        $getProductParamData = [
            '@sorttype' => $this->sorttype
        ];

        if ($labels) {
            $getProductParamData['@matchAllLabel'] = $labels;
        }
        if ($cates) {
            $getProductParamData['@hasAnyCategory'] = $cates;
        }
        if ($tags) {
            $getProductParamData['@matchAllTag'] = $cates;
        }


        return $getProductParamData;



    }
    public function getUrlParams()
    {
        $labels = $this->getLabelIDs();
        $cates = $this->getCateIDs();
        $tags = $this->getTagIDs();
        $urlParams = [
            'sorttype' => $this->sorttype
        ];

        if ($labels) {
            $urlParams['match_labels'] = implode(',', $labels);
        }
        if ($cates) {
            $urlParams['in_any_category'] = implode(',', $cates);
        }
        if ($tags) {
            $urlParams['match_tags'] = implode(',', $tags);
        }



        return $urlParams;


    }


    /**
     * lay du lieu form
     * @return array
     */
    public function toFormData()
    {
        $this->applyMeta();
        $data = $this->toArray();
        $tags = [];
        if (count($this->collectionTags)) {
            foreach ($this->collectionTags as $tagged) {
                $tags[] = $tagged->tag_id;
            }
        }
        $data['tags'] = $tags;

        if($this->featureRef){
            $data['featured_image'] = $this->featureRef->file_id;
        }
        $data['labels'] = $this->getLabelIDs();
        $data['categories'] = $this->getCateIDs();
        return $data;
    }



    /**
     * get avatar url
     * @param boolean $urlencode mã hóa url
     * @return string
     */
    public function getFeaturedImage($urlencode=false)
    {
        if($this->featureRef && $file = get_media_file(['id' => $this->featureRef->file_id])){
            $url = $file->url;
        }
        else{
            $url = url('static/images/default.png');
        }


        if($urlencode) return urlencode($url);
        return $url;
    }


}
