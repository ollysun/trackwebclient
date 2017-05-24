<?php
/**
 * Created by PhpStorm.
 * User: Kalu
 * Date: 18/05/2017
 * Time: 10:27
 */

namespace app\services;


class PDOService
{
    public $pdoConn = null;
public function __construct()
{
    try {
        $db_host = 'trackplusdbserver.cqnljhscd9gz.eu-central-1.rds.amazonaws.com';  //  hostname
        $db_name = 'tnt';  //  databasename
        $db_user = 'root';  //  username
        $user_pw = 'thelcmof8is2';  //  password
        $con = new \PDO('mysql:host='.$db_host.'; dbname='.$db_name, $db_user, $user_pw);
        $con->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );
        $con->exec("SET CHARACTER SET utf8");  //  return all sql requests as UTF-8
        $this->pdoConn=$con;
    }
    catch (\PDOException $err) {
        echo $err->getMessage() . "<br />";
    }
    return $this->pdoConn;
}
}