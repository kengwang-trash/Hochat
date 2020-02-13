<?php

class Output
{

    public static function Display($status, $msg, $data = [])
    {
        if (count($_POST) == 0) {
            self::RawOutput($status, $msg);
        } else {
            self::JSONOutput($status, $msg, $data);
        }
    }

    public static function RawOutput($status, $msg)
    {
        echo $msg;
    }

    public static function JSONOutput($status, $msg, $data = [])
    {
        header('Content-type: text/json');
        $arr = [
            'status' => $status,
            'msg' => $msg,
            'data' => $data,
        ];
        echo json_encode($arr);
    }

    public static function CutHtml(
        $string,
        $length,
        $postfix = '&hellip;',
        $isHtml = true
    )
    {
        $string = trim($string);
        $postfix = (strlen(strip_tags($string)) > $length) ? $postfix : '';
        $i = 0;
        $tags = []; // change to array() if php version < 5.4

        if ($isHtml) {
            preg_match_all(
                '/<[^>]+>([^<]*)/',
                $string,
                $tagMatches,
                PREG_OFFSET_CAPTURE | PREG_SET_ORDER
            );
            foreach ($tagMatches as $tagMatch) {
                if ($tagMatch[0][1] - $i >= $length) {
                    break;
                }

                $tag = substr(strtok($tagMatch[0][0], " \t\n\r\0\x0B>"), 1);
                if ($tag[0] != '/') {
                    $tags[] = $tag;
                } elseif (end($tags) == substr($tag, 1)) {
                    array_pop($tags);
                }

                $i += $tagMatch[1][1] - $tagMatch[0][1];
            }
        }

        return substr($string, 0, $length = min(strlen($string), $length + $i))
            . (count(
                $tags = array_reverse($tags)
            ) ? '</' . implode('></', $tags) . '>' : '') . $postfix;
    }

}

