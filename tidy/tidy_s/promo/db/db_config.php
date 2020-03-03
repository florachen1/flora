<?php
    global $_PROMO_DB;	

    //  外部開發機資料庫
    $_PROMO_DB['host'] = "127.0.0.1"; // localhost 35.240.236.48
    $_PROMO_DB['username'] = "root";
    $_PROMO_DB['password'] = "ro@t";
    $_PROMO_DB['dbname'] = "ali88_promo_db";
    $_PROMO_DB['backend_dbname'] = "mango";
    $_PROMO_DB['port'] = 3306;
    // docker 引入環境變數
    // $file_get_contents = file_get_contents('http://localhost/ali88_api/module/docker.json');
    // $get_contents = json_decode($file_get_contents, JSON_UNESCAPED_UNICODE);
    // if($get_contents['docker'] === true)
    // {
    //     $_PROMO_DB['host'] = getenv('db_host');
    //     $_PROMO_DB['username'] = getenv('db_username');
    //     $_PROMO_DB['password'] = getenv('db_password');
    //     $_PROMO_DB['port'] = getenv('db_port');
    // }
?>