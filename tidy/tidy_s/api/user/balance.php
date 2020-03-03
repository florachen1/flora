<?php
    require_once("../../log/log_config.php");
    require_once("../../../../ali88_api/module/outputlog.php");
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
    require_once("../../../../../../ali88_api/db/dbclass.php");
    require_once("../../../../../../ali88_api/module/checkparamsexists.php");
    // require_once("../../../../../module/getheader.php");
    // require_once("../../../../../module/snowflake.php");
    // require_once("../redis/redis_config.php");
    // require_once("../../../../../redis/redisclass.php");
    require_once("../../mid_brdg/balance.php");
    require_once("../../../../../../ali88_api/module/sendget.php");
    require_once("../config.php");
    // require_once("../../desclass.php");
    require_once("../getparams.php");

    if($_TIDYS_LOG['tidys_debug'] == TRUE)  outputlog(basename(__FILE__, '.php')."_start", "../../".$_TIDYS_LOG['dir'].getlogdate().$_TIDYS_LOG['tidys_log']);

    // TIDY 發過來的參數
    if( isset($_POST['token']) )         $post_params["token"]        = $_POST["token"];
    if( isset($_POST['request_uuid']) )  $post_params["request_uuid"] = $_POST["request_uuid"];
    if( isset($_POST['game_id']) )       $post_params["game_id"]      = $_POST["game_id"];
    if( isset($_POST['client_id']) )     $post_params["client_id"]    = $_POST["client_id"];
    
    // 透過 function getparams 取得參數
    // $post_params1 = getparams($post_params["token"]);

    $postdata = json_encode($post_params,JSON_UNESCAPED_UNICODE);
    // 插入log計算時間
    if($_TIDYS_LOG['tidys_debug'] == TRUE)  outputlog(basename(__FILE__, '.php')."_start postdata: ".$postdata, "../../".$_TIDYS_LOG['dir'].getlogdate().$_TIDYS_LOG['tidys_log']);

?>
