<?php
// function launch_s($jsondata)
// {
    //  PC 或 Mobile 呼叫這支 API 進行玩家登入
    // require_once("db/db_config.php");
    // require_once("../../../../db/dbclass.php");
    // require_once("../../../../module/checkparamsexists.php");
    // // require_once("redis/redis_config.php");
    // // require_once("../../../../redis/redisclass.php");
    // // require_once("../../../../module/sendget.php");
    // require_once("../../../../module/outputlog.php");
    // require_once("log/log_config.php");
    // // require_once("default_config.php");
    // require_once("desclass.php");
    // require_once("../../../kernel/promo/createaccount.php");

    require_once("db/db_config.php");
    require_once("../../ali88_api/db/dbclass.php");
    require_once("../../ali88_api/module/checkparamsexists.php");
    // require_once("redis/redis_config.php");
    // require_once("../../../../redis/redisclass.php");
    // require_once("../../../../module/sendget.php");
    require_once("../../ali88_api/module/outputlog.php");
    require_once("log/log_config.php");
    // require_once("default_config.php");
    require_once("api/user/outside/info.php");
    require_once("api/user/outside.php");
    require_once("api/game/outside/link.php");
    require_once("changecurrency.php");
    require_once("promo/createaccount.php");

    $jsondata    = $_GET["jsondata"];

    // 插入log計算時間
    // if($_SAS_LOG['sas_debug'] == TRUE)  outputlog(basename(__FILE__, '.php')."_start jsondata: ".$jsondata, "../../../middelware/sa/sa_s/".$_SAS_LOG['dir'].getlogdate().$_SAS_LOG['sas_log']);
    if($_TIDYS_LOG['tidys_debug'] == TRUE)  outputlog(basename(__FILE__, '.php')."_start jsondata: ".$jsondata, $_TIDYS_LOG['dir'].getlogdate().$_TIDYS_LOG['tidys_log']);
    $data = json_decode($jsondata,JSON_UNESCAPED_UNICODE); 

    //  取出參數
    $post_params["account"]      = $data["account"];
    $post_params["agent_id"]     = $data["agent_id"];
    $post_params["ev_mode"]      = !isset($data["ev_mode"]) ? "2" : $data["ev_mode"];
    $post_params["currency"]     = $data["currency"];
    $post_params["game_id"]      = $data["game_id"];
    $post_params["homeUrl"]      = $data["homeUrl"];
    $post_params["language"]     = empty($data["language"]) ? "en" : $data["language"];
    
    //  判斷參數是否有效
    $arr_str = array(); //  組合檢查的參數目標名稱
    array_push($arr_str,"account");
    array_push($arr_str,"agent_id");
    array_push($arr_str,"game_id");
    array_push($arr_str,"ev_mode");
    array_push($arr_str,"currency");
    array_push($arr_str,"homeUrl");
    array_push($arr_str,"language");
    $check_res = checkParamsExists($post_params,$arr_str);
    $respond['resultcode']  =   $check_res;

    // 如果參數ev_mode為0就填test
    // if($post_params["ev_mode"] == "0")
    // {
    //     $post_params["ev_mode"] = "test";
    //     require_once("cgs_test_config.php");
    // }
    // else
    // {
    //     $post_params["ev_mode"] = "open";
    //     require_once("cgs_open_config.php");
    // }
    switch($post_params["ev_mode"]) 
    {
        // 測試環境
        case '0':
            require_once("test_tidyclass.php");
            break;

        // 正式環境
        case '1':
            require_once("open_tidyclass.php");
            break;

        // demo環境
        case '2':
            require_once("test_tidyclass.php");
            break;

        default:
            require_once("test_tidyclass.php");
            break;
    }
    
    if ($respond['resultcode'] == ResultCode::SUCCESS) 
    {
        // 試玩遊戲連結
        if($post_params["ev_mode"] == "2")
        {
            $resultdata = TidyApi::call('api/game/outside/demo/link', 'POST', array('game_id' => $data['game_id'], 'back_url' => $post_params["homeUrl"]));
            header('Location: '.$resultdata['link']);
            return;
        }
        
        // 確認是否為替換過的帳號
        $db = new DB_CLASS();
        $db->connect_db($_SAS_DB['host'], $_SAS_DB['username'], $_SAS_DB['password'], $_SAS_DB['dbname'], $_SAS_DB['port']);
        $sql_str = 'SELECT `account_new` FROM `change_account` WHERE `account_old` = "'.$post_params['account'].'" ';
        $result_query = $db->query($sql_str);
        $result_array = $db->get_result_to_array($result_query);
        if (count($result_array) > 0)
            $post_params['account'] = $result_array[0]['account_new'];
        $db->close_connect();
        
        // 將幣別從字母代碼轉換為數字代碼
        $currency = changecurrency($post_params["currency"]);
        
        // 組合傳入 function info 參數 查詢用戶是否存在
        $info['username'] = $post_params["agent_id"]."_".$post_params["account"];
        $infodata = json_encode($info, JSON_UNESCAPED_UNICODE);
        $resultinfo = info($infodata);
        
        // 用戶不存在
        if($resultinfo == 0)
        {
            // 組合傳入 function outside 參數 新增用戶
            $outside['username'] = $post_params["agent_id"]."_".$post_params["account"];
            $outside['currency'] = $currency;
            $outside['uid']      = $outside['username'];
            $outsidedata = json_encode($outside, JSON_UNESCAPED_UNICODE);
            $resultoutside = outside($outsidedata);

            // 新增用戶失敗
            if($resultoutside < 0)
            {
                // error log
                if($_TIDYS_LOG['tidys_debug'] == TRUE)  outputlog(basename(__FILE__, '.php')."_error : 新增用戶失敗 [outsidedata: ".$outsidedata."]", "../".$_TIDYS_LOG['dir'].getlogdate().$_TIDYS_LOG['tidys_error_log']);
            }
        }

        // 登入資料正確後call 建立帳號
        // 組合傳入 function createaccount 參數
        $createaccount['account']    = $post_params["account"];
        $createaccount['agent_name'] = $post_params["agent_id"];
        $createaccount['currency']   = $post_params["currency"];
        $createaccount['gamer']      = "TIDY";
        $creatdata = json_encode($createaccount, JSON_UNESCAPED_UNICODE);
        $result1 = createaccount($creatdata);
        
        // 用戶存在
        // 組合傳入 function gamelink 參數 取得遊戲連結
        $link['game_id']  = $post_params["game_id"];
        $link['username'] = $post_params["agent_id"]."_".$post_params["account"];
        $link['back_url'] = $post_params["homeUrl"];
        $link['lang']     = empty($data["language"]) ? "en" : $data["language"];
        $link['uid']      = $link['username'];
        $link['currency'] = $currency;
        $linkdata = json_encode($link, JSON_UNESCAPED_UNICODE);
        $resultlink = gamelink($linkdata);
        
        // 插入log計算時間
        if($_TIDYS_LOG['tidys_debug'] == TRUE)  outputlog(basename(__FILE__, '.php')."_end resultlink: ".$resultlink.", createaccount_result: ".$result1, $_TIDYS_LOG['dir'].getlogdate().$_TIDYS_LOG['tidys_log']);
        // $result = $resultlink;
        header('Location: '.$resultlink); 
    }

    // 插入log計算時間
    // if($_SAS_LOG['sas_debug'] == TRUE)  outputlog(basename(__FILE__, '.php')."_end result: ".$result, "../../../middelware/sa/sa_s/".$_SAS_LOG['dir'].getlogdate().$_SAS_LOG['sas_log']);
    // if($_TIDYS_LOG['tidys_debug'] == TRUE)  outputlog(basename(__FILE__, '.php')."_end result: ".$resultlink, $_TIDYS_LOG['dir'].getlogdate().$_TIDYS_LOG['tidys_log']);

    // return  $result;
// }
?>