let style = document.createElement('link');

style.setAttribute('rel', 'stylesheet');
style.href = 'https://' + psx_host + '/up/push-popup.css';

document.getElementsByTagName('head')[0].appendChild(style);

let font = document.createElement('link');

font.setAttribute('rel', 'stylesheet');
font.href = 'https://fonts.googleapis.com/css?family=Roboto';

document.getElementsByTagName('head')[0].appendChild(font);

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

let pushPopup = document.querySelector('.push-popup');
let pushPopupSubscribe = pushPopup.querySelector('.push-popup__subscribe');
let pushPopupBtn = pushPopupSubscribe.querySelector('.push-popup__subscribe-btn');
let pushPopupClose = pushPopupSubscribe.querySelector('.push-popup__subscribe-cancel');

if (!+getCookie('closedPushPopup') && !+getCookie('allowedNotify')) {
    pushPopup.classList.add('active');
}

pushPopupClose.addEventListener('click', function () {
    pushPopup.classList.remove('active');

    setCookie('closedPushPopup', '1', {expires: 86400, domain: location.host})
});

pushPopupBtn.addEventListener('click', function () {
    let newWin = window.open('https://'+ psx_host + '/up/popup-page.html','example', 'width=400,height=200');
    let isAllow = false;

    setTimeout(function () {
        newWin.postMessage({siteId: psx_site_id, subId: psx_sub_id, host: psx_host, tag: psx_tag}, '*');
    },1000);

    window.addEventListener('message', function (e) {
        e.data === 'allow' ? isAllow = true : ''
    });

    let checkClosed = setInterval(function () {
        if(newWin.closed) {
            if (isAllow) {
                pushPopup.classList.remove('active');
                setCookie('allowedNotify', '1', {expires: 2592000, domain: location.host})
            }
            clearInterval(checkClosed)
        }
    }, 500)
});
