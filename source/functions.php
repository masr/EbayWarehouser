<?php

function distance($lat1, $long1, $lat2, $long2)
{
    return sqrt(pow($lat1 - $lat2, 2) + pow($long1 - $long2, 2));
}


function aia_unique($arrs)
{
    $box = array();
    $ret = array();
    foreach ($arrs as $arr) {
        $arr2 = $arr;
        sort($arr2);
        if (in_array($arr2, $box)) {
            continue;
        } else {
            $ret[] = $arr;
            $box[] = $arr2;
        }
    }
    return $ret;
}


function array_permutate_combine($arr, $num)
{
    $rs = array();
    if ($num == 0) {
        return array(array());
    }
    foreach ($arr as $k => $ele) {
        $arr2 = $arr;
        unset($arr2[$k]);
        $arr_ret = array_permutate_combine($arr2, $num - 1);

        foreach ($arr_ret as $k_ret => $v_ret) {
            array_unshift($arr_ret[$k_ret], $ele);
        }
        $rs = array_merge($rs, $arr_ret);
    }
    $rs = aia_unique($rs);
    return $rs;
}



function select_mysql_sql($sql,$con, $limit=0)
{
    empty($limit) or $sql .= ' limit 0,' . $limit;
    $rs = mysql_query($sql, $con);
    $result_arr = array();
    while ($row = mysql_fetch_array($rs, MYSQL_ASSOC)) {
        $result_arr[] = $row;
    }

    return $result_arr;
}



?>