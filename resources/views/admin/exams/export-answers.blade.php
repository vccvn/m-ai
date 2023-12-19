@php
    use Gomee\Html\Dom\HtmlDomParser;
    $tidy = new \tidy();
    $question_count = 0;

    $alphabet = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
@endphp
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    {{-- <title>thithuthptqg.vn</title> --}}
    {{-- <meta name="generator" content="LibreOffice 7.3.5.2 (Linux)" /> --}}
    {{-- <meta name="author" content="thithuthptqg.vn" /> --}}
    <meta name="created" content="{{ date('Y-m-d') }}T{{ date('H:i:s') }}" />
    <meta name="changed" content="{{ date('Y-m-d') }}T{{ date('H:i:s') }}" />
    <style type="text/css">
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
</head>

<body lang="vi" text="#000000" link="#000080" vlink="#800000" dir="ltr">

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
                // if ($item->style) {
                //     if (preg_match('/\;$/i', trim($item->style))) {
                //         $item->style .= 'vertical-align: middle;font-size:11pt;';
                //     } else {
                //         $item->style .= ';vertical-align: middle;font-size:11pt;';
                //     }
                // } else {
                //     $item->style = 'vertical-align: middle;font-size:11pt;';
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
                //         $lastParent->innertext = "<span size=\"3\" style=\"font-size: 11pt;padding-bottom:3pt;\"><b>" . ($slug == 'TA' ? '' : 'Câu') . " {$question_count}.</b></span> " . $lastParent->innertext;
                //         $isset = true;
                //     }
                // }
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
            // if ($type != 'group' && !$isset) {
            //     $questionHtml = "<span size=\"3\" style=\"font-size: 11pt;padding-bottom:3pt;\"><b>" . ($slug == 'TA' ? '' : 'Câu') . " {$question_count}.</b></span>" . $questionHtml;
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
                                                            <font size="3" style="font-size: 11pt"><b>{{ $alphabet[$loop->index] }} </b></font>
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
                                                                    <font size="3" style="font-size: 11pt"><b>{{ $alphabet[$loop->index] }} </b></font>
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
                                                    <font size="3" style="font-size: 11pt"><b>{{ $alphabet[$loop->index] }} </b></font>
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
                                                            <font size="3" style="font-size: 11pt"><b>{{ $alphabet[$loop->index] }} </b></font>
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
</body>

</html>
