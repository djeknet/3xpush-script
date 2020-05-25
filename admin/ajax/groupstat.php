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
include("../func/stat_new.php");

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
$update_from = text_filter($_GET['update_from']);
$update_to = text_filter($_GET['update_to']);
$sid = intval($_GET['sid']);
$ref = text_filter($_GET['ref']);
$sended = intval($_GET['sended']);
$sended_type = intval($_GET['sended_type']);
$clicks = intval($_GET['clicks']);
$clicks_type = intval($_GET['clicks_type']);
$status = intval($_GET['status']);

if (!$start_date) $start_date = gettime($settings['days_stat']);

if ($start_date && $end_date) {
    $where = "AND createtime >= '" . $start_date . "' AND createtime <= '" . $end_date . "' ";
} elseif ($start_date) {
    $where = "AND createtime >= '" . $start_date . "' ";
} elseif ($end_date) {
    $where = "AND createtime <= '" . $end_date . "' ";
}
if ($update_from && $update_to) {
    $where = "AND last_update >= '" . $update_from . "' AND last_update <= '" . $update_to . "' ";
} elseif ($update_from) {
    $where = "AND last_update >= '" . $update_from . "' ";
} elseif ($update_to) {
    $where = "AND last_update <= '" . $update_to . "' ";
}
if ($sid) {
    $where .= "AND sid='$sid' ";
}
if ($ref) {
    $where .= "AND referer LIKE '%$ref%' ";
}

if ($sended) {
    if ($sended_type == 0) {
        $sended_types = ">=";
    } elseif ($sended_type == 1) {
        $sended_types = "<=";
    }
    $where .= "AND sended $sended_types '$sended'  ";
}
if ($clicks) {
    if ($clicks_type == 0) {
        $clicks_types = ">=";
    } elseif ($clicks_type == 1) {
        $clicks_types = "<=";
    }
    $where .= "AND clicks $clicks_types '$clicks'  ";
}

if ($status == 1) {
    $where .= "AND del=0  ";
    $statussel[$status] = "selected";
} elseif ($status == 2) {
    $where .= "AND del=1  ";
}
 if ($check_login['root']==1) {
                                     $only_my = intval($_GET['only_my']);
                                     $admin_id = text_filter($_GET['admin_id']);
                                     } else {
                                      $only_my=1;  
                                      }
                                     if ($admin_id) {
                                     $where .= "AND admin_id='$admin_id'  ";
                                     $dopurl .= "&admin_id=$admin_id";
                                     }
                                      if ($only_my==1) {
                                      $where .= "AND admin_id=".$check_login['getid']." ";
                                      } 

if ($config['memcache_ip']) {
$code = md5('groupstat'.$where.$type);
$stat = $memcached->get($code);
if (!$stat) {
$stat = group_stat_new($where, $type);
$memcached->set($code, $stat, MEMCACHE_COMPRESSED, time() + 300);
}
} else {
$stat = group_stat_new($where, $type);
}
$allsubs = $stat['all'];

if ($type == 'regions') {

    $data = array();
    if (is_array($stat)) {
        foreach ($stat['country'] as $key => $value) {
            if ($key=='DEL') continue;
            $subs = $value['subs'];
            if ($subs > 0) $ctr = round(($subs / $allsubs) * 100, 0); else $ctr = 0;
            if ($ctr >= 1) {
                $data['title'][] = $key;
                $data['data'][] = $value['subs'];
            }
        }
    }
    echo chart_pie("pieChart1", $data);

    if ($stat != false) {
        echo '<br /> <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">' . _REGION . '</th>
                                            <th scope="col">' . _SUBS . '</th>
                                            <th scope="col">' . _STATUSACTIVE . '</th>
                                            <th scope="col">' . _SENDED . '</th>
                                        </tr>
                                    </thead>
                                    <tbody>';


        foreach ($stat['country'] as $key => $value) {
            if ($key=='DEL') continue;
            if (!$key) $key = 'unknown';
            $subs = $value['subs'];
            $sended = $value['sended'];
            $unsubs = $stat['country']['DEL'][$key];

            if ($subs > 0) $ctr = round(($subs / $allsubs) * 100, 0); else $ctr = 0;
            if ($ctr >= 1) {
                if ($unsubs) {
                    $actproc = ($unsubs / $subs) * 100;
                    $actproc = round(100 - $actproc, 0);
                } else $actproc = 100;

                if (!$sended) {
                    $sended = 0;
                    $send_per_user = 0;
                } else {
                    $send_per_user = round($sended / $subs, 1);
                }
                echo "<tr>
                                            <th scope=\"row\"><img src=images/flags/" . $key . ".gif width=20 height=12 align=absmiddle> $key</th>
                                            <td>" . $subs . " (" . $ctr . "%)</td>
                                            <td>" . $actproc . "%</td>
                                            <td>" . $sended . " (<span title='" . _SENDEDUSER . "'>" . $send_per_user . "</span>)</td>
                                        </tr>";
            }
        }

        echo '</tbody>
            </table>';
    }else {
        echo '<div>'._NODATA.'</div>';
    }

    exit;

}

if ($type == 'os') {

     $data = array();
    if (is_array($stat)) {
        foreach ($stat['os'] as $key => $value) {
            if ($key=='DEL') continue;
            $subs = $value['subs'];
            if ($subs > 0) $ctr = round(($subs / $allsubs) * 100, 0); else $ctr = 0;
            if ($ctr >= 1) {
                $data['title'][] = $key;
                $data['data'][] = $value['subs'];
            }
        }
    }
    echo chart_pie("pieChart2", $data);

    if ($stat != false) {
    echo '<br />
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">' . _OS . '</th>
                                            <th scope="col">' . _SUBS . '</th>
                                            <th scope="col">' . _STATUSACTIVE . '</th>
                                            <th scope="col">' . _SENDED . '</th>
                                        </tr>
                                    </thead>
                                    <tbody>';


        foreach ($stat['os'] as $key => $value) {
            if ($key=='DEL') continue;
            if (!$key) $key = 'unknown';
            $subs = $value['subs'];
            $sended = $value['sended'];
            $unsubs = $stat['os']['DEL'][$key];

            if ($subs > 0) $ctr = round(($subs / $allsubs) * 100, 0); else $ctr = 0;
            if ($ctr >= 1) {
                if ($unsubs) {
                    $actproc = ($unsubs / $subs) * 100;
                    $actproc = round(100 - $actproc, 0);
                } else $actproc = 100;

                if (!$sended) {
                    $sended = 0;
                    $send_per_user = 0;
                } else {
                    $send_per_user = round($sended / $subs, 1);
                }
                echo "<tr>
                                            <th scope=\"row\"><img src=\"images/os/" . $key . ".png\" border=0 align=absmiddle title=\"" . $key . "\"> " . $key . "</th>
                                            <td>" . $subs . " (" . $ctr . "%)</td>
                                            <td>" . $actproc . "%</td>
                                            <td>" . $sended . " (<span title='" . _SENDEDUSER . "'>" . $send_per_user . "</span>)</td>
                                        </tr>";
            }
        }
        echo '</tbody>
                                </table>';
    }
    else {
        echo '<div>'._NODATA.'</div>';
    }



    exit;

}


if ($type == 'devices') {

     $data = array();
    if (is_array($stat)) {
        foreach ($stat['device'] as $key => $value) {
            if ($key=='DEL') continue;
            $subs = $value['subs'];
            if ($subs > 0) $ctr = round(($subs / $allsubs) * 100, 0); else $ctr = 0;
            if ($ctr >= 1) {
                $data['title'][] = $key;
                $data['data'][] = $value['subs'];
            }
        }
    }
    echo chart_pie("pieChart4", $data);

    if ($stat != false) {
    echo '<br />
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">' . _TYPE . '</th>
                        <th scope="col">' . _SUBS . '</th>
                        <th scope="col">' . _STATUSACTIVE . '</th>
                        <th scope="col">' . _SENDED . '</th>
                    </tr>
                </thead>
                <tbody>';


        foreach ($stat['device'] as $key => $value) {
            if ($key=='DEL') continue;
            if (!$key) $key = 'unknown';
            $subs = $value['subs'];
            $sended = $value['sended'];
            $unsubs = $stat['device']['DEL'][$key];

            if ($subs > 0) $ctr = round(($subs / $allsubs) * 100, 2); else $ctr = 0;

            if ($unsubs) {
                $actproc = ($unsubs / $subs) * 100;
                $actproc = round(100 - $actproc, 0);
            } else $actproc = 100;

            if (!$sended) {
                $sended = 0;
                $send_per_user = 0;
            } else {
                $send_per_user = round($sended / $subs, 1);
            }
            echo "<tr>
                                            <th scope=\"row\"><img src=\"images/device/" . $key . ".png\" border=0 align=absmiddle title=\"" . $key . "\"> " . $key . "</th>
                                            <td>" . $subs . " (" . $ctr . "%)</td>
                                            <td>" . $actproc . "%</td>
                                            <td>" . $sended . " (<span title='" . _SENDEDUSER . "'>" . $send_per_user . "</span>)</td>
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

    $data = array();
    if (is_array($stat)) {
        foreach ($stat['agent'] as $key => $value) {
            if ($key=='DEL') continue;
            $subs = $value['subs'];
            if ($subs > 0) $ctr = round(($subs / $allsubs) * 100, 0); else $ctr = 0;
            if ($ctr >= 1) {
                $data['title'][] = $key;
                $data['data'][] = $value['subs'];
            }
        }
    }
    echo chart_pie("pieChart3", $data);

    if ($stat != false) {
    echo '<br />
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">' . _BROWSER . '</th>
                        <th scope="col">' . _SUBS . '</th>
                        <th scope="col">' . _STATUSACTIVE . '</th>
                        <th scope="col">' . _SENDED . '</th>
                    </tr>
                </thead>
                <tbody>';


        foreach ($stat['agent'] as $key => $value) {
            if ($key=='DEL') continue;
            if (!$key) $key = 'unknown';
            $subs = $value['subs'];
            $sended = $value['sended'];
            $unsubs = $stat['agent']['DEL'][$key];

            if ($subs > 0) $ctr = round(($subs / $allsubs) * 100, 2); else $ctr = 0;
            if ($ctr >= 1) {
                if ($unsubs) {
                    $actproc = ($unsubs / $subs) * 100;
                    $actproc = round(100 - $actproc, 0);
                } else $actproc = 100;

                if (!$sended) {
                    $sended = 0;
                    $send_per_user = 0;
                } else {
                    $send_per_user = round($sended / $subs, 1);
                }
                echo "<tr>
                                            <th scope=\"row\"><img src=\"images/browser/" . $key . ".png\" border=0 align=absmiddle title=\"" . $key . "\"> " . $key . "</th>
                                            <td>" . $subs . " (" . $ctr . "%)</td>
                                            <td>" . $actproc . "%</td>
                                            <td>" . $sended . " (<span title='" . _SENDEDUSER . "'>" . $send_per_user . "</span>)</td>
                                        </tr>";
            }
        }
        echo '</tbody></table>';
    }
    else {
        echo '<div>'._NODATA.'</div>';
    }

    exit;
}

// brand
if ($type == 'brand') {

     $data = array();
    if (is_array($stat)) {
        foreach ($stat['brand'] as $key => $value) {
            if ($key=='DEL') continue;
            $subs = $value['subs'];
            if (!$key) $key = 'unknown';
            if ($subs > 0) $ctr = round(($subs / $allsubs) * 100, 0); else $ctr = 0;
            if ($ctr >= 10) {
                $data['title'][] = $key;
                $data['data'][] = $value['subs'];
            } else {
                $data['title'][99] = _OTHER;
                $data['data'][99] += $value['subs'];
            }
        }
    }
    echo chart_pie("pieChart5", $data);
    if ($stat != false) {
    echo '<br />
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">' . _MODEL . '</th>
                                            <th scope="col">' . _SUBS . '</th>
                                            <th scope="col">' . _STATUSACTIVE . '</th>
                                            <th scope="col">' . _SENDED . '</th>
                                        </tr>
                                    </thead>
                                    <tbody>';


        foreach ($stat['brand'] as $key => $value) {
            if ($key=='DEL') continue;
            if (!$key) $key = 'unknown';
            $subs = $value['subs'];
            $sended = $value['sended'];
            $unsubs = $stat['brand']['DEL'][$key];

            if ($subs > 0) $ctr = round(($subs / $allsubs) * 100, 2); else $ctr = 0;
            if ($ctr >= 1) {
                if ($unsubs) {
                    $actproc = ($unsubs / $subs) * 100;
                    $actproc = round(100 - $actproc, 0);
                } else $actproc = 100;

                if (!$sended) {
                    $sended = 0;
                    $send_per_user = 0;
                } else {
                    $send_per_user = round($sended / $subs, 1);
                }
                echo "<tr>
                                            <th scope=\"row\">" . $key . "</th>
                                            <td>" . $subs . " (" . $ctr . "%)</td>
                                            <td>" . $actproc . "%</td>
                                            <td>" . $sended . " (<span title='" . _SENDEDUSER . "'>" . $send_per_user . "</span>)</td>
                                        </tr>";
            }
        }
        echo '</tbody>
                                </table>';
    }
    else {
        echo '<div>'._NODATA.'</div>';
    }

    exit;

}

//network
if ($type == 'network') {

     $data = array();
    if (is_array($stat)) {
        foreach ($stat['network'] as $key => $value) {
            if ($key=='DEL') continue;
            $subs = $value['subs'];
            if ($subs > 0) $ctr = round(($subs / $allsubs) * 100, 0); else $ctr = 0;
            if ($ctr > 1) {
                $data['title'][] = $key;
                $data['data'][] = $value['subs'];
            } else {
                $data['title'][99] = _OTHER;
                $data['data'][99] += $value['subs'];
            }
        }
    }
    echo chart_pie("pieChart6", $data);

    if ($stat != false) {
    echo '<br />
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">' . _NETWORK . '</th>
                                            <th scope="col">' . _SUBS . '</th>
                                            <th scope="col">' . _STATUSACTIVE . '</th>
                                            <th scope="col">' . _SENDED . '</th>
                                        </tr>
                                    </thead>
                                    <tbody>';


        $i = 0;
        foreach ($stat['network'] as $key => $value) {
            if ($key=='DEL') continue;

            if ($i <= 10) {
                $subs = $value['subs'];
                $sended = $value['sended'];
                $unsubs = $stat['network']['DEL'][$key];

                if ($subs > 0) $ctr = round(($subs / $allsubs) * 100, 2); else $ctr = 0;

                if ($unsubs) {
                    $actproc = ($unsubs / $subs) * 100;
                    $actproc = round(100 - $actproc, 0);
                } else $actproc = 100;

                if (!$sended) {
                    $sended = 0;
                    $send_per_user = 0;
                } else {
                    $send_per_user = round($sended / $subs, 1);
                }
                echo "<tr>
                                            <th scope=\"row\">" . $key . "</th>
                                            <td>" . $subs . " (" . $ctr . "%)</td>
                                            <td>" . $actproc . "%</td>
                                            <td>" . $sended . " (<span title='" . _SENDEDUSER . "'>" . $send_per_user . "</span>)</td>
                                        </tr>";
                $i++;
            }
        }

        echo '</tbody>
                                </table>';
    }
    else {
        echo '<div>'._NODATA.'</div>';
    }

    exit;

}

