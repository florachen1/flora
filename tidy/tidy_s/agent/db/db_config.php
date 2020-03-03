<?php
    global $_AGENT_DB;	

    //  本地資料庫
    /*$_DB['host'] = "127.0.0.1";
    $_DB['username'] = "root";
    $_DB['password'] = "ro@t";
    $_DB['dbname'] = "ali88_db";
    $_DB['port'] = 3306;*/

    //  外部開發機資料庫
    $_AGENT_DB['host'] = "localhost"; // localhost 35.240.236.48
    $_AGENT_DB['username'] = "root";
    $_AGENT_DB['password'] = "ro@t";
    $_AGENT_DB['ali88_backend']   = "ali88_backend_db";
    $_AGENT_DB['mango_backend'] = "918kiss_dev";
    $_AGENT_DB['port'] = 3306;

    // $_AGENT_DB['host'] = "10.0.10.223"; // localhost 35.240.236.48
    // $_AGENT_DB['username'] = "ali88";
    // $_AGENT_DB['password'] = "yeZhFnYcBtb8QquY";
    // $_AGENT_DB['dbname'] = "ali88_backend_db";
    // $_AGENT_DB['port'] = 3306;
    
    // docker 引入環境變數
    // $file_get_contents = file_get_contents('http://localhost/ali88_agent/docker.json');
    // $get_contents = json_decode($file_get_contents, JSON_UNESCAPED_UNICODE);
    // if($get_contents['docker'] === true)
    // {
    //     $_AGENT_DB['host'] = getenv('db_host');
    //     $_AGENT_DB['username'] = getenv('db_username');
    //     $_AGENT_DB['password'] = getenv('db_password');
    //     $_AGENT_DB['port'] = getenv('db_port');
    // }
?>