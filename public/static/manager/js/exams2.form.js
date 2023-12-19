function OrderItem(selector) {
    var $el = $(selector);
    this.$el = $el;
    var $i = $el.data('max-index') + 1 || 0;
    var $j = 0;
    var html = '<table class="table table-bordered text-center table-exams">\n' +
        '            <thead class="thead-dark">\n' +
        '            <tr>\n' +
        '                <th><input type="text" class="form-control" name="content[subject][$i][name]"\n' +
        '                           placeholder="Nhập môn thi" required="true"></th>\n' +
        '                <th colspan="5">Số câu hỏi theo mức độ</th>\n' +
        '            </tr>\n' +
        '            <tr>\n' +
        '                <th>Chuyên đề</th>\n' +
        '                <th>Nhận biết</th>\n' +
        '                <th>Thông hiểu</th>\n' +
        '                <th>Vận dụng</th>\n' +
        '                <th>Vận dụng cao</th>\n' +
        '                <th></th>\n' +
        '            </tr>\n' +
        '            </thead>\n' +
        '            <tbody class="append-topic-$i">\n' +
        '            <tr>\n' +
        '                <th scope="row"><input type="text" class="form-control"\n' +
        '                                       name="content[subject][$i][topic][$j][topic_id]" placeholder="Nhập chuyên đề">\n' +
        '                </th>\n' +
        '                <td><input type="number" class="form-control"\n' +
        '                           name="content[subject][$i][topic][$j][first_level_question_qty]" value="0" placeholder="Nhập số câu hỏi">\n' +
        '                </td>\n' +
        '                <td><input type="number" class="form-control"\n' +
        '                           name="content[subject][$i][topic][$j][second_level_question_qty]"\n' +
        '                           placeholder="Nhập số câu hỏi" value="0"></td>\n' +
        '                <td><input type="number" class="form-control"\n' +
        '                           name="content[subject][$i][topic][$j][third_level_question_qty]" value="0" placeholder="Nhập số câu hỏi">\n' +
        '                </td>\n' +
        '                <td><input type="number" class="form-control"\n' +
        '                           name="content[subject][$i][topic][$j][fourth_level_question_qty]"\n' +
        '                           placeholder="Nhập số câu hỏi" value="0"></td>\n' +
        '                <td>\n' +
        '                    <a href="javascript:void(0)" data-original-title="Thêm"\n' +
        '                       data-toggle="m-tooltip" data-index="$i" data-placement="left" title=""\n' +
        '                       class="text-accent btn-edit-item btn btn-outline-accent btn-sm m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air">\n' +
        '                        <i class="flaticon-add">\n' +
        '                        </i>\n' +
        '                    </a>\n' +
        // '                    <a href="javascript:void(0);" data-toggle="m-tooltip" data-placement="left"\n' +
        // '                       data-original-title="Xóa"\n' +
        // '                       class="btn-delete text-danger btn btn-outline-danger btn-sm m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air">\n' +
        // '                        <i class="flaticon-delete-1"></i>\n' +
        // '                    </a>\n' +
        '                </td>\n' +
        '            </tr>\n' +
        '            </tbody>\n' +
        '        </table>';
    var appendTopic = '            <tr class="append-topic-item-$i$j">\n' +
        '                <th scope="row"><input type="text" class="form-control"\n' +
        '                                       name="content[subject][$i][topic][$j][name]" placeholder="Nhập chuyên đề">\n' +
        '                </th>\n' +
        '                <td><input type="number" class="form-control"\n' +
        '                           name="content[subject][$i][topic][$j][first_level_question_qty]" value="0"  placeholder="Nhập số câu hỏi">\n' +
        '                </td>\n' +
        '                <td><input type="number" class="form-control"\n' +
        '                           name="content[subject][$i][topic][$j][second_level_question_qty]"\n' +
        '                           placeholder="Nhập số câu hỏi" value="0"></td>\n' +
        '                <td><input type="number" class="form-control"\n' +
        '                           name="content[subject][$i][topic][$j][third_level_question_qty]" value="0" placeholder="Nhập số câu hỏi">\n' +
        '                </td>\n' +
        '                <td><input type="number" class="form-control"\n' +
        '                           name="content[subject][$i][topic][$j][fourth_level_question_qty]"\n' +
        '                           placeholder="Nhập số câu hỏi" value="0"></td>\n' +
        '                <td>\n' +
        '                    <a href="javascript:void(0)" data-index="$i" data-original-title="Thêm"\n' +
        '                       data-toggle="m-tooltip" data-placement="left" title=""\n' +
        '                       class="text-accent btn-edit-item btn btn-outline-accent btn-sm m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air">\n' +
        '                        <i class="flaticon-add">\n' +
        '                        </i>\n' +
        '                    </a>\n' +
        '                    <a href="javascript:void(0);" data-index="$i$j" data-toggle="m-tooltip" data-placement="left"\n' +
        '                       data-original-title="Xóa"\n' +
        '                       class="btn-delete text-danger btn btn-outline-danger btn-sm m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air">\n' +
        '                        <i class="flaticon-delete-1"></i>\n' +
        '                    </a>\n' +
        '                </td>\n' +
        '            </tr>\n';
    this.init = function () {
        $el.on('click', '.btn-add-exams-item', function () {
            var data = html.replaceAll("$i", $i);
            var replaceTopic = data.replaceAll("$j", $j);
            $('.append-data-table').append(replaceTopic);
            $i++;
        });

        $el.on('click', '.text-accent', function () {

            var index = this.getAttribute('data-index');
            var topicIndex = $('.append-topic-' + index);
            // $j = topicIndex.data('max-index');
            $j++;
            var replaceSubject = appendTopic.replaceAll("$i", index);
            var replaceTopic = replaceSubject.replaceAll("$j", $j);
            topicIndex.append(replaceTopic);
        });
        $el.on('click', '.btn-delete', function () {
            let index = this.getAttribute('data-index');
            $('.append-topic-item-' + index).remove();
            // $el.find('#order-item-'+index).hide(300, function(){
            //     $(this).remove();
            // });
        });
    };
}


if (typeof Exams == "undefined") {
    var Exams = {};
}

Exams.form = {
    list: {},
    add: function (selector) {
        var $el = $(selector);
        if ($el.length) {
            let $select = new OrderItem($el[0]);
            $select.init();
            this.list[$el.data('id')] = $select;
        }
    }
};


$(function () {
    var $cs = $('.form-list-exams');

    if ($cs.length) {
        $cs.each(function (i, el) {
            Exams.form.add(el);

            var totalPoints = 0;
            $(this).find('input[type=number]').each(function(){
                totalPoints += parseInt($(this).val());
            });
            $("input[name='content[current_question]']").val(totalPoints);
        });
    }
});
