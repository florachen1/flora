<?php
    require_once("../../log/log_config.php");
    require_once("../../../../../../module/outputlog.php");
    if($_TIDYS_LOG['tidys_debug'] == TRUE)  outputlog(basename(__FILE__, '.php')."_start space run: ", "../../".$_TIDYS_LOG['dir'].getlogdate().$_TIDYS_LOG['tidys_log']);

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
    if( isset($_POST['token']) )         $post_params["token"]        = $_POST["token"];
    if( isset($_POST['request_uuid']) )  $post_params["request_uuid"] = $_POST["request_uuid"];
    if( isset($_POST['game_id']) )       $post_params["game_id"]      = $_POST["game_id"];
    if( isset($_POST['client_id']) )     $post_params["client_id"]    = $_POST["client_id"];
    
    $data = json_encode($post_params,JSON_UNESCAPED_UNICODE);
   
    // 插入log計算時間
    if($_TIDYS_LOG['tidys_debug'] == TRUE)  outputlog(basename(__FILE__, '.php')."_start data: ".$data, "../../".$_TIDYS_LOG['dir'].getlogdate().$_TIDYS_LOG['tidys_log']);
    
    // 透過 function getparams 取得參數
    $post_params = getparams($post_params["token"]);

    $postdata = json_encode($post_params,JSON_UNESCAPED_UNICODE);
    
    // 插入log計算時間
    if($_TIDYS_LOG['tidys_debug'] == TRUE)  outputlog(basename(__FILE__, '.php')." postdata: ".$postdata, "../../".$_TIDYS_LOG['dir'].getlogdate().$_TIDYS_LOG['tidys_log']);

    $accountstr = strchr($post_params["accountId"], "_");
    $agent = strtok($post_params["accountId"], "_");
    $gamer_name = "TIDY";

    // 組合傳給 mid_brdg balance 資料
    $jsondata['account']    = $account;
    $jsondata['agent']      = $agent;
    $jsondata['gamer_name'] = $gamer_name;
    $mid = json_encode($jsondata,JSON_UNESCAPED_UNICODE);

    // 向 mid_brdg balance 請求餘額查詢
    $res_tidy = mid_balance($mid);
    
    // 插入log計算時間
    if($_TIDYS_LOG['tidys_debug'] == TRUE)  outputlog(basename(__FILE__, '.php')." res_tidy: ".$res_tidy, "../../".$_TIDYS_LOG['dir'].getlogdate().$_TIDYS_LOG['tidys_log']);
            
    // 組合 mid_brdg balance 回傳的資料
    $res_data = json_decode($res_tidy, JSON_UNESCAPED_UNICODE);

    //  組合回應的資料
    $respond_data['uid']          = $post_params["accountId"];
    $respond_data['request_uuid'] = $post_params["request_uuid"];
    $balance                      = floatval($res_data['balance']);
    $respond_data['balance']      = floor($balance*pow(10, 2))/pow(10, 2);
    $respond_data['currency']     = $currency;
    $respond = json_encode($respond_data, JSON_UNESCAPED_UNICODE);

    switch ($res_data['errorCode'][0])
    {
        case    '1':  //  成功
                $res_data['errorCode'] = 0; //  成功
                break;
        case    '2':  //  account 找不到
                $res_data['errorCode'] = $_TIDYS_API_SETTING['errorCode']['unknowerror']; // 其他錯誤
                $respond_data['error_msg'] = "unknown";
                break;
        case    '9':  //  找不到後台
                $res_data['errorCode'] = $_TIDYS_API_SETTING['errorCode']['unknowerror']; // 其他錯誤
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