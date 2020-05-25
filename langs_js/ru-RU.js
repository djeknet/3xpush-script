var globalTranslations = {
    subscribeText: 'Включите уведомления для {host}. Настройки > Дополнительные > Настройки сайтов (Настройки контента) > Уведомления > Найдите {host} и разрешите уведомления.',
    blockText: 'Нажмите «Разрешить», чтобы продолжить просмотр сайта.'
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
