@php
    use Gomee\Html\Dom\HtmlDomParser;
    $tidy = new \tidy();
    $question_count = 0;

    $alphabet = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
@endphp
<html xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns:m="http://schemas.microsoft.com/office/2004/12/omml" xmlns="http://www.w3.org/TR/REC-html40">

<head>
    <meta http-equiv=Content-Type content="text/html; charset=utf-8">
    <title></title>
    <style>
        v\:* {
            behavior: url(#default#VML);
        }

        o\:* {
            behavior: url(#default#VML);
        }

        w\:* {
            behavior: url(#default#VML);
        }

        .shape {
            behavior: url(#default#VML);
        }
    </style>
    <style>
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
            opacity: 0;
            display: none;
            color: #fff;
        }

        table#hrdftrtbl * {
            color: #fff;
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
    </style>
    <!--[if gte mso 9]>
        <xml>
        <w:WordDocument>
        <w:View>Print</w:View>
        <w:Zoom>90</w:Zoom>
        <w:DoNotOptimizeForBrowser/>
        </w:WordDocument>
        </xml>
        <![endif]-->
</head>

<body>
    <div class="Section1">

        <div>
            {!! $exam->header !!}
        </div>
        @php
            $question_count = 0;

            $alphabet = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
        @endphp
        <table width="100%" style="width: 100%;">
            <thead>
                <th align="center" style="text-align: center;">STT</th>
                <th>Nội dung câu hỏi</th>
                <th align="center" style="text-align: center; width: 15%">Đáo án</th>
            </thead>
            <tbody>
                @php

                    $parseQuestionContent = function ($content, $type, $slug = 'any') use ($tidy) {
                        $cleanStyle = function ($element, $factory) {
                            if ($element->style) {
                                $element->style = str_replace(['&quot;', '"'], "'", trim(trim($element->style), ';'));
                            }
                            if ($element->style) {
                                if (preg_match('/\;$/i', trim($element->style))) {
                                    $element->style .= 'vertical-align: middle;font-size:12pt;';
                                } else {
                                    $element->style .= ';vertical-align: middle;font-size:12pt;';
                                }
                            } else {
                                $element->style = 'vertical-align: middle;font-size:12pt;';
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
                            // if ($item->style) {
                            //     if (preg_match('/\;$/i', trim($item->style))) {
                            //         $item->style .= 'vertical-align: middle;font-size:12pt;';
                            //     } else {
                            //         $item->style .= ';vertical-align: middle;font-size:12pt;';
                            //     }
                            // } else {
                            //     $item->style = 'vertical-align: middle;font-size:12pt;';
                            // }
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
                            // if ($type != 'group' && !$isset && $canSet) {
                            //     $lastParent = htmlGetLastParent($item);
                            //     if ($parentText = str_slug(trim(strip_tags($lastParent->innertext(), ['img'])))) {
                            //         $lastParent->innertext = "<span size=\"3\" style=\"font-size: 12pt;padding-bottom:3pt;\"><b>" . ($slug == 'TA' ? '' : 'Câu') . " {$question_count}.</b></span> " . $lastParent->innertext;
                            //         $isset = true;
                            //     }
                            // }
                            $cleanTableHasOneTD($cleanTableHasOneTD, $item);
                            $currenthtml = $item->outertext();
                            // preg_match_all('/\<img\s[^>]*src=(\'[^\']+\'|\"[^\"]+\")\s[^>]*\>/i', $currenthtml, $matches);
                            // if ($matches[0]) {
                            //     foreach ($matches[0] as $tag) {
                            //         // $currenthtml = str_replace($tag, '<span align="middle" style="vertical-align: middle;font-size: 12pt">'.$tag.'</span> ', $currenthtml);
                            //     }
                            // }

                            $questionHtml .= $currenthtml;

                            $questionHtml = str_replace('align="justify"', '', $questionHtml);
                            $i++;
                        }
                        // if ($type != 'group' && !$isset) {
                        //     $questionHtml = "<span size=\"3\" style=\"font-size: 12pt;padding-bottom:3pt;\"><b>" . ($slug == 'TA' ? '' : 'Câu') . " {$question_count}.</b></span>" . $questionHtml;
                        // }

                        return $questionHtml;
                    };
                @endphp
                @if ($t = count($questionListMap))
                    @foreach ($questionListMap as $questionMap)
                        @php
                            $mapType = $questionMap['type'] ?? '';
                            $slug = $questionMap['subject']->slug;
                            $points = to_number($questionMap['points'] ?? 0);
                            $title = $questionMap['title'] ?? null;

                        @endphp
                        @if ($mapType == 'subject-topic')
                            @if ($questionMap['topics'] && count($questionMap['topics']))
                                @foreach ($questionMap['topics'] as $topicData)
                                    @if ($topicData['questions'] && count($topicData['questions']))
                                        @foreach ($topicData['questions'] as $question)
                                            @php
                                                $question_count++;
                                                $content = $parseQuestionContent($question->content, $question->type, $slug);
                                            @endphp

                                            @if ($question->type != 'group')
                                                <tr>
                                                    <td align="center" style="text-align: center">{{ $question_count }}</td>
                                                    <td>{!! $content !!}</td>
                                                    <td align="center" style="text-align: center; width: 15%">
                                                        @foreach ($question->answers as $answer)
                                                            @if ($question->answer_uuid == $answer->uuid)
                                                                <font size="3" style="font-size: 12pt"><b>{{ $alphabet[$loop->index] }} </b></font>
                                                            @endif
                                                        @endforeach
                                                    </td>
                                                </tr>
                                            @else
                                                @php
                                                    $question_count--;
                                                @endphp
                                                @if ($question->children && count($question->children))
                                                    @foreach ($question->children as $child)
                                                        @php
                                                            $question_count++;
                                                            $content = $parseQuestionContent($child->content, $question->type, $slug);
                                                        @endphp
                                                        <tr>
                                                            <td align="center" style="text-align: center">{{ $question_count }}</td>
                                                            <td>{!! $content !!}</td>
                                                            <td align="center" style="text-align: center; width: 15%">
                                                                @foreach ($child->answers as $answer)
                                                                    @if ($child->answer_uuid == $answer->uuid)
                                                                        <font size="3" style="font-size: 12pt"><b>{{ $alphabet[$loop->index] }} </b></font>
                                                                    @endif
                                                                @endforeach
                                                            </td>
                                                        </tr>
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
                                    @php
                                        $question_count++;

                                    @endphp

                                    @if ($question->type != 'group')
                                        <tr>
                                            <td align="center" style="text-align: center">{{ $question_count }}</td>
                                            <td>{!! $question->content !!}</td>
                                            <td align="center" style="text-align: center; width: 15%">
                                                @foreach ($question->answers as $answer)
                                                    @if ($question->answer_uuid == $answer->uuid)
                                                        <font size="3" style="font-size: 12pt"><b>{{ $alphabet[$loop->index] }} </b></font>
                                                    @endif
                                                @endforeach
                                            </td>
                                        </tr>
                                    @else
                                        @php
                                            $question_count--;
                                        @endphp
                                        @if ($question->children && count($question->children))
                                            @foreach ($question->children as $child)
                                                @php
                                                    $question_count++;
                                                @endphp
                                                <tr>
                                                    <td align="center" style="text-align: center">{{ $question_count }}</td>
                                                    <td>{!! $child->content !!}</td>
                                                    <td align="center" style="text-align: center; width: 15%">
                                                        @foreach ($child->answers as $answer)
                                                            @if ($child->answer_uuid == $answer->uuid)
                                                                <font size="3" style="font-size: 12pt"><b>{{ $alphabet[$loop->index] }} </b></font>
                                                            @endif
                                                        @endforeach
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    @endif
                                @endforeach
                            @endif
                        @endif
                    @endforeach
                @endif

            </tbody>
        </table>
        <p class="western" align="justify" style="line-height: 115%; margin-bottom: 0in">
            <br />

        </p>
        <div class="footer">
            {!! $exam->footer !!}
        </div>

    </div>


    <div style='mso-element:header' id=h1>
        <font color="#fff"  style="color: #fff">
        {{-- <p class=MsoHeader>{{ $exam->name }}</p> --}}
        </font>
    </div>
    <div style='mso-element:footer' id=f1>
        <span style='position: absolute;z-index:-1'>
            <font color="#fff" style="color: #fff">
            <div class=MsoFooter>
                <table width="100%" style="width: 100%">
                    <tr>
                        <td>{{ $exam->name }} - Mã đề {{ '{' . '{MADE}' . '}' }}</td>
                        <td style="text-align: right">
                            Trang <span style='mso-field-code: PAGE'><span style='mso-no-proof:yes'></span> from <span style='mso-field-code: NUMPAGES'></span></span>
                        </td>
                    </tr>
                </table>


            </div>
        </font>
        </span>
    </div>

</body>

</html>
