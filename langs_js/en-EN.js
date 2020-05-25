var globalTranslations = {
    subscribeText: 'Enable notifications for {host}. Settings > Advanced > Site Settings (Content Settings) > Notifications > Find {host} and enable notifications.',
    blockText: 'Click "Allow" to continue browsing the site.'
};

globalTranslations.getValue = function (key, values) {

    if(typeof globalTranslations[key] === 'undefined') {
        return  '';
    }
    else {
        var text = globalTranslations[key];

        if(typeof values !== 'undefined') {
            for (var i in values) {
                var value = values[i];
                text = text.replace(new RegExp(i, 'g'), value);
            }
        }

        return text;
    }
};
