<?php
set_time_limit(1000);
error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: text/html; charset=utf-8');

require_once("../../include/mysql.php");
require_once("../../include/func.php");
require_once("../../include/SxGeo.php");
$SxGeo = new SxGeo('../../include/SxGeoMax.dat');

include("../../include/info.php");
include("../../include/stat.php");
include("../forms.php");

if ($config['memcache_ip']) {
$memcached = new Memcache;
$memcached->pconnect($config['memcache_ip'], $config['memcache_port']);
}

$check_login = check_login();
if ($check_login['root']!=1) {
    exit;
}

$settings = settings("AND admin_id=".$check_login['getid']."");

$lang = $settings['lang'];

if ($lang!='ru') $lang = 'en';
include("../langs/" . $lang . ".php");

$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : '';
$type = text_filter($type);
$start_date = text_filter($_GET['start_date']);
$end_date = text_filter($_GET['end_date']);
$today = date("Y-m-d");

if (!$start_date) $start_date = gettime($settings['days_stat']);

 if ($start_date && $end_date) {
                                     $where = "AND date >= '".$start_date."' AND date <= '".$end_date."' ";
                                     } elseif ($start_date) {
                                     $where = "AND date >= '".$start_date."' ";
                                     } elseif ($end_date) {
                                     $where = "AND date <= '".$end_date."' ";
                                     }



if ($type == 'all') {

$stat = feed_region_prices($where);
$feeds = feeds();

    if ($stat != false) {
      
        echo '<br /> <table id="basic-datatables" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th scope="col">'._FEED.'</th>
                                            <th scope="col">'._REGION.'</th>
                                            <th scope="col">mobile</th>
                                            <th scope="col">'._CLICKPRICETITLE.'</th>';
                                            
                                            echo '</tr>
                                    </thead>
                                    <tbody>';


        foreach ($stat as $fid => $arr) {
            $feed_name = $feeds[$fid]['name'];
            
             foreach ($arr as $key => $arr2) {
                foreach ($arr2 as $cc => $arr3) {
                   foreach ($arr3 as $mobile => $arr4) {
                 
                    if ($arr4['money']) $price = round($arr4['money']/$arr4['requests'], 4); else $price = 0;
                    if ($mobile==1) $mobile = "<span class=green>yes</span>"; else $mobile = "no"; 
                echo "<tr>
                       <td>".$feed_name."</td>
                       <td>" . $cc . "</td>
                       <td>" . $mobile . "</td>
                       <td>" . $price . "$</td>
                       </tr>";
                       }
                       }
                }
            }
        

        echo '</tbody>
            </table>';
            
            echo "<script>
$('#basic-datatables').DataTable();
</script>";
            
    }else {
        echo '<div>'._NODATA.'</div>';
    }

    exit;

}
                           
    
