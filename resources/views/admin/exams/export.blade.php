@php
    use Gomee\Html\Dom\HtmlDomParser;
    $tidy = new \tidy();
    $question_count = 0;

    $alphabet = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
@endphp

@php
    $parseQuestionContent = function ($content, $type, $question_count, $slug = 'any') use ($tidy) {
        $cleanStyle = function ($element, $factory) {
            if ($element->style) {
                $element->style = str_replace(['&quot;', '"'], "'", trim(trim($element->style), ';'));
            }
            if ($element->style) {
                if (preg_match('/\;$/i', trim($element->style))) {
                    $element->style .= 'vertical-align: middle;font-size:11pt;';
                } else {
                    $element->style .= ';vertical-align: middle;font-size:11pt;';
                }
            } else {
                $element->style = 'vertical-align: middle;font-size:11pt;';
            }

            if ($element->face) {
                if (!preg_match('/symbol/i', $element->face)) {
                    $element->face = '';
                }
            }
            if (count($ch = $element->children())) {
                foreach ($ch as $c) {
                    $factory($c, $factory);
                }
            }
        };
        $cleanTableHasOneTD = function ($self, $element, $tagConvertTo = 'div') {
            if (count($children = $element->children())) {
                foreach ($children as $child) {
                    $self($self, $child, $tagConvertTo);
                }
            }
            if ($element->tag == 'table') {
                if (count($tds = $element->find('td,th')) == 1) {
                    $td = $tds[0]->innertext;
                    $element->innertext = $td;
                    $element->tag = $tagConvertTo;
                } else {
                    $c = 0;
                    $trs = $element->find('tr');
                    $innerText = '';

                    if ($trs) {
                        foreach ($trs as $tr) {
                            if (count($tdh = $tr->find('td,th')) == 1) {
                                $innerText .= '<div>' . $tdh[0]->innertext . '</div>';
                                $c++;
                            }
                        }
                    }
                    if ($c == count($trs)) {
                        $element->tag = 'div';
                        $element->innertext = $innerText;
                    }
                }
            }
            return $element;
        };
        $content = $tidy->repairString($content, []);

        $htmlDom = HtmlDomParser::str_get_html($content);
        $questionContents = $htmlDom->find('body > *');
        $needParagraph = count($htmlDom->find('body > p')) < 1;
        $hasParagraph = false;
        foreach ($questionContents as $key => $item) {
            if ($item->tag != 'p') {
                if ($item->find('p')) {
                    $hasParagraph = true;
                }
            }
            // else {

            // }
            $cleanStyle($item, $cleanStyle);
        }

        $questionHtml = '';
        $i = 0;
        $isset = false;
        $canSet = true;
        foreach ($questionContents as $index => $item) {
            // $item->align = 'middle';
            $questionImgs = $item->find('img');
            if (count($questionImgs)) {
                foreach ($questionImgs as $key => $img) {
                    $img->style .= 'vertical-align: middle;margin-bottom:-3pt;position:relative;';
                    $img->align = 'absmiddle';
                }
            }
            if ($item->style) {
                if (preg_match('/\;$/i', trim($item->style))) {
                    $item->style .= 'position:relative';
                } else {
                    $item->style .= ';position:relative;';
                }
            } else {
                $item->style = 'position:relative;';
            }
            if ($item->tag == 'font') {
                $item->tag = 'span';
            }
            // $fonts = $item->find('font');
            // if($fonts && count($fonts)){
            //     foreach ($fonts as $f) {
            //         if($f->tag == 'font') $f->tag = 'span';
            //     }
            // }
            $fonts = $item->find('*');
            if ($fonts && count($fonts)) {
                foreach ($fonts as $f) {
                    if ($f->tag != 'img') {
                        if ($f->style) {
                            if (preg_match('/\;$/i', trim($f->style))) {
                                $f->style .= 'padding-bottom:3pt;';
                            } else {
                                $f->style .= ';padding-bottom:3pt;';
                            }
                        } else {
                            $f->style = 'padding-bottom:3pt;';
                        }
                    }
                }
            }
            if ($i == 0) {
                if ($t = count($p = $item->find('p'))) {
                    $p[0]->align = 'left';
                    for ($ind = 1; $ind < $t; $ind++) {
                        $p[$ind]->align = null;
                    }

                    if ($p[0]->style) {
                        if (preg_match('/\;$/i', trim($p[0]->style))) {
                        } else {
                            $p[0]->style .= ';';
                        }
                        if (preg_match('/text\-align\:\s*center/i', $p[0]->style)) {
                            $p[0]->style = preg_replace('/text\-align\:\s*center/i', 'text-align: left', $p[0]->style);
                        } else {
                            $p[0]->style .= 'text-align: left;';
                        }
                        $p[0]->style .= 'line-height: 155%;';
                    } else {
                        $p[0]->style = 'text-align: left;line-height: 155%;';
                    }
                } else {
                }
            }

            if ($item->tag == 'img') {
                $canSet = false;
            }
            if ($type != 'group' && !$isset && $canSet) {
                $lastParent = htmlGetLastParent($item);
                if ($parentText = str_slug(trim(strip_tags($lastParent->innertext(), ['img'])))) {
                    $lastParent->innertext = "<span size=\"3\" style=\"font-size: 11pt;padding-bottom:3pt;\"><b>" . ($slug == 'TA' ? '' : 'Câu') . " {$question_count}.</b></span> " . $lastParent->innertext;
                    $isset = true;
                }
            }
            $cleanTableHasOneTD($cleanTableHasOneTD, $item);
            $currenthtml = $item->outertext();
            // preg_match_all('/\<img\s[^>]*src=(\'[^\']+\'|\"[^\"]+\")\s[^>]*\>/i', $currenthtml, $matches);
            // if ($matches[0]) {
            //     foreach ($matches[0] as $tag) {
            //         // $currenthtml = str_replace($tag, '<span align="middle" style="vertical-align: middle;font-size: 11pt">'.$tag.'</span> ', $currenthtml);
            //     }
            // }

            $questionHtml .= $currenthtml;

            $questionHtml = str_replace('align="justify"', '', $questionHtml);
            $i++;
        }
        if ($type != 'group' && !$isset) {
            $questionHtml = "<span size=\"3\" style=\"font-size: 11pt;padding-bottom:3pt;\"><b>" . ($slug == 'TA' ? '' : 'Câu') . " {$question_count}.</b></span>" . $questionHtml;
        }

        return $questionHtml;
    };
    $parseAnswerContent = function ($content, $label) use ($tidy) {
        $cleanStyle = function ($element, $factory) {
            if ($element->style) {
                $element->style = str_replace(['&quot;', '"'], "'", trim(trim($element->style), ';'));
            }
            if ($element->face) {
                if (!preg_match('/symbol/i', $element->face)) {
                    $element->face = '';
                }
            }
            if (count($ch = $element->children())) {
                foreach ($ch as $c) {
                    $factory($c, $factory);
                }
            }
        };
        $answerContent = $tidy->repairString($content, []);

        $a = trim($answerContent);
        if (preg_match('/<\/*[A-z]+$/is', $a)) {
            $answerContent .= '>';
        }

        $answerDom = HtmlDomParser::str_get_html($answerContent);
        $answerContent = '';
        $answerContents = $answerDom->find('body > *');
        $icm = 0;
        $isset = false;

        foreach ($answerContents as $key => $ansItem) {
            $answerImgs = $ansItem->find('img');
            if (count($answerImgs)) {
                foreach ($answerImgs as $img) {
                    $img->style .= 'vertical-align: middle;';
                    $img->align = 'middle';
                }
            }

            $answerContent .= $ansItem->outertext();
        }
        $answerContent = $tidy->repairString($answerContent, []);
        $answerDom = HtmlDomParser::str_get_html($answerContent);
        $answerContent = '';
        $answerContents = $answerDom->find('body > *');
        $icm = 0;
        $isset = false;

        foreach ($answerContents as $key => $ansItem) {
            $cleanStyle($ansItem, $cleanStyle);
            if (!$isset && $ansItem->tag != 'img' && $icm == 0) {
                if ($ansItem->tag == 'p') {
                    $text = $ansItem->innertext;
                    $ansItem->innertext = "<b align=\"left\">$label. </b> " . $text;
                    $isset = true;
                } elseif (count($p = $ansItem->find('p'))) {
                    $text = $p[0]->innertext;
                    $p[0]->innertext = "<b align=\"left\">$label. </b> " . $p[0]->innertext;
                    $isset = true;
                } elseif ($ansItem->tag == 'div') {
                    $text = $ansItem->innertext;
                    $ansItem->innertext = "<b align=\"left\">$label. </b> " . $text;
                    $isset = true;
                }
            }

            if ($ansItem->style) {
                if (preg_match('/\;$/i', trim($ansItem->style))) {
                    $ansItem->style .= 'vertical-align: middle;font-size:11pt;';
                } else {
                    $ansItem->style .= ';vertical-align: middle;font-size:11pt;';
                }
            } else {
                $ansItem->style = 'vertical-align: middle;font-size:11pt;';
            }

            $answerContent .= $ansItem->outertext();
            $icm++;
        }
        if (!$isset) {
            $answerContent = "<b align=\"left\">$label. </b> " . $answerContent;
        }
        return $answerContent;
    };

    $inssetEndOfFirst = function ($content, $label) use ($tidy) {
        $answerContent = $tidy->repairString($content, []);

        $answerDom = HtmlDomParser::str_get_html($answerContent);
        $answerContent = '';
        $answerContents = $answerDom->find('body > *');
        $icm = 0;
        $isset = false;
        foreach ($answerContents as $key => $ansItem) {
            if ($key == 0) {
                if ($lastParent = htmlGetLastParent($ansItem)) {
                    $lastParent->innertext .= $label;
                }
            }
        }
        return $answerContents->outertext();
    };

@endphp

<html>

<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    {{-- <title>thithuthptqg.vn</title> --}}
    {{-- <meta name="generator" content="LibreOffice 7.3.5.2 (Linux)" /> --}}
    {{-- <meta name="author" content="thithuthptqg.vn" /> --}}
    <meta name="created" content="{{ date('Y-m-d') }}T{{ date('H:i:s') }}" />
    <meta name="changed" content="{{ date('Y-m-d') }}T{{ date('H:i:s') }}" />
    <style>
        @page {
            size: 8.27in 11.69in;
            margin-right: 0.59in;
            margin-top: 0.39in;
            margin-bottom: 0.21in
        }

        p {
            color: #000000;
            line-height: 115%;
            orphans: 2;
            widows: 2;
            margin-bottom: 0.1in;
            direction: ltr;
            background: transparent
        }

        p.western {
            font-family: "Times New Roman", serif;
            font-size: 14pt;
        }

        p.cjk {
            font-family: "Calibri", sans-serif;
            font-size: 14pt
        }

        p.ctl {
            font-family: "Times New Roman", serif;
            font-size: 11pt;
        }

    </style>
    {{-- <style>

        @page {
            size: 21cm 29.7cm;
            margin: 1cm 1cm 1cm 1cm;
        }

        @page Section1 {
            mso-header-margin: .5in;
            mso-footer-margin: .5in;
            mso-header: h1;
            mso-footer: f1;
            margin-left: 1.75cm;
            margin-right: 1.75cm;
            margin-top: 1.75cm;
        }

        div.Section1 {
            page: Section1;
        }

        table#hrdftrtbl {
            margin: 0in 0in 0in 0in;
            width: 1px;
            height: 1px;
            overflow: hidden;
        }


        p.MsoFooter,
        li.MsoFooter,
        div.MsoFooter {
            margin: 0in;
            margin-bottom: .0001pt;
            mso-pagination: widow-orphan;
            tab-stops: center 3.0in right 6.0in;
            font-size: 9.0pt;
        }

        p.MsoFooter td,
        li.MsoFooter td,
        div.MsoFooter td {
            font-size: 9pt
        }

        p.MsoHeader,
        li.MsoHeader,
        div.MsoHeader {
            margin: 0in;
            margin-bottom: .0001pt;
            mso-pagination: widow-orphan;
            tab-stops: center 3.0in right 6.0in;
            font-size: 9.0pt;
        }

        .exam-content {
            margin-left: 0.75cm;
            margin-right: 0.75cm;
        }

        .wartermark-wrapper {
            position: relative;
            width: 1px;
            height: 1px;
            overflow: hidden;
        }

        .wartermark-wrapper .water-content {
            position: absolute;
        }
    </style> --}}
{{-- <!--[if gte mso 9]>
        <xml>
        <w:WordDocument>
        <w:View>Print</w:View>
        <w:Zoom>90</w:Zoom>
        <w:DoNotOptimizeForBrowser/>
        </w:WordDocument>
        </xml>
        <![endif]--> --}}
</head>

<body lang="vi" text="#000000" link="#000080" vlink="#800000" dir="ltr">
    <div class="Section1">
        <div>{!! $exam->header !!}</div>

        @if ($t = count($questionListMap))
            @foreach ($questionListMap as $questionMap)
                @php
                    $mapType = $questionMap['type'] ?? '';

                    $slug = 'aby';
                @endphp
                @if ($mapType == 'subject-topic')
                    @if ($questionMap['subject'] ?? [])
                        @php
                            $slug = $questionMap['subject']->slug;
                            $points = to_number($questionMap['points'] ?? 0);
                            $title = $questionMap['title'] ?? null;
                        @endphp
                        @if ($title)
                            <div class="subject-header">
                                {!! $title !!}
                            </div>
                        @endif
                    @endif
                    @if ($questionMap['topics'] && count($questionMap['topics']))
                        @foreach ($questionMap['topics'] as $topicData)
                            @if ($topicData['questions'] && count($topicData['questions']))
                                @foreach ($topicData['questions'] as $question)
                                    @php
                                        $question_count++;
                                        $content = $parseQuestionContent($question->content, $question->type, $question_count, $slug);
                                    @endphp
                                    @if ($question->type != 'group')
                                        <div id="Section{{ $question_count }}" style="vertical-align: middle; text-align: left; line-height: 155%;">{!! $content !!}</div>

                                        @php
                                            $answerIndex = 0;
                                            $lastItemIndex = count($question->answers) - 1;

                                        @endphp
                                        <table border="0" style="border: none; width:100%; margin-bottom: 30px" width="100%">
                                            @foreach ($question->answers as $answer)
                                                @if ($answerIndex == 0 || $answerIndex % 2 == 0)
                                                    <tr>
                                                @endif
                                                @php
                                                    $answerContent = $parseAnswerContent($answer->content, $alphabet[$answerIndex]);
                                                @endphp
                                                <td border="0" width="50%" style="width: 50%; border: none; vertical-align: top; padding-right: 15px" align="top">
                                                    <p class="western" style="line-height: 155%; vertical-align: middle;">

                                                        {!! $answerContent !!}
                                                    </p>
                                                </td>
                                                @if ($answerIndex == $lastItemIndex && $answerIndex % 2 == 0)
                                                    <td></td>
                                                @endif
                                                @if ($answerIndex == $lastItemIndex || $answerIndex % 2 == 1)
                                                    </tr>
                                                @endif
                                                @php
                                                    $answerIndex++;
                                                @endphp
                                            @endforeach
                                        </table>
                                        <br>
                                        <br>
                                    @else
                                        <div id="Section{{ $question_count }}-0" style="vertical-align: middle; text-align: left; line-height: 155%;">
                                            <h4>
                                                @if ($slug == 'TA')
                                                    Choose the option A, B, C or D to answer the following questions based on the information provided in the text. Then write the correct answer on the answer sheet.
                                                @else
                                                    Đọc thông tin sau và trả lời các câu hỏi
                                                @endif
                                            </h4>
                                        </div>
                                        <div id="Section{{ $question_count }}-1" style="vertical-align: middle; text-align: left; line-height: 155%;">{!! $content !!}</div>
                                        @php
                                            $question_count--;
                                        @endphp
                                        @if ($question->children && count($question->children))
                                            @foreach ($question->children as $child)
                                                @php
                                                    $question_count++;
                                                    $content = $parseQuestionContent($child->content, $child->type, $question_count, $slug);
                                                @endphp
                                                <div id="Section{{ $question_count }}" style="vertical-align: middle; text-align: left; line-height: 155%;">{!! $content !!}</div>
                                                @php
                                                    $answerIndex = 0;
                                                    $lastItemIndex = count($question->answers) - 1;

                                                @endphp
                                                <table border="0" style="border: none; width:100%; margin-bottom: 30px" width="100%">
                                                    @foreach ($child->answers as $answer)
                                                        @if ($answerIndex == 0 || $answerIndex % 2 == 0)
                                                            <tr>
                                                        @endif
                                                        @php
                                                            $answerContent = $parseAnswerContent($answer->content, $alphabet[$answerIndex]);
                                                        @endphp
                                                        <td border="0" width="50%" style="width: 50%; border: none; vertical-align: top; padding-right: 15px" align="top">
                                                            <p class="western" style="line-height: 155%; vertical-align: middle;">

                                                                {!! $answerContent !!}
                                                            </p>
                                                        </td>
                                                        @if ($answerIndex == $lastItemIndex && $answerIndex % 2 == 0)
                                                            <td></td>
                                                        @endif
                                                        @if ($answerIndex == $lastItemIndex || $answerIndex % 2 == 1)
                                                            </tr>
                                                        @endif
                                                        @php
                                                            $answerIndex++;
                                                        @endphp
                                                    @endforeach
                                                </table>
                                                <br>
                                                <br>
                                            @endforeach
                                        @endif
                                    @endif
                                @endforeach
                            @endif
                        @endforeach
                    @endif
                @elseif($mapType == 'topic')
                    @if ($questionMap['questions'] && count($tquestionMap['questions']))
                        @foreach ($questionMap['questions'] as $question)
                            {{--  --}}
                            @php
                                $question_count++;
                                // $content = preg_replace('/<div>[\r\n\t\s]*<p>/i', '<p', $content);
                                $content = $parseQuestionContent($question->content, $question->type, $question_count, $slug);
                            @endphp
                            @if ($question->type != 'group')
                                <div id="Section{{ $question_count }}" style="vertical-align: middle; text-align: left; line-height: 155%;">{!! $content !!}</div>

                                @php
                                    $answerIndex = 0;
                                    $lastItemIndex = count($question->answers) - 1;

                                @endphp
                                <table border="0" style="border: none; width:100%; margin-bottom: 30px" width="100%">
                                    @foreach ($question->answers as $answer)
                                        @if ($answerIndex == 0 || $answerIndex % 2 == 0)
                                            <tr>
                                        @endif
                                        @php
                                            $answerContent = $parseAnswerContent($answer->content, $alphabet[$answerIndex]);
                                        @endphp
                                        <td border="0" width="50%" style="width: 50%; border: none; vertical-align: top; padding-right: 15px" align="top">
                                            <p class="western" style="line-height: 155%; vertical-align: middle;">

                                                {!! $answerContent !!}
                                            </p>
                                        </td>
                                        @if ($answerIndex == $lastItemIndex && $answerIndex % 2 == 0)
                                            <td></td>
                                        @endif
                                        @if ($answerIndex == $lastItemIndex || $answerIndex % 2 == 1)
                                            </tr>
                                        @endif
                                        @php
                                            $answerIndex++;
                                        @endphp
                                    @endforeach
                                </table>
                                <br>
                                <br>
                            @else
                                <div id="Section{{ $question_count }}-0" style="vertical-align: middle; text-align: left; line-height: 155%;">
                                    <h4>
                                        @if ($slug == 'TA')
                                            Choose the option A, B, C or D to answer the following questions based on the information provided in the text. Then write the correct answer on the answer sheet.
                                        @else
                                            Đọc thông tin sau và trả lời các câu hỏi
                                        @endif
                                    </h4>
                                </div>
                                <div id="Section{{ $question_count }}-1" style="vertical-align: middle; text-align: left; line-height: 155%;">{!! $content !!}</div>
                                @php
                                    $question_count--;
                                @endphp
                                @if ($question->children && count($question->children))
                                    @foreach ($question->children as $child)
                                        @php
                                            $question_count++;
                                            $content = $parseQuestionContent($child->content, $child->type, $question_count, $slug);
                                        @endphp
                                        <div id="Section{{ $question_count }}" style="vertical-align: middle; text-align: left; line-height: 155%;">{!! $content !!}</div>
                                        @php
                                            $answerIndex = 0;
                                            $lastItemIndex = count($question->answers) - 1;

                                        @endphp
                                        <table border="0" style="border: none; width:100%; margin-bottom: 30px" width="100%">
                                            @foreach ($child->answers as $answer)
                                                @if ($answerIndex == 0 || $answerIndex % 2 == 0)
                                                    <tr>
                                                @endif
                                                @php
                                                    $answerContent = $parseAnswerContent($answer->content, $alphabet[$answerIndex]);
                                                @endphp
                                                <td border="0" width="50%" style="width: 50%; border: none; vertical-align: top; padding-right: 15px" align="top">
                                                    <p class="western" style="line-height: 155%; vertical-align: middle;">

                                                        {!! $answerContent !!}
                                                    </p>
                                                </td>
                                                @if ($answerIndex == $lastItemIndex && $answerIndex % 2 == 0)
                                                    <td></td>
                                                @endif
                                                @if ($answerIndex == $lastItemIndex || $answerIndex % 2 == 1)
                                                    </tr>
                                                @endif
                                                @php
                                                    $answerIndex++;
                                                @endphp
                                            @endforeach
                                        </table>
                                        <br>
                                        <br>
                                    @endforeach
                                @endif
                            @endif
                        @endforeach
                    @endif
                @endif
            @endforeach
        @endif
        <div class="footer">
            {!! $exam->footer !!}
        </div>

    </div>
{{--
    <div class="wartermark-wrapper">
        <div class="water-content">
            <font color="#fff" style="color: #fff">
                <div style='mso-element:header' id=h1>
                    <p class=MsoHeader>HEADER</p>

                </div>
                <div style='mso-element:footer' id=f1>
                    <div style='position:relative;z-index:-1'>
                        <p class=MsoFooter>
                            <table width="100%" style="width: 100%">
                                <tr>
                                    <td>{{ $exam->name }} - Mã đề {{ '{' . '{MADE}' . '}' }}</td>
                                    <td style="text-align: right">
                                        Trang <span style='mso-field-code: PAGE'><span style='mso-no-proof:yes'></span> from <span style='mso-field-code: NUMPAGES'></span></span>
                                    </td>
                                </tr>
                            </table>


                        </p>

                    </div>

                </div>
            </font>
        </div>
    </div> --}}

</body>

</html>
