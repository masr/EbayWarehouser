<?php

function recommend_houses($num, $seller_id)
{
    global $con;
    $rs = mysql_query('select w.warehouse_id wid, z.latitude,z.longtitude from warehouse w,ziptoll z
                   where w.zip_code=z.zip', $con);

    $houses = array();
    while ($row = mysql_fetch_array($rs, MYSQL_ASSOC)) {
        $houses[$row['wid']] = $row;
        $houses[$row['wid']]['density'] = 0;
        $houses[$row['wid']]['combine_density'] = 0;
    }

    $rs = mysql_query("select z.latitude,z.longtitude from transaction t, user u , ziptoll z
          where t.buyer_id=u.user_id and u.normalized_zip_code=z.zip and t.seller_id=$seller_id");


    while ($row = mysql_fetch_array($rs, MYSQL_ASSOC)) {
        $ulat = $row['latitude'];
        $ulong = $row['longtitude'];
        $near_house_id = 0;
        $nearest = -1;
        foreach ($houses as $id => $house) {
            $wlat = $house['latitude'];
            $wlong = $house['longtitude'];
            $distance = distance($ulat, $ulong, $wlat, $wlong);
            if ($distance < $nearest || $nearest == -1) {
                $nearest = $distance;
                $near_house_id = $id;
            }
        }
        $houses[$near_house_id]['density'] += 1;
    }


    $wids = array_keys($houses);
    $all_combines = array_permutate_combine($wids, $num);
    $best_nearest = -1;
    $best_combine = array();

    foreach ($all_combines as $combine) {
        $total = 0;
        foreach ($houses as $id => $house) {
            if (in_array($id, $combine)) {
                continue;
            }
            $nearest = -1;
            foreach ($combine as $member_id) {
                $member_house = $houses[$member_id];
                $distance = distance($house['latitude'], $house['longtitude'], $member_house['latitude'], $member_house['longtitude']);
                if ($distance < $nearest || $nearest == -1) {
                    $nearest = $distance;
                }
            }
            $total += $nearest;
        }
        if ($total < $best_nearest || $best_nearest == -1) {
            $best_nearest = $total;
            $best_combine = $combine;
        }
    }

    $best_houses = array_filter($houses, function ($ele) use ($best_combine) {
        return in_array($ele['wid'], $best_combine);
    });

    $all_density = 0;
    foreach ($houses as $house) {
        $nearest_id = -1;
        $nearest = -1;
        $all_density += $house['density'];
        foreach ($best_houses as $member_house) {
            $distance = distance($house['latitude'], $house['longtitude'], $member_house['latitude'], $member_house['longtitude']);
            if ($distance < $nearest || $nearest == -1) {
                $nearest_id = $member_house['wid'];
                $nearest = $distance;
            }
        }
        $best_houses[$nearest_id]['combine_density'] += $house['density'];
    }

    foreach ($best_houses as $k => $house) {
        $best_houses[$k]['portion'] = floor($house['combine_density'] / $all_density * 100);
    }
    return $best_houses;
}

?>
