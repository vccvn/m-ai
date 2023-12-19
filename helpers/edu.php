<?php


if(!function_exists('get_privacy_course_type_labels')){
    function get_privacy_course_type_labels()
    {
        return Course::TYPE_PRIVACY_LABELS;
    }
}

if(!function_exists('get_commerce_course_type_labels')){
    function get_commerce_course_type_labels()
    {
        return Course::TYPE_PRIVACY_LABELS;
    }
}
if(!function_exists('get_privacy_lesson_type_labels')){
    function get_privacy_lesson_type_labels()
    {
        return Lesson::TYPE_PRIVACY_LABELS;
    }
}

if(!function_exists('get_commerce_lesson_type_labels')){
    function get_commerce_lesson_type_labels()
    {
        return Lesson::TYPE_PRIVACY_LABELS;
    }
}
if(!function_exists('get_course_options')){
    function get_course_options($args = [])
    {
        return app(CourseRepository::class)->getDataOptions($args);
    }
}


if(!function_exists('get_course')){
    function get_course($args = [])
    {
        return app(CourseRepository::class)->mode('mask')->detail($args);
    }
}


if(!function_exists('get_courses')){
    function get_courses($args = [])
    {
        return app(CourseRepository::class)->mode('mask')->getData($args);
    }
}
if(!function_exists('get_course_sortby_options')){
    function get_course_sortby_options()
    {
        $options = get_edu_config('courses.list.sortby', []);

        return $options;
    }
}


if(!function_exists('get_subject_options')){
    function get_subject_options($args = []){
        return app(\App\Repositories\Subjects\SubjectRepository::class)->getDataOptions($args);
    }
}
if(!function_exists('get_subject_topic_map')){
    function get_subject_topic_map($args = []){
        $results = app(\App\Repositories\Subjects\SubjectRepository::class)->with('topics')->get();
        $map = [];

        foreach ($results as $key => $subject) {
            $sm = [];
            if(count($subject->topics)){
                foreach ($subject->topics as $topic) {
                    $sm[$topic->id] = $topic->name;
                }
            }
            $map[$subject->id] = $sm;
        }
        return $map;
    }
}


if(!function_exists('get_grade_options')){
    function get_grade_options($args = []){
        return app(GradeRepository::class)->getDataOptions($args);
    }
}
if(!function_exists('get_grade_options_by_subject')){
    function get_grade_options_by_subject($args = []){
        if(array_key_exists('subject_id', $args) && (!$args['subject_id'] || !get_subject_options(['id' => $args['subject_id']]))) unset($args['subject_id']);
        if(array_key_exists('id', $args) && !$args['id']) unset($args['id']);

        return app(GradeRepository::class)->getDataOptions($args);
    }
}

if(!function_exists('get_subject_data_map')){
    function get_subject_data_map($args = []){
        $subjects = app(\App\Repositories\Subjects\SubjectRepository::class)->with('topics')->get($args);
        $map = [];
        foreach ($subjects as $key => $subject) {
            $sm = [];

            if(count($subject->topics)){
                foreach ($subject->topics as $topic) {
                    $sm[$topic->id] = $topic->toArray();
                }
            }
            $map[$subject->id] = [
                'id' => $subject->id,
                'slug' => $subject->slug,
                'name' => $subject->name,
                'max_level' => $subject->max_level,
                'level_labels' => $subject->getLevelLabels(),
                'topics' => $sm,
                'grades' => $subject->getGradeMap()
            ];
        }
        return $map;
    }
}

if(!function_exists('get_exam_privacy_labels')){
    function get_exam_privacy_labels()
    {
        return Exam::TYPE_ALL_LABELS;
    }
}

if(!function_exists('get_public_exams')){
    function get_public_exams($args = [])
    {
        return app(ExamRepository::class)->getData(array_merge($args, ['type' => Exam::TYPE_PUBLIC]));
    }
}
if(!function_exists('get_first_public_exams')){
    function get_first_public_exams($args = [])
    {
        return app(ExamRepository::class)->orderBy('id', 'DESC')->detail(array_merge($args, ['type' => Exam::TYPE_PUBLIC]));
    }
}


if(!function_exists('get_public_exam_options')){
    function get_public_exam_options($args = [], $firstDefault = null)
    {
        return app(ExamRepository::class)->getDataOptions(array_merge($args, ['type' => Exam::TYPE_PUBLIC]), $firstDefault);
    }
}

if(!function_exists('get_etr_privacy_options')){
    function get_etr_privacy_options()
    {
        return ExamTestResult::getPrivacyOptions();
    }
}



if(!function_exists('get_school_options')){
    function get_school_options($args = [], $firstDefault = null)
    {
        return app(SchoolRepository::class)->getSchoolOptiobs(array_merge($args, ['type' => Exam::TYPE_PUBLIC]), $firstDefault);
    }
}



if(!function_exists('get_exam_test_combos')){
    function get_exam_test_combos($args = [])
    {
        /**
         * @var ExamTestComboRepository
         */
        $etc = app(ExamTestComboRepository::class);
        return $etc->get($args);
    }
}



if(!function_exists('get_user_rank')){
    function get_user_rank($exam_id = 0, $round = 'week', $args = [])
    {
        /**
         * @var TopUserRepository
         */
        $etc = app(TopUserRepository::class);
        return $etc->mode('mask')->cache('get-user-rank', setting('cache_data_time', 0), $args)->getTopUsers( $exam_id, 'week',$args );
    }
}
if(!function_exists('create_hash_engine')){
    function create_hash_engine($hash_key){
        $key = file_get_contents(storage_path('encryption/enc.key'));
        $keyUnzip = gzuncompress(base64_decode(substr($key, 4)));
        $path = storage_path('encryption/data/' . md5(uniqid()) . '.php');
        // echo base64_decode($keyUnzip);
        file_put_contents($path, base64_decode($keyUnzip));
        // echo file_get_contents($path);
        if(file_exists($path)){
            require_once $path;
            if(class_exists('\BCAHash')){
                $engine = new \BCAHash($hash_key);

                return $engine;

            }
            try {
                unlink($path);
            } catch (\Throwable $th) {
                //throw $th;
            }
        }
    }
}
