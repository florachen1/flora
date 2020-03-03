<?php
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
    $postparams  = json_decode($json_data,JSON_UNESCAPED_UNICODE);

    // 插入log計算時間
    if($_SAS_LOG['sas_debug'] == TRUE)  outputlog(basename(__FILE__, '.php')."_start jsondata: ".$json_data.", json_data: ".$json_data, "../".$_SAS_LOG['dir'].getlogdate().$_SAS_LOG['sas_log']);
    
    // record的createtime
    $post_params['createtime'] = date('c');
    
    $gamer_name                = $postparams["gamer_name"];
    $post_params['wtoken']     = $postparams["wtoken"];
    $post_params['channelId']  = $postparams["channelId"];
    $post_params['accountId']  = $postparams["accountId"];
    $post_params['gameType']   = $postparams["gameType"];
    $post_params['roundId']    = $postparams["roundId"];
    $post_params['amount']     = floatval($postparams["amount"]);
    $post_params['currency']   = $postparams["currency"];
    $post_params['mtcode']     = $postparams["mtcode"];
    $post_params['eventTime']  = $postparams["eventTime"];
    $post_params['agent']      = $postparams["agent"];

    $data['currency']     = $post_params['currency'];
    $data['errorCode']    = ResultCode::SUCCESS;
    // $data['amount']       = "5000";
    // $data['before_money'] = "10000";
    // $data['after_money']  = "5000";

    
    $post_params['amount'] = $post_params['amount'] * $_AGENT['money_thousands']; // 為了不要有小數點

    // 判斷要使用哪個後台
    $backend = setbackend($json_data);

    if(!empty($backend))
    {
        // DB 取帳號money
        $db = new DB_CLASS();
        $db->connect_db($_AGENT_DB['host'], $_AGENT_DB['username'], $_AGENT_DB['password'], $backend, $_AGENT_DB['port']);
        $sql_str    =   'SELECT `money` FROM `account` WHERE `account` = "'.$post_params['accountId'].'" AND `agent` = "'.$post_params['agent'].'" ';
        $result_query =   $db->query($sql_str);
        $result_array   =   $db->get_result_to_array($result_query);
        if (count($result_array) > 0)
        { 
            $floatmoney = floatval($result_array[0]['money']);
            $money = intval($floatmoney * $_AGENT['money_thousands']);
            if($money < 0)
            {
                $data['errorCode'] = ResultCode::NEGATIVE; //  餘額為負值
                // error log
                if($_AGENT_LOG['agent_debug'] == TRUE)  outputlog(basename(__FILE__, '.php')."_error : 餘額為負值 [帳號: ".$post_params['accountId'].", 遊戲商: ".$gamer_name.", json_data: ".$json_data."]", $_AGENT_LOG['dir'].getlogdate().$_AGENT_LOG['agent_error_log']);
            }    
            else
            {
                $data['after_money'] = $money - $post_params['amount'];
                if($data['after_money'] < 0)
                {
                    $data['errorCode'] = ResultCode::INSUFFICIENT; //  餘額不足
                    // error log
                    if($_AGENT_LOG['agent_debug'] == TRUE)  outputlog(basename(__FILE__, '.php')."_error : 餘額不足 [帳號: ".$post_params['accountId'].", 遊戲商: ".$gamer_name.", json_data: ".$json_data."]", $_AGENT_LOG['dir'].getlogdate().$_AGENT_LOG['agent_error_log']);
                }    
                else    
                {   
                    $post_params['amount'] = strval($post_params['amount'] / $_AGENT['money_thousands']);
                    $post_params['before_money'] = strval($money / $_AGENT['money_thousands']);                
                    $post_params['after_money'] = strval($data['after_money'] / $_AGENT['money_thousands']);
                    $sql_str = "UPDATE account SET money = ".$post_params['after_money']." WHERE account = '".$post_params['accountId']."' AND agent = '".$post_params['agent']."' ";
                    $result_query = $db->query($sql_str);
                    $jsondata = json_encode(json_encode($post_params,JSON_UNESCAPED_UNICODE));
                    
                    $url = $_AGENT['writelog_url'];
                    $port = 80;
                    $timeout = 3; 
                    $param['backend']    = $backend;
                    $param['gamer_name'] = $gamer_name;
                    $param['table']      = "transaction_log";
                    $param['mtcode']     = $post_params["mtcode"];
                    $param['json_data']  = $jsondata;
                    async($url, $port, $timeout, $param);
                    
                    // $sql_str = 'INSERT INTO `transaction_log` (`gamer_name`,`unique_key`,`jsondata`) VALUES ("'.$gamer_name.'","'.$post_params['mtcode'].'",'.$jsondata.')';
                    // $result_query = $db->query($sql_str);
                    // $insert_id = $db->get_insert_id();
                    // if ($insert_id == 0)
                    // {
                    //     $data['errorCode'] = ResultCode::INSERT_DB_ERROR; //  insert db 錯誤
                    //     // error log
                    //     if($_AGENT_LOG['agent_debug'] == TRUE)  outputlog(basename(__FILE__, '.php')."_error : insert db 錯誤 [帳號: ".$post_params['accountId'].", 遊戲商: ".$gamer_name.", jsondata: ".$jsondata."]", $_AGENT_LOG['dir'].getlogdate().$_AGENT_LOG['agent_error_log']);
                    // }    
                }
            }
        }
        else 
        {
            $data['errorCode'] = ResultCode::ACCOUNT_NOTFOUND; //  account 找不到   
            // error log
            if($_AGENT_LOG['agent_debug'] == TRUE)  outputlog(basename(__FILE__, '.php')."_error : account 找不到 [帳號: ".$post_params['accountId'].", 遊戲商: ".$gamer_name.", json_data: ".$json_data."]", $_AGENT_LOG['dir'].getlogdate().$_AGENT_LOG['agent_error_log']);
        }   
        $db->close_connect();
    }
    else
    {
        $data['errorCode'] = ResultCode::NOTFOUND_BACKEND; //  找不到後台
        // error log
        if($_AGENT_LOG['agent_debug'] == TRUE)  outputlog(basename(__FILE__, '.php')."_error : backend 找不到 [jsondata: ".$json_data."]", $_AGENT_LOG['dir'].getlogdate().$_AGENT_LOG['agent_error_log']);
    }

    $data['before_money'] = empty($post_params['before_money']) ? "0" : $post_params['before_money'];
    $data['after_money']  = empty($post_params['after_money'])  ? "0" : $post_params['after_money']; // 還原為實際的金額並轉換為字串
    $respond_data = json_encode($data,JSON_UNESCAPED_UNICODE);
    
    // 插入log計算時間
    if($_SAS_LOG['sas_debug'] == TRUE)  outputlog(basename(__FILE__, '.php')."_end respond_data: ".$respond_data.", json_data: ".$json_data, "../".$_SAS_LOG['dir'].getlogdate().$_SAS_LOG['sas_log']);

    echo $respond_data;
?>