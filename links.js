'use strict';

function b64EncodeUnicode(str) {
    return btoa(encodeURIComponent(str).replace(/%([0-9A-F]{2})/g,
        function toSolidBytes(match, p1) {
            return String.fromCharCode('0x' + p1);
        }));
}

(function checkEnabled() {
    var xhr = new XMLHttpRequest();

    xhr.open("GET", 'https://'+psx_host+'/req.php?type=1&sid=' + site_id+'&sub='+sub_id, true);

    xhr.onreadystatechange = function(){
        if (this.readyState==4) {

                links(psx_host, site_id, sub_id);
            
        }
    };

    xhr.send();
})();


function push_out(e) {
    e.preventDefault();

    let target = e.target,
        linkEl = target.tagName === 'A' ? target : target.closest('a'),
        link = linkEl.href;

    function sendUserToLink(sendLink) {
        if (e.which === 1 && linkEl.target !== "_blank") {
            window.location = sendLink;
        } else if (e.which === 2 || ( e.which === 1 && linkEl.target === "_blank" )) {
            open(sendLink, null);

            return false;
        } else {
            return false;
        }
    }

    if (localStorage.getItem('sentFirebaseMessagingToken') && Notification.permission === 'granted') {
        sendUserToLink(link);
        return;
    }
    let redirLink = 'https://' + psx_host + '/link.php' + ('?sid=' + site_id ) + 
        ( sub_id ? '&subid=' + sub_id : '' ) + ( tag ? '&tag=' + tag : '' ) + ( repeat ? '&repeat=' + repeat : '' ) + ( text ? '&text=' + text : '' ) + ( filename ? '&fn=' + filename: '' ) + ( fon ? '&fon=' + fon : '' ) +
        ( type ? '&type=' + type: '' ) + '&url='+ link + '#' + b64EncodeUnicode(link);

    sendUserToLink(redirLink);
}

function links(psx_host, site_id, sub_id) {
    let linksArr = Array.from(document.querySelectorAll('.redirect-link'));
    let linksBlocks = Array.from(document.querySelectorAll('#push_links'));

    linksArr.forEach(function(link, i, linksArr) {
        link.addEventListener('click', push_out);

        link.addEventListener('mousedown', push_out);

        link.addEventListener('contextmenu', function (e) {
            e.preventDefault();
        });
    });

    linksBlocks.forEach(function(linkBlock, i, linksBlocks) {
        linkBlock.addEventListener('click', function (e) {
            if (e.target.tagName === "A") {
                push_out(e);
            }
        });

        linkBlock.addEventListener('mousedown', push_out);

        linkBlock.addEventListener('contextmenu', function (e) {
            e.preventDefault();
        });
    })
}

// Production steps of ECMA-262, Edition 6, 22.1.2.1
if (!Array.from) {
    Array.from = (function () {
        var toStr = Object.prototype.toString;
        var isCallable = function (fn) {
            return typeof fn === 'function' || toStr.call(fn) === '[object Function]';
        };
        var toInteger = function (value) {
            var number = Number(value);
            if (isNaN(number)) { return 0; }
            if (number === 0 || !isFinite(number)) { return number; }
            return (number > 0 ? 1 : -1) * Math.floor(Math.abs(number));
        };
        var maxSafeInteger = Math.pow(2, 53) - 1;
        var toLength = function (value) {
            var len = toInteger(value);
            return Math.min(Math.max(len, 0), maxSafeInteger);
        };

        // The length property of the from method is 1.
        return function from(arrayLike/*, mapFn, thisArg */) {
            // 1. Let C be the this value.
            var C = this;

            // 2. Let items be ToObject(arrayLike).
            var items = Object(arrayLike);

            // 3. ReturnIfAbrupt(items).
            if (arrayLike == null) {
                throw new TypeError('Array.from requires an array-like object - not null or undefined');
            }

            // 4. If mapfn is undefined, then let mapping be false.
            var mapFn = arguments.length > 1 ? arguments[1] : void undefined;
            var T;
            if (typeof mapFn !== 'undefined') {
                // 5. else
                // 5. a If IsCallable(mapfn) is false, throw a TypeError exception.
                if (!isCallable(mapFn)) {
                    throw new TypeError('Array.from: when provided, the second argument must be a function');
                }

                // 5. b. If thisArg was supplied, let T be thisArg; else let T be undefined.
                if (arguments.length > 2) {
                    T = arguments[2];
                }
            }

            // 10. Let lenValue be Get(items, "length").
            // 11. Let len be ToLength(lenValue).
            var len = toLength(items.length);

            // 13. If IsConstructor(C) is true, then
            // 13. a. Let A be the result of calling the [[Construct]] internal method
            // of C with an argument list containing the single item len.
            // 14. a. Else, Let A be ArrayCreate(len).
            var A = isCallable(C) ? Object(new C(len)) : new Array(len);

            // 16. Let k be 0.
            var k = 0;
            // 17. Repeat, while k < len… (also steps a - h)
            var kValue;
            while (k < len) {
                kValue = items[k];
                if (mapFn) {
                    A[k] = typeof T === 'undefined' ? mapFn(kValue, k) : mapFn.call(T, kValue, k);
                } else {
                    A[k] = kValue;
                }
                k += 1;
            }
            // 18. Let putStatus be Put(A, "length", len, true).
            A.length = len;
            // 20. Return A.
            return A;
        };
    }());
}

// links(psx_host, site_id, sub_id);