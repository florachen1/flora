<?php
    function setbackend($jsondata)
    {
        global $_AGENT_DB;

        require_once("db/db_config.php");

        $post_params = json_decode($jsondata,JSON_UNESCAPED_UNICODE);
        $result = null;
        // 判斷要使用哪個後台
        if(!empty($post_params['backend']))
        {
            switch($post_params['backend'])
            {
                case 'ali88':
                    $result = $_AGENT_DB['ali88_backend'];
                break; 
                case '918kiss':
                    $result = $_AGENT_DB['mango_backend'];
                break; 
                default:
                    $result = null;
                break;
            }
        }
        
        return $result;
    }
?>