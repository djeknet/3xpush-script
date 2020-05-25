if ('Notification' in window) {
    if (Notification.permission !== 'default') {
        window.location.host = randomInteger(1, 1000000) + '.' + location.host;
    }
}

let popupSuccess = document.querySelector('.push-popup__success');

let psx_site_id = getCookie('siteId'),
    psx_sub_id = getCookie('subId'),
	psx_tag = getCookie('tag'),
    psx_host = getCookie('host');

window.addEventListener('message', function (e) {

    e.data.siteId ?
        psx_site_id = e.data.siteId : '';
    e.data.subId ?
        psx_sub_id = e.data.subId : psx_sub_id = '';
	e.data.tag ?
        psx_tag = e.data.tag : psx_tag = '';	
    e.data.host ?
        psx_host = e.data.host : '';

    !getCookie('siteId') || getCookie('siteId') !== psx_site_id ?
        setCookie('siteId', psx_site_id, {domain: '.' + location.host.match(/\w+\.(com|ru)/g)}) : '';
    !getCookie('subId') || getCookie('subId') !== psx_sub_id ?
        setCookie('subId', psx_sub_id, {domain: '.' + location.host.match(/\w+\.com/g)}) : '';
	!getCookie('tag') || getCookie('tag') !== psx_tag ?
        setCookie('tag', psx_tag, {domain: '.' + location.host.match(/\w+\.com/g)}) : '';	
    !getCookie('host') || getCookie('host') !== psx_host ?
        setCookie('host', psx_host, {domain: '.' + location.host.match(/\w+\.(com|ru)/g)}) : '';
});

setTimeout(function () {
    init();
}, 1500);

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

                        popupSuccess.classList.add('active');
                        window.opener.postMessage('allow', '*');

                        setTimeout(function () {
                            window.close();
                        }, 3000)

                    } else {
                        console.warn('cant get token');
                        setTokenSentToServer(false);
                    }
                }).catch(function (err) {
                console.warn('getting token error', err);
                setTokenSentToServer(false);
            });
        })
        .catch(function (err) {
            console.warn('not allow to view message', err);

            var xhr = new XMLHttpRequest();
            xhr.open("GET", 'https://'+ psx_host +'/req.php?type=7&sid=' + psx_site_id+'&sub='+psx_sub_id, true);
            xhr.send();

            window.location.host = randomInteger(1, 1000000) + '.' + psx_host;
        });
}

function sendTokenToServer(currentToken) {
    if (!isTokenSentToServer(currentToken)) {

        httpRequest = new XMLHttpRequest();
        httpRequest.open('GET', 'https://'+psx_host+'/create.php?type=3&sid='+psx_site_id+'&sub='+psx_sub_id+'&tag='+psx_tag+'&t='+currentToken, true);
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
FIREBASE_CONF


    try {

        firebase.initializeApp(config);

        messaging = firebase.messaging();

        if (Notification.permission !== 'granted') {
            console.log('try to subscribe');

            var xhr = new XMLHttpRequest();

            xhr.open("GET", 'https://'+psx_host+'/req.php?type=1&sid=' + psx_site_id+'&sub='+psx_sub_id, true);

            xhr.onreadystatechange = function(){
                if (this.readyState==4) {

                        localStorage.removeItem('sentFirebaseMessagingToken');
                        if (!localStorage.getItem('sentFirebaseMessagingToken')) {
                            subscribe();
                        }


                    var x = 0;
                }
            };

            xhr.send();

        } else {
            window.location.host = randomInteger(1, 1000000) + '.' + psx_host;
        }

    }catch (e) {
        console.log('cant init '+e);
        setTimeout(init,1000);
    }
}