<?php
    // 將幣別從字母代碼轉換為數字代碼
    function changecurrency($data)
    {
        global $_TIDYS_LOG;
        
        require_once("../../ali88_api/module/outputlog.php");
        require_once("log/log_config.php");
        
        // 插入log計算時間
        if($_TIDYS_LOG['tidys_debug'] == TRUE)  outputlog(basename(__FILE__, '.php')."_start data: ".$data, $_TIDYS_LOG['dir'].getlogdate().$_TIDYS_LOG['tidys_log']);
    
        switch($data)
        {
            case 'CNY':
                $result = "156";
                break;
            
            default:
                $result = -1;
                break;
        }

        // 插入log計算時間
        if($_TIDYS_LOG['tidys_debug'] == TRUE)  outputlog(basename(__FILE__, '.php')."_end result: ".$result, $_TIDYS_LOG['dir'].getlogdate().$_TIDYS_LOG['tidys_log']);
    
        return $result;
    }
?>