<?php
    require_once("desclass.php");
    
    $key = "g9G16nTs";
    $crypt = new DES($key);
    
    // balance
    // $data1 = "username=100bdl0test&currency=CNY"; 
    
    // bet
    // $data1 = "username=100bdl0test&currency=CNY&amount=20.00&txnid=843828123434&timestamp=2019-10-25 12:34:56.789&ip=121.122.123.124&gametype=bac&platform=0&hostid=601&gameid=133849980&betdetails={“details”:[{“type”:1,”amount”:5},{“type”:2,”amount”:10},{“type”:41,”amount”:10}]}";
    
    // win
    // $data1 = "username=100bdl0test&currency=CNY&amount=20.00&txnid=843828125323&timestamp=2019-10-25 12:34:56.789&gametype=bac&Payouttime=2018-02-03 12:34:56&hostid=123&gameid=133849980";
    
    // lost
    // $data1 = "username=100bdl0test&currency=CNY&txnid=8438234356625&timestamp=2019-10-25 12:34:56.789&gametype=bac&Payouttime=2018-02-03 12:34:56&hostid=123&gameid=133849980";
    
    // cancel
    $data1 = "username=100bdl0test&currency=CNY&amount=20.00&txnid=4345234534534&timestamp=2019-10-25 12:34:56.789&gametype=bac&gameid=133849980&txn_reverse_id=843828123434";

    $q = $crypt->encrypt($data1);
    $data = urlencode($q);
    echo $data;
?>