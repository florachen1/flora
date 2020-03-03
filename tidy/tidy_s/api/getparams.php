<?php
    require_once("../../vendor/autoload.php");
    use \Firebase\JWT\JWT;
    use \Curl\Curl;

    function getparams($data)
    {
        global $_TIDYS_LOG;
        
        require_once("../../log/log_config.php");
        require_once("../../../../ali88_api/module/outputlog.php");

        // 插入log計算時間
        // if($_TIDYS_LOG['tidys_debug'] == TRUE)  outputlog(basename(__FILE__, '.php')."_start jsondata: ".$jsondata, $_TIDYS_LOG['dir'].getlogdate().$_TIDYS_LOG['tidys_log']);
        
        $secretKey = "fe63b0b8dcb16e71beb88dc923a9a381";
        // $token = $data['token'];

        try
        {
            $result = JWT::decode($data, $secretKey, array('HS256'));
        }
        catch(Exception $e)
        {
            $result = -1;    
        }

        // 插入log計算時間
        if($_TIDYS_LOG['tidys_debug'] == TRUE)  outputlog(basename(__FILE__, '.php')."_end result: ".$result, $_TIDYS_LOG['dir'].getlogdate().$_TIDYS_LOG['tidys_log']);
        
        return $result;
    }
?>