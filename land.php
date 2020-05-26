<?php
// 3xpush Script - Push Subscription Management System 
// Copyright 2020 Evgeniy Orel
// Site: https://script.3xpush.com/
// Email: script@3xpush.com
// Telegram: @Evgenfalcon
//
// ======================================================================
// This file is part of 3xpush Script.
//
// 3xpush Script is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// 3xpush Script is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with 3xpush Script.  If not, see <https://www.gnu.org/licenses/>.
//======================================================================

ini_set('display_errors',false);
error_reporting(0);

header('Content-Type: text/html; charset=utf-8');

require_once("include/mysql.php");
require_once("include/func.php");
require_once("include/info.php");
include_once 'include/devicedetector/spyc/Spyc.php';
include_once 'include/devicedetector/autoload.php';

if ($config['memcache_ip']) {
$memcached = new Memcache;
$memcached->pconnect($config['memcache_ip'], $config['memcache_port']);
}

use DeviceDetector\DeviceDetector;
$lang = get_lang();

if ($lang != 'ru') $lang='en';

include("langs/".$lang.".php");

$dev = intval($_GET['dev']);
$id = intval($_GET['id']);
$landing_id = intval($_GET['lid']); // id лендинга из таблицы landings
$test = intval($_GET['test']); // id лендинга из таблицы landings
$subid = text_filter($_GET['subid']);
$tag = text_filter($_GET['tag']);
$price = text_filter($_GET['price']);
if (!$price) $price=0;
$settings = settings();
//$host = get_host();
$host = $settings['domain_link'];

if ($id || $test==1) {
if ($dev==1) {
  $check_login = check_login(); 
  if ($check_login['root']!=1) $dev=0; 
}
$agent = getenv("HTTP_USER_AGENT");

$deviceDetector = new DeviceDetector($agent);
$deviceDetector->parse();

$br = $deviceDetector->getClient();

list($brid) = $db->sql_fetchrow($db->sql_query("SELECT id FROM  browsers WHERE `key` = '".$br['name']."'"));
    if (!$test) {
list($html, $land_options, $land_id, $admin_id) = $db->sql_fetchrow($db->sql_query("SELECT html, land_options, land_id, admin_id FROM sites WHERE id='$id'"));

if (stripos($land_id, ",") !== false) {
if ($config['memcache_ip']) {   
$code = md5("last_land".$id);
$last_land = $memcached->get($code);  
    }
   
    $lands = explode(',', $land_id);
    
    // если в кэше есть id последнего показанного лендинга, то удляем его из массива
    if ($last_land) {
   if (($key = array_search($last_land, $lands)) !== false) {
    unset($lands[$key]);
    }
    $lands = array_values($lands);
    }
    
    $count = count($lands);
    $count = $count - 1;
    $rand = rand(0, $count);
    $land_id = $lands[$rand];
    
     if ($config['memcache_ip']) {   
$memcached->set($code, $land_id, false, time() + 600);    
    }
    
    }
 
$land_options = json_decode($land_options, true);

$db->sql_query('INSERT INTO daystat (date, admin_id, sid, subid, land_views, traf_cost)
        VALUES (CURRENT_DATE(), "'.$admin_id.'", ' . $id . ', "'.$subid.'", 1, "'.$price.'")
         ON DUPLICATE KEY UPDATE land_views=land_views+1, traf_cost=traf_cost+'.$price.'')  or $error = mysqli_error();

$db->sql_query('INSERT INTO browser_stat (date, browser_id, admin_id, sid, land_views, traf_cost)
        VALUES (CURRENT_DATE(), "'.$brid.'", "'.$admin_id.'", ' . $id . ', 1, "'.$price.'")
         ON DUPLICATE KEY UPDATE land_views=land_views+1, traf_cost=traf_cost+'.$price.'')  or $error = mysqli_error();
         
$db->sql_query('INSERT INTO landing_stat (id, date, admin_id, sid, subid, land_id, views)
        VALUES (NULL, CURRENT_DATE(), "'.$admin_id.'", ' . $id . ', "'.$subid.'", '.$land_id.', 1)
         ON DUPLICATE KEY UPDATE views=views+1')  or $error = mysqli_error();  
                }


if (!$land_options[0]['psx_time']) $land_options[0]['psx_time'] = 0;
if (!$land_options[0]['blocksite']) $land_options[0]['blocksite'] = 0;
if (!$land_options[0]['hasBlockCross']) $land_options[0]['hasBlockCross'] = 0;
if (!$land_options[0]['repeat']) $land_options[0]['repeat'] = 0;
if ($land_options[0]['nourllink']!=1) $land_options[0]['link'] = '';

if ($landing_id) $land_id = $landing_id; // если лендинг передан по ссылке, то берем его
if ($land_id) {
$db->sql_query("UPDATE landings SET views=views+1 WHERE id=" . $land_id . "");

 list($html) = $db->sql_fetchrow($db->sql_query("SELECT html FROM landings WHERE id='$land_id'"));
 $land_options[0]['tab']=1;
}


require_once 'admin/models/LangsModel.php';
LangsModel::getInstance()->replaceMacros($html);

$html = htmlspecialchars_decode($html, ENT_QUOTES);

if ($dev==1) {
echo "var psx_host = '".$host."';
var psx_site_id = '".$id."';
var psx_sub_id = '".$subid."';
var psx_tag = '".$tag."';
var psx_time = '".$land_options[0]['psx_time']."';
var blocksite = ".$land_options[0]['blocksite'].";
var hasBlockCross = ".$land_options[0]['hasBlockCross'].";
var blockText = '".$land_options[0]['blockText']."';
var repeat = '".$land_options[0]['repeat']."';
var psx_lid = '".$land_id."';
var link = '".$land_options[0]['link']."';<br>";

echo "errors:<br>";
print_r($error);
} else {
echo $html;
}

$ref = htmlspecialchars(stripslashes(getenv("HTTP_REFERER")));
if ($test || $dev) exit;

echo "<script>
'use strict';
var psx_host = '".$host."';
var psx_site_id = '".$id."';
var psx_sub_id = '".$subid."';
var psx_tag = '".$tag."';
var psx_time = '".$land_options[0]['psx_time']."';
var blocksite = ".$land_options[0]['blocksite'].";
var hasBlockCross = ".$land_options[0]['hasBlockCross'].";
var blockText = '".$land_options[0]['blockText']."';
var repeat = '".$land_options[0]['repeat']."';
var psx_lid = '".$land_id."';
var link = '".$land_options[0]['link']."';
var userfrom = '".$ref."';
</script>\n";


if ($land_options[1]['tab']==1) {
echo "<script>
let s = document.createElement('script');
s.src=\"https://\"+psx_host+\"/links.js\";
document.getElementsByTagName('head')[0].appendChild(s);
let site_id = '".$id."',
sub_id = '".$subid."', 
tag = '".$tag."', 
repeat = '".$land_options[1]['repeat']."', 
fon ='',
text ='".$land_options[1]['text']."',
type = ".$land_options[1]['type'].";
</script>";

}

if ($land_options[2]['tab']==1) {

echo "<script>
let s2 = document.createElement('script'),
fb = document.createElement('script');
fb.src=\"https://www.gstatic.com/firebasejs/5.2.0/firebase.js\";
document.getElementsByTagName('head')[0].appendChild(fb);
s2.src=\"https://\"+psx_host+\"/closing-content.js\";
document.getElementsByTagName('head')[0].appendChild(s2);
</script>";
}
} else {
echo 'empty id';
exit;
}

if ($land_options[0]['tab']==1) {
?>
<script src="admin/js/jquery.min.js"></script>
<script src="https://www.gstatic.com/firebasejs/5.2.0/firebase.js"></script>
<script>
$('body').bind('click', function() {
  fullScreen(document.documentElement) ;
});

function fullScreen(element) {
  if(element.requestFullscreen) {
    element.requestFullscreen();
  } else if(element.webkitrequestFullscreen) {
    element.webkitRequestFullscreen();
  } else if(element.mozRequestFullscreen) {
    element.mozRequestFullScreen();
  }
}
    function getCookie(name) {
        let matches = document.cookie.match(new RegExp(
            "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
        ));
        return matches ? decodeURIComponent(matches[1]) : undefined;
    }


    function setCookie(name, value, options) {
        options = options || {};

        let expires = options.expires;

        if (typeof expires === "number" && expires) {
            var d = new Date();
            d.setTime(d.getTime() + expires * 1000);
            expires = options.expires = d;
        }
        if (expires && expires.toUTCString) {
            options.expires = expires.toUTCString();
        }

        value = encodeURIComponent(value);

        let updatedCookie = name + "=" + value;

        for (let propName in options) {
            updatedCookie += "; " + propName;
            let propValue = options[propName];
            if (propValue !== true) {
                updatedCookie += "=" + propValue;
            }
        }

        document.cookie = updatedCookie;
    }

    function getParam(paramName) {
        let search = window.location.search.slice(1);
        let paramIndex = search.indexOf(paramName),
            paramStr;

        if (paramIndex !== -1) {
            if (search.indexOf('&', paramIndex) !== -1) {
                paramStr = search.slice(paramIndex, search.indexOf('&', paramIndex));
            } else {
                paramStr = search.slice(paramIndex);
            }

            let param = paramStr.slice(paramStr.indexOf('=') + 1);

            return param;
        }
    }


    let text = document.querySelector('.redirect__text'),
        img = document.querySelector('.redirect__img');


    if (!link) {
        let link = getParam('url');
    }
    if (!repeat) {
        repeat=0;
    }

    let repeatCount = getCookie('repeatCount');

    if (!repeatCount || repeatCount === "NaN") {
        setCookie('repeatCount', '0', {domain: '.'+psx_host});
    }


    function randomInteger(min, max) {
        var rand = min + Math.random() * (max + 1 - min);
        rand = Math.floor(rand);
        return rand;
    }

    function subscribe() {

        messaging.requestPermission()
            .then(function () {

                messaging.getToken()
                    .then(function (currentToken) {
                        if (currentToken) {
                            sendTokenToServer(currentToken);
                            repeatCount = getCookie('repeatCount');
                            repeatCount++;

                            setCookie('repeatCount', String(repeatCount), {domain: '.'+psx_host});

                            if (+repeat <= +repeatCount && link) {
                                setTimeout(function () {
                                    window.location = link;
                                }, 1000);

                                text.innerHTML = "<?php echo SUBSCRIBETHANKS; ?>";
                                img.src = "img/ok.png"
                            } else if(repeat!=0) {
                                window.location.host = randomInteger(1, 1000000) + '.'+psx_host;
                            }


                        } else {
                            console.warn('cant get token');
                            setTokenSentToServer(false);
                        }
                    }).catch(function (err) {
                    console.warn('get token error', err);
                    setTokenSentToServer(false);

                    setTimeout(function () {
                        document.location.reload(true);
                    }, 2000);
                });
            })
            .catch(function (err) {
                console.warn('not allow to view message', err);

                var xhr = new XMLHttpRequest();
                xhr.open("GET", 'https://'+psx_host+'/req.php?type=7&sid=' + psx_site_id+'&sub='+psx_sub_id+'&lid='+psx_lid, true);
                xhr.send();
                   if (link) {
                setTimeout(function () {
                window.location = link;
                }, 1000);
                } else {
                setTimeout(function () {
                window.location.host = randomInteger(1, 1000000) + '.'+psx_host;
                }, 1000);    
                }
            });
    }

    function sendTokenToServer(currentToken) {
        if (!isTokenSentToServer(currentToken)) {

            httpRequest = new XMLHttpRequest();
            httpRequest.open('GET', 'https://'+psx_host+'/create.php?type=1&sid='+psx_site_id+'&sub='+psx_sub_id+'&tag='+psx_tag+'&lid='+psx_lid+'&t='+currentToken+'&uf='+userfrom, true);
            httpRequest.send();

            setTokenSentToServer(currentToken);
        } else {
            console.log('token already sended');
        }
    }

    function isTokenSentToServer(currentToken) {
        return window.localStorage.getItem('sentFirebaseMessagingToken') == currentToken;
    }

    function setTokenSentToServer(currentToken) {
        window.localStorage.setItem(
            'sentFirebaseMessagingToken',
            currentToken ? currentToken : ''
        );
    }


    function init() {
        // Initialize Firebase
<?php
  echo $settings['firebase_conf'];
?>
        try {

            firebase.initializeApp(config);

            messaging = firebase.messaging();

            if (Notification.permission !== 'granted') {
                console.log('try to subscribe');

                var xhr = new XMLHttpRequest();

                xhr.open("GET", 'https://'+psx_host+'/req.php?type=1&sid=' + psx_site_id+'&sub='+psx_sub_id+'&lid='+psx_lid, true);

                xhr.onreadystatechange = function(){
                    if (this.readyState==4) {

                            localStorage.clear();
                            if (!localStorage.getItem('sentFirebaseMessagingToken')) {
                                setTimeout(subscribe(), psx_time);
                            }

                        var x = 0;
                    }
                };

                xhr.send();

            } else if (link) {
                window.location = link;
            }

        }catch (e) {
            console.log('cant init '+e);
            setTimeout(init,1000);
        }
    }

    var blockOverlay = document.createElement('div');

    if (typeof blocksite !== "undefined") {
        if (blocksite) {
            blockOverlay.id = 'block-overlay';
            blockOverlay.innerHTML = `
        <div style="position: fixed; top: 0; left: 0; bottom: 0; right: 0; background: rgba(0, 0, 0, .7); display: flex; justify-content: center">
            <div style="position:absolute; top: 35%; max-width: 350px; font-family: Helvetica, sans-serif; font-size: 25px; font-weight: 500; color: #fff; text-align: center;">
                ${typeof blockText !== "undefined" ? blockText ? blockText : `<?php echo BLOCKTEXT; ?>` : `<?php echo BLOCKTEXT; ?>`}
                    </div>
${typeof hasBlockCross !== "undefined"
                ? hasBlockCross
                    ? `<div style="position: absolute; top: 16px; right: 16px; width: 32px; height: 32px; cursor: pointer" class="block-overlay__cross">
                         <span style="position:absolute; transform: rotate(45deg); left: calc(50% - 2px); display: block; width: 4px; height: 32px; background: #fff; border-radius: 5px"></span>
                         <span style="position:absolute; transform: rotate(-45deg); right: calc(50% - 2px); display: block; width: 4px; height: 32px; background: #fff; border-radius: 5px"></span>
                       </div>`
                    : ``
                : ``}
        </div>
        `;
        }
    }

    if ('Notification' in window) {
        if (Notification.permission === 'granted' && link) {
            setTimeout(function () {
                window.location = link;
            }, 2000);
        }
          if (Notification.permission === 'default') {
            document.querySelector('body').appendChild(blockOverlay);
        }

        init();
    }

     blockOverlay.addEventListener('click', function (e) {
        if (e.target.closest('.block-overlay__cross')) {
            blockOverlay.style.display = 'none'
        }
    })


</script>

<?php
}
?>
