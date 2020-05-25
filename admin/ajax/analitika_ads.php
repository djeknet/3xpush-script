<?php
set_time_limit(1000);
error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: text/html; charset=utf-8');

require_once("../../include/mysql.php");
require_once("../../include/func.php");
require_once("../../include/info.php");
require_once("../../include/stat.php");
require_once("../forms.php");

if ($config['memcache_ip']) {
$memcached = new Memcache;
$memcached->pconnect($config['memcache_ip'], $config['memcache_port']);
}

$check_login = check_login();
if ($check_login['root']!=1) {
    exit;
}

$lang = isset($_REQUEST['lang']) ? $_REQUEST['lang'] : 'en';
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

if ($type=='all') {
    $where .= "AND type=0 ";
} elseif ($type=='ads') {
    $where .= "AND type=1 ";
} elseif ($type=='sites') {
    $where .= "AND type=3 ";
} elseif ($type=='users') {
    $where .= "AND type=4 ";
} elseif ($type=='payment') {
    $where .= "AND type=5 ";
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
                $data['title']['объявлений'][] = isset($value['myads_all']) ? $value['myads_all'] : 0;
                $data['title']['сайтов'][] = isset($value['sites_all']) ? $value['sites_all'] : 0;
                $data['title']['пользователей'][] = isset($value['users_all']) ? $value['users_all'] : 0;
                $data['title']['COEF'][] = isset($coef) ? $coef : 0;
                $data['title']['user profit'][] = isset($profit_user) ? $profit_user : 0;
                $data['title']['прибыль'][] = isset($profit) ? $profit : 0;
                $data['title']['акт. акков'][] = isset($value['active_users']) ? $value['active_users'] : 0;
                $data['title']['баланс на юз'][] = isset($avg_balance) ? $avg_balance : 0;
            
        }
    }
 
    echo chart_line2("chart_line", $data);

    if ($stat != false) {
      
        echo '<br /> <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">Дата</th>
                                            <th scope="col">us '.tooltip("всего пользователей", "left").'</th>
                                            <th scope="col">act us '.tooltip("активных пользователей", "left").'</th>
                                            <th scope="col">sites '.tooltip("сайтов", "left").'</th>
                                            <th scope="col">ads '.tooltip("объявлений", "left").'</th>
                                            <th scope="col">balance '.tooltip("общий баланс", "left").'</th>
                                            <th scope="col">ac balance '.tooltip("аккаунтов с балансом", "left").'</th>
                                            <th scope="col">avg balance '.tooltip("средний баланс аккаунтов с балансом", "left").'</th>
                                            <th scope="col">money in '.tooltip("пополнений за день", "left").'</th>
                                            <th scope="col">wm '.tooltip("заработок вебмастеров", "left").'</th>
                                            <th scope="col">adv '.tooltip("расход рекламодателей", "left").'</th>
                                            <th scope="col">profit '.tooltip("прибыль проекта", "left").'</th>
                                            <th scope="col">us profit '.tooltip("прибыль с пользователя", "left").'</th>
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
        if ($today==$key) $key = "<strong>сегодня</strong>";

                echo "<tr>
                       <td>".$key."</td>
                       <td>" . $value['users_all'] . "</td>
                       <td>" . $value['active_users'] . " <span class=small title='процент от всех'>(".$proc[0]."%)</span></td>
                       <td>" . $value['sites_all'] . "</td>
                       <td>" . $value['myads_all'] . "</td>
                       <td>" . $value['balance'] . "$</td>
                       <td>" . $value['balance_accounts'] . "  <span class=small title='процент от всех'>(".$proc[1]."%)</span></td>
                       <td>" . $avg[1] . "$</td>
                       <td>" . $value['money_in'] . "$</td>
                       <td>" . $value['wm_money'] . "$ <span title='разница с неделю назад'>(".$wm_diff.")</span></td>
                       <td>" . $value['adv_money'] . "$ <span title='разница с неделю назад'>(".$adv_diff.")</span></td>
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
    
