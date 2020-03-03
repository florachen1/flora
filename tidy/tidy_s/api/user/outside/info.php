<?php
    // 查詢用戶是否存在
    function info($jsondata) 
    {
        global $_TIDYS_LOG;
        
        require_once("log/log_config.php");
        require_once("../../ali88_api/module/outputlog.php");
        
        // 插入log計算時間
        if($_TIDYS_LOG['tidys_debug'] == TRUE)  outputlog(basename(__FILE__, '.php')."_start jsondata: ".$jsondata, $_TIDYS_LOG['dir'].getlogdate().$_TIDYS_LOG['tidys_log']);
        
        $data = json_decode($jsondata, JSON_UNESCAPED_UNICODE);

        $resultdata = TidyApi::call('api/user/outside/info', 'GET', array('username' => $data['username']));

        $resultjson = json_encode($resultdata, JSON_UNESCAPED_UNICODE);

        // 插入log計算時間
        if($_TIDYS_LOG['tidys_debug'] == TRUE)  outputlog(basename(__FILE__, '.php')."_end resultjson: ".$resultjson, $_TIDYS_LOG['dir'].getlogdate().$_TIDYS_LOG['tidys_log']);
        
        return $resultdata['check'];
    }
?>