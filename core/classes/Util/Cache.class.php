<?php

class Util_Cache
{
    static function get($cache_name, $cache_time = 3600)
    {
        static $result = array();
        if (!empty ($result [$cache_name])) {
            return $result [$cache_name];
        }

        $cache_file_path = ROOT_PATH . '/temp/all_caches/' . $cache_name . '.php';

        if (file_exists($cache_file_path)) {
            if ($cache_time != -1) {
                $compilefilestat = @stat($cache_file_path);
                $compilefiletime = $compilefilestat ['mtime'];
                if (time() < $cache_time + $compilefiletime) {
                    include_once($cache_file_path);
                    $result [$cache_name] = $data;
                    return $result [$cache_name];
                }
                return false;
            } else {
                //case -1 always use cache
                include_once($cache_file_path);
                $result [$cache_name] = $data;
                return $result [$cache_name];
            }
        } else {
            return false;
        }
    }

    static function get_raw($cache_name, $cache_time = 3600)
    {
        static $result = array();
        if (!empty ($result [$cache_name])) {
            return $result [$cache_name];
        }

        $cache_file_path = ROOT_PATH . '/temp/all_caches/' . $cache_name . '.php';

        if (file_exists($cache_file_path)) {
            if ($cache_time != -1) {
                $compilefilestat = @stat($cache_file_path);
                $compilefiletime = $compilefilestat ['mtime'];
                if (time() < $cache_time + $compilefiletime) {
                    return file_get_contents($cache_file_path);
                }
                return false;
            } else {
                //case -1 always use cache
                return file_get_contents($cache_file_path);
            }
        } else {
            return false;
        }
    }

    static function set_raw($cache_name, $caches, $desc = "")
    {
        $cache_file_path = ROOT_PATH . '/temp/all_caches/' . $cache_name . '.php';
        file_put_contents($cache_file_path, $caches, LOCK_EX);
    }

    static function set($cache_name, $caches, $desc = "")
    {
        $cache_file_path = ROOT_PATH . '/temp/all_caches/' . $cache_name . '.php';
        $content = "<?php\r\n/*$desc*/\r\n";
        $content .= "\$data = " . var_export($caches, true) . ";\r\n";
        $content .= "?>";
        file_put_contents($cache_file_path, $content, LOCK_EX);
    }
}


