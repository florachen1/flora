<?php
    function changeagent($jsondata, $action)
    {
        global $_KERNEL_LOG;
        global $_AGENTBRDG_DB;
        global $AGENTBRDG_REDIS_DB;

        if($action == "launch")
        {
            require_once("../db/db_config.php");
            require_once("../../../../db/dbclass.php");
            require_once("../../log/log_config.php");
            require_once("../../../../module/outputlog.php");
            require_once("../redis/redis_config.php");
            require_once("../../../../redis/redisclass.php");

            $data = json_decode($jsondata,JSON_UNESCAPED_UNICODE);

            // 插入log計算時間
            if($_KERNEL_LOG['kernel_debug'] == TRUE)  outputlog("changeagent_launch_".basename(__FILE__, '.php')."_start account: ".$data["account"], "../../".$_KERNEL_LOG['dir'].getlogdate().$_KERNEL_LOG['kernel_log']);

            //  先查 redis 是否有資料
            $redisdb = new REDIS_CLASS();
            $redisdb->connect_db($AGENTBRDG_REDIS_DB['host'],$AGENTBRDG_REDIS_DB['AGENTBRDG_REDIS_AGENTDATA'],$AGENTBRDG_REDIS_DB['port']);
            if ($redisdb->is_exists($AGENTBRDG_REDIS_DB['change_grant'].$data["backend"]."_".$data["agent_id"]) == false) 
            {
                //  redis 沒資料就去 DB 撈資料
                $db = new DB_CLASS();
                $db->connect_db($_AGENTBRDG_DB['host'], $_AGENTBRDG_DB['username'], $_AGENTBRDG_DB['password'], $_AGENTBRDG_DB['dbname'], $_AGENTBRDG_DB['port']);
                $sql_str = 'SELECT `agent_id` FROM `change_agent` WHERE `agent_name` = "'.$data["agent_id"].'" AND `backend_name` = "'.$data["backend"].'" ';
                $result_query = $db->query($sql_str);
                $result_array = $db->get_result_to_array($result_query);
                if (count($result_array) > 0)
                {
                    //  撈好資料後新增到 redis 裡面
                    $redisdb->set_data($AGENTBRDG_REDIS_DB['change_grant'].$data["backend"]."_".$data["agent_id"],$result_array[0]["agent_id"],$AGENTBRDG_REDIS_DB['sysdata_expired_sec']);
                    $result = $result_array[0]["agent_id"];
                }
                else    
                {
                    $sql_str = 'INSERT INTO `change_agent` (`agent_name`,`backend_name`) VALUES ("'.$data["agent_id"].'","'.$data["backend"].'")';
                    $result_query = $db->query($sql_str);
                    $insert_id = $db->get_insert_id();
                    if ($insert_id == 0)
                    {
                        $result = null;
                        // error log
                        if($_KERNEL_LOG['kernel_debug'] == TRUE)  outputlog("changeagent_launch_".basename(__FILE__, '.php')."_error : db insert 失敗 [jsondata: ".$jsondata, "../../".$_KERNEL_LOG['dir'].getlogdate().$_KERNEL_LOG['kernel_error_log']);      
                    }  
                    else
                    {
                        $sql_str = 'SELECT `agent_id` FROM `change_agent` WHERE `agent_name` = "'.$data["agent_id"].'" AND `backend_name` = "'.$data["backend"].'" ';
                        $result_query = $db->query($sql_str);
                        $result_array = $db->get_result_to_array($result_query);
                        if (count($result_array) > 0)
                        {
                            //  撈好資料後新增到 redis 裡面
                            $redisdb->set_data($AGENTBRDG_REDIS_DB['change_grant'].$data["backend"]."_".$data["agent_id"],$result_array[0]["agent_id"],$AGENTBRDG_REDIS_DB['sysdata_expired_sec']);
                            $result = $result_array[0]["agent_id"];
                        }
                        else    
                        {
                            $result = null;
                            // error log
                            if($_KERNEL_LOG['kernel_debug'] == TRUE)  outputlog("changeagent_launch_".basename(__FILE__, '.php')."_error : insert agent 後找不到 agent_id [jsondata: ".$jsondata, "../../".$_KERNEL_LOG['dir'].getlogdate().$_KERNEL_LOG['kernel_error_log']);      
                        }
                    }          
                }
                $db->close_connect(); 
            }
            else
                //  取出需要的資料
                $result = $redisdb->get_data($AGENTBRDG_REDIS_DB['change_grant'].$data["backend"]."_".$data["agent_id"]);
            $redisdb->close_connect();

            // 插入log計算時間
            if($_KERNEL_LOG['kernel_debug'] == TRUE)  outputlog("changeagent_launch_".basename(__FILE__, '.php')."_end account: ".$data["account"], "../../".$_KERNEL_LOG['dir'].getlogdate().$_KERNEL_LOG['kernel_log']);
        }
        elseif($action == "trawallet") 
        {
            require_once("../db/db_config.php");
            require_once("../../../../db/dbclass.php");
            require_once("../../log/log_config.php");
            require_once("../../../../module/outputlog.php");
            require_once("../redis/redis_config.php");
            require_once("../../../../redis/redisclass.php");

            $data = json_decode($jsondata,JSON_UNESCAPED_UNICODE);
            
            //  先查 redis 是否有資料
            $redisdb = new REDIS_CLASS();
            $redisdb->connect_db($AGENTBRDG_REDIS_DB['host'],$AGENTBRDG_REDIS_DB['AGENTBRDG_REDIS_AGENTDATA'],$AGENTBRDG_REDIS_DB['port']);
            if ($redisdb->is_exists($AGENTBRDG_REDIS_DB['change_grant'].$data["agent_id"]) == false) 
            {
                //  redis 沒資料就去 DB 撈資料
                $db = new DB_CLASS();
                $db->connect_db($_AGENTBRDG_DB['host'], $_AGENTBRDG_DB['username'], $_AGENTBRDG_DB['password'], $_AGENTBRDG_DB['dbname'], $_AGENTBRDG_DB['port']);
                $agent_id = $data["agent_id"];
                $sql_str = 'SELECT `agent_id` FROM `change_agent` WHERE `agent_name` = "'.$agent_id.'" AND `backend_name` = "'.$data["backend"].'" ';
                $result_query = $db->query($sql_str);
                $result_array = $db->get_result_to_array($result_query);
                if (count($result_array) > 0)
                {
                    //  撈好資料後新增到 redis 裡面
                    $redisdb->set_data($AGENTBRDG_REDIS_DB['change_grant'].$data["agent_id"],$result_array[0]["agent_id"],$AGENTBRDG_REDIS_DB['sysdata_expired_sec']);
                    $result = $result_array[0]["agent_id"];    
                }
                else    
                {
                    $result = null;
                    // error log
                    if($_KERNEL_LOG['kernel_debug'] == TRUE)  outputlog("changeagent_launch_".basename(__FILE__, '.php')."_error : db insert 失敗 [agent_id: ".$agent_id, "../../".$_KERNEL_LOG['dir'].getlogdate().$_KERNEL_LOG['kernel_error_log']);      
                } 
                $db->close_connect();
            }
            else
                //  取出需要的資料
                $result = $redisdb->get_data($AGENTBRDG_REDIS_DB['change_grant'].$data["agent_id"]);
            $redisdb->close_connect(); 

            // 插入log計算時間
            if($_KERNEL_LOG['kernel_debug'] == TRUE)  outputlog("changeagent_launch_".basename(__FILE__, '.php')."_end jsondata: ".$jsondata, "../../".$_KERNEL_LOG['dir'].getlogdate().$_KERNEL_LOG['kernel_log']);
        }
        else
        {
            // require_once("../../../../kernel/agent_brdg/db/db_config.php");
            // require_once("../../../../../db/dbclass.php");
            // require_once("../../../../kernel/log/log_config.php");
            // require_once("../../../../../module/outputlog.php");
            // require_once("../../../../kernel/agent_brdg/redis/redis_config.php");
            // require_once("../../../../../redis/redisclass.php");

            require_once("../../../ali88_api/api/kernel/agent_brdg/db/db_config.php");
            require_once("../../../ali88_api/db/dbclass.php");
            require_once("../../../ali88_api/api/kernel/log/log_config.php");
            require_once("../../../ali88_api/module/outputlog.php");
            require_once("../../../ali88_api/api/kernel/agent_brdg/redis/redis_config.php");
            require_once("../../../ali88_api/redis/redisclass.php");

            $data = json_decode($jsondata,JSON_UNESCAPED_UNICODE);
            
            //  先查 redis 是否有資料
            $redisdb = new REDIS_CLASS();
            $redisdb->connect_db($AGENTBRDG_REDIS_DB['host'],$AGENTBRDG_REDIS_DB['AGENTBRDG_REDIS_AGENTDATA'],$AGENTBRDG_REDIS_DB['port']);
            if ($redisdb->is_exists($AGENTBRDG_REDIS_DB['change_grant'].$data["agent"]) == false) 
            {
                //  redis 沒資料就去 DB 撈資料
                $db = new DB_CLASS();
                $db->connect_db($_AGENTBRDG_DB['host'], $_AGENTBRDG_DB['username'], $_AGENTBRDG_DB['password'], $_AGENTBRDG_DB['dbname'], $_AGENTBRDG_DB['port']);
                $agent_id = $data["agent"];
                $match = "/^\d+$/";
                if(preg_match($match, $agent_id))
                {
                    $sql_str = 'SELECT `agent_name`,`backend_name` FROM `change_agent` WHERE `agent_id` = "'.$agent_id.'" ';
                    $result_query = $db->query($sql_str);
                    $result_array = $db->get_result_to_array($result_query);
                    if (count($result_array) > 0)
                    {
                        //  撈好資料後新增到 redis 裡面
                        $redisdb->set_data($AGENTBRDG_REDIS_DB['change_grant'].$agent_id, $result_array[0]["backend_name"]."_".$result_array[0]["agent_name"],$AGENTBRDG_REDIS_DB['sysdata_expired_sec']);
                        $result['agent']   = $result_array[0]["agent_name"]; 
                        $result['backend'] = $result_array[0]["backend_name"];   
                    }
                    else    
                    {
                        $result = null;
                        // error log
                        if($_KERNEL_LOG['kernel_debug'] == TRUE)  outputlog("changeagent_launch_".basename(__FILE__, '.php')."_error : db insert 失敗 [agent_id: ".$agent_id, "../../../../kernel/".$_KERNEL_LOG['dir'].getlogdate().$_KERNEL_LOG['kernel_error_log']);      
                    } 
                }
                // else // 要取回agent name 接平台商
                // {
                //     $sql_str = 'SELECT `agent_id` FROM `change_agent` WHERE `agent_name` = "'.$agent_id.'" ';
                //     $result_query = $db->query($sql_str);
                //     $result_array = $db->get_result_to_array($result_query);
                //     if (count($result_array) > 0)
                //     {
                //         //  撈好資料後新增到 redis 裡面
                //         $redisdb->set_data($AGENTBRDG_REDIS_DB['change_grant'].$data["agent"],$result_array[0]["agent_id"],$AGENTBRDG_REDIS_DB['sysdata_expired_sec']);
                //         $result = $result_array[0]["agent_id"];    
                //     }
                //     else    
                //     {
                //         $result = null;
                //         // error log
                //         if($_KERNEL_LOG['kernel_debug'] == TRUE)  outputlog("changeagent_launch_".basename(__FILE__, '.php')."_error : db insert 失敗 [agent_id: ".$agent_id, "../../../../kernel/".$_KERNEL_LOG['dir'].getlogdate().$_KERNEL_LOG['kernel_error_log']);      
                //     } 
                // }
                $db->close_connect();
            }
            else
            {
                //  取出需要的資料
                $str = $redisdb->get_data($AGENTBRDG_REDIS_DB['change_grant'].$data["agent"]);
                $agentstr = strchr($str, "_");
                $result['agent']   = substr($agentstr, 1);
                $result['backend'] = strtok($str, "_");
            }
                
            $redisdb->close_connect(); 

            // 插入log計算時間
            if($_KERNEL_LOG['kernel_debug'] == TRUE)  outputlog("changeagent_launch_".basename(__FILE__, '.php')."_end jsondata: ".$jsondata, "../".$_KERNEL_LOG['dir'].getlogdate().$_KERNEL_LOG['kernel_log']);
        }

        return $result;
    }
?>