<?php

@$dev = intval($_GET['dev']);
set_time_limit(1000);
if ($dev==1) {
error_reporting(E_ALL);
ini_set('display_errors', 1); 
} else {
 error_reporting(0);
ini_set('display_errors', 0);   
}

header('Content-Type: text/html; charset=utf-8');

require_once("../../include/mysql.php");
require_once("../../include/func.php");
require_once("../../include/SxGeo.php");
$SxGeo = new SxGeo('../../include/SxGeoMax.dat');

include("../../include/info.php");
include("../../include/stat.php");

if ($config['memcache_ip']) {
$memcached = new Memcache;
$memcached->pconnect($config['memcache_ip'], $config['memcache_port']);
}

$check_login = check_login();
if ($check_login==false) {
    exit;
}

$lang = isset($_REQUEST['lang']) ? $_REQUEST['lang'] : 'en';
if ($lang!='ru') $lang = 'en';
include("../langs/" . $lang . ".php");

$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : '';
$type = text_filter($type);

$start_date = text_filter($_GET['start_date']);
$end_date = text_filter($_GET['end_date']);


if ($start_date && $end_date) {
    $where = "AND date >= '" . $start_date . "' AND date <= '" . $end_date . "' ";
} elseif ($start_date) {
    $where = "AND date >= '" . $start_date . "' ";
} elseif ($end_date) {
    $where = "AND date <= '" . $end_date . "' ";
}


if ($config['memcache_ip']) {
$code = md5('pricestat'.$where.$type);
$stat = $memcached->get($code);
if (!$stat) {
$stat = prices($where, $type);
$memcached->set($code, $stat, MEMCACHE_COMPRESSED, time() + 300);
}
} else {
$stat = prices($where, $type);
}

$os = os();
$browser = browsers();
$ip_ranges_arr = ip_ranges();
$isolist = isolist();
 foreach ($isolist as $key => $value) {
 $region_titles[$value['iso']] = $value[$lang];
 }
                                      
if ($type == 'regions') {

    if ($stat != false) {
       
        echo '<table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">' . _REGION . '</th>
                                            <th scope="col">CPC</th>
                                            <th scope="col">CPM</th>
                                            <th scope="col">CPM send</th>
                                        </tr>
                                    </thead>
                                    <tbody>';

        $region_stat = region_stat($where);
        
        foreach ($stat['region'] as $key => $value) {
            if (!$key) continue;
            $region_name = $region_titles[$key];
            if (!$region_name) continue;
            $cpc_money = $value['cpc_money'];
            $cpv_money = $value['cpv_money'];
            $views = $cpc_views = $value['views'];
            $cpv_views = $value['cpv_views']; // показы с оплатой за просмотры
            if ($cpv_views>0) $cpc_views = $views - $cpv_views; // показы с оплатой за клик
            
            $sended = $region_stat[$key]['sended'];
            $money = $region_stat[$key]['money'];
            if ($money > 0 && $sended>0) {
                 $cpm = round(($money/$sended)*1000,3);
            } else $cpm='-';
            
            if ($cpc_money && $cpc_views) $cpc = round($cpc_money/$cpc_views, 3); else $cpc=0;
            if ($cpv_money && $cpv_views) $cpv = round($cpv_money/$cpv_views, 3); else $cpv=0;
            if ($cpc==0 && $cpv==0) continue;
            if ($cpv==0) $cpv='-';
            if ($cpc==0) $cpc='-';
 
                echo "<tr>
                <th scope=\"row\"><img src=images/flags/" . $key . ".gif width=20 height=12 align=absmiddle> <a href=# data-toggle=\"modal\" data-target=\"#popup\" onclick=\"aj('price_days.php','".$type."|".$key."',1); return false;\">".$key." ".$region_titles[$key]."</a></th>
                                            <td>" . $cpc . "</td>
                                            <td>" . $cpv . "</td>
                                            <td>".$cpm."</td>
                                        </tr>";
            }
       

        echo '</tbody>
            </table>';
    } else {
        echo '<div>'._NODATA.'</div>';
    }

    exit;

}

//ip_range
if ($type == 'ip_range') {

    if ($stat != false) {
    echo '<table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">' . _IPRANGE . '</th>
                                            <th scope="col">CPC</th>
                                            <th scope="col">CPM</th>
                                        </tr>
                                    </thead>
                                    <tbody>';


        foreach ($stat['ip_range'] as $key => $value) {

                if (!$key) continue;
            $cpc_money = $value['cpc_money'];
            $cpv_money = $value['cpv_money'];
            $views = $cpc_views = $value['views'];
            $cpv_views = $value['cpv_views']; // показы с оплатой за просмотры
            if ($cpv_views>0) $cpc_views = $views - $cpv_views; // показы с оплатой за клик
            
            $cpc = round($cpc_money/$cpc_views, 3);
            $cpv = round($cpv_money/$cpv_views, 3);
            if ($cpv==0) $cpv='-';
            if ($cpc==0) $cpc='-';
            
            $name = $ip_ranges_arr[$key]['title'];
            $cc = $ip_ranges_arr[$key]['cc'];
            
                echo "<tr>
                                            <th scope=\"row\">[".$cc."] " . $name . "</th>
                                             <td>" . $cpc . "</td>
                                            <td>" . $cpv . "</td>
                                        </tr>";
            
        }

        echo '</tbody>
                                </table>';
    }
    else {
        echo '<div>'._NODATA.'</div>';
    }

    exit;

}

if ($type == 'os') {

    if ($stat != false) {
    echo '<table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">' . _OS . '</th>
                                            <th scope="col">CPC</th>
                                            <th scope="col">CPM</th>
                                        </tr>
                                    </thead>
                                    <tbody>';


        foreach ($stat['os'] as $key => $value) {
             if (!$key) continue;
            $cpc_money = $value['cpc_money'];
            $cpv_money = $value['cpv_money'];
            $views = $cpc_views = $value['views'];
            $cpv_views = $value['cpv_views']; // показы с оплатой за просмотры
            if ($cpv_views>0) $cpc_views = $views - $cpv_views; // показы с оплатой за клик
            
            $cpc = round($cpc_money/$cpc_views, 3);
            $cpv = round($cpv_money/$cpv_views, 3);
            if ($cpv==0) $cpv='-';
            if ($cpc==0) $cpc='-';
            $name = $os[$key]['name'];
            $icon = $os[$key]['key'];
            if ($name=='Linux') $icon = 'GNULinux';
            if ($name=='Other') $icon = 'unknown';
            
                echo "<tr>
                                            <th scope=\"row\"><img src=\"images/os/" . $icon . ".png\" border=0 align=absmiddle title=\"" . $name . "\"> " . $name . "</th>
                                            <td>" . $cpc . "</td>
                                            <td>" . $cpv . "</td>
                                        </tr>";
            }
        
        echo '</tbody>
                                </table>';
    }
    else {
        echo '<div>'._NODATA.'</div>';
    }



    exit;

}


if ($type == 'devtype') {

    if ($stat != false) {
    echo '<table class="table">
                <thead>
                    <tr>
                        <th scope="col">' . _TYPE . '</th>
                        <th scope="col">CPC</th>
                        <th scope="col">CPM</th>
                    </tr>
                </thead>
                <tbody>';


        foreach ($stat['devtype'] as $key => $value) {
             if (!$key) $key = 'unknown';
            $cpc_money = $value['cpc_money'];
            $cpv_money = $value['cpv_money'];
            $views = $cpc_views = $value['views'];
            $cpv_views = $value['cpv_views']; // показы с оплатой за просмотры
            if ($cpv_views>0) $cpc_views = $views - $cpv_views; // показы с оплатой за клик
            
            $cpc = round($cpc_money/$cpc_views, 3);
            $cpv = round($cpv_money/$cpv_views, 3);
            if ($cpv==0) $cpv='-';
            if ($cpc==0) $cpc='-';
            
            echo "<tr>
                                            <th scope=\"row\"><img src=\"images/device/" . $key . ".png\" border=0 align=absmiddle title=\"" . $key . "\"> " . $key . "</th>
                                           <td>" . $cpc . "</td>
                                            <td>" . $cpv . "</td>
                                           
                                        </tr>";
        }
        echo '</tbody>
             </table>';
    }
    else {
        echo '<div>'._NODATA.'</div>';
    }

    exit;

}

// browser
if ($type == 'browser') {

$browser_stat = browser_stat($where);

    if ($stat != false) {
    echo '<table class="table">
                <thead>
                    <tr>
                        <th scope="col">' . _BROWSER . '</th>
                        <th scope="col">CPC</th>
                        <th scope="col">CPM</th>
                        <th scope="col">CPM send</th>
                    </tr>
                </thead>
                <tbody>';


        foreach ($stat['browser'] as $key => $value) {
             if (!$key) continue;
            $cpc_money = $value['cpc_money'];
            $cpv_money = $value['cpv_money'];
            $views = $cpc_views = $value['views'];
            $cpv_views = $value['cpv_views']; // показы с оплатой за просмотры
            if ($cpv_views>0) $cpc_views = $views - $cpv_views; // показы с оплатой за клик
            
            $cpc = round($cpc_money/$cpc_views, 3);
            $cpv = round($cpv_money/$cpv_views, 3);
            if ($cpv==0) $cpv='-';
            if ($cpc==0) $cpc='-';
            
            $name = $browser[$key]['name'];
            $icon = $browser[$key]['key'];
            if ($name=='Mozilla') $icon = 'Firefox';
            
            $sended = $browser_stat[$key]['sended'];
            $money = $browser_stat[$key]['money'];
            if ($money > 0 && $sended>0) {
                 $cpm = round(($money/$sended)*1000,3);
            } else $cpm='-';
            
                echo "<tr>
                                            <th scope=\"row\"><img src=\"images/browser/" . $icon . ".png\" border=0 align=absmiddle title=\"" . $name . "\"> " . $name . "</th>
                                            <td>" . $cpc . "</td>
                                            <td>" . $cpv . "</td>
                                            <td>".$cpm."</td>
                                        </tr>";
            }
        
        echo '</tbody></table>';
    }
    else {
        echo '<div>'._NODATA.'</div>';
    }

    exit;
}

// brand
if ($type == 'device') {

    if ($stat != false) {
    echo '<table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">' . _MODEL . '</th>
                                            <th scope="col">CPC</th>
                        <th scope="col">CPM</th>
                                        </tr>
                                    </thead>
                                    <tbody>';

           $i=0;  
        foreach ($stat['device'] as $key => $value) {
            if ($i >= 10) break;
            
             if (!$key) $key = 'unknown';
            $cpc_money = $value['cpc_money'];
            $cpv_money = $value['cpv_money'];
            $views = $cpc_views = $value['views'];
            $cpv_views = $value['cpv_views']; // показы с оплатой за просмотры
            if ($cpv_views>0) $cpc_views = $views - $cpv_views; // показы с оплатой за клик
            
            $cpc = round($cpc_money/$cpc_views, 3);
            $cpv = round($cpv_money/$cpv_views, 3);
            if ($cpv==0) $cpv='-';
            if ($cpc==0) $cpc='-';
            
                echo "<tr>
                                            <th scope=\"row\">" . $key . "</th>
                                           <td>" . $cpc . "</td>
                                            <td>" . $cpv . "</td>
                                        </tr>";
                                        
                                        $i++;
            
        }
        echo '</tbody>
                                </table>';
    }
    else {
        echo '<div>'._NODATA.'</div>';
    }

    exit;

}

// brand
if ($type == 'lang') {

    if ($stat != false) {
    echo '<table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">' . _LANG1 . '</th>
                                            <th scope="col">CPC</th>
                        <th scope="col">CPM</th>
                                        </tr>
                                    </thead>
                                    <tbody>';

           $i=0;  
        foreach ($stat['lang'] as $key => $value) {
            if ($i >= 20) break;
            
             if (!$key) continue;
            $cpc_money = $value['cpc_money'];
            $cpv_money = $value['cpv_money'];
            $views = $cpc_views = $value['views'];
            $cpv_views = $value['cpv_views']; // показы с оплатой за просмотры
            if ($cpv_views>0) $cpc_views = $views - $cpv_views; // показы с оплатой за клик
            
            $cpc = round($cpc_money/$cpc_views, 3);
            $cpv = round($cpv_money/$cpv_views, 3);
            if ($cpv==0) $cpv='-';
            if ($cpc==0) $cpc='-';
            
                echo "<tr>
                                            <th scope=\"row\">" . $key . "</th>
                                           <td>" . $cpc . "</td>
                                            <td>" . $cpv . "</td>
                                        </tr>";
                                        
                                        $i++;
            
        }
        echo '</tbody>
                                </table>';
    }
    else {
        echo '<div>'._NODATA.'</div>';
    }

    exit;

}



