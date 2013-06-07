<?php
error_reporting(E_ERROR);
header('Access-Control-Allow-Origin: *');
require_once './source/functions.php';
require_once './source/alg.php';

$con = mysql_connect("qinsight", "root", "msheric");
mysql_select_db("ebaymap", $con);
if (!$con) {
    die('Could not connect: ' . mysql_error());
}

//$_GET = array('type' => 'recommend_houses', 'num' => '2', 'seller_id' => '1007817649');
if ($_GET['type'] == 'all_warehouses') {
    $rs = select_mysql_sql("select w.city,w.warehouse_id,z.longtitude,z.latitude,w.state,w.zip_code
                            from warehouse w,ziptoll z where w.zip_code=z.zip", $con);
    exit(json_encode($rs));
} else if ($_GET['type'] == 'recommend_houses') {
    $num = intval($_GET['num']);
   // $seller_id = $_GET['seller_id'];
    $result = recommend_houses($num, '1007817649');
    exit(json_encode($result));
} else if ($_GET['type'] == 'all_trans') {
    $seller_id = $_GET['seller_id'];
    $result = select_mysql_sql("select z.latitude,z.longtitude,count(*) counter
                            from `transaction` t, `user` u ,ziptoll z
                            where t.seller_id=$seller_id
                            and t.buyer_id=u.user_id
                            and u.normalized_zip_code=z.zip
                            group by z.latitude,z.longtitude
                            order by counter desc", $con);
    exit(json_encode($result));
}

?>
