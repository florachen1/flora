<?php
    function agent_endround($jsondata)
    {
        global $_KERNEL_LOG;
        global $_AGENTBRDG_URL;
    
        require_once("../log/log_config.php");
        require_once("../../../ali88_api/module/outputlog.php");
        require_once("../../../ali88_api/module/sendget.php");
        require_once("../agent_brdg/config.php");
        require_once("../agent_brdg/changeagent.php");

        // 插入log計算時間
        if($_KERNEL_LOG['kernel_debug'] == TRUE)  outputlog("agent_".basename(__FILE__, '.php')."_start jsondata: ".$jsondata, "../".$_KERNEL_LOG['dir'].getlogdate().$_KERNEL_LOG['kernel_log']);   
        
        // 轉換agent
        $changeagent = changeagent($jsondata, "endround");
        if(!empty($changeagent))
        {
            $data = json_decode($jsondata, JSON_UNESCAPED_UNICODE);
            $data['agent']   = $changeagent['agent'];
            $data['backend'] = $changeagent['backend'];
            $json_data = json_encode($data, JSON_UNESCAPED_UNICODE);
            // 與平台確認endround
            $respond_data = send_get($_AGENTBRDG_URL['endround_url']."?jsondata=".$json_data);
        }

        // 插入log計算時間
        if($_KERNEL_LOG['kernel_debug'] == TRUE)  outputlog("agent_".basename(__FILE__, '.php')."_end jsondata: ".$jsondata, "../".$_KERNEL_LOG['dir'].getlogdate().$_KERNEL_LOG['kernel_log']);

        return $respond_data;
    }
?>