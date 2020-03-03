<?php
    require_once("db/db_config.php");
    require_once("../../../ali88_api/db/dbclass.php");
	require_once("../../../ali88_api/module/outputlog.php");
    require_once("codelist.php");

    if( isset($_POST['table']) )      $post_params["table"]      = $_POST["table"];
    if( isset($_POST['backend']) )    $post_params["backend"]    = $_POST["backend"];
    if( isset($_POST['gamer_name']) ) $post_params["gamer_name"] = $_POST["gamer_name"];
    if( isset($_POST['mtcode']) )     $post_params["mtcode"]     = $_POST["mtcode"];
    if( isset($_POST['json_data']) )  $post_params["json_data"]  = $_POST["json_data"];

    $jsondata = json_encode($post_params,JSON_UNESCAPED_UNICODE);

    $db = new DB_CLASS();
    $db->connect_db($_AGENT_DB['host'], $_AGENT_DB['username'], $_AGENT_DB['password'], $post_params["backend"], $_AGENT_DB['port']);
    $sql_str = 'INSERT INTO '.$post_params['table'].' (`gamer_name`,`unique_key`,`jsondata`) VALUES ("'.$post_params["gamer_name"].'","'.$post_params["mtcode"].'",'.$post_params["json_data"].')';
    $result_query = $db->query($sql_str);
    $insert_id = $db->get_insert_id();
    if ($insert_id == 0)
    {
        $data['errorCode'] = ResultCode::INSERT_DB_ERROR; //  insert db 錯誤
        // error log
        if($_AGENT_LOG['agent_debug'] == TRUE)  outputlog(basename(__FILE__, '.php')."_error : db insert 失敗 [jsondata: ".$jsondata."]", "../".$_AGENT_LOG['dir'].getlogdate().$_AGENT_LOG['agent_error_log']);      
    }        
    
    $db->close_connect();
?>