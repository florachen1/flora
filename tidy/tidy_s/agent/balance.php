<?php
    require_once("db/db_config.php");
    require_once("../../../ali88_api/db/dbclass.php");
	require_once("../../../ali88_api/module/outputlog.php");
    require_once("../log/log_config.php");
    require_once("codelist.php");
    require_once("config.php");
    require_once("setbackend.php");

    //  取出參數
    $json_data    = $_GET["jsondata"];
    $post_params  = json_decode($json_data,JSON_UNESCAPED_UNICODE);
    
    // 插入log計算時間
    if($_SAS_LOG['sas_debug'] == TRUE)  outputlog(basename(__FILE__, '.php')."_start account: ".$post_params['account'].", json_data: ".$json_data, "../".$_SAS_LOG['dir'].getlogdate().$_SAS_LOG['sas_log']);

    $data['errorCode'] = ResultCode::SUCCESS;
    // $data['balance'] = "10000";

    // 判斷要使用哪個後台
    $backend = setbackend($json_data);

    if(!empty($backend))
    {
        // DB 取帳號money
        $db = new DB_CLASS();
        $db->connect_db($_AGENT_DB['host'], $_AGENT_DB['username'], $_AGENT_DB['password'], $backend, $_AGENT_DB['port']);
        $sql_str    =   'SELECT `money` FROM `account` WHERE `account` = "'.$post_params['account'].'" AND `agent` = "'.$post_params['agent'].'" ';
        $result_query =   $db->query($sql_str);
        $result_array   =   $db->get_result_to_array($result_query);
        if (count($result_array) > 0)
        {
            $data['balance'] = $result_array[0]['money'];
        }
        else    
        {
            $data['balance'] = "0";
            $data['errorCode'] = ResultCode::ACCOUNT_NOTFOUND; //  account 找不到    
            // error log
            if($_SAS_LOG['sas_debug'] == TRUE)  outputlog(basename(__FILE__, '.php')."_error : account 找不到 [帳號: ".$post_params['account'].", 遊戲商: ".$post_params['gamer_name'].", json_data: ".$json_data."]", "../".$_SAS_LOG['dir'].getlogdate().$_SAS_LOG['sas_error_log']);
        }
        $db->close_connect();
    }
    else
    {
        $data['balance'] = "0";
        $data['errorCode'] = ResultCode::NOTFOUND_BACKEND; //  找不到後台
        // error log
        if($_SAS_LOG['sas_debug'] == TRUE)  outputlog(basename(__FILE__, '.php')."_error : backend 找不到 [jsondata: ".$json_data."]", "../".$_SAS_LOG['dir'].getlogdate().$_SAS_LOG['sas_error_log']);
    }
        
    $data['returnTime']   =   date('c');

    $respond_data = json_encode($data,JSON_UNESCAPED_UNICODE);
    
    // 插入log計算時間
    if($_SAS_LOG['sas_debug'] == TRUE)  outputlog(basename(__FILE__, '.php')."_end account: ".$post_params['account'].", json_data: ".$json_data, "../".$_SAS_LOG['dir'].getlogdate().$_SAS_LOG['sas_log']);

    echo $respond_data;
?>