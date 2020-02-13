<?php

class ErrorHandle
{
    public static function Error(
        $error_level,
        $error_message,
        $error_file,
        $error_line,
        $error_context
    )
    {
        if ($error_level == E_NOTICE) {
            return;
        }
        $c = date("YmdHis") . rand(1111, 9999);
        $error = [
            'n' => $c,
            'l' => $error_level,
            'm' => $error_message,
            'f' => $error_file,
            'p' => $error_line,
            'c' => $error_context,
        ];
        self::RecordError($error);
        if ($error_level != E_WARNING) {
            self::DisplayError($error_message, $c);
        }
        if ($error_level > E_NOTICE) {
            exit;
        }
    }

    public static function RecordError($error)
    {
        $f = fopen(ROOT . 'error.log', "a");
        fputs($f, json_encode($error));
        fputs($f, PHP_EOL);
        fclose($f);
    }

    public static function DisplayError($msg, $i)
    {
        Output::Display(false, '出问题了! 错误编号: ' . $i . ' <br>' . $msg);
    }

}

//开发的时候取消注释即可查看错误
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
//set_error_handler("ErrorHandle::Error");
