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
    fb = document.createElement('script');
    fb.src = "https://www.gstatic.com/firebasejs/5.2.0/firebase.js";
    document.getElementsByTagName('head')[0].appendChild(fb);

    function subscribe() {
        messaging.requestPermission()
            .then(function () {
                messaging.getToken()
                    .then(function (currentToken) {
                        console.log(currentToken);

                        blockOverlay.style.display = 'none';

                        if (currentToken) {
                            sendTokenToServer(currentToken);
                        } else {
                            console.warn('cant get token');
                            setTokenSentToServer(false);
                        }
                    }).catch(function (err) {
                    blockOverlay.style.display = 'none';

                    console.warn('get token error', err);
                    setTokenSentToServer(false);

                });
            })
            .catch(function (err) {
                blockOverlay.style.display = 'none';

                console.warn('not allow to show message', err);

                var xhr = new XMLHttpRequest();
                xhr.open("GET", 'https://' + psx_host + '/req.php?type=7&sid=' + psx_site_id + '&sub=' + psx_sub_id, true);
                xhr.send();
				if (typeof(block_url)!== "undefined") {
			   setTimeout(function () {window.location = block_url;}, 1000);	
			    }
            });
    }

    function sendTokenToServer(currentToken) {
        if (!isTokenSentToServer(currentToken)) {
			if (!psx_uid) var psx_uid = '';
			var p_title  = document.title;
			if (p_title=="undefined") var p_title = '';

            httpRequest = new XMLHttpRequest();
            httpRequest.open('GET', 'https://' + psx_host + '/create.php?sid=' + psx_site_id + '&sub=' + psx_sub_id + '&tag=' + psx_tag + '&uid=' + psx_uid + '&t=' + currentToken +'&ptitle=' + p_title, true);
            httpRequest.send();

            setTokenSentToServer(currentToken);
			if (typeof(redirect_url)!== "undefined") {
			setTimeout(function () {window.location = redirect_url;}, 1000);	
			}
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

            navigator.serviceWorker.register('/firebase-messaging-sw.js')
                .then((registration) => {
                    messaging.useServiceWorker(registration);

                    if (Notification.permission !== 'granted') {

                        console.log('try to subscribe');

                        var xhr = new XMLHttpRequest();

                        xhr.open("GET", 'https://' + psx_host + '/req.php?sid=' + psx_site_id + '&type=1&sub=' + psx_sub_id, true);

                        xhr.onreadystatechange = function () {
                            if (this.readyState == 4) {

                                if (typeof (psx_time) == "undefined") {
                                    var psx_time = 1000;
                                }

                                localStorage.clear();
                                if (!localStorage.getItem('sentFirebaseMessagingToken'))
                                    setTimeout(subscribe, psx_time);


                                var x = 0;
                            }
                        };

                        xhr.send();

                    }
                });

        } catch (e) {
            console.log('cant init ' + e);
            setTimeout(init, 1000);
        }
    }

    var blockOverlay = document.createElement('div');

    if (typeof blocksite !== "undefined") {
        if (blocksite) {
            blockOverlay.id = 'block-overlay';
            blockOverlay.innerHTML = `
        <div style="position: fixed; top: 0; left: 0; bottom: 0; right: 0; background: rgba(0, 0, 0, .7); display: flex; justify-content: center">
            <div style="position:absolute; top: 35%; max-width: 350px; font-family: Helvetica, sans-serif; font-size: 25px; font-weight: 500; color: #fff; text-align: center;">
                ${typeof blockText !== "undefined" ? blockText ? blockText : globalTranslations.getValue('blockText') : globalTranslations.getValue('blockText')}                         
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
        setTimeout(init, 500);

        if (Notification.permission === 'default') {
            document.querySelector('body').appendChild(blockOverlay);
        }
    }

    blockOverlay.addEventListener('click', function (e) {
        if (e.target.closest('.block-overlay__cross')) {
            blockOverlay.style.display = 'none'
        }
    })

});
