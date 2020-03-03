<?php
    // require_once("db/db_config.php");
    // require_once("../ali88_api/db/dbclass.php");
	// require_once("../ali88_api/module/outputlog.php");
    // require_once("log/log_config.php");
    // require_once("codelist.php");
    // require_once("config.php");
    // require_once("../ali88_api/module/async.php");
    // require_once("setbackend.php");

    require_once("db/db_config.php");
    require_once("../../../ali88_api/db/dbclass.php");
	require_once("../../../ali88_api/module/outputlog.php");
    require_once("../log/log_config.php");
    require_once("codelist.php");
    require_once("config.php");
    require_once("../../../ali88_api/module/async.php");
    require_once("setbackend.php");

    //  取出參數
    $json_data    = $_GET["jsondata"];
    $post_params  = json_decode($json_data,JSON_UNESCAPED_UNICODE);
    
    // 插入log計算時間
    if($_SAS_LOG['sas_debug'] == TRUE)  outputlog(basename(__FILE__, '.php')."_start mtcode: ".$post_params['mtcode'], "../".$_SAS_LOG['dir'].getlogdate().$_SAS_LOG['sas_log']);

    $data['errorCode'] = ResultCode::SUCCESS;
    
    // 判斷要使用哪個後台
    $backend = setbackend($json_data);

    if(!empty($backend))
    {
        // DB 取紀錄
        $db = new DB_CLASS();
        $db->connect_db($_AGENT_DB['host'], $_AGENT_DB['username'], $_AGENT_DB['password'], $backend, $_AGENT_DB['port']);
        
        // 依照遊戲商不同設定做調整
        switch($post_params['gamer_name'])
        {
            case 'CG':
                $result_mtcode = explode("-", $post_params['mtcode']);
                $refundmtcode = str_replace($result_mtcode[1], "refund", $post_params['mtcode']);
            break;

            case 'SA':
                $refundmtcode = $post_params['txnid'];
                $result_mtcode[1] = "bet";
            break;
        }
        
        $sql_str    =   'SELECT `jsondata` FROM `transaction_log` WHERE `unique_key` = "'.$refundmtcode.'" ';
        $result_query =   $db->query($sql_str);
        $result_array   =   $db->get_result_to_array($result_query);
        if(count($result_array) > 0)
        {
            $data['errorCode'] = ResultCode::REFUND; // 已經被refund
            // error log
            if($_SAS_LOG['sas_debug'] == TRUE)  outputlog(basename(__FILE__, '.php')."_error : 已經被refund [refundmtcode: ".$refundmtcode.", 遊戲商: ".$post_params['gamer_name']."]", "../".$_SAS_LOG['dir'].getlogdate().$_SAS_LOG['sas_error_log']);
        }    
        else
        {
            $sql_str    =   'SELECT `jsondata` FROM `transaction_log` WHERE `unique_key` = "'.$post_params['mtcode'].'" ';
            $result_query =   $db->query($sql_str);
            $result_array   =   $db->get_result_to_array($result_query);
            if (count($result_array) > 0)
            {
                $result = json_decode($result_array[0]['jsondata'],JSON_UNESCAPED_UNICODE);
                $sql_str    =   'SELECT `money` FROM `account` WHERE `account` = "'.$result['accountId'].'" AND `agent` = "'.$post_params['agent'].'" ';
                $result_query =   $db->query($sql_str);
                $result_array   =   $db->get_result_to_array($result_query);
                if (count($result_array) > 0)
                {
                    $floatmoney = floatval($result_array[0]['money']);
                    $money = intval($floatmoney * $_AGENT['money_thousands']);
                    $floatamount = floatval($result['amount']);
                    $result['amount'] = intval($floatamount * $_AGENT['money_thousands']); // 為了不要有小數點
                    if($result_mtcode[1] == ("endround" || "bet"))    
                    {
                        $result_mtcode[1] == "bet" ? $var = 1 : $var = -1;
                        $result['amount'] = $result['amount'] * $var;
                    }
                    $aftermoney = ($money + $result['amount'])/ $_AGENT['money_thousands'];
                    $after_money = strval($aftermoney);
                    $sql_str = "UPDATE account SET money = ".$after_money." WHERE account = '".$result['accountId']."' AND agent = '".$post_params['agent']."' ";
                    $result_query = $db->query($sql_str);
                    
                    $url = $_AGENT['writelog_url'];
                    $port = 80;
                    $timeout = 3; 
                    $param['backend']    = $backend;
                    $param['gamer_name'] = $post_params['gamer_name'];
                    $param['table']      = "transaction_log";
                    $param['mtcode']     = $refundmtcode;
                    $param['json_data']  = "'".$post_params['mtcode']."'";
                    async($url, $port, $timeout, $param);

                    // $sql_str = 'INSERT INTO `transaction_log` (`gamer_name`,`unique_key`,`jsondata`) VALUES ("'.$post_params['gamer_name'].'","'.$refundmtcode.'","'.$post_params['mtcode'].'")';
                    // $result_query = $db->query($sql_str);
                    // $insert_id = $db->get_insert_id();
                    // if ($insert_id == 0) 
                    // {
                    //     $data['errorCode'] = ResultCode::INSERT_DB_ERROR; //  insert db 錯誤
                    //     // error log
                    //     if($_AGENT_LOG['agent_debug'] == TRUE)  outputlog(basename(__FILE__, '.php')."_error : insert db 錯誤 [refundmtcode: ".$refundmtcode.", 遊戲商: ".$post_params['gamer_name'].", mtcode: ".$post_params['mtcode']."]", $_AGENT_LOG['dir'].getlogdate().$_AGENT_LOG['agent_error_log']);
                    // }   
                }

            }
            else
            {
                $data['errorCode'] = ResultCode::NOTFOUND_RECORD; // 找不到紀錄
                // error log
                if($_SAS_LOG['sas_debug'] == TRUE)  outputlog(basename(__FILE__, '.php')."_error : 找不到紀錄 [mtcode: ".$post_params['mtcode'].", 遊戲商: ".$post_params['gamer_name']."]", "../".$_SAS_LOG['dir'].getlogdate().$_SAS_LOG['sas_error_log']);
            }    
        }
        $db->close_connect();
    }
    else
    {
        $data['errorCode'] = ResultCode::NOTFOUND_BACKEND; //  找不到後台
        // error log
        if($_AGENT_LOG['agent_debug'] == TRUE)  outputlog(basename(__FILE__, '.php')."_error : backend 找不到 [jsondata: ".$json_data."]", $_AGENT_LOG['dir'].getlogdate().$_AGENT_LOG['agent_error_log']);
    }

    $data['accountId']  =   empty($result['accountId']) ? "" : $result['accountId'];
    $data['balance']    =   empty($after_money) ? "0" : $after_money;
    $data['currency']   =   empty($result['currency']) ? "" : $result['currency'];
    $data['returnTime'] =   date('c');

    $respond_data = json_encode($data,JSON_UNESCAPED_UNICODE);

    // 插入log計算時間
    if($_SAS_LOG['sas_debug'] == TRUE)  outputlog(basename(__FILE__, '.php')."_end mtcode: ".$post_params['mtcode'], "../".$_SAS_LOG['dir'].getlogdate().$_SAS_LOG['sas_log']);

    echo $respond_data;

?>