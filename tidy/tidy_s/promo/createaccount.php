<?php
    function createaccount($jsondata)
    {
        global $_KERNEL_LOG;
        global $_PROMO_DB;

        
        $data = json_decode($jsondata, JSON_UNESCAPED_UNICODE);
        $account    = $data['account'];
        $agent_name = $data['agent_name'];
        $currency   = $data['currency'];
        $gamer      = $data['gamer'];

        switch($gamer)
        {
            case 'CG':
                require_once("../../../../kernel/promo/db/db_config.php");
                require_once("../../../../../db/dbclass.php");
                require_once("../../../../../module/outputlog.php");
                require_once('../../../../../module/header_ajax.php');
                require_once("../../../../kernel/log/log_config.php");
                require_once("../../../../kernel/promo/createaccount.php");
                $path = "../../../../kernel/";
            break;

            case 'SA':
                require_once("../../../kernel/promo/db/db_config.php");
                require_once("../../../../db/dbclass.php");
                require_once("../../../../module/outputlog.php");
                require_once('../../../../module/header_ajax.php');
                require_once("../../../kernel/log/log_config.php");
                require_once("../../../kernel/promo/createaccount.php");
                $path = "../../../kernel/";
            break;

            case 'TIDY':
                // require_once("../../../kernel/promo/db/db_config.php");
                // require_once("../../../../db/dbclass.php");
                // require_once("../../../../module/outputlog.php");
                // require_once('../../../../module/header_ajax.php');
                // require_once("../../../kernel/log/log_config.php");
                // require_once("../../../kernel/promo/createaccount.php");
                // $path = "../../../kernel/";
                
                require_once("promo/db/db_config.php");
                require_once("../../ali88_api/db/dbclass.php");
                require_once("../../ali88_api/module/outputlog.php");
                // require_once('../../../../module/header_ajax.php');
                require_once("log/log_config.php");
                // require_once("../../../kernel/promo/createaccount.php");
                $path = "";
            break;
        }
        
        // 插入log計算時間
        if($_KERNEL_LOG['kernel_debug'] == TRUE)  outputlog(basename(__FILE__, '.php')."_start jsondata: ".$jsondata, $path.$_KERNEL_LOG['dir'].getlogdate().$_KERNEL_LOG['promo_log']);

        $accountid = $agent_name."_".$account;
        $result = 0;
        $db = new DB_CLASS();
        $db->connect_db($_PROMO_DB['host'], $_PROMO_DB['username'], $_PROMO_DB['password'], $_PROMO_DB['dbname'], $_PROMO_DB['port']);
        $sql_str    =   'SELECT `accountid` FROM `account` WHERE `accountid` = "'.$accountid.'" AND `currency` = "'.$currency.'" AND `gamer` = "'.$gamer.'" ';
        $result_query =   $db->query($sql_str);
        $result_array   =   $db->get_result_to_array($result_query);
        if (count($result_array) == 0)
        {
            $sql_str = 'INSERT INTO `account` (`agent`,`accountid`,`currency`,`gamer`) VALUES ("'.$agent_name.'","'.$accountid.'","'.$currency.'","'.$gamer.'")';
            $result_query = $db->query($sql_str);
            $insert_id = $db->get_insert_id();
            if ($insert_id == 0)
            {
                $result = 1;
                // error log
                if($_KERNEL_LOG['kernel_debug'] == TRUE)  outputlog(basename(__FILE__, '.php')."_error : db insert 失敗 [agent_name: ".$agent_name.", account: ".$accountid."]", $path.$_KERNEL_LOG['dir'].getlogdate().$_KERNEL_LOG['promo_error_log']);      
            }  
        }    
        $db->close_connect();  
        
        // 插入log計算時間
        if($_KERNEL_LOG['kernel_debug']== TRUE)  outputlog(basename(__FILE__, '.php')."_end agent_name: ".$agent_name.", account: ".$account.", result: ".$result, $path.$_KERNEL_LOG['dir'].getlogdate().$_KERNEL_LOG['promo_log']);
    
        return $result;
    }
?>