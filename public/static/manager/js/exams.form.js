$(function () {
    function ExamCreator(config) {
        this.urls = config && config.urls ? config.urls : {};
        this.subjectList = {};
        this.data = {};
        this.templates = { item: "", list: "" };
        this.init_list = ["urls", "templates", 'data'];
        this.topicIndex = -1;
        this.subjectIndex = -1;
        this.currentSubjectId = -1;
        // this.maxIndex = $('.form-list-exams') || -1;
        var started = false;
        var self = this;
        /**
         * init
         * @param {object} args
         */
        this.init = args => {
            App.setDefault(this, args || this.options);
            this.onStart();
        };

        this.addTopic = function (index_subject, subject_id, render_after) {
            var data = {};
            // this.subjectList[index_subject].topic_index++;
            var $renderAfter = null;
            try {
                if(render_after && $(render_after).length) $renderAfter = $(render_after);
            } catch (error) {

            }
            if($renderAfter){
                var dataIndex = $renderAfter.data('index');
                data.index_topic = String(dataIndex).split("-").shift() + "-" + App.str.rand();
            }else{
                this.topicIndex++;
                data.index_topic = this.topicIndex;

            }
            data.index_subject = index_subject || this.subjectIndex;
            // select topic
            var topic_options = {};
            if (subject_id && self.data.subject_topic_map && Object.prototype.hasOwnProperty.call(self.data.subject_topic_map, subject_id)) {
                topic_options = self.data.subject_topic_map[subject_id];
            }
            var topicSelectHtml = '<select name="content[subjects][' + data.index_subject + '][topics][' + this.topicIndex + '][topic_id]" class="form-control m-input topic-select-input">'
            +'<option value="0">Tất cả</option>';
            if (topic_options) {
                Object.keys(topic_options).map(function (k) {
                    topicSelectHtml += '<option value="' + k + '">' + topic_options[k] + '</option>';
                })
            }

            topicSelectHtml += '</select>';

            data.topic_select = topicSelectHtml;
            var html = App.str.eval(this.templates.topic_item, data);
            if($renderAfter){
                $renderAfter.after(html)
            }
            else if (!isNaN(parseInt(index_subject))) {
                $('.append-topic-' + index_subject).append(html);
            } else {
                $('#table-exams-' + data.index_subject + ' .append-topic').append(html);
            }

            $('.subject-item-block').each(function(i, el){
                self.checkMaxLevel(el)
            })
            self.checkNumQuestion();

        };

        this.addSubject = function (question) {
            var data = Object.assign({}, question);

            this.subjectIndex++;
            this.subjectList[this.subjectIndex] = { 'topic_index': 0 };
            data.index_subject = this.subjectIndex;

            // select subject
            var default_subject = 0;
            var subjectSelectHtml = '<select name="content[subjects][' + this.subjectIndex + '][subject_id]" id="content-' + data.index_subject + '-subject-id" class="form-control m-input subject-select-input">';
            if (self.data.subject_options) {
                Object.keys(self.data.subject_options).map(function (k) {
                    if (default_subject === 0) {
                        default_subject = k;
                    }
                    subjectSelectHtml += '<option value="' + k + '" ' + ((k == self.currentSubjectId) ? 'selected' : '') + '>' + self.data.subject_options[k] + '</option>';
                })
                self.currentSubjectId = default_subject;
            }
            subjectSelectHtml += '</select>';
            data.subject_select = subjectSelectHtml;

            let grade_options = self.data.subject_data_map[default_subject]?.grades;
            if (!grade_options) grade_options = {
                empty: {
                    id: 0,
                    name: "Tất cả"
                }
            };
            var gradeSelectHtml = '<select name="content[subjects][' + this.subjectIndex + '][grade_id]" id="content-' + data.index_subject + '-grade-id" class="form-control m-input grade-select-input">'
            +'<option value="0">Tất cả</option>';;
            if (grade_options) {
                gradeSelectHtml += Object.keys(grade_options).map(function (k) {
                    let grade_id = data.grade_id ? data.grade_id : (self.currentGradeId ? self.currentGradeId : 0);
                    let grade = grade_options[k];
                    return '<option value="' + grade.id + '" ' + ((grade_id == grade.id) ? 'selected' : '') + '>' + grade.name + '</option>';
                }).join("");
            }
            gradeSelectHtml += '</select>';
            data.grade_select = gradeSelectHtml;
            // select topic
            var topic_options = {};
            if (self.currentSubjectId && self.data.subject_topic_map && Object.prototype.hasOwnProperty.call(self.data.subject_topic_map, self.currentSubjectId)) {

                topic_options = self.data.subject_topic_map[parseInt(self.currentSubjectId)];
            }
            var topicSelectHtml = '<select name="content[subjects][' + this.subjectIndex + '][topics][' + this.topicIndex + '][topic_id]" class="form-control m-input topic-select-input">'
            +'<option value="0">Tất cả</option>';
            if (topic_options) {
                Object.keys(topic_options).map(function (k) {
                    topicSelectHtml += '<option value="' + k + '">' + topic_options[k] + '</option>';
                })
            }
            topicSelectHtml += '</select>';

            data.topic_select = topicSelectHtml;
            // select corect answer
            $('.form-list-exams .append-data-table').append(App.str.eval(this.templates.subject_item, data));
            $('#subject-item-block-' + data.index_subject).find('.crazy-ckeditor').each((i, el) => addCkeditor(el));

        };

        this.checkNumQuestion = function () {
            var totalQuestion = 0;
            var inValidValues = [];
            var maxQuestion = parseInt($('#question_total').val());
            $('.sinput.available .question_number').each(function (i, el) {
                var tong = parseInt($(el).val());
                if (!isNaN(tong) && tong > 0) {
                    totalQuestion += tong;
                }else if(isNaN(tong)){
                    inValidValues.push();
                }
            });
            if(inValidValues.length){
                $('.sticky-btn-submit-form').addClass('disabled');
                $('.btn-submit-form').prop('disabled', true);
                $('.dropdown-toggle-split').prop('disabled', true);
                return App.Swal.warning("Các giá trị " + inValidValues.join(",  ") + " Không hợp lệ");
            }
            $('#current_question_total').val(totalQuestion);
            if (maxQuestion < totalQuestion) {
                $('.sticky-btn-submit-form').addClass('disabled');
                $('.btn-submit-form').prop('disabled', true);
                $('.dropdown-toggle-split').prop('disabled', true);
            } else {
                $('.sticky-btn-submit-form').removeClass('disabled');
                $('.btn-submit-form').prop('disabled', false);
                $('.dropdown-toggle-split').prop('disabled', false);
            }
        };

        this.checkMaxLevel = function checkMaxLevel(table) {
            var subject_id = $(table).find('.subject-select-input').val();
            if (!subject_id || !self.data.subject_data_map[subject_id]) return false;
            var subject = self.data.subject_data_map[subject_id];
            var max_level = subject.max_level;
            var levelLabels = subject.level_labels;
            let i = 1;
            if (levelLabels) {
                Object.keys(levelLabels).map(n => {
                    label = levelLabels[n];
                    if (i <= max_level) {
                        $(table).find(".slabel.level-" + n).removeClass('d-none').html(label);
                        $(table).find(".sinput.level-" + n).removeClass('d-none').addClass('available');

                        i++;
                    }
                })
            }
            for (let index = i; index < 11; index++) {
                $(table).find(".slabel.level-" + index).addClass('d-none');
                $(table).find(".sinput.level-" + index).addClass('d-none').removeClass('available').find('input').val(0);

            }
        }

        this.onChangeSubject = function onChangeSubject(subject_id, table) {
            var topicSelectHtml = '<option value="0">Tất cả</option>';
            var topic_options = {};
            // var subject_id = $(this).val();
            if (subject_id && self.data.subject_topic_map && Object.prototype.hasOwnProperty.call(self.data.subject_topic_map, subject_id)) {
                topic_options = self.data.subject_topic_map[subject_id];
            }
            if (topic_options) {
                Object.keys(topic_options).map(function (k) {
                    topicSelectHtml += '<option value="' + k + '">' + topic_options[k] + '</option>';
                })
            }
            $(table).find('.topic-select-input').each(function (i, el) {
                $(el).html(topicSelectHtml);
            });

            let grade_options = self.data.subject_data_map[subject_id]?.grades;
            if (!grade_options) grade_options = {
                empty: {
                    id: 0,
                    name: "Tất cả"
                }
            };
            var grade_id = $(table).find('.grade-select-input').val();
            var gradeSelectHtml = '<option value="0">Tất cả</option>';
            if (grade_options) {
                gradeSelectHtml += Object.keys(grade_options).map(function (k) {
                    // let grade_id = data.grade_id ? data.grade_id : (self.currentGradeId ? self.currentGradeId : 0);
                    let grade = grade_options[k];
                    return '<option value="' + grade.id + '" ' + ((grade_id == grade.id) ? 'selected' : '') + '>' + grade.name + '</option>';
                }).join("");
            }

            $(table).find('.grade-select-input').each(function (i, el) {
                $(el).html(gradeSelectHtml);
            });
            self.checkMaxLevel(table);
            self.checkNumQuestion();
        }

        this.onChangeType = value => value == 'protected' ? $('.col-time-input').addClass('d-none') : $('.col-time-input').removeClass('d-none');

        this.onStart = function () {
            if (started) return;
            started = true;
            var maxIndex = parseInt($('.form-list-exams').data('max-index'));
            if (!isNaN(maxIndex)) self.subjectIndex = maxIndex;
            this.topicIndex = this.data.max_topic_index;
            $(document).on("change", ".subject-select-input", function (e) {
                // self.currentSubjectId = $(this).val();
                self.onChangeSubject($(this).val(), $(this).closest(".subject-item-block"))

            });

            $(document).on("click", ".btn-delete", function (e) {
                e.preventDefault();
                self.checkNumQuestion();
                var $table = $(this).closest(".subject-item-block");
                $('.append-topic-item-' + $(this).data('index')).hide(300, function () {
                    $(this).remove();
                    if ($table.find("tbody tr").length == 0) {
                        $table.remove();
                    }
                    self.checkNumQuestion();
                });
                return false;
            });

            $(document).on("click", ".btn-edit-item", function (e) {
                var index_topic = $(this).data('index-subject');
                var subject_id = $(this).closest(".subject-item-block").find('.subject-select-input').val();
                self.addTopic(index_topic, subject_id, $(this).closest('tr'));


                e.preventDefault();
                self.checkNumQuestion();
                return false;
            });
            //them mon thi
            $(document).on("click", ".btn-add-exams-item", function (e) {
                e.preventDefault();
                self.addSubject(self.data.empty_exam);
                self.addTopic('', self.currentSubjectId);

                self.checkNumQuestion();
                return false;
            });

            $(document).on("click", ".btn-remove-subject-block", function(e){
                e.preventDefault();
                $($(this).data('ref')).hide(300, function(){
                    $(this).remove();

                    self.checkNumQuestion();
                });
                return false;
            });

            if ($('.append-data-table .table-exams').length == 0 && this.data && this.data.empty_exam) {
                self.addSubject(this.data.empty_exam);
                self.addTopic('', self.currentSubjectId);

                $('.subject-item-block').each(function(i, el){
                    self.checkMaxLevel(el)
                })
                self.checkNumQuestion();
            }

            $(document).on("change", ".question_number", function (e) {
                self.checkNumQuestion();
            });

            $(document).on("change", "#question_total", function (e) {
                var value = parseInt($(this).val());
                if(!isNaN(value) && value < 0) {
                    $(this).val(0);
                    return App.Swal.warning("Giá trị " + value + " không hợp lệ");
                }
                self.checkNumQuestion();
            });



            var totalQuestion = 0;
            $('.sinput.available .question_number').each(function (i, el) {
                var tong = parseInt($(el).val());
                if (!isNaN(tong)) {
                    totalQuestion += tong;
                }
            });
            $('#current_question_total').val(totalQuestion);

            self.onChangeType($('input#type').val());


            $('.subject-item-block').each(function(i, el){
                self.checkMaxLevel(el)
            })
            self.checkNumQuestion();
            setInterval(() => {
                $('.subject-item-block').each(function(i, el){
                    self.checkMaxLevel(el)
                })
                self.checkNumQuestion();
            }, 500);
        };

    }

    App.examCreator = new ExamCreator();
    App.examCreator.init(exams_create_data);

});
