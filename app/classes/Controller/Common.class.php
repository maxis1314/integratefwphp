<?php

class Controller_Common
{
    var $smarty;
    var $cache_id = 0;
    var $cache_time = 0;
    var $report_root;
    var $disable_cache = false;
    var $logger;

    function logit($type, $content)
    {
        $data = array(
            'user_id' => $_SESSION['user_id'],
            'type' => $type,
            'content' => $content."\n\n".$this->get_client_info(),
        );
    }

    function show_404($code){
        $this->display('report_noready.dwt',array('code'=>$code));
        exit;
    }

    protected function get_client_info()
    {
        $useragent = $_SERVER['HTTP_USER_AGENT'];
        $cookie = var_export($_COOKIE['cookie'],true);
        $httpreferrer = $_SERVER['HTTP_REFERER'];
        $HttpClientIP = $_SERVER['HTTP_CLIENT_IP'];
        $RemAddr = $_SERVER['REMOTE_ADDR'];
        $CacheControl = $_SERVER['HTTP_CACHE_CONTROL'];
        $XForward = $_SERVER['HTTP_X_FORWARDED_FOR'];
        $querystring = $_SERVER['QUERY_STRING'];

        return " User Agent:$useragent Cookie: $cookie HTTP Referrer: $httpreferrer HTTP Client IP: $HttpClientIP Remote Address: $RemAddr Cache Control: $CacheControl X Forward: $XForward Query String: $querystring";
    }

    public function __construct()
    {
        $this->init();
    }

    function get_assign()
    {
        return $this->smarty->get_assign();
    }

    function check_access()
    {
        if (empty($_SESSION['user_id'])) {
            ecs_header("Location: login.php\n");
            exit;
        }
    }

    function check_simple_login($name, $pass, $message = "please login")
    {
        $passmd5 = md5($pass);
        $inputpass = isset($_POST['gaga123']) ? $_POST['gaga123'] : '';
        if (isset($_COOKIE[$name]) || $inputpass) {
            if (isset($_COOKIE[$name]) && $_COOKIE[$name] == $passmd5) {

            } else if ($inputpass == $pass) {
                setcookie($name, $passmd5, time() + 3600 * 24 * 5, "/");
            } else {
                echo $message;
                echo "<form method=POST><input type=password name='gaga123'><input type=submit></form>";
                exit();
            }
        } else {
            echo $message;
            echo "<form method=POST><input type=password name='gaga123'><input type=submit></form>";
            exit();
        }
    }

    public function set_cache($cache_id, $time)
    {
        if ($this->disable_cache) {
            return;
        }
        $this->cache_id = $cache_id;
        $this->cache_time = $time;
        $this->display_cache($cache_id, $time);
    }

    protected function init()
    {
        
 
    }


    protected function display_cache($cache_id = "", $time = 3600)
    {
        $result = Util_Cache::get($cache_id, $time);
        if ($result) {
            //echo "use cache";
            echo $result;
            echo '<!-- uc -->';
            exit;
        }
    }

    protected function display($tpl, $data = null)
    {
        foreach ($data as $key => $value) {
            $this->smarty->assign($key, $value);
        }
        $result = $this->smarty->get_display($tpl);
        echo $result;
        echo '<!-- sc -->';
        if ($this->cache_id) {
            //echo "set cache";
            Util_Cache::set($this->cache_id, $result);
        }
        if(false){
            $cache_file_path = ROOT_PATH.'/../../homepage/report/webroot/genreport/mini.php';
            $content = '<?php
require(dirname(__FILE__) . "/../../includes/init.php");';
            $content .= "\$data = " . var_export($data, true) . ";\r\n";
            $content .= 'foreach($data as $k=>$v){
	$smarty->assign($k,$v);
}

$smarty->display("'.$tpl.'");

?>';
            file_put_contents($cache_file_path, $content, LOCK_EX);
        }

    }

    protected function output_json($data)
    {
        echo json_encode($data);
        exit;
    }

    function isGet()
    {
        return $_SERVER['REQUEST_METHOD'] == 'GET' ? true : false;
    }

    function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] == 'POST' ? true : false;
    }

    protected function quick_info($message, $detail = "")
    {
        $this->info($message);
        $this->smarty->assign('detail', $detail);
        $this->display('admin_alert.dwt');
    }

    protected function quick_warn($message, $detail = "")
    {
        $this->warn($message);
        $this->smarty->assign('detail', $detail);
        $this->display('admin_alert.dwt');
    }

    protected function quick_error($message, $detail = "")
    {
        $this->error($message);
        $this->smarty->assign('detail', $detail);
        $this->display('admin_alert.dwt');
    }

    protected function error($message)
    {
        $this->smarty->assign('message', $message);
        $this->smarty->assign('message_style', 'ui-state-error');
    }

    protected function warn($message)
    {
        $this->smarty->assign('message', $message);
        $this->smarty->assign('message_style', 'ui-state-highlight');
    }

    protected function info($message)
    {
        $this->smarty->assign('message', $message);
        $this->smarty->assign('message_style', 'ui-priority-primary');
//     .ui-state-highlight：对高亮或者选中元素应用的 Class。对元素及其子元素的文本、链接、图标应用 "highlight" 容器样式。
//    .ui-state-error：对错误消息容器元素应用的 Class。对元素及其子元素的文本、链接、图标应用 "error" 容器样式。
//    .ui-state-error-text：对只有无背景的错误文本颜色应用的 Class。可用于表单标签，也可以对子图标应用错误图标颜色。
//    .ui-state-disabled：对禁用的 UI 元素应用一个暗淡的不透明度。意味着对一个已经定义样式的元素添加额外的样式。
//    .ui-priority-primary：对第一优先权的按钮应用的 Class。应用粗体文本。
//    .ui-priority-secondary：对第二优先权的按钮应用的 Class。应用正常粗细的文本，对元素应用轻微的透明度。
    }

}


