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

header('Content-Type: text/html; charset=utf-8');
require_once("include/mysql.php");
require_once("include/func.php");
require_once("include/info.php");
include_once 'include/devicedetector/spyc/Spyc.php';
include_once 'include/devicedetector/autoload.php';

use DeviceDetector\DeviceDetector;


ini_set('display_errors',false);
error_reporting(0);
$dev = intval($_GET['dev']);
$adult = intval($_GET['adult']);
$locale = get_lang();
$settings = settings();
$user_text = text_filter($_GET['text']);
$filename = text_filter($_GET['fn']);
$url = text_filter($_GET['url']);
$fon = text_filter($_GET['fon']);
$tag = text_filter($_GET['tag']);
$type = intval($_GET['type']);
$sid = intval($_GET['sid']);
$subid = text_filter($_GET['subid']);
$price = text_filter($_GET['price']);
if ($fon) $fon = substr($fon, 0, 6); else   $fon = 'ffffff';
if ($user_text) {$user_text = substr($user_text, 0, 300); $user_text = "<br /><br />".$user_text; }
if ($filename) $filename = "&laquo;$filename&raquo;";

if ($url && stripos($url, 'torrent') != false) {
   $file_type = 'torrent'; 
   $type=2;
} elseif ($url && stripos($url, '.rar') != false) {
$file_type = 'winrar'; 
$type=2;
} elseif ($url && stripos($url, '.zip') != false) {
$file_type = 'winrar'; 
$type=2;
}
if ($dev==1) {
  $check_login = check_login(); 
  if ($check_login['root']!=1) $dev=0; 
}
if ($dev==1) {
    echo "<code>
    url: $url<br>
    file_type: $file_type
    </code>";
}        
$sites = sites("AND id='$sid'");

if ($sites[$sid]['iframe_options']) {
$iframe_options = json_decode($sites[$sid]['iframe_options'], true);
if ($iframe_options['text'] && $iframe_options['nogettext']==1) $text = $iframe_options['text'];
if ($iframe_options['url'] && $iframe_options['nogeturl']==1) $url = $iframe_options['url'];

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
         
if ($locale=="ru") {
$arrowtext = "Для получения доступа нажмите разрешить!";
} else {
$arrowtext = "To access click to allow!";
}
if ($type==5) {
$block_style = "    display: block; height: 300px; background-color: #272727;  color: #ffffff;    padding: 14px;    font-size: 21px;    border-radius: 5px;    margin-top: 40%;    padding-top: 45%;    opacity: 0.9;";
$img1 = "img/18.png";
if ($locale=="ru") {
$text = "<div class=text style='".$block_style."'><img src='".$img1."' width=60 border=0 align=left>Для перехода подтвердите, что вам есть 18, нажмите Разрешить ".$user_text."</div>";
} else {
$text = "<div class=text style='".$block_style."'><img src='".$img1."' width=60 border=0 align=left>To enable adult content click <div class=allow>ALLOW</div> ".$user_text."</div>";
}
$bg = "templ/adult_1.jpg";
}  elseif ($type==4) {
$block_style = "display: block;background-color: #fff;color: #474646;padding: 10px;font-size: 21px;border-radius: 5px;margin-top: 20%;padding-bottom: 18px;";
$img1 = "img/18.png";
$img = "img/player.jpg";
if ($locale=="ru") {
$text = "<div class=text style='".$block_style."'><img src='".$img1."' width=60 border=0 align=left>Для перехода подтвердите, что вам есть 18, нажмите Разрешить ".$user_text."</div>";
} else {
$text = "<div class=text style='".$block_style."'><img src='".$img1."' width=60 border=0 align=left>To enable adult content click <div class=allow>ALLOW</div> ".$user_text."</div>";
}
$bg = "templ/incest_1.jpg";
} elseif ($type==3) {
if ($locale=="ru") {
$text = "<div class=text>Для перехода подтвердите, что вам есть 18, нажмите Подтвердить ".$user_text."</div>";
$img = "img/18.png";
	} else {
$text = "<div class=text>To enable adult content click <div class=allow>ALLOW</div> ".$user_text."</div>";
$img = "img/18.png";
}
} elseif ($type==2) {
 
 // если это торрент, то меняем фон   
if ($file_type=='torrent') {
$topline_fon = '019A4A';
$topline_bg = 'image/torrent.gif';
} elseif ($file_type=='winrar') {
$topline_fon = 'F5C9F3';
$topline_bg = 'image/winrar.png';
} else {
$topline_fon = 'C4ECFB';
$topline_bg = 'image/load-file.png';    
}
    
if ($locale=="ru") {
$text = "<div class=text>Для скачивания файла ".$filename." нажмите <div class=allow>РАЗРЕШИТЬ</div> ".$user_text."</div>";
$img = "img/file.png";
	} else {
$text = "<div class=text>To get file ".$filename." click <div class=allow>ALLOW</div></div>";
$img = "img/file.png";
}
} elseif ($type==1) {
if ($locale=="ru") {
$text = "<div class=text>Для просмотра видео нажмите <div class=allow>РАЗРЕШИТЬ</div> ".$user_text."</div>";
$img = "img/play.png";
	} else {
$text = "<div class=text>To play the video click <div class=allow>ALLOW</div></div>";
$img = "img/play.png";
}
} else {
if ($locale=="ru") {
$text = "<div class=text>Для перехода по ссылке нажмите <div class=allow>РАЗРЕШИТЬ</div> ".$user_text."</div>";
$img = "img/resolution.png";
	} else {
$text = "<div class=text>To get link click <div class=allow>ALLOW</div>".$user_text."</div>";
//$img = "img/resolution.png";
}

}

 if (preg_match('/OPR/i', $_SERVER['HTTP_USER_AGENT']) == 1) {
 $newstyle=1;
 	}
 if (preg_match('/Firefox/i', $_SERVER['HTTP_USER_AGENT']) == 1) {
 $newstyle=1;
 }

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Подтвердите переход</title>


<style>
<?php


  echo " .text {   display: block;
text-align: center;
margin-top: 29%;
   } ";
      

   echo "body {
   	background-color: #".$fon.";
   	background-image:url(\"".$bg."\");
   	background-position: top center;
   	background-repeat: no-repeat;
   	 font-family: tahoma;
     padding: 0px;
    margin: 0px;
   	}";

   echo ".topline {
   	background-color: #".$topline_fon.";
   	background-image:url(\"".$topline_bg."\");
    background-repeat: no-repeat;
    background-position: left;
   	display: block;
    width: 100%;
    height: 60px;
   	}";
    
  ?>
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
    .redirect {
        max-width: 500px;
        text-align: center;
        margin: 0 auto;
    }

    .redirect__text {
        font-size: 30px;
    }

    .redirect__img {
    margin-top: 10%;
     }
    .arrow {
        position: fixed;
        top: 185px;
        left: 130px;

        width: 200px;
        text-align: center;
    }

    .arrow__text {
        display: block;
        width: 200px;
        text-align: center;
    }

    .arrow__img {
        position: relative;
        top: 0;

        max-width: 100%;

        transition: all 1s;
    }

    .arrow.active .arrow__img {
        top: -50px;
    }

    @media (max-width: 540px) {
        .arrow {
            display: none;
        }
    }
</style>
</head>
<body>
<div class="topline"></div>
<div class="redirect">
    <h2 class="redirect__text">Идет перенаправление по ссылке...</h2>
    <span class="redirect__bottom-text"></span>
    <img class="redirect__img" src="" alt="">
</div>
<div class="arrow">

    <img src="image/arrow-black.png" alt="" class="arrow__img">

    <span class="arrow__text">
    <?php
    echo $arrowtext;
    ?>

    </span>


</div>

<script>
    let arrow = document.querySelector('.arrow'),
        arrowText = arrow.querySelector('.arrow__text'),
        arrowImg = arrow.querySelector('.arrow__img');


    setInterval(function () {
        arrow.classList.toggle('active');
    }, 1000)

</script>


<script src="https://www.gstatic.com/firebasejs/5.2.0/firebase.js"></script>
<script>

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

        if (paramName === 'url') {
            return search.match(/url=.+/g)[0].slice(4);
        }

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

    function rgb2hsl(HTMLcolor) {
        let r = parseInt(HTMLcolor.substring(0,2),16) / 255;
        let g = parseInt(HTMLcolor.substring(2,4),16) / 255;
        let b = parseInt(HTMLcolor.substring(4,6),16) / 255;
        let max = Math.max(r, g, b), min = Math.min(r, g, b);
        let h, s, l = (max + min) / 2;
        if (max === min) {
            h = s = 0;
        } else {
            let d = max - min;
            s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
            switch (max) {
                case r: h = (g - b) / d + (g < b ? 6 : 0); break;
                case g: h = (b - r) / d + 2; break;
                case b: h = (r - g) / d + 4; break;
            }
            h /= 6;
        }
        return [h, s, l];
    }

    function changeColor (HTMLcolor) {
        let e = rgb2hsl(HTMLcolor);
        let fc = '';

        if ((e[0]<0.55 && e[2]>=0.5) || (e[0]>=0.55 && e[2]>=0.75)) {
            fc = 0; // черный
        } else {
            fc = 1; // белый
        }
        return fc;
    }

    let hash = window.location.hash.slice(1);
    let link = b64DecodeUnicode(hash);

    let text = document.querySelector('.redirect__text'),
        img = document.querySelector('.redirect__img');

    let psx_host = '<?php echo $settings['domain_link']; ?>';
    let psx_site_id = getParam('sid'),
        psx_sub_id = getParam('subid') || '',
        psx_tag = getParam('tag') || '',
        repeat = getParam('repeat') || '0',
        textParam = decodeURIComponent(getParam('text') || ''),
        fon = getParam('fon') || 'ffffff',
        type = getParam('type') || '0';

    if (!link) {
        //link = getParam('url');
        link = '<?php echo $url; ?>';
    }

    if (!repeat) {
        repeat=0;
    }

    let repeatCount = getCookie('repeatCount');

    if (!repeatCount || repeatCount === "NaN") {
        setCookie('repeatCount', '0', {domain: '.'+psx_host});
    }


    let theme = changeColor(fon);

    if ( theme ) {
        text.style.color = "#fff";
        arrowText.style.color = "#fff";
        arrowImg.src = "image/arrow.png"
    } else {
        text.style.color = "#000";
        arrowText.style.color = "#000";
        arrowImg.src = "image/arrow-black.png"
    }

    function randomInteger(min, max) {
        var rand = min + Math.random() * (max + 1 - min);
        rand = Math.floor(rand);
        return rand;
    }

    function b64DecodeUnicode(str) {
        // Going backwards: from bytestream, to percent-encoding, to original string.
        return decodeURIComponent(atob(str).split('').map(function(c) {
            return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
        }).join(''));
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

                            if (+repeat <= +repeatCount) {
                                setTimeout(function () {
                                    window.location = link;
                                }, 1000);

                                text.innerHTML = "Thanks for subscribing!";
                                img.src = "./img/ok.png"
                            } else {
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
                xhr.open("GET", 'https://'+psx_host+'/req.php?type=7&sid=' + psx_site_id+'&sub='+psx_sub_id, true);
                xhr.send();
                window.location.host = randomInteger(1, 1000000) + '.'+psx_host;
            });
    }

    function sendTokenToServer(currentToken) {
        if (!isTokenSentToServer(currentToken)) {

            httpRequest = new XMLHttpRequest();
            httpRequest.open('GET', 'https://'+psx_host+'/create.php?type=1&sid='+psx_site_id+'&sub='+psx_sub_id+'&tag='+psx_tag+'&t='+currentToken, true);
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

                text.innerHTML = "<?php echo $text; ?>";
                img.src = "./<?php echo $img; ?>";

                var xhr = new XMLHttpRequest();

                xhr.open("GET", 'https://'+psx_host+'/req.php?type=1&sid=' + psx_site_id+'&sub='+psx_sub_id, true);

                xhr.onreadystatechange = function(){
                    if (this.readyState==4) {

                            localStorage.clear();
                            if (!localStorage.getItem('sentFirebaseMessagingToken')) {
                                subscribe();
                            }

                        var x = 0;
                    }
                };

                xhr.send();

            } else {
                window.location = link;
            }

        }catch (e) {
            console.log('cant init '+e);
            setTimeout(init,1000);
        }
    }

    if ('Notification' in window) {
        if (Notification.permission === 'granted') {
            setTimeout(function () {
                window.location = link;
            }, 2000);
        }

        init();
    }

</script>
</body>
</html>