<?php
    // 將幣別從字母代碼轉換為數字代碼
    function changecurrency($jsondata)
    {
        global $_TIDYS_LOG;
        
        $data = json_decode($jsondata,JSON_UNESCAPED_UNICODE);
        
        if($data['action'] == "launch")
        {
            require_once("../../../../module/outputlog.php");
            require_once("log/log_config.php");
            $path = "";
        }
        else
        {
            require_once("../../../../../../module/outputlog.php");
            require_once("../../log/log_config.php");
            $path = "../../";
        }
        
        // 插入log計算時間
        if($_TIDYS_LOG['tidys_debug'] == TRUE)  outputlog(basename(__FILE__, '.php')."_start jsondata: ".$jsondata, $path.$_TIDYS_LOG['dir'].getlogdate().$_TIDYS_LOG['tidys_log']);
    
        switch($data['currency'])
        {
            case 'CNY':
                $result = "156";
                break;

            case '156':
                $result = "CNY";
                break;
            
            default:
                $result = -1;
                break;
        }

        // 插入log計算時間
        if($_TIDYS_LOG['tidys_debug'] == TRUE)  outputlog(basename(__FILE__, '.php')."_end result: ".$result, $path.$_TIDYS_LOG['dir'].getlogdate().$_TIDYS_LOG['tidys_log']);
    
        return $result;
    }
?>