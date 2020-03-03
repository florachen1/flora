<?php
    function mid_bet($jsondata)
    {
        global $_KERNEL_LOG;

        // require_once("../../../../kernel/log/log_config.php");
        // require_once("../../../../../module/outputlog.php");
        // require_once("../../../../kernel/agent_brdg/seamless/bet.php");
        
        require_once("../../../ali88_api/api/kernel/log/log_config.php");
        require_once("../../../ali88_api/module/outputlog.php");
        require_once("../agent_brdg/seamless/bet.php");

        // 插入log計算時間
        // if($_KERNEL_LOG['kernel_debug'] == TRUE)  outputlog("mid_".basename(__FILE__, '.php')."_start jsondata: ".$jsondata, "../../../../kernel/".$_KERNEL_LOG['dir'].getlogdate().$_KERNEL_LOG['kernel_log']);
        if($_KERNEL_LOG['kernel_debug'] == TRUE)  outputlog("mid_".basename(__FILE__, '.php')."_start jsondata: ".$jsondata, "../".$_KERNEL_LOG['dir'].getlogdate().$_KERNEL_LOG['kernel_log']);
    
        // call agent_brdg bet
        $respond_data = agent_bet($jsondata);
        
        // 插入log計算時間
        // if($_KERNEL_LOG['kernel_debug'] == TRUE)  outputlog("mid_".basename(__FILE__, '.php')."_end jsondata: ".$jsondata, "../../../../kernel/".$_KERNEL_LOG['dir'].getlogdate().$_KERNEL_LOG['kernel_log']);
        if($_KERNEL_LOG['kernel_debug'] == TRUE)  outputlog("mid_".basename(__FILE__, '.php')."_end jsondata: ".$jsondata, "../".$_KERNEL_LOG['dir'].getlogdate().$_KERNEL_LOG['kernel_log']);
            
        return $respond_data;
    }
?>