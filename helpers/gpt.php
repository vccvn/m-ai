<?php

use App\Repositories\GPT\CriteriaRepository;
use App\Repositories\GPT\TopicRepository;

if (!function_exists('get_parent_topic_options')) {
    /**
     * lấy danh sách danh mục cha
     * @param mixed $max_level
     * @return array
     */
    function get_parent_topic_options($max_level = 2)
    {
        return TopicRepository::getParentSelectOptions($max_level);
    }
}



if (!function_exists('get_topic_options')) {
    /**
     * lấy danh sách danh mục
     * @param mixed $args
     * @return array
     */
    function get_topic_options($args = [], $firstDefault = null)
    {
        $params = array_filter($args, function ($value) {
            return is_string($value) ? (strlen($value) > 0) : (is_array($value) ? (count($value) > 0) : true);
        });
        $options = TopicRepository::getTopicSelectOptions(array_merge(['trashed_status' => 0], $params), $firstDefault);

        return $options;
    }
}

if (!function_exists('get_topics')) {
    /**
     * lấy danh sách danh mục
     * @param mixed $args
     * @return array
     */
    function get_topics($args = [])
    {
        $params = array_filter($args, function ($value) {
            return is_string($value) ? (strlen($value) > 0) : (is_array($value) ? (count($value) > 0) : true);
        });
        $options = app(TopicRepository::class)->getData(array_merge(['trashed_status' => 0], $params));

        return $options;
    }
}


if (!function_exists('get_topic_map')) {
    /**
     * lấy map danh mục thuộc tinh
     * @param mixed $args
     * @return array
     */
    function get_topic_map($args = [])
    {
        $options = TopicRepository::getTopicMap($args);
        return $options;
    }
}

if (!function_exists('get_criteria_list')) {
    /**
     * lấy danh sách criteria
     * @param array $args
     * @return array
     */
    function get_criteria_list($args = [])
    {
        return app(CriteriaRepository::class)->get($args);
    }
}
