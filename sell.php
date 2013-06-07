<?php
    header('Access-Control-Allow-Origin: *');
    $seller_id=$_GET['seller_id'];
    $con=mysql_connect("qinsight","root","msheric");
    if (!$con)
    {
        die('Could not connect: ' . mysql_error());
    }

    mysql_select_db("ebaymap", $con);
    $result = mysql_query("select z.latitude,z.longtitude,count(*) counter
                            from `transaction` t, `user` u ,ziptoll z
                            where t.seller_id=1007817649
                            and t.buyer_id=u.user_id
                            and u.normalized_zip_code=z.zip
                            group by z.latitude,z.longtitude
                            order by counter desc");

    $ret=array();
    while($row=mysql_fetch_array($result)){
        /*echo $row['ITEM_ID'].' '.$row['BUYER_ID'];
        $user = $row['buyer_id'];
        $zip_result = mysql_query("SELECT zip_code FROM user WHERE USER_ID = $user");
        if($zip=mysql_fetch_array($zip_result)){
        $ret[]=$zip['zip_code'];
        }*/
        $ret[] = array ('lat'=>$row['latitude'],'lng'=>$row['longtitude'],'type'=>0,'counter'=>$row['counter']);
    }
    //type: 0 for buyer; 1 for warehouse order by fee desc; 2 for warehouse order by density desc; 3 fro warehouse order by d/f desc;
    $result_1 = mysql_query("select z.latitude, z.longtitude, w.density, w.warehourse_id, w.state, w.city, w.zip_code, w.Fee, w.D_F
                              from ziptoll z, WAREHOURSE2 w
                              where w.zip_code = z.zip
                              order by Fee desc");
    while($row_1 =mysql_fetch_array($result_1)){
        $wh = array('density'=>$row_1['density'],'warehouse_id'=>$row_1['warehourse_id'],'state'=>$row_1['state'],'city'=>$row_1['city'],'zip_code'=>$row_1['zip_code'],'Fee'=>$row_1['Fee'],'D_F'=>$row_1['D_F']);
        $ret[] = array ('lat'=>$row_1['latitude'],'lng'=>$row_1['longtitude'],'type'=>1,'counter'=>$wh);
    }

    $result_2 = mysql_query("select z.latitude, z.longtitude, w.density, w.warehourse_id, w.state, w.city, w.zip_code, w.Fee, w.D_F
                              from ziptoll z, WAREHOURSE2 w
                              where w.zip_code = z.zip
                              order by density desc");
    while($row_2 =mysql_fetch_array($result_2)){
        $wh = array('density'=>$row_2['density'],'warehouse_id'=>$row_2['warehourse_id'],'state'=>$row_2['state'],'city'=>$row_2['city'],'zip_code'=>$row_2['zip_code'],'Fee'=>$row_2['Fee'],'D_F'=>$row_2['D_F']);
        $ret[] = array ('lat'=>$row_2['latitude'],'lng'=>$row_2['longtitude'],'type'=>2,'counter'=>$wh);
    }

    $result_3 = mysql_query("select z.latitude, z.longtitude, w.density, w.warehourse_id, w.state, w.city, w.zip_code, w.Fee, w.D_F
                                from ziptoll z, WAREHOURSE2 w
                                where w.zip_code = z.zip
                                order by density desc");
    while($row_3 =mysql_fetch_array($result_3)){
        $wh = array('density'=>$row_3['density'],'warehouse_id'=>$row_3['warehourse_id'],'state'=>$row_3['state'],'city'=>$row_3['city'],'zip_code'=>$row_3['zip_code'],'Fee'=>$row_3['Fee'],'D_F'=>$row_3['D_F']);
        $ret[] = array ('lat'=>$row_3['latitude'],'lng'=>$row_3['longtitude'],'type'=>3,'counter'=>$wh);
    }
    //foreach($ret as $zipcode){
    //    }

    /*$buyer = mysql_query("SELECT zip_code FROM user WHERE USER_ID = $row['BUYER_ID']");
    echo mysql_fetch_arrey($buyer);*/
    mysql_close($con);

   echo json_encode($ret);
?>
