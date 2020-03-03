<?php
    require_once("../../../../../../db/dbclass.php");
    require_once("../../db/db_config.php");
    require_once("../../log/log_config.php");
    require_once("../../../../../../module/outputlog.php");
    require_once("../config.php");

    if( isset($_POST['table']) )            $post_params["table"]            = $_POST["table"];
    if( isset($_POST['agent']) )            $post_params["agent"]            = $_POST["agent"];
    if( isset($_POST['transaction_uuid']) ) $post_params["transaction_uuid"] = $_POST["transaction_uuid"];
    if( isset($_POST['gameid']) )           $post_params["gameid"]           = $_POST["gameid"];
    if( isset($_POST['timestamp']) )        $post_params["timestamp"]        = $_POST["timestamp"];
    if( isset($_POST['amount_str']) )       $post_params["amount_str"]       = $_POST["amount_str"];
    if( isset($_POST['json_data']) )        $post_params["json_data"]        = $_POST["json_data"];
    $jsondata = json_encode($post_params,JSON_UNESCAPED_UNICODE);

    $db = new DB_CLASS();
    $db->connect_db($_SAS_DB['host'], $_SAS_DB['username'], $_SAS_DB['password'], $_SAS_DB['dbname'], $_SAS_DB['port']);
    
    if($post_params['table'] == "tra_log_change")
    {
        $sql_str = 'SELECT `json_data` FROM `tra_log_res` WHERE JSON_UNQUOTE(JSON_EXTRACT(`json_data`, "$.gameid")) = "'.$post_params["gameid"].'" ';
        $result_query = $db->query($sql_str);
        $result_array = $db->get_result_to_array($result_query);
        if (count($result_array) > 0)
            $betdata = json_decode($result_array[0]['json_data'],JSON_UNESCAPED_UNICODE);
        else 
        {
            $betdata = null;
            // error log
            if($_SAS_LOG['sas_debug'] == TRUE)  outputlog(basename(__FILE__, '.php')."_error : 找不到betdata [jsondata: ".$jsondata."]", "../".$_SAS_LOG['dir'].getlogdate().$_SAS_LOG['sas_error_log']);
        }  
        
        // 組合整合後的資料
        $changedata['SerialNumber']      = $post_params['txnid'];
        $changedata['GameType']          = $betdata['gametype'];
        $changedata['LogTime']           = $post_params['timestamp'];
        $changedata['BetMoney']          = $betdata['amount'];
        $changedata['Win']               = $post_params['amount_str'];
        $changedata['ThirdPartyAccount'] = $betdata["username"];
        $changedata['currency']          = $betdata['currency'];
        $changejson = json_encode(json_encode($changedata,JSON_UNESCAPED_UNICODE));
        $unixtime = strtotime($post_params['timestamp']);
        $sql_str = 'INSERT INTO `tra_log_change` (`agent_name`,`unique_key`,`json_data`,`unixtime`) VALUES ("'.$post_params["agent"].'","'.$post_params["gameid"].'",'.$changejson.',"'.$unixtime.'")';
        $result_query = $db->query($sql_str);
        $insert_id = $db->get_insert_id();
        if ($insert_id == 0) 
        {
            $res_data['errorCode'] = $_SAS_API_SETTING['errorCode']['dbinserterror']; //  db 寫入交易紀錄失敗
            // error log
            if($_TIDYS_LOG['tidys_debug'] == TRUE)  outputlog(basename(__FILE__, '.php')."_error : db insert 失敗 [jsondata: ".$jsondata.", changejson: ".$changejson."]", "../../".$_TIDYS_LOG['dir'].getlogdate().$_TIDYS_LOG['tidys_error_log']);      
        } 
    }
    else
    {
        $sql_str = 'INSERT INTO '.$post_params['table'].' (`agent_name`,`unique_key`,`json_data`) VALUES ("'.$post_params["agent"].'","'.$post_params["transaction_uuid"].'",'.$post_params["json_data"].')';
        $result_query = $db->query($sql_str);
        $insert_id = $db->get_insert_id();
        if ($insert_id == 0)
        {
            $res_data['errorCode'] = $_TIDYS_API_SETTING['errorCode']['dbinserterror']; //  系統錯誤
            // error log
            if($_TIDYS_LOG['tidys_debug'] == TRUE)  outputlog(basename(__FILE__, '.php')."_error : db insert 失敗 [jsondata: ".$jsondata."]", "../../".$_TIDYS_LOG['dir'].getlogdate().$_TIDYS_LOG['tidys_error_log']);      
        }
    }
    $db->close_connect();
?>