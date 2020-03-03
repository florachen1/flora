<?php
    class ResultCode 
    {
        const UNUSED           = ['0', '不使用'];          //  不使用
        const SUCCESS          = ['1', '成功'];            //  成功
        const ACCOUNT_NOTFOUND = ['2','account 找不到'];   //  account 找不到 
        const NEGATIVE         = ['3','餘額為負值'];       //  餘額為負值
        const INSUFFICIENT     = ['4','餘額不足'];         //  餘額不足
        const INSERT_DB_ERROR  = ['5','insert db 錯誤'];   //  insert db 錯誤
        const UPDATE_DB_ERROR  = ['6','update db 錯誤'];   //  update db 錯誤
        const NOTFOUND_RECORD  = ['7','找不到紀錄'];       //  找不到紀錄
        const REFUND           = ['8','已經被refund'];     //  已經被refund
        const NOTFOUND_BACKEND = ['9','找不到後台'];       //  找不到後台
    };
?>