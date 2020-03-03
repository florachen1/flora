<?php
    global $_TIDYS_API_SETTING;

    // balance 相關
    $_TIDYS_API_SETTING['errorCode'] = array();
    // $_SAS_API_SETTING['errorCode']['accounterror'] = 1000; //  會員帳號不存在
    $_TIDYS_API_SETTING['errorCode']['unknowerror'] = 99-000; //  其他錯誤
    
    //  bet 相關
    
    // $_CGS_API_SETTING['errorCode']['negative'] = 107;   //  餘額為負值
    // $_CGS_API_SETTING['errorCode']['mtcode_Repeated'] = 105; //  mtcode 重複
    // $_CGS_API_SETTING['errorCode']['eventTimeError'] = 108; //  eventTime 格式錯誤
    $_TIDYS_API_SETTING['errorCode']['insufficient'] = 99-005;  //  餘額不足
    // $_SAS_API_SETTING['errorCode']['error'] = 1005;         //  一般錯誤
    // $_SAS_API_SETTING['errorCode']['accounterror']  = 1000; //  會員帳號不存在
    $_TIDYS_API_SETTING['errorCode']['unknowerror'] = 99-000; //  其他錯誤

    //  endround 相關
    // $_CGS_API_SETTING['endround_errorCode']['negative'] = 107;   //  餘額為負值
    // $_CGS_API_SETTING['endround_errorCode']['mtcode_Repeated'] = 105; //  mtcode 重複
    // $_CGS_API_SETTING['endround_errorCode']['eventTimeError'] = 108; //  eventTime 格式錯誤
    // $_CGS_API_SETTING['endround_errorCode']['unknowerror'] = 104; //  其他錯誤

    // //  recode 相關
    // $_CGS_API_SETTING['recode_errorCode']['notfoundrecode'] = 103; //  未查到紀錄-無效單

    // //  refund 相關
    // $_CGS_API_SETTING['refund_errorCode']['notfoundrecode'] = 111; //  未查到紀錄-無效單
    // $_CGS_API_SETTING['endround_errorCode']['unknowerror']  = 104; //  其他錯誤
    // $_CGS_API_SETTING['errorCode']['dbinserterror'] = 110; //  db 寫入交易紀錄失敗

    // writelog
    $_TIDYS_API_SETTING['writelog_url'] = "http://localhost/tidy/tidy_s/api/writelog.php";
?>