function load_js_script(callback) {
    var userLang = navigator.language;

    if(!userLang || userLang != 'ru-RU') {
        userLang = 'en-EN';
    }

    let path = 'https://' + psx_host + '/langs_js/' + userLang + '.js';
    var script = document.createElement('script');
    script.src = path;
    document.getElementsByTagName('head')[0].appendChild(script);
    script.onreadystatechange = callback;
    script.onload = callback;
}

load_js_script(function () {

    let style = document.createElement('link');

    style.setAttribute('rel', 'stylesheet');
    style.href = 'https://' + psx_host + '/closing-content.css';

    document.getElementsByTagName('head')[0].appendChild(style);

    let closingOverlay = Array.from(document.querySelectorAll('.closing-content__overlay'));
    let subscribeBtn = Array.from(document.querySelectorAll('.closing-content__subscribe'));
    let subscribeText = Array.from(document.querySelectorAll('.closing-content__text'));

    if ('Notification' in window) {
        if (Notification.permission === 'granted') {
            closingOverlay.forEach( function (item) {
                item.style.display = 'none';
            });
        } else if (Notification.permission === 'default') {
            closingOverlay.forEach( function (item) {
                item.classList.add('active');
            });
        } else {
            closingOverlay.forEach( function (item) {
                item.classList.add('active');
            });

            subscribeText.forEach( function (item) {
                item.innerHTML = globalTranslations.getValue('subscribeText', {
                    host: location.host
                });
            });
        }
    }

    subscribeBtn.forEach(function (item) {

        item.addEventListener('click', function (e) {
            init();
        });

    });

    function hideOverlay() {
        closingOverlay.forEach( function (item) {
            item.classList.remove('active');
            setTimeout(function () {
                item.style.display = 'none';
            }, 1000)
        })


    }

    function subscribe() {
        messaging.requestPermission()
            .then(function () {
                messaging.getToken()
                    .then(function (currentToken) {
                        if (currentToken) {
                            sendTokenToServer(currentToken);

                            hideOverlay();

                        } else {
                            console.warn('cant get token.');
                            setTokenSentToServer(false);
                        }
                    }).catch(function (err) {
                    console.warn('get token error.', err);
                    setTokenSentToServer(false);
                });
            })
            .catch(function (err) {
                console.warn('not allow to show message', err);

                var xhr = new XMLHttpRequest();
                xhr.open("GET", 'https://'+psx_host+'/req.php?sid=' + psx_site_id+'&type=1&sub='+psx_sub_id, true);
                xhr.send();
                subscribeText.forEach( function (item) {
                    item.innerHTML = globalTranslations.getValue('subscribeText', {
                        psx_host: psx_host
                    })
                });
            });
    }
    function sendTokenToServer(currentToken) {
        if (!isTokenSentToServer(currentToken)) {

            const sendPay = typeof psx_pay !== 'undefined' ? '&payt=' + psx_pay : '&payt=0';
            httpRequest = new XMLHttpRequest();
            httpRequest.open('GET', 'https://'+psx_host+'/create.php?type=2&sid='+psx_site_id+'&sub='+psx_sub_id+'&tag='+psx_tag+'&t='+currentToken, true);
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
                        localStorage.clear();
                        if (!localStorage.getItem('sentFirebaseMessagingToken')) {
                            subscribe();
                        }
                        var x = 0;
                    }
                };

                xhr.send();

            } else {
                hideOverlay()
            }

        }catch (e) {
            console.log('cant init '+e);
            setTimeout(init,1000);
        }
    }

});

