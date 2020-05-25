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

if ($type=='all') {
    $where .= "AND type=0 ";
} elseif ($type=='sites') {
    $where .= "AND type=3 ";
} elseif ($type=='users') {
    $where .= "AND type=4 ";
} 

$stat = sysstat($where);

if ($type == 'all') {

    $data = array();
    if (is_array($stat)) {
        $stat_graph = array_reverse($stat);
        foreach ($stat_graph as $key => $value) {
            
            $all_obj = $value['myads_all'] + $value['sites_all'];
            $coef = round(($value['users_all'] / $all_obj) * 100, 0);
            $profit_user = round($value['adv_money']/$value['users_all'], 2);
            $profit = round($value['adv_money'] - $value['wm_money'], 2);
            $avg_balance = round($value['balance'] / $value['balance_accounts'], 2);

                $data['legends'][] = $key;
                $data['title'][_SENDEDALL][] = isset($value['myads_all']) ? $value['myads_all'] : 0;
                $data['title'][_SITES_ALL][] = isset($value['sites_all']) ? $value['sites_all'] : 0;
                $data['title'][_USERS_ALL][] = isset($value['users_all']) ? $value['users_all'] : 0;
                $data['title']['COEF'][] = isset($coef) ? $coef : 0;
                $data['title']['user profit'][] = isset($profit_user) ? $profit_user : 0;
                $data['title'][_PROFIT][] = isset($profit) ? $profit : 0;
                $data['title'][_ACT_ACCOUNTS][] = isset($value['active_users']) ? $value['active_users'] : 0;
                $data['title'][_ACCOUNT_BALANCE][] = isset($avg_balance) ? $avg_balance : 0;
            
        }
    }
 
    echo chart_line2("chart_line", $data);

    if ($stat != false) {
      
        echo '<br /> <table class="table table-small">
                                    <thead>
                                        <tr>
                                            <th scope="col">'._DATE.'</th>
                                            <th scope="col">us '.tooltip(_USERS_ALL1, "left").'</th>
                                            <th scope="col">act us '.tooltip(_ACT_ACCOUNTS1, "left").'</th>
                                            <th scope="col">sites '.tooltip(_SITES_ALL, "left").'</th>
                                            <th scope="col">send '.tooltip(_SENDEDALL, "left").'</th>
                                            <th scope="col">balance '.tooltip(_TOTAL_BALANCE, "left").'</th>
                                            <th scope="col">ac balance '.tooltip(_BALANCE_ACCTOUNT, "left").'</th>
                                            <th scope="col">avg balance '.tooltip(_BALANCE_ACCTOUNT_AVG, "left").'</th>
                                            <th scope="col">wm '.tooltip(_WM_MONEY, "left").'</th>
                                            <th scope="col">adv '.tooltip(_FEEDS_MONEY, "left").'</th>
                                            <th scope="col">profit '.tooltip(_PROFIT, "left").'</th>
                                            <th scope="col">us profit '.tooltip(_PROFIT_US, "left").'</th>
                                        </tr>
                                    </thead>
                                    <tbody>';


        foreach ($stat as $key => $value) {
        $profit = round($value['adv_money'] - $value['wm_money'], 2);
        $profit_user = round($value['adv_money']/$value['users_all'], 2);
        if ($value['wm_money'] > 0 && $value['wm_money_weekago'] > 0 ) {
            $wm_diff = round($value['wm_money'] - $value['wm_money_weekago'], 2);
            if ($wm_diff < 0) {
                $wm_diff = "<span class=red>".$wm_diff."$</span>";
            } else {
                $wm_diff = "<span class=greed>+".$wm_diff."$</span>";
            }
        } else $wm_diff = '?';
         if ($value['adv_money'] > 0 && $value['adv_money_weekago'] > 0 ) {
            $adv_diff = round($value['adv_money'] - $value['adv_money_weekago'], 2);
            if ($adv_diff < 0) {
                $adv_diff = "<span class=red>".$adv_diff."$</span>";
            } else {
                $adv_diff = "<span class=greed>+".$adv_diff."$</span>";
            }
        } else $adv_diff = '?';
        
        $proc[0] = round(($value['active_users']/$value['users_all'])*100, 0);
        $proc[1] = round(($value['balance_accounts']/$value['users_all'])*100, 0);
        $avg[1] = round($value['balance']/$value['balance_accounts'], 2);
        if (!$value['money_in']) $value['money_in'] = 0;
        if ($today==$key) $key = "<strong>"._TODAY."</strong>";

                echo "<tr>
                       <td>".$key."</td>
                       <td>" . $value['users_all'] . "</td>
                       <td>" . $value['active_users'] . " <span class=small title='"._ANALIT_TEXT1."'>(".$proc[0]."%)</span></td>
                       <td>" . $value['sites_all'] . "</td>
                       <td>" . $value['myads_all'] . "</td>
                       <td>" . $value['balance'] . "$</td>
                       <td>" . $value['balance_accounts'] . "  <span class=small title='"._ANALIT_TEXT1."'>(".$proc[1]."%)</span></td>
                       <td>" . $avg[1] . "$</td>
                       <td>" . $value['wm_money'] . "$ <span title='"._ANALIT_TEXT2."'>(".$wm_diff.")</span></td>
                       <td>" . $value['adv_money'] . "$ <span title='"._ANALIT_TEXT2."'>(".$adv_diff.")</span></td>
                       <td>" . $profit . "$</td>
                       <td>" . $profit_user . "$</td>
                       </tr>";
            }
        

        echo '</tbody>
            </table>';
    }else {
        echo '<div>'._NODATA.'</div>';
    }

    exit;

}

if ($type == 'sites') {
    
    if ($stat != false) {
            $data = array();
    if (is_array($stat)) {
        $stat_graph = array_reverse($stat);
        foreach ($stat_graph as $key => $value) {
        if ($value['active'] > 0) {
         $proc[0] = round(($value['active']/$value['sites_all'])*100, 0);
        } else $proc[0]=0;
        
        $proc[1] = round(($value['sites']/$value['sites_all'])*100, 0);
        $proc[2] = round(($value['landings']/$value['sites_all'])*100, 0);

                $data['legends'][] = $key;
                $data['title'][_ANALIT_TEXT3][] = isset($value['sites_all']) ? $value['sites_all'] : 0;
                $data['title'][_ACTIVE][] = isset($proc[0]) ? $proc[0] : 0;
                $data['title'][_ANALIT_TEXT4][] = $proc[1];
                $data['title'][_ANALIT_TEXT5][] = $proc[2];
            
        }
    }
 
    echo chart_line2("chart_line3", $data);
    
      echo '<br /> <table class="table table-small">
                                    <thead>
                                        <tr>
                                            <th scope="col">'._DATE.'</th>
                                            <th scope="col">all '.tooltip(_ANALIT_TEXT3, "left").'</th>
                                            <th scope="col">new '.tooltip(_ANALIT_TEXT6, "left").'</th>
                                            <th scope="col">sites '.tooltip(_SITES_ALL, "left").'</th>
                                            <th scope="col">land '.tooltip(_ANALIT_TEXT7, "left").'</th>
                                            <th scope="col">active '.tooltip(_ANALIT_TEXT8, "left").'</th>
                                            <th scope="col">act 7 '.tooltip(_ANALIT_TEXT9, "left").'</th>
                                            <th scope="col">act 30 '.tooltip(_ANALIT_TEXT10, "left").'</th>
                                            <th scope="col">act 60 '.tooltip(_ANALIT_TEXT11, "left").'</th>
                                            <th scope="col">act 90 '.tooltip(_ANALIT_TEXT12, "left").'</th>
                                            <th scope="col">act 120 '.tooltip(_ANALIT_TEXT13, "left").'</th>
                                            <th scope="col">act 200 '.tooltip(_ANALIT_TEXT14, "left").'</th>
                                        </tr>
                                    </thead>
                                    <tbody>';
      
        foreach ($stat as $key => $value) {
            
        if ($value['active'] > 0) {
         $proc[0] = round(($value['active']/$value['sites_all'])*100, 0);
        } else $proc[0]=0;
         if ($value['active_7days'] > 0) {
         $proc[1] = round(($value['active_7days']/$value['sites_all'])*100, 0);
        } else $proc[1]=0;
        if ($value['active_30days'] > 0) {
         $proc[2] = round(($value['active_30days']/$value['sites_all'])*100, 0);
        } else $proc[2]=0;
        if ($value['active_60days'] > 0) {
         $proc[3] = round(($value['active_60days']/$value['sites_all'])*100, 0);
        } else $proc[3]=0;
        if ($value['active_90days'] > 0) {
         $proc[4] = round(($value['active_90days']/$value['sites_all'])*100, 0);
        } else $proc[4]=0;
        if ($value['active_120days'] > 0) {
         $proc[5] = round(($value['active_120days']/$value['sites_all'])*100, 0);
        } else $proc[5]=0;
        if ($value['active_200days'] > 0) {
         $proc[6] = round(($value['active_200days']/$value['sites_all'])*100, 0);
        } else $proc[6]=0;
        
        if ($value['sites'] > 0) {
         $proc[7] = round(($value['sites']/$value['sites_all'])*100, 0);
        } else $proc[7]=0;
        if ($value['landings'] > 0) {
         $proc[8] = round(($value['landings']/$value['sites_all'])*100, 0);
        } else $proc[8]=0;

        
        if (!$value['active_7days']) $value['active_7days'] = 0;
        if (!$value['active_30days']) $value['active_30days'] = 0;
        if (!$value['active_60days']) $value['active_60days'] = 0;
        if (!$value['active_90days']) $value['active_90days'] = 0;
        if (!$value['active_120days']) $value['active_120days'] = 0;
        if (!$value['active_200days']) $value['active_200days'] = 0;
        if (!$value['sites_new']) $value['sites_new'] = 0;
        if (!$value['active']) $value['active'] = 0;
        if ($today==$key) $key = "<strong>"._TODAY."</strong>";

                echo "<tr>
                       <td>".$key."</td>
                       <td>" . $value['sites_all'] . "</td>
                       <td>" . $value['sites_new'] . "</td>
                       <td>" . $value['sites'] . " <span class=small title='"._ANALIT_TEXT1."'>(".$proc[7]."%)</span></td>
                       <td>" . $value['landings'] . " <span class=small title='"._ANALIT_TEXT1."'>(".$proc[8]."%)</span></td>
                       <td>" . $value['active'] . " <span class=small title='"._ANALIT_TEXT1."'>(".$proc[0]."%)</span></td>
                       <td>" . $value['active_7days'] . "  <span class=small title='"._ANALIT_TEXT1."'>(".$proc[1]."%)</span></td>
                       <td>" . $value['active_30days'] . " <span class=small title='"._ANALIT_TEXT1."'>(".$proc[2]."%)</span></td>
                       <td>" . $value['active_60days'] . " <span class=small title='"._ANALIT_TEXT1."'>(".$proc[3]."%)</span></td>
                       <td>" . $value['active_90days'] . " <span class=small title='"._ANALIT_TEXT1."'>(".$proc[4]."%)</span></td>
                       <td>" . $value['active_120days'] ." <span class=small title='"._ANALIT_TEXT1."'>(".$proc[5]."%)</span></td>
                       <td>" . $value['active_200days'] ." <span class=small title='"._ANALIT_TEXT1."'>(".$proc[6]."%)</span></td>
                       </tr>";
            }
        

        echo '</tbody>
            </table>';
    } else {
        echo '<div>'._NODATA.'</div>';
    }                              
    
}

if ($type == 'users') {
    
    if ($stat != false) {
            $data = array();
    if (is_array($stat)) {
        $stat_graph = array_reverse($stat);
        foreach ($stat_graph as $key => $value) {
        if ($value['active'] > 0) {
         $proc[0] = round(($value['active']/$value['users_all'])*100, 0);
        } else $proc[0]=0;
                $data['legends'][] = $key;
                $data['title'][_USERS_ALL1][] = isset($value['users_all']) ? $value['users_all'] : 0;
                $data['title']['активных'][] = isset($value['active']) ? $value['active'] : 0;
                $data['title']['проц. активных'][] = $proc[0];
            
        }
    }
 
    echo chart_line2("chart_line".$type, $data);
    
      echo '<br /> <table class="table table-small">
                                    <thead>
                                        <tr>
                                            <th scope="col">'._DATE.'</th>
                                            <th scope="col">all '.tooltip(_USERS_ALL1, "left").'</th>
                                            <th scope="col">active '.tooltip(_ACTIVE, "left").'</th>
                                            <th scope="col">new '.tooltip(_ANALIT_TEXT6, "left").'</th>
                                            <th scope="col">guest '.tooltip(_ANALIT_TEXT15, "left").'</th>
                                            <th scope="col">block '.tooltip(_ANALIT_TEXT16, "left").'</th>
                                            <th scope="col">online '.tooltip(_ANALIT_TEXT17, "left").'</th>
                                            <th scope="col">nomail '.tooltip(_ANALIT_TEXT18, "left").'</th>
                                            <th scope="col">nopromo '.tooltip(_ANALIT_TEXT19, "left").'</th>
                                            <th scope="col">automoney '.tooltip(_ANALIT_TEXT20, "left").'</th>
                                        </tr>
                                    </thead>
                                    <tbody>';
      
        foreach ($stat as $key => $value) {
            
        if ($value['active'] > 0) {
         $proc[0] = round(($value['active']/$value['users_all'])*100, 0);
        } else $proc[0]=0;
        
        if ($value['new'] > 0 && $value['new_wm'] > 0) {
         $proc[1] = round(($value['new_wm']/$value['new'])*100, 0);
        } else $proc[1]=0;
        if ($value['new'] > 0 && $value['new_adv'] > 0) {
         $proc[2] = round(($value['new_adv']/$value['new'])*100, 0);
        } else $proc[2]=0;
        if ($value['online_contact'] > 0) {
         $proc[3] = round(($value['online_contact']/$value['users_all'])*100, 0);
        } else $proc[3]=0;
        if ($value['get_mail_off'] > 0) {
         $proc[4] = round(($value['get_mail_off']/$value['users_all'])*100, 0);
        } else $proc[4]=0;
        if ($value['promo_mail_off'] > 0) {
         $proc[5] = round(($value['promo_mail_off']/$value['users_all'])*100, 0);
        } else $proc[5]=0;
        if ($value['auto_money'] > 0) {
         $proc[6] = round(($value['auto_money']/$value['users_all'])*100, 0);
        } else $proc[6]=0;

        if (!$value['new']) $value['new'] = 0;
        if (!$value['guests']) $value['guests'] = 0;
        if (!$value['new_wm']) $value['new_wm'] = 0;
        if (!$value['new_adv']) $value['new_adv'] = 0;
        if (!$value['blocked']) $value['blocked'] = 0;
        if (!$value['get_mail_off']) $value['get_mail_off'] = 0;
        if (!$value['promo_mail_off']) $value['promo_mail_off'] = 0;
        if (!$value['active']) $value['active'] = 0;
        if ($today==$key) $key = "<strong>сегодня</strong>";

                echo "<tr>
                       <td>".$key."</td>
                       <td>" . $value['users_all'] . "</td>
                       <td>" . $value['active'] . " <span class=small title='"._ANALIT_TEXT1."'>(".$proc[0]."%)</span></td>
                       <td>" . $value['new'] . "</td>
                       <td>" . $value['guests'] . "</td>
                       <td>" . $value['blocked'] . "</td>
                       <td>" . $value['online_contact'] . " <span class=small title='"._ANALIT_TEXT1."'>(".$proc[3]."%)</span></td>
                       <td>" . $value['get_mail_off'] . " <span class=small title='"._ANALIT_TEXT1."'>(".$proc[4]."%)</span></td>
                       <td>" . $value['promo_mail_off'] ." <span class=small title='"._ANALIT_TEXT1."'>(".$proc[5]."%)</span></td>
                       <td>" . $value['auto_money'] ." <span class=small title='"._ANALIT_TEXT1."'>(".$proc[6]."%)</span></td>
                       </tr>";
            }
        

        echo '</tbody>
            </table>';
    } else {
        echo '<div>'._NODATA.'</div>';
    }                              
    
}
