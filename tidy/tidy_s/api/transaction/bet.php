<?php
    // 呼叫這支 API 進行玩家餘額的查詢
    // require_once("../db/db_config.php");
    // require_once("../../../../../db/dbclass.php");
    // require_once("../../../../../module/checkparamsexists.php");
    // // require_once("../../../../../module/getheader.php");
    // // require_once("../../../../../module/snowflake.php");
    // // require_once("../redis/redis_config.php");
    // // require_once("../../../../../redis/redisclass.php");
    // require_once("../../../../kernel/mid_brdg/balance.php");
    // require_once("../../../../../module/sendget.php");
    // require_once("config.php");
    // require_once("../desclass.php");
    // require_once("getparams.php");

    require_once("../../db/db_config.php");
    require_once("../../../../../../db/dbclass.php");
    require_once("../../../../../../module/checkparamsexists.php");
    // require_once("../../../../../module/getheader.php");
    // require_once("../../../../../module/snowflake.php");
    // require_once("../redis/redis_config.php");
    // require_once("../../../../../redis/redisclass.php");
    require_once("../../mid_brdg/balance.php");
    require_once("../../../../../../module/sendget.php");
    require_once("../config.php");
    // require_once("../../desclass.php");
    require_once("../getparams.php");

    if($_TIDYS_LOG['tidys_debug'] == TRUE)  outputlog(basename(__FILE__, '.php')."_start", "../../".$_TIDYS_LOG['dir'].getlogdate().$_TIDYS_LOG['tidys_log']);

    // TIDY 發過來的參數
    if( isset($_POST['token']) )            $post_params["token"]            = $_POST["token"];
    if( isset($_POST['request_uuid']) )     $post_params["request_uuid"]     = $_POST["request_uuid"];
    if( isset($_POST['transaction_uuid']) ) $post_params["transaction_uuid"] = $_POST["transaction_uuid"];
    if( isset($_POST['uid']) )              $post_params["uid"]              = $_POST["uid"];
    if( isset($_POST['bet_id']) )           $post_params["bet_id"]           = $_POST["bet_id"];
    if( isset($_POST['currency']) )         $post_params["currency"]         = $_POST["currency"];
    if( isset($_POST['amount']) )           $post_params["amount"]           = $_POST["amount"];
    if( isset($_POST['game_id']) )          $post_params["game_id"]          = $_POST["game_id"];
    if( isset($_POST['client_id']) )        $post_params["client_id"]        = $_POST["client_id"];
    
    $data = json_encode($post_params,JSON_UNESCAPED_UNICODE);
   
    // 插入log計算時間
    if($_TIDYS_LOG['tidys_debug'] == TRUE)  outputlog(basename(__FILE__, '.php')."_start data: ".$data, "../../".$_TIDYS_LOG['dir'].getlogdate().$_TIDYS_LOG['tidys_log']);
    
    // 透過 function getparams 取得參數
    $post_params = getparams($post_params["token"]);

    $postdata = json_encode($post_params,JSON_UNESCAPED_UNICODE);
    
    // 插入log計算時間
    if($_TIDYS_LOG['tidys_debug'] == TRUE)  outputlog(basename(__FILE__, '.php')." postdata: ".$postdata, "../../".$_TIDYS_LOG['dir'].getlogdate().$_TIDYS_LOG['tidys_log']);

    $agent = "";
    
    // DB寫 request log    
    $reqdata = json_encode(json_encode($post_params,JSON_UNESCAPED_UNICODE));       
    $url = $_TIDYS_API_SETTING['writelog_url'];
    $port = 80;
    $timeout = 3; 
    $param['agent']            = $agent;
    $param['table']            = "tra_log_req";
    $param['transaction_uuid'] = $post_params["transaction_uuid"];
    $param['json_data']        = $reqdata;
    async($url, $port, $timeout, $param);

    $gamer_name = "TIDY";
    $post_params["username"] = $account;
    $amount_str = strval($post_params["amount"]);
    $wallet_type = "seamless";
    
    // 將幣別從字母代碼轉換為數字代碼
    // 組合傳入 function changecurrency 參數
    $changecurrency['action']   = "bet";
    $changecurrency['currency'] = $post_params["currency"];
    $changecurrencydata = json_encode($changecurrency, JSON_UNESCAPED_UNICODE);
    $currency = changecurrency($changecurrencydata);

    // 組合傳給 mid_brdg bet 資料
    $jsondata['gamer_name']  =  $gamer_name;
    $jsondata['wtoken']      =  "";
    $jsondata['channelId']   =  "";
    $jsondata['accountId']   =  $account;
    $jsondata['gameType']    =  $post_params["game_id"];
    $jsondata['roundId']     =  $post_params["bet_id"];
    $jsondata['amount']      =  $amount_str;
    $jsondata['currency']    =  $currency;
    $jsondata['mtcode']      =  $post_params["transaction_uuid"];
    $jsondata['eventTime']   =  "";
    $jsondata['agent']       =  $agent;
    $mid = json_encode($jsondata,JSON_UNESCAPED_UNICODE);
    
    // 向 mid_brdg bet 請求資料
    $res_tidy = mid_bet($mid);

    // 插入log計算時間
    if($_TIDYS_LOG['tidys_debug'] == TRUE)  outputlog(basename(__FILE__, '.php')." res_tidy: ".$res_tidy, "../../".$_TIDYS_LOG['dir'].getlogdate().$_TIDYS_LOG['tidys_log']);
    
    // 組合 mid_brdg bet 回傳的資料
    $res_data = json_decode($res_tidy,JSON_UNESCAPED_UNICODE);
    $post_params["before_money"] = $res_data['before_money'];
    $post_params["after_money"]  = $res_data['after_money'];
    $post_params["errorCode"]    = $res_data['errorCode'];
    $post_params["username"]     = $agent."_".$post_params["username"];
    $tidyjsondata = json_encode(json_encode($post_params,JSON_UNESCAPED_UNICODE));

    // DB寫 respond log
    // $param['table'] = "tra_log_res";
    // $param['mtcode'] = $post_params["mtcode"];
    // $param['json_data'] = $cgjsondata;
    // async($url, $port, $timeout, $param);
    $db = new DB_CLASS();
    $db->connect_db($_SAS_DB['host'], $_SAS_DB['username'], $_SAS_DB['password'], $_SAS_DB['dbname'], $_SAS_DB['port']);
    $sql_str = 'INSERT INTO `tra_log_res` (`agent_name`,`unique_key`,`json_data`) VALUES ("'.$agent.'","'.$post_params["transaction_uuid"].'",'.$tidyjsondata.')';
    $result_query = $db->query($sql_str);
    $insert_id = $db->get_insert_id();
    if ($insert_id == 0)
    {
        $res_data['errorCode'] = $_TIDYS_API_SETTING['errorCode']['unknowerror']; //  db 寫入交易紀錄失敗  
        // error log
        if($_TIDYS_LOG['tidys_debug'] == TRUE)  outputlog(basename(__FILE__, '.php')."_error : db insert 失敗 [agent: ".$agent.", transaction_uuid: ".$post_params["transaction_uuid"].", jsondata: ".$tidyjsondata."]", "../../".$_TIDYS_LOG['dir'].getlogdate().$_TIDYS_LOG['tidys_error_log']);      
    }    
    $db->close_connect();

    //  準備回應內容
    $respond_data['uid']          = $post_params["username"];
    $respond_data['balance']      = floor($res_data['after_money']*pow(10, 2))/pow(10, 2);
    $respond_data['request_uuid'] = $post_params['request_uuid'];
    $respond_data['currency']     = $post_params['currency'];
    $respond = json_encode($respond_data, JSON_UNESCAPED_UNICODE);
    
    switch ($res_data['errorCode'][0])
    {
        case    '1':  //  成功
                $res_data['errorCode'] = 0; //  成功
                break;
        case    '2':  //  account 找不到
                $res_data['errorCode'] = $_TIDYS_API_SETTING['errorCode']['unknowerror'];  //  其他錯誤
                $respond_data['error_msg'] = "unknown";
                break;
        case    '3':  //  餘額為負值
                $res_data['errorCode'] = $_TIDYS_API_SETTING['errorCode']['insufficient']; //  餘額不足
                $respond_data['error_msg'] = "not_enough_balance";
                break;
        case    '4':  //  餘額不足
                $res_data['errorCode'] = $_TIDYS_API_SETTING['errorCode']['insufficient']; //  餘額不足
                $respond_data['error_msg'] = "not_enough_balance";
                break;
        case    '5':  //  insert db 錯誤
                $res_data['errorCode'] = $_TIDYS_API_SETTING['errorCode']['unknowerror'];  //  其他錯誤
                $respond_data['error_msg'] = "unknown";
                break;
        case    '9':  //  找不到後台
                $res_data['errorCode'] = $_TIDYS_API_SETTING['errorCode']['unknowerror'];  //  其他錯誤
                $respond_data['error_msg'] = "unknown";
                break;
    }

    $respond_data['errorCode'] = strval($res_data['errorCode']);

    if($respond_data['errorCode'] == 0)
        TidyApi::call('api/user/balance', 'POST', array('uid' => $respond_data['uid'], 'currency' => $respond_data['currency'], 'request_uuid' => $respond_data['request_uuid'], 'balance' => $respond_data['balance']));
    else
        TidyApi::call('api/user/balance', 'POST', array('error_code' => $respond_data['errorCode'], 'error_msg' => $respond_data['error_msg'], 'request_uuid' => $respond_data['request_uuid']));
   
    // 插入log計算時間
    if($_TIDYS_LOG['tidys_debug'] == TRUE)  outputlog(basename(__FILE__, '.php')."_end respond: ".$respond, "../../".$_TIDYS_LOG['dir'].getlogdate().$_TIDYS_LOG['tidys_log']);
?>