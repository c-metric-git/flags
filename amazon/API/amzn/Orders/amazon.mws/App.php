<?php

class App {

    private static $_debug;
    private static $_console;
    private static $_filename;

    public static function init($_debug = false) {
        chdir(dirname(__FILE__));
        self::$_debug = $_debug;
        chdir('..');
        self::$_filename = 'amazon_orders_cron.log';
        return true;
    }

    public static function mailOnErorrs($from, $to, $subject) {

        if (!is_array($to)) {
            $to = array($to);
        }

        $attachments = array();
        $attachments[] = array('path' => self::$_filename, 'name' => self::$_filename);

        $content = @file_get_contents(self::$_filename);
        if (stripos($content, 'error:') !== false || stripos($content, 'exception') !== false) {
            foreach ($to as $t) {
                self::_sendMail($from, $t, $subject, $content, false, false, $attachments);
            }
            @fclose(self::$_console);
            self::$_console = false;
            rename(self::$_filename, self::$_filename . '-' . (string)time());
        }
    }

    private static function _sendMail($from, $to, $subject, $mailContent, $bcc = false, $replyTo = false, $attachments = false) {
        if (empty($to)) {
            throw new Exception('No mail to');
        }
        if (empty($mailContent)) {
            throw new Exception('No mail content');
        }
        $headers = 'MIME-Version: 1.0' . PHP_EOL;
        $headers .= "From: " . $from . PHP_EOL;
        if ($replyTo) {
            $headers .= "Reply-To: " . $replyTo . PHP_EOL;
        }
        if ($bcc) {
            $headers .= "Bcc: " . $bcc . PHP_EOL;
        }
        $headers .= 'X-Mailer: PHP/' . phpversion() . PHP_EOL;
        if (!$attachments) {
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . PHP_EOL;
        } else {
            $random_hash = md5(date('r', time()));

            $headers .= "Content-Type: multipart/mixed; boundary=\"PHP-mixed-" . $random_hash . "\"";

            $mailContent = "
--PHP-mixed-$random_hash
Content-type: text/html; charset=iso-8859-1'

$mailContent";

            foreach ($attachments as $attachment) {
                $attachmentContent = chunk_split(base64_encode(file_get_contents($attachment['path'])));
                $attachmentName = $attachment['name'];

                $mailContent .= "
--PHP-mixed-$random_hash
Content-Type: text/plain; name=$attachmentName
Content-Transfer-Encoding: base64
Content-Disposition: attachment

$attachmentContent";
            }

            $mailContent .= "
--PHP-mixed-$random_hash--";
        }

        /*if (mail($to, $subject, $mailContent, $headers) === false) {
            throw new Exception('Unable to send mail to ' . $to);
        }*/
    }

    /**
     *
      public static function for_each($collection, $qualifiedName, $qualifiedName2 = null)
      {
      if ($collection !== false) {
      foreach($collection as $entry) {
      if ($qualifiedName2) {
      $qualifiedName::$qualifiedName2($entry);
      } else {
      $qualifiedName($entry);
      }
      }
      }
      }
     */
    public static function toArray($value, $create = false) {
        if ($create) {
            return is_array($value) ? $value : array();
        } else {
            return is_array($value) ? $value : array($value);
        }
    }

    public static function toMap($array, $key) {
        $map = array();
        if ($array) {
            foreach ($array as $el) {
                if (isset($el->$key)) {
                    $map[$el->$key] = $el;
                }
            }
        }
        return $map;
    }

    public static function toMapArray($array, $key) {
        $map = array();
        if ($array) {
            foreach ($array as $el) {
                if (is_object($el)) {
                    if (isset($el->$key)) {
                        $map[$el->$key][] = $el;
                    }
                } else if (is_array($el)) {
                    if (isset($el[$key])) {
                        $map[$el[$key]][] = $el;
                    }
                }
            }
        }
        return $map;
    }

    public static function toArrayFromObjectMembers($obj, $member) {
        $arr = array();
        foreach ($obj as $el) {
            $arr[] = $el->$member;
        }
        return $arr;
    }

    public static function objPosByMember($haystack, $needle, $member) {
        for ($i = 0; $i < count($haystack); $i++) {
            if ($haystack[$i]->$member == $needle) {
                return $i;
            }
        }
        return false;
    }

    public static function addUnique(& $array, $element) {
        if (!array_search($element, $array)) {
            $array[] = $element;
        }
        return $array;
    }

    public static function splitArray($arr, $group_size) {
        $groups = array();

        if (!$group_size) {
            return array($arr);
        }

        if (is_array($arr) && count($arr) > 0) {
            $one_group = array();
            for ($i = 0; $i < count($arr); $i++) {
                if ($i > 0 && $i % $group_size == 0) {
                    if ($one_group) {
                        $groups[] = $one_group;
                    }
                    $one_group = array();
                }

                $one_group[] = $arr[$i];
            }

            if ($one_group) {
                $groups[] = $one_group;
            }
        }

        return $groups;
    }

    public static function isEmpty($var) {
        if ($var) {
            if (is_numeric($var)) {
                return false;
            } else if (is_string($var)) {
                return strlen(trim($var)) == 0;
            } else if (is_array($var)) {
                return count($var) == 0;
            }
        }
        return true;
    }

    public static function isNotEmpty($var) {
        return !App::isEmpty($var);
    }

    public static function explode($delimiter, $str, $removeEmptyStrings = false) {
        $array = explode($delimiter, $str);
        if ($removeEmptyStrings) {
            $tmp = array();
            foreach ($array as $el) {
                if (App::isEmpty($el)) {
                    continue;
                }
                $tmp[] = $el;
            }
            $array = $tmp;
        }
        return $array;
    }

    public static function info($m1, $m2 = '', $m3 = '', $m4 = '', $m5 = '', $m6 = '') {
        if (self::$_debug) {
            $msg = "info: " . print_r($m1, true) . print_r($m2, true) . print_r($m3, true) . print_r($m4, true) . print_r($m5, true) . print_r($m6, true);
            self::_write($msg);
        }
    }

    public static function warning($m1, $m2 = '', $m3 = '', $m4 = '', $m5 = '', $m6 = '') {
        if (self::$_debug) {
            $msg = "warning: " . print_r($m1, true) . print_r($m2, true) . print_r($m3, true) . print_r($m4, true) . print_r($m5, true) . print_r($m6, true);
            self::_write($msg);
        }
    }

    public static function error($m1, $m2 = '', $m3 = '', $m4 = '', $m5 = '', $m6 = '') {
        $msg = "error: " . print_r($m1, true) . print_r($m2, true) . print_r($m3, true) . print_r($m4, true) . print_r($m5, true) . print_r($m6, true);
        self::_write($msg);
    }

    public static function ex($ex, $m1, $m2 = '', $m3 = '', $m4 = '', $m5 = '', $m6 = '') {
        $msg = print_r($m1, true) . print_r($m2, true) . print_r($m3, true) . print_r($m4, true) . print_r($m5, true) . print_r($m6, true);
        $msg = "an exception has occured during $msg: " . $ex->getMessage();
        self::_write($msg);
    }

    public static function debug($m1, $m2 = '', $m3 = '', $m4 = '', $m5 = '', $m6 = '', $m7 = '') {
        if (self::$_debug) {
            $msg = "_debug: " . print_r($m1, true) . print_r($m2, true) . print_r($m3, true) . print_r($m4, true) . print_r($m5, true) . print_r($m6, true) . print_r($m7, true);
            self::_write($msg);
        }
    }

    private static function _write($msg) {
        if (!self::$_console) {
            date_default_timezone_set('UTC');
            $uri = self::$_filename;
            self::$_console = fopen($uri, "a");
            $stats = fstat(self::$_console);
            if ($stats) {
                $size = $stats['size'] / 1000000;
                if ($size > 10) {
                    @fclose(self::$_console);
                    self::$_console = @fopen($uri, "w");
                }
            }
        }

        if (self::$_console) {
            $msg = strftime("%Y-%m-%d %H:%M:%S ") . " " . $msg;

            fwrite(self::$_console, print_r($msg, true) . "\n");
            fflush(self::$_console);
        }
    }

}
