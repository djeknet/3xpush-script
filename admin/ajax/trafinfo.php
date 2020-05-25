<?php
set_time_limit(300);
error_reporting(0);
ini_set('display_errors', 0);
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
$filter['os'] = text_filter($_GET['os']);
$filter['browser'] = text_filter($_GET['browser']);
$filter['devtype'] = text_filter($_GET['devtype']);
$filter['device'] = text_filter($_GET['device']);
$filter['region'] = text_filter($_GET['region']);
$filter['iprange'] = intval($_GET['iprange']);
$filter['sid'] = intval($_GET['sid']);

if ($filter['os']) {
   $where = "AND os='".$filter['os']."' "; 
}
if ($filter['browser']) {
   $where .= "AND browser='".$filter['browser']."' "; 
}
if ($filter['devtype']) {
   $where .= "AND devtype='".$filter['devtype']."' "; 
}
if ($filter['iprange']) {
   $where .= "AND iprange='".$filter['iprange']."' "; 
}
if ($filter['region']) {
   $where .= "AND cc='".$filter['region']."' "; 
}
if ($filter['sid']) {
   $where .= "AND sid='".$filter['sid']."' "; 
}

if ($config['memcache_ip']) {
$code = md5('pricestat'.$where.$type);
$stat = $memcached->get($code);
if (!$stat) {
$stat = parter_traf_group($where, $type);
$memcached->set($code, $stat, MEMCACHE_COMPRESSED, time() + 300);
}
} else {
$stat = parter_traf_group($where, $type);
}

$os = os();
$browser = browsers();
$ip_ranges_arr = ip_ranges();

$isolist = isolist();
 foreach ($isolist as $key => $value) {
 $region_titles[$value['iso']] = $value[$lang];
 }
                                      
if ($type == 'cc') {

    if ($stat != false) {
       
        echo '<table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">' . _REGION . '</th>
                                            <th scope="col">' . _REQUEST . '</th>
                                            <th scope="col">%</th>
                                        </tr>
                                    </thead>
                                    <tbody>';

       
       $all_request = $stat['ALL'];
        foreach ($stat as $key => $value) {
            if (!$key) continue;
            if ($key=='ALL') continue;
             $proc = round(($value / $all_request) * 100, 0);
            if ($proc < 1) continue;
           
            $region_name = $region_titles[$key];
            if (!$region_name) continue;
            $value = bigint($value);
            
     
 
                echo "<tr>
                <th scope=\"row\"><img src=images/flags/" . $key . ".gif width=20 height=12 align=absmiddle> ".$key." ".$region_titles[$key]."</th>
                                            <td>" . $value . "</td>
                                            <td>" . $proc . "%</td>
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
if ($type == 'iprange') {

    if ($stat != false) {
    echo '<table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">' . _IPRANGE . '</th>
                                            <th scope="col">' . _REQUEST . '</th>
                                            <th scope="col">%</th>
                                        </tr>
                                    </thead>
                                    <tbody>';

         $all_request = $stat['ALL'];
        foreach ($stat as $key => $value) {
            if (!$key) continue;
            if ($key=='ALL') continue;
             $proc = round(($value / $all_request) * 100, 0);
            //if ($proc < 1) continue;
           
            $value = bigint($value);
            
            $name = $ip_ranges_arr[$key]['title'];
            $cc = $ip_ranges_arr[$key]['cc'];
            
     
 
                echo "<tr>
                <th scope=\"row\">[".$cc."] " . $name . "</th>
                                            <td>" . $value . "</td>
                                            <td>" . $proc . "%</td>
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
                                            <th scope="col">' . _REQUEST . '</th>
                                            <th scope="col">%</th>
                                        </tr>
                                    </thead>
                                    <tbody>';

          $all_request = $stat['ALL'];
        foreach ($stat as $key => $value) {
            if (!$key) continue;
            if ($key=='ALL') continue;
            if ($key=='os') continue;
             $proc = round(($value / $all_request) * 100, 0);
             if ($proc < 1) continue;

            $value = bigint($value);
            
                echo "<tr>
                <th scope=\"row\"><img src=\"images/os/" . $key . ".png\" border=0 align=absmiddle title=\"" . $key . "\"> " . $key . "</th>
                                            <td>" . $value . "</td>
                                            <td>" . $proc . "%</td>
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
                        <th scope="col">' . _REQUEST . '</th>
                        <th scope="col">%</th>
                    </tr>
                </thead>
                <tbody>';


                  $all_request = $stat['ALL'];
        foreach ($stat as $key => $value) {
            if (!$key) continue;
            if ($key=='ALL') continue;
            if ($key=='devtype') continue;
             $proc = round(($value / $all_request) * 100, 0);
              if ($proc < 1) continue;

            $value = bigint($value);
            
             $name = $os[$key]['name'];
            $icon = $os[$key]['key'];
            
     
 
                echo "<tr>
                <th scope=\"row\"><img src=\"images/\device/" . $key . ".png\" border=0 align=absmiddle> " . $key . "</th>
                                            <td>" . $value . "</td>
                                            <td>" . $proc . "%</td>
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
                        <th scope="col">' . _REQUEST . '</th>
                        <th scope="col">%</th>
                    </tr>
                </thead>
                <tbody>';

        $all_request = $stat['ALL'];
        $filter_br = array('Avast Secure Br', 'Vivaldi', 'Liebao', 'Mercury', 'Coc Coc', 'Iron', 'Opera Mini', 'Epic', 'Reeder');
        foreach ($stat as $key => $value) {
            if (!$key) continue;
            if ($key=='ALL') continue;

            //if ($value < 100000) continue;
             $proc = round(($value / $all_request) * 100, 0);
             if ($proc < 1) continue;
             if (in_array($key, $filter_br)) continue;

            $value = bigint($value);

            if ($key=='browser') $key = 'unknown';
            
     
 
                echo "<tr>
                <th scope=\"row\"><img src=\"images/browser/" . $key . ".png\" border=0 align=absmiddle> " . $key . "</th>
                                            <td>" . $value . "</td>
                                            <td>" . $proc . "%</td>
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
                                            <th scope="col">' . _REQUEST . '</th>
                        <th scope="col">%</th>
                                        </tr>
                                    </thead>
                                    <tbody>';

           $i=0;  

         $all_request = $stat['ALL'];
         $i=0; 
        foreach ($stat as $key => $value) {
              if ($i >= 10) break;
            if (!$key) continue;
            if ($key=='ALL') continue;
             $proc = round(($value / $all_request) * 100, 0);

            $value = bigint($value);

                echo "<tr>
                <th scope=\"row\">" . $key . "</th>
                                            <td>" . $value . "</td>
                                            <td>" . $proc . "%</td>
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
                                            <th scope="col">' . _REQUEST . '</th>
                        <th scope="col">%</th>
                                        </tr>
                                    </thead>
                                    <tbody>';

           $all_request = $stat['ALL'];
         $i=0; 
        foreach ($stat as $key => $value) {
            if ($i >= 20) break;
            if (!$key) continue;
            if ($key=='ALL') continue;
             $proc = round(($value / $all_request) * 100, 0);

            $value = bigint($value);

                echo "<tr>
                <th scope=\"row\">" . $key . "</th>
                                            <td>" . $value . "</td>
                                            <td>" . $proc . "%</td>
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



