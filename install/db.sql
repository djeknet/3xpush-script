 CREATE TABLE IF NOT EXISTS `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(20) NOT NULL,
  `name` varchar(50) NOT NULL,
  `pass` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `email_check` smallint(1) NOT NULL DEFAULT '1',
  `check_code` varchar(100) NOT NULL,
  `hash` varchar(50) NOT NULL,
  `role` smallint(1) NOT NULL DEFAULT '1',
  `root` smallint(1) NOT NULL DEFAULT '0',
  `owner_id` int(1) NOT NULL DEFAULT '0',
  `last_login` datetime NOT NULL,
  `status` smallint(1) NOT NULL DEFAULT '1',
  `ip` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `cc` varchar(3) NOT NULL,
  `telegram` varchar(50) NOT NULL,
  `skype` varchar(50) NOT NULL,
  `get_mail` smallint(1) NOT NULL DEFAULT '1',
  `promo_mail` smallint(1) NOT NULL DEFAULT '1',
  `auto_money` smallint(1) NOT NULL DEFAULT '0' COMMENT '1 - automatic payments',
  `score_id` int(11) NOT NULL DEFAULT '0' COMMENT 'admins_scrore id for automatic payments',
  `reg_from` varchar(300) NOT NULL,
  `ref_active` smallint(1) NOT NULL DEFAULT '0' COMMENT '1 - referral program is on',
  `new_pass_code` varchar(300) NOT NULL,
  `deny_sending` smallint(1) NOT NULL DEFAULT '0' COMMENT '1 - not allow sending',
  `notif_teleg` smallint(1) NOT NULL DEFAULT '1',
  `notif_push` smallint(1) NOT NULL DEFAULT '1',
  `city` varchar(30) NOT NULL,
  `check_city` smallint(1) NOT NULL DEFAULT '1',
  `is_support` smallint(1) NOT NULL DEFAULT '0' COMMENT '1 - user support',
  `last_edit` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`),
  KEY `cc` (`cc`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;


DROP TRIGGER IF EXISTS `balanse`;
CREATE TRIGGER `balanse` AFTER INSERT ON `admins`
 FOR EACH ROW INSERT INTO balance (`admin_id`) VALUES (NEW.id);


CREATE TABLE IF NOT EXISTS `admins_score` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `payment_id` int(11) NOT NULL,
  `score` varchar(50) NOT NULL,
  `check_code` varchar(300) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admin_id` (`admin_id`,`payment_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='payout wallets' AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Структура таблицы `advs`
--

CREATE TABLE IF NOT EXISTS `advs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) unsigned NOT NULL DEFAULT '0',
  `feed_id` int(5) NOT NULL DEFAULT '0',
  `hash` varchar(100) NOT NULL COMMENT 'title, description',
  `create_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_date` datetime NOT NULL,
  `icon` text NOT NULL,
  `title` varchar(100) CHARACTER SET utf8mb4 NOT NULL,
  `description` varchar(300) CHARACTER SET utf8mb4 NOT NULL,
  `image` text NOT NULL,
  `url` text NOT NULL,
  `sended` int(11) NOT NULL DEFAULT '0',
  `uniq_sended` int(11) NOT NULL DEFAULT '0',
  `views` int(11) NOT NULL DEFAULT '0',
  `clicks` int(11) NOT NULL DEFAULT '0',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00',
  `blocked` smallint(1) NOT NULL DEFAULT '0',
  `why_block` varchar(50) NOT NULL,
  `unsubs` int(11) NOT NULL DEFAULT '0',
  `last_check` date NOT NULL,
  `icon_hash` varchar(50) NOT NULL,
  `image_hash` varchar(50) NOT NULL,
  `icon_errors` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`hash`,`feed_id`),
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `blocked` (`blocked`),
  KEY `last_check` (`last_check`),
  KEY `feed_id` (`feed_id`),
  KEY `admin_id` (`admin_id`),
  KEY `icon_hash` (`icon_hash`,`image_hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='push ads' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `advs_stat`
--

CREATE TABLE IF NOT EXISTS `advs_stat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `advs_id` int(11) NOT NULL,
  `clicks` int(11) NOT NULL DEFAULT '0',
  `clicks_nopay` int(11) NOT NULL DEFAULT '0',
  `wm_money` decimal(10,3) NOT NULL DEFAULT '0.000',
  `money` decimal(10,3) NOT NULL DEFAULT '0.000',
  PRIMARY KEY (`id`),
  UNIQUE KEY `date` (`date`,`advs_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='feed ad statistics' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `advs_targets`
--

CREATE TABLE IF NOT EXISTS `advs_targets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `advs_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `region` varchar(2) NOT NULL,
  `os_id` int(11) NOT NULL DEFAULT '0',
  `browser_id` int(11) NOT NULL DEFAULT '0',
  `lang` varchar(2) NOT NULL,
  `devtype` varchar(15) NOT NULL,
  `device` varchar(15) NOT NULL,
  `ip_range` int(11) NOT NULL DEFAULT '0',
  `views` int(11) NOT NULL DEFAULT '0',
  `cpc_money` decimal(10,3) NOT NULL DEFAULT '0.000',
  PRIMARY KEY (`id`),
  UNIQUE KEY `advs_id` (`advs_id`,`region`,`os_id`,`browser_id`,`lang`,`devtype`,`device`,`ip_range`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='feed ad targeting' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `alerts`
--

CREATE TABLE IF NOT EXISTS `alerts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) unsigned NOT NULL DEFAULT '0',
  `date` datetime NOT NULL,
  `type` varchar(10) NOT NULL,
  `text` text NOT NULL,
  `view` smallint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `alerts_hash`
--

CREATE TABLE IF NOT EXISTS `alerts_hash` (
  `date` datetime NOT NULL,
  `hash` varchar(50) NOT NULL,
  UNIQUE KEY `hash` (`hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='repeated notification hashes';

-- --------------------------------------------------------

--
-- Структура таблицы `balance`
--

CREATE TABLE IF NOT EXISTS `balance` (
  `admin_id` int(11) NOT NULL,
  `summa` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `allmoney` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `last_edit` datetime NOT NULL,
  `last_sum` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `check_code` varchar(50) NOT NULL,
  `comission` int(2) NOT NULL DEFAULT '0' COMMENT 'комиссия от цены рекла',
  UNIQUE KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Структура таблицы `browser_stat`
--

CREATE TABLE IF NOT EXISTS `browser_stat` (
  `date` date NOT NULL,
  `browser_id` mediumint(11) NOT NULL,
  `admin_id` int(11) unsigned NOT NULL DEFAULT '0',
  `sid` int(11) NOT NULL DEFAULT '0',
  `land_views` int(11) NOT NULL DEFAULT '0',
  `requests` int(11) NOT NULL DEFAULT '0',
  `subscribers` int(11) NOT NULL DEFAULT '0',
  `unsubs` int(11) NOT NULL DEFAULT '0',
  `sended` int(11) NOT NULL DEFAULT '0',
  `uniq_sended` int(11) NOT NULL DEFAULT '0',
  `img_views` int(11) NOT NULL DEFAULT '0',
  `closed` int(11) NOT NULL DEFAULT '0',
  `clicks` int(11) NOT NULL DEFAULT '0',
  `money` decimal(10,3) NOT NULL DEFAULT '0.000',
  `blocked_requests` int(11) NOT NULL DEFAULT '0',
  `empty_send` int(11) NOT NULL DEFAULT '0',
  `traf_cost` decimal(10,3) NOT NULL DEFAULT '0.000',
  UNIQUE KEY `date` (`date`,`browser_id`,`sid`),
  KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `clickstat`
--

CREATE TABLE IF NOT EXISTS `clickstat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL DEFAULT '0',
  `createtime` datetime NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `subscriber_id` varchar(100) NOT NULL,
  `advs_id` int(11) NOT NULL,
  `sid` int(11) NOT NULL,
  `subid` varchar(30) NOT NULL,
  `money` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `ip` varchar(30) NOT NULL,
  `feed_id` smallint(2) NOT NULL DEFAULT '0',
  `minutes` int(11) NOT NULL DEFAULT '0',
  `click_id` bigint(20) NOT NULL,
  `os` varchar(30) NOT NULL,
  `browser` varchar(30) NOT NULL,
  `cc` varchar(2) NOT NULL,
  `device` varchar(20) NOT NULL,
  `brand` varchar(30) NOT NULL,
  `model` varchar(30) NOT NULL,
  `comment` text NOT NULL,
  `days` int(11) NOT NULL DEFAULT '0' COMMENT 'дней подписки',
  PRIMARY KEY (`id`),
  KEY `date` (`date`,`subscriber_id`,`sid`,`ip`),
  KEY `advs_id` (`advs_id`),
  KEY `subscriber_id` (`subscriber_id`),
  KEY `feed_id` (`feed_id`),
  KEY `admin_id` (`admin_id`),
  KEY `click_id` (`click_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='user clicks stat' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `content`
--

CREATE TABLE IF NOT EXISTS `content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(30) NOT NULL,
  `section_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(30) NOT NULL,
  `title` text NOT NULL,
  `content` longtext NOT NULL,
  `status` smallint(1) NOT NULL DEFAULT '0',
  `views` int(11) NOT NULL DEFAULT '0',
  `cteate_date` date NOT NULL,
  `pub_date` date NOT NULL,
  `pageurl` varchar(300) NOT NULL,
  `code` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='контент и страницы' AUTO_INCREMENT=10 ;

--
-- Структура таблицы `content_section`
--

CREATE TABLE IF NOT EXISTS `content_section` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titles` varchar(300) NOT NULL,
  `sorts` int(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='разделы контента' AUTO_INCREMENT=4 ;

--
-- Дамп данных таблицы `content_section`
--

INSERT INTO `content_section` (`id`, `titles`, `sorts`) VALUES
(1, '{"ru":"тест","en":"test"}', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `crons`
--

CREATE TABLE IF NOT EXISTS `crons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `location` varchar(50) DEFAULT NULL,
  `cronfile` varchar(255) DEFAULT NULL,
  `frequency` int(11) DEFAULT NULL,
  `is_stable` tinyint(4) NOT NULL DEFAULT '0',
  `last_start` datetime DEFAULT NULL,
  `last_end` datetime DEFAULT NULL,
  `count_errors` int(11) NOT NULL DEFAULT '0',
  `count` int(11) NOT NULL DEFAULT '0',
  `time` float NOT NULL DEFAULT '0',
  `description` text,
  `time_from` time DEFAULT NULL,
  `time_to` time DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=26 ;

--
-- Дамп данных таблицы `crons`
--

INSERT INTO `crons` (`id`, `location`, `cronfile`, `frequency`, `is_stable`, `last_start`, `last_end`, `count_errors`, `count`, `time`, `description`, `time_from`, `time_to`) VALUES
(2, 'master', 'emailsender', 10, 1, '2020-05-19 19:33:41', '2020-05-19 19:33:44', 0, 66, 77.7666, 'email sending', '00:00:00', '23:59:00'),
(4, 'master', 'homestat', 5, 1, '2020-05-19 19:33:44', '2020-05-19 19:33:44', 0, 24, 0.150005, 'home stat save', '00:00:00', '23:00:00'),
(7, 'master', 'temp_table', 10, 1, '2020-05-12 17:56:25', '2019-09-21 15:58:33', 0, 34, 35.1624, 'updating information in temp_table', '00:00:00', '23:00:00'),
(9, 'master', 'errors_notification', 10, 1, '2020-05-19 19:33:44', '2020-05-19 19:33:44', 0, 14, 0.0918267, 'notification of admins about system errors', '00:00:00', '23:00:00'),
(10, 'master', 'news_alerts', 10, 1, '2020-05-19 19:33:44', '2020-05-19 19:33:44', 0, 11, 0.204034, 'newsletter alerts to users', '00:00:00', '23:00:00'),
(12, 'master', 'referal_pay', 3600, 1, '2019-08-20 08:48:12', '2019-08-20 08:48:12', 0, 15, 0.18956, 'calculation with referrals from the income of attracted users', '00:00:00', '01:00:00'),
(16, 'master', 'referal_activate', 3600, 1, '2020-05-19 19:33:44', '2020-05-19 19:33:44', 0, 6, 0.0819998, 'automatically checks active users and activates the ref system for them, and send notifications', '00:00:00', '23:00:00'),
(17, 'master', 'new_sites', 3600, 1, '2020-05-19 19:33:44', '2020-05-19 19:33:44', 0, 4, 0.0775349, 'check added sites for which there are no subscriptions in a few days', '00:00:00', '23:00:00'),
(22, 'master', 'clear_day', 600, 1, NULL, NULL, 0, 0, 0, 'daily data reset', '23:50:00', '23:59:00'),
(23, 'master', 'subscribers_del', 3600, 0, NULL, NULL, 0, 0, 0, 'checking subscribers for activity, if there is no activity, then mark the user as remote', '00:00:00', '23:59:00'),
(24, 'master', 'sysstat', 100, 1, '2020-05-19 19:33:44', '2020-05-19 19:33:44', 0, 3, 0.0687869, 'save system information for analitics', '00:00:00', '23:59:00'),
(25, 'master', 'sysstat', 100, 1, '2020-05-19 19:33:44', '2020-05-19 19:33:44', 0, 3, 0.042814, 'save system information for analitics', '00:00:00', '23:59:00');

-- --------------------------------------------------------

--
-- Структура таблицы `daystat`
--

CREATE TABLE IF NOT EXISTS `daystat` (
  `date` date NOT NULL,
  `admin_id` int(11) NOT NULL DEFAULT '0',
  `sid` int(11) NOT NULL,
  `subid` varchar(50) NOT NULL DEFAULT '0',
  `land_views` int(11) NOT NULL DEFAULT '0',
  `requests` int(11) NOT NULL DEFAULT '0',
  `subscribers` int(11) unsigned NOT NULL DEFAULT '0',
  `unsubs` int(11) unsigned NOT NULL DEFAULT '0',
  `sended` int(11) NOT NULL DEFAULT '0',
  `uniq_sended` int(11) NOT NULL DEFAULT '0',
  `img_views` int(11) unsigned NOT NULL DEFAULT '0',
  `closed` int(11) NOT NULL DEFAULT '0',
  `clicks` int(11) NOT NULL DEFAULT '0',
  `money` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `blocked_requests` int(11) unsigned NOT NULL DEFAULT '0',
  `empty_send` int(11) unsigned NOT NULL DEFAULT '0',
  `traf_cost` decimal(10,4) NOT NULL DEFAULT '0.0000',
  UNIQUE KEY `date` (`date`,`sid`,`subid`) USING BTREE,
  KEY `date_2` (`date`),
  KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;

--
-- Структура таблицы `domains`
--

CREATE TABLE IF NOT EXISTS `domains` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL DEFAULT '0',
  `domain` varchar(50) NOT NULL,
  `updated` date NOT NULL,
  `ssl_ready` smallint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `domain` (`domain`),
  KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='домены юзеров' AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Структура таблицы `faq`
--

CREATE TABLE IF NOT EXISTS `faq` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` text NOT NULL,
  `answer` longtext NOT NULL,
  `sorts` int(11) NOT NULL DEFAULT '0',
  `type` enum('wm','adv','all') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `feeds`
--

CREATE TABLE IF NOT EXISTS `feeds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) unsigned NOT NULL DEFAULT '0',
  `name` varchar(30) NOT NULL,
  `url` text NOT NULL,
  `status` smallint(1) NOT NULL DEFAULT '0',
  `feed_title` varchar(30) NOT NULL,
  `feed_body` varchar(30) NOT NULL,
  `feed_link_click_action` varchar(30) NOT NULL,
  `feed_link_icon` varchar(30) NOT NULL,
  `feed_link_image` varchar(30) NOT NULL,
  `feed_bid` varchar(30) NOT NULL,
  `convert_rate` varchar(5) NOT NULL,
  `feed_winurl` varchar(30) NOT NULL,
  `regions` varchar(300) NOT NULL,
  `max_send` int(11) NOT NULL DEFAULT '0',
  `total_sended` int(11) NOT NULL DEFAULT '0',
  `tested` smallint(1) NOT NULL DEFAULT '0',
  `coef` smallint(2) NOT NULL DEFAULT '0',
  `site` varchar(300) NOT NULL,
  `feed_button1` varchar(30) NOT NULL,
  `feed_button2` varchar(30) NOT NULL,
  `timeout_next` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='feeds options' AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `feeds`
--

INSERT INTO `feeds` (`id`, `admin_id`, `name`, `url`, `status`, `feed_title`, `feed_body`, `feed_link_click_action`, `feed_link_icon`, `feed_link_image`, `feed_bid`, `convert_rate`, `feed_winurl`, `regions`, `max_send`, `total_sended`, `tested`, `coef`, `site`, `feed_button1`, `feed_button2`, `timeout_next`) VALUES
(1, 1, '3xpush feed', 'https://api.3xpush.com/?action=get_ads&key=5ffecc12e1f68734eb766ab31854500aa58e8275&sid=49&subid=SUB&ip=IP&ua=AGENT&lang=LANG&date=DATE&uid=UID', 1, 'title', 'body', 'url', 'icon', 'image', 'cpc', '', '', '', 0, 10, 1, 0, 'https://3xpush.com', 'button1', 'button2', '2020-05-04 10:52:16');

-- --------------------------------------------------------

--
-- Структура таблицы `feeds_templ`
--

CREATE TABLE IF NOT EXISTS `feeds_templ` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `url` varchar(300) NOT NULL,
  `feed_title` varchar(30) NOT NULL,
  `feed_body` varchar(30) NOT NULL,
  `feed_link_click_action` varchar(30) NOT NULL,
  `feed_link_icon` varchar(30) NOT NULL,
  `feed_link_image` varchar(30) NOT NULL,
  `feed_bid` varchar(30) NOT NULL,
  `convert_rate` varchar(5) NOT NULL,
  `feed_winurl` varchar(30) NOT NULL,
  `site` varchar(100) NOT NULL,
  `status` smallint(1) NOT NULL DEFAULT '0',
  `params` varchar(300) NOT NULL,
  `feed_button1` varchar(50) NOT NULL,
  `feed_button2` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='шаблоны для фидов' AUTO_INCREMENT=17 ;

--
-- Дамп данных таблицы `feeds_templ`
--

INSERT INTO `feeds_templ` (`id`, `name`, `url`, `feed_title`, `feed_body`, `feed_link_click_action`, `feed_link_icon`, `feed_link_image`, `feed_bid`, `convert_rate`, `feed_winurl`, `site`, `status`, `params`, `feed_button1`, `feed_button2`) VALUES
(6, '3xpush feed', 'https://api.3xpush.com/?action=get_ads&key={token}&sid={sid}&subid=SUB&ip=IP&ua=AGENT&lang=LANG&date=DATE&uid=UID', 'title', 'body', 'url', 'icon', 'image', 'cpc', '', '', 'https://3xpush.com', 1, '{"param1":"token","param2":"sid","param3":""}', 'button1', 'button2'),
(7, 'Adskeeper', 'http://api.adskeeper.co.uk/{id}?content_type=json&token={token}&ip=IP&ua=AGENT', 'title', 'description', 'link', 'icon', 'image', 'cpc', '', '', 'http://adskeeper.co.uk', 1, '{"param1":"id","param2":"token","param3":""}', '', ''),
(8, 'Adsviafeed', 'http://213.227.149.10/code/feed/?pid={pid}&ip=IP&ua=AGENT&ref=REF&lang=LANG&limit=10&sourceid=SITE_ID', 'title', 'description', 'link', 'icon', 'image', 'cpc', '', '', 'https://adsviafeed.com/', 1, '{"param1":"pid","param2":"","param3":""}', '', ''),
(9, 'Evadav', 'http://eu2.evadavdsp.pro/dsp/ph/feed?sspid={sspid}&secret={secret}&ip=IP&ua=AGENT&subid=SITE_ID&uid=UID&lang=LANG', 'title', 'descr', 'link', 'icon', 'image', 'cpc', '', '', 'https://evadav.com/', 1, '{"param1":"sspid","param2":"secret","param3":""}', '', ''),
(10, 'Kokos.click', 'https://api.kokos.click/teaser/{token}/?site_id={site_id}&theme_id=3&allowed_themes=3,4,5,13,14,15&ip=IP&ua=AGENT&format=json', 'title', 'description', 'link', 'icon', 'image', 'bid', '/73', '', 'http://kokos.click', 1, '{"param1":"token","param2":"site_id","param3":""}', '', ''),
(11, 'Ppc.buzz', 'http://xmlppcbuzz.com/search?id={id}&token={token}&sid={sid}&keywords=&ip=IP&ref=REF&ua=AGENT&format=json', 'title', 'desc', 'clickurl', 'pixel', '', 'bid', '', '', 'https://partner.ppc.buzz', 1, '{"param1":"id","param2":"token","param3":"sid"}', '', ''),
(12, 'Bidspush', 'http://bidspushxml.com/push/?format=json&lid={lid}&token={token}&source=SITE_ID&ip=IP&ua=AGENT&referer=REF&userid=UID&lang=LANG&age=DATE&timeout=300', 'title', 'description', 'clickurl', 'icon', 'image', 'bid', '', '', 'https://bidspush.com/', 1, '{"param1":"lid","param2":"token","param3":""}', '', ''),
(13, 'ltvads.com', 'http://ideafix.xyz/ssp/feed?token={token}&ua=AGENT&ip=IP&count=1&type=push', 'title', 'descr', 'url', 'image', 'big_image', 'bid', '', 'win_url', 'https://ltvads.com/', 1, '{"param1":"token","param2":"","param3":""}', '', ''),
(14, 'push.house', 'http://feed.push.house/feed.php?uid={uid}&hash={hash}&ua=AGENT&ip=IP&site={site}', 'title', 'text', 'link', 'icon', 'img', 'cpc', '', '', 'http://feed.push.house/', 1, '{"param1":"uid","param2":"hash","param3":"site"}', '', ''),
(15, 'Adscompass', 'http://eu.binder.adplatform.pro/?token={token}&ip=IP&ua=AGENT&sid=SITE_ID&subid=UID&date=DATE&lang=LANG', 'title', 'desc', 'click_url', 'icon_url', 'image_url', 'cpc', '', '', 'https://platform.adscompass.com', 1, '{"param1":"token","param2":"","param3":""}', '', ''),
(16, 'Hilltopadsfeed', 'http://{name}.hilltopadsfeed.com/ask?ua=AGENT&ip=IP&source=SITE_ID', 'title', 'description', 'link', 'icon', 'image', 'cpc', '', '', 'https://hilltopads.com', 1, '{"param1":"name","param2":"","param3":""}', '', '');

-- --------------------------------------------------------

--
-- Структура таблицы `feed_region_prices`
--

CREATE TABLE IF NOT EXISTS `feed_region_prices` (
  `date` date NOT NULL,
  `feed_id` int(11) NOT NULL,
  `cc` varchar(2) NOT NULL COMMENT 'iso region',
  `mob` smallint(1) NOT NULL DEFAULT '0' COMMENT '1 - mobile',
  `money` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `requests` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `date` (`date`,`feed_id`,`cc`,`mob`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='стата по ценам в фидах';


--
-- Структура таблицы `feed_stat`
--

CREATE TABLE IF NOT EXISTS `feed_stat` (
  `date` date NOT NULL,
  `admin_id` int(11) unsigned NOT NULL DEFAULT '0',
  `feed_id` smallint(2) NOT NULL DEFAULT '0',
  `requests` int(11) NOT NULL DEFAULT '0',
  `alltime` decimal(10,3) NOT NULL DEFAULT '0.000',
  `sended` int(11) unsigned NOT NULL DEFAULT '0',
  `clicks` int(11) unsigned NOT NULL DEFAULT '0',
  `money` decimal(10,4) unsigned NOT NULL DEFAULT '0.0000',
  `wm_money` decimal(10,4) NOT NULL DEFAULT '0.0000' COMMENT 'вебмастеру',
  `empty` int(11) NOT NULL DEFAULT '0',
  `unsubs` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `date` (`date`,`feed_id`),
  KEY `feed_id` (`feed_id`),
  KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Структура таблицы `home_stat`
--

CREATE TABLE IF NOT EXISTS `home_stat` (
  `name` varchar(30) NOT NULL,
  `value` text NOT NULL,
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Структура таблицы `journal`
--

CREATE TABLE IF NOT EXISTS `journal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `admin_id` int(11) NOT NULL,
  `ip` varchar(30) NOT NULL,
  `cc` varchar(2) NOT NULL,
  `agent` varchar(100) NOT NULL,
  `page` varchar(300) NOT NULL,
  `action` varchar(300) NOT NULL,
  `error` smallint(1) NOT NULL DEFAULT '0' COMMENT '1 - ошибка при запросе',
  PRIMARY KEY (`id`),
  KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='журнал действий' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `landings`
--

CREATE TABLE IF NOT EXISTS `landings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(30) NOT NULL,
  `preview` varchar(100) NOT NULL,
  `html` longtext NOT NULL,
  `cteated` date NOT NULL,
  `status` smallint(1) NOT NULL DEFAULT '0',
  `used` int(11) NOT NULL DEFAULT '0',
  `views` int(11) NOT NULL DEFAULT '0',
  `subs` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='готовые лендинги' AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

--
-- Структура таблицы `landing_stat`
--

CREATE TABLE IF NOT EXISTS `landing_stat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `admin_id` int(11) NOT NULL,
  `sid` int(11) NOT NULL,
  `subid` varchar(30) NOT NULL,
  `land_id` int(11) NOT NULL,
  `views` int(11) NOT NULL DEFAULT '0',
  `requests` int(11) NOT NULL DEFAULT '0',
  `blocked_requests` int(11) NOT NULL DEFAULT '0',
  `subs` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `date_2` (`date`,`sid`,`subid`,`land_id`),
  KEY `date` (`date`,`admin_id`,`sid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='статистика лендингов по подпискам' AUTO_INCREMENT=3 ;

--
-- Структура таблицы `langs`
--

CREATE TABLE IF NOT EXISTS `langs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `lang` varchar(255) DEFAULT NULL,
  `text` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_lang` (`name`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `macros`
--

CREATE TABLE IF NOT EXISTS `macros` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT '0',
  `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=42 ;

-- --------------------------------------------------------

--
-- Структура таблицы `macros_langs`
--

CREATE TABLE IF NOT EXISTS `macros_langs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `macros_id` int(11) DEFAULT NULL,
  `lang` varchar(32) DEFAULT NULL,
  `text` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `macros_id_lang` (`macros_id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `mails`
--

CREATE TABLE IF NOT EXISTS `mails` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `admin_id` int(11) NOT NULL DEFAULT '0',
  `email` varchar(50) NOT NULL,
  `title` varchar(300) NOT NULL,
  `content` longtext NOT NULL,
  `status` smallint(1) NOT NULL DEFAULT '0',
  `create_time` datetime NOT NULL,
  `group_id` int(11) NOT NULL DEFAULT '0',
  `lang` varchar(2) NOT NULL,
  `views` int(3) NOT NULL DEFAULT '0',
  `clicks` int(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `admin_id` (`admin_id`,`status`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Структура таблицы `mails_stat`
--

CREATE TABLE IF NOT EXISTS `mails_stat` (
  `date` date NOT NULL,
  `sended` int(11) NOT NULL DEFAULT '0',
  `error_send` int(11) NOT NULL DEFAULT '0',
  `views` int(11) NOT NULL DEFAULT '0',
  `clicks` int(11) NOT NULL DEFAULT '0',
  `unsubs` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='статистика email рассылок';


--
-- Структура таблицы `mails_stat_hour`
--

CREATE TABLE IF NOT EXISTS `mails_stat_hour` (
  `date` date NOT NULL,
  `hour` int(2) NOT NULL,
  `views` int(11) NOT NULL DEFAULT '0',
  `clicks` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `date` (`date`,`hour`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='открытия писем по времени';

-- --------------------------------------------------------

--
-- Структура таблицы `myads`
--

CREATE TABLE IF NOT EXISTS `myads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) unsigned NOT NULL DEFAULT '0',
  `date` date NOT NULL,
  `time` time NOT NULL COMMENT 'время создания',
  `cid` int(2) NOT NULL DEFAULT '0',
  `title` varchar(50) NOT NULL,
  `text` varchar(120) NOT NULL,
  `icon` varchar(200) CHARACTER SET utf8 NOT NULL,
  `image` varchar(200) CHARACTER SET utf8 NOT NULL,
  `url` text CHARACTER SET utf8 NOT NULL,
  `regions` varchar(300) CHARACTER SET utf8 NOT NULL,
  `sids` varchar(300) CHARACTER SET utf8 NOT NULL,
  `tags` text CHARACTER SET utf8 NOT NULL,
  `langs` varchar(100) CHARACTER SET utf8 NOT NULL,
  `sended` int(11) NOT NULL DEFAULT '0',
  `last_send` datetime NOT NULL,
  `auctions` int(11) NOT NULL DEFAULT '0' COMMENT 'выигранные аукционы',
  `views` int(11) NOT NULL DEFAULT '0',
  `clicks` int(11) NOT NULL DEFAULT '0',
  `last_click` datetime NOT NULL,
  `status` smallint(1) NOT NULL DEFAULT '1',
  `maxsend` int(11) NOT NULL DEFAULT '0',
  `options` text NOT NULL,
  `moderate` smallint(1) NOT NULL DEFAULT '0',
  `user_maxsend` mediumint(2) NOT NULL DEFAULT '0',
  `way_block` varchar(30) NOT NULL,
  `last_edit` datetime NOT NULL,
  `send_time` datetime NOT NULL,
  `unsubs` int(11) NOT NULL DEFAULT '0' COMMENT 'отписок при рассылке',
  `subscribers` int(11) NOT NULL DEFAULT '0' COMMENT 'получателей',
  `sended_wrong` int(11) NOT NULL DEFAULT '0' COMMENT 'ошибок рассылки',
  `subsid` text NOT NULL COMMENT 'subscribers id',
  `loop_send` smallint(1) NOT NULL DEFAULT '0',
  `loop_finish` smallint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `admin_id` (`admin_id`),
  KEY `moderate` (`moderate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `news`
--

CREATE TABLE IF NOT EXISTS `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `title` varchar(300) NOT NULL,
  `content` text NOT NULL,
  `send_alert` smallint(1) NOT NULL DEFAULT '0',
  `alert_sended` smallint(1) NOT NULL DEFAULT '0' COMMENT '1 - alert отправлен',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `news`
--

INSERT INTO `news` (`id`, `date`, `title`, `content`, `send_alert`, `alert_sended`) VALUES
(1, '2020-03-30', '{"ru":"Приветствуем!","en":"Hello!"}', '{"ru":"Поздравляем с  успешной установкой и запуском 3xpush Script! Теперь вы можете начать  привлекать партнеров, которые будут приносить вам новых подписчиком, либо же самим начать собирать подписки. Затем подключить партнерские рекламные фиды и зарабатывать с рассылок рекламы. Успешной работы!","en":"text news2"}', 0, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `payment`
--

CREATE TABLE IF NOT EXISTS `payment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL DEFAULT '0',
  `create_time` datetime NOT NULL,
  `update_time` datetime NOT NULL,
  `type` tinyint(2) NOT NULL DEFAULT '0',
  `payment_type` smallint(1) NOT NULL DEFAULT '0',
  `summa` decimal(10,3) NOT NULL DEFAULT '0.000',
  `ostatok` decimal(10,3) NOT NULL DEFAULT '0.000',
  `status` smallint(1) NOT NULL DEFAULT '0',
  `out_id` varchar(50) NOT NULL DEFAULT '0',
  `comment` text NOT NULL,
  `sys_info` text NOT NULL,
  `out_ip` varchar(50) NOT NULL,
  `score` varchar(50) NOT NULL,
  `spisano` decimal(10,3) NOT NULL DEFAULT '0.000',
  `checksum` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `admin_id` (`admin_id`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='лог пополнений баланса' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `payment_type`
--

CREATE TABLE IF NOT EXISTS `payment_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `logo` varchar(100) NOT NULL,
  `status` smallint(1) NOT NULL DEFAULT '1',
  `minsumma` int(5) NOT NULL DEFAULT '0',
  `comission` decimal(5,2) NOT NULL DEFAULT '0.00',
  `withdrowal` smallint(1) NOT NULL DEFAULT '1' COMMENT '1 - вывод разрешен',
  `texts` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='платежные системы' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `referals`
--

CREATE TABLE IF NOT EXISTS `referals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `owner` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `money` decimal(10,3) NOT NULL DEFAULT '0.000',
  PRIMARY KEY (`id`),
  UNIQUE KEY `owner` (`owner`,`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='таблица связей рефералов' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `refstat`
--

CREATE TABLE IF NOT EXISTS `refstat` (
  `date` date NOT NULL,
  `admin_id` int(11) NOT NULL,
  `money` decimal(10,3) NOT NULL DEFAULT '0.000',
  `proc` int(2) NOT NULL DEFAULT '0',
  `active_users` int(11) NOT NULL DEFAULT '0',
  `all_users` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `date` (`date`,`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='заработок на рефах по дням';

-- --------------------------------------------------------

--
-- Структура таблицы `region_stat`
--

CREATE TABLE IF NOT EXISTS `region_stat` (
  `date` date NOT NULL,
  `admin_id` int(11) unsigned NOT NULL DEFAULT '0',
  `sid` int(11) NOT NULL,
  `cc` varchar(2) NOT NULL,
  `requests` int(11) NOT NULL DEFAULT '0',
  `subscribers` int(11) NOT NULL DEFAULT '0',
  `sended` int(11) NOT NULL DEFAULT '0',
  `closed` int(11) NOT NULL DEFAULT '0',
  `img_views` int(11) NOT NULL DEFAULT '0',
  `clicks` int(11) NOT NULL DEFAULT '0',
  `cpv_money` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `money` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `empty` int(11) NOT NULL DEFAULT '0',
  `unsubs` int(11) unsigned NOT NULL DEFAULT '0',
  UNIQUE KEY `date` (`date`,`admin_id`,`sid`,`cc`),
  KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;

-- --------------------------------------------------------

--
-- Структура таблицы `reports`
--

CREATE TABLE IF NOT EXISTS `reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `type` varchar(20) NOT NULL,
  `status` varchar(10) NOT NULL,
  `description` text NOT NULL,
  `how_long` decimal(10,2) NOT NULL DEFAULT '0.00',
  `ip` varchar(50) NOT NULL,
  `cron_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `type` (`type`,`status`),
  KEY `cron_id` (`cron_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `send_loop`
--

CREATE TABLE IF NOT EXISTS `send_loop` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subs_id` int(11) NOT NULL,
  `myads_id` int(11) NOT NULL,
  `sended` int(11) NOT NULL DEFAULT '0',
  `is_view` smallint(1) NOT NULL DEFAULT '0',
  `last_send` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subs_id` (`subs_id`,`myads_id`),
  KEY `is_view` (`is_view`),
  KEY `subs_id_2` (`subs_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='лог рассылок до просмотра объявления' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `send_report`
--

CREATE TABLE IF NOT EXISTS `send_report` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL DEFAULT '0',
  `sid` int(10) unsigned NOT NULL,
  `createtime` datetime NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `feed_hash` varchar(64) NOT NULL,
  `subscriber_id` int(11) NOT NULL,
  `subid` varchar(50) NOT NULL,
  `tag` varchar(100) NOT NULL,
  `clicks` int(11) NOT NULL DEFAULT '0',
  `click_time` datetime NOT NULL,
  `view_time` datetime NOT NULL,
  `feed_id` smallint(1) NOT NULL DEFAULT '0',
  `adv_id` int(10) NOT NULL DEFAULT '0',
  `feed_adv_id` int(11) NOT NULL DEFAULT '0',
  `money` decimal(5,2) NOT NULL DEFAULT '0.00',
  `ctr` decimal(5,3) NOT NULL DEFAULT '0.000',
  `unsubs` smallint(1) NOT NULL DEFAULT '0',
  `min_price` decimal(5,3) NOT NULL DEFAULT '0.000',
  `max_price` decimal(5,3) NOT NULL DEFAULT '0.000',
  `min_ctr` decimal(5,3) NOT NULL DEFAULT '0.000',
  `max_ctr` decimal(5,3) NOT NULL DEFAULT '0.000',
  `all_advs` mediumint(5) NOT NULL DEFAULT '0',
  `hours` smallint(2) NOT NULL DEFAULT '0',
  `position` mediumint(3) NOT NULL DEFAULT '1',
  `comment` varchar(300) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_unique` (`subscriber_id`,`feed_hash`,`createtime`),
  KEY `sid` (`sid`),
  KEY `date` (`last_update`),
  KEY `subscriber_id` (`subscriber_id`),
  KEY `feed_hash` (`feed_hash`,`unsubs`),
  KEY `tag` (`tag`),
  KEY `subid` (`subid`),
  KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL,
  `value` text NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`admin_id`,`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1030 ;

--
-- Дамп данных таблицы `settings`
--

INSERT INTO `settings` (`id`, `admin_id`, `name`, `value`, `created`) VALUES
(4, 0, 'enable_messaging', '1', '2018-07-25 10:59:58'),
(5, 0, 'send_every', '4', '2018-07-25 10:59:58'),
(6, 0, 'user_messages', '2', '2018-07-25 10:59:58'),
(7, 0, 'dont_send_after_click', '1', '2018-07-25 10:59:58'),
(8, 0, 'mass_mess_count', '1000', '2018-07-25 10:59:58'),
(9, 0, 'trunk_title', '30', '2018-07-25 10:59:58'),
(10, 0, 'trunk_description', '120', '2018-07-25 10:59:58'),
(18, 0, 'domain', '', '2018-10-24 09:14:17'),
(45, 0, 'test', '0', '2018-11-05 13:26:03'),
(46, 0, 'test_token', '', '2018-11-05 13:26:09'),
(48, 0, 'kurs', '62', '2018-11-13 17:59:32'),
(49, 0, 'minstavka', '0.001', '2018-11-17 07:04:17'),
(50, 0, 'stopwords', '', '2018-12-27 08:13:58'),
(51, 0, 'block_ctr', '0', '2018-12-28 23:48:33'),
(52, 0, 'block_unsubs', '1', '2018-12-28 23:48:33'),
(53, 0, 'sorttype', '1', '2019-01-09 00:13:47'),
(54, 0, 'user_messages_days', '3', '2019-01-09 23:25:15'),
(55, 0, 'max_send', '3', '2019-01-09 23:26:26'),
(56, 0, 'max_adv_send', '1000', '2019-01-09 23:29:20'),
(57, 0, 'cr_block', '0.3', '2019-01-17 00:26:06'),
(58, 0, 'lang', 'ru', '2019-02-11 06:51:49'),
(59, 0, 'time_to_live', '30', '2019-02-28 10:19:58'),
(60, 0, 'image_send', '1', '2019-02-28 10:25:51'),
(61, 0, 'server_key', 'key', '2019-02-28 10:34:26'),
(62, 0, 'check_url', '1', '2019-03-08 05:28:02'),
(64, 0, 'firebase_conf', '', '2019-03-09 17:56:18'),
(65, 0, 'test_id', '1', '2019-03-20 08:00:35'),
(67, 0, 'days_stat', '14', '2019-03-30 15:40:24'),
(68, 0, 'timezone', 'Europe/Moscow', '2019-04-06 04:38:13'),
(589, 0, 'minsumma', '1', '2019-05-06 10:12:59'),
(593, 0, 'sitename', '3xpush', '2019-05-11 11:01:47'),
(594, 0, 'minsumma_checkout', '1', '2019-05-11 15:42:46'),
(619, 0, 'support_mail', 'support@localhost', '2019-05-17 07:56:31'),
(620, 0, 'from_mail', 'noreply@localhost', '2019-05-17 07:56:52'),
(622, 0, 'siteurl', 'localhost', '2019-05-21 10:28:21'),
(767, 0, 'domain_code', 'localhost', '2019-05-23 14:37:08'),
(768, 0, 'domain_link', 'localhost', '2019-05-23 15:13:24'),
(769, 0, 'telegram', '', '2019-06-04 05:54:42'),
(772, 0, 'send_fornew', '1', '2019-06-08 15:27:36'),
(773, 0, 'send_afterview', '2', '2019-06-08 15:27:36'),
(774, 0, 'send_afterclick', '3', '2019-06-08 15:27:42'),
(789, 0, 'system_mail', 'system@localhost', '2019-07-18 13:06:13'),
(940, 0, 'feeds_proc', '10', '2019-08-18 07:00:36'),
(982, 0, 'sending_on', '1', '2020-02-10 00:28:41'),
(983, 0, 'traf_exchange_on', '1', '2020-02-10 00:33:07'),
(986, 0, 'register_on', '1', '2020-02-10 00:54:23'),
(987, 0, 'email_confirm', '1', '2020-02-10 02:16:57'),
(988, 0, 'black_ip', '', '2020-02-10 11:38:45'),
(993, 0, 'google_recaptcha', '', '2020-02-11 14:16:27'),
(994, 0, 'captcha_register', '0', '2020-02-11 14:26:22'),
(995, 0, 'captcha_login', '0', '2020-02-12 04:12:11'),
(996, 0, 'google_recaptcha_public', '', '2020-02-12 04:18:34'),
(999, 0, 'allow_copyland', '1', '2020-04-19 06:56:47'),
(1000, 0, 'exchange_min', '100', '2020-04-19 10:55:33'),
(1001, 0, 'allow_referal', '1', '2020-04-19 11:35:32'),
(1002, 0, 'referal_manual', '1', '2020-04-19 15:07:11'),
(1003, 0, 'ns_domain', 'ns1.domain.com,ns2.domain.com', '2020-04-19 15:15:25'),
(1004, 0, 'allow_domains', '0', '2020-04-19 16:44:09'),
(1005, 0, 'langs', 'ru,en', '2020-04-20 05:47:11'),
(1027, 0, 'feed_timeout', '2', '2020-05-14 12:26:53'),
(1028, 0, 'allow_admins', '1', '2020-05-15 08:57:12'),
(1029, 0, 'allow_options', '0', '2020-05-15 09:07:58');

-- --------------------------------------------------------

--
-- Структура таблицы `sites`
--

CREATE TABLE IF NOT EXISTS `sites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL DEFAULT '0',
  `date` date NOT NULL,
  `type` smallint(1) NOT NULL DEFAULT '1',
  `title` varchar(300) NOT NULL,
  `url` varchar(50) NOT NULL,
  `subscribers` int(11) NOT NULL DEFAULT '0',
  `unsubs` int(11) NOT NULL DEFAULT '0',
  `last_subscribe` datetime NOT NULL,
  `html` text NOT NULL,
  `land_options` text NOT NULL,
  `iframe_options` text NOT NULL,
  `postback` text NOT NULL,
  `land_id` varchar(100) NOT NULL DEFAULT '0',
  `partner_api` smallint(1) NOT NULL DEFAULT '0' COMMENT '1 - api get adv',
  `comission` int(5) NOT NULL DEFAULT '0' COMMENT 'комиссия для сайта',
  `status` smallint(1) NOT NULL DEFAULT '1',
  `request_limit` int(11) NOT NULL DEFAULT '0' COMMENT 'лимит запросов в час',
  `clickconf` text NOT NULL,
  `cid_filter` varchar(300) NOT NULL,
  `stopwords` text NOT NULL,
  `category` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `sites_category`
--

CREATE TABLE IF NOT EXISTS `sites_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titles` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

--
-- Дамп данных таблицы `sites_category`
--

INSERT INTO `sites_category` (`id`, `titles`) VALUES
(1, '{"ru":"Общая","en":"General"}'),
(2, '{"ru":"Игры","en":"Games"}'),
(3, '{"ru":"Кино, музыка","en":"Cinema, music"}'),
(4, '{"ru":"Новости","en":"News"}'),
(5, '{"ru":"Женские сайты","en":"Women sites"}'),
(6, '{"ru":"Здоровье","en":"Health"}'),
(7, '{"ru":"Финансы","en":"Finance"}'),
(8, '{"ru":"Кулинария","en":"Cookery"}'),
(9, '{"ru":"Технологии","en":"Technologies"}'),
(10, '{"ru":"Развлекательный портал","en":"Entertainment portal"}'),
(11, '{"ru":"Криптовалюты","en":"Cryptocurrencies"}'),
(12, '{"ru":"Заработок","en":"Earnings"}'),
(13, '{"ru":"Сайты для взрослых","en":"Adult sites"}');

-- --------------------------------------------------------

--
-- Структура таблицы `sites_nopay`
--

CREATE TABLE IF NOT EXISTS `sites_nopay` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `sid` int(11) NOT NULL,
  `reason` varchar(50) NOT NULL,
  `clicks` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `date` (`date`,`sid`,`reason`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='причины отклоненных кликов с сайтов' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `subscribers`
--

CREATE TABLE IF NOT EXISTS `subscribers` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL DEFAULT '0',
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT 'подписчик id из admins',
  `sid` int(11) DEFAULT NULL,
  `token` char(200) DEFAULT NULL,
  `ip` char(30) DEFAULT NULL,
  `browser` char(255) DEFAULT NULL,
  `os` varchar(30) NOT NULL,
  `browser_short` varchar(20) NOT NULL,
  `lang` varchar(5) NOT NULL DEFAULT 'ru' COMMENT 'HTTP_ACCEPT_LANGUAGE',
  `device` varchar(30) NOT NULL COMMENT 'mobile device',
  `brand` varchar(30) NOT NULL COMMENT 'mobile device brand',
  `model` varchar(30) NOT NULL COMMENT 'mobile device model',
  `createtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_update` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_send` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `next_send` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_send_id` int(11) NOT NULL DEFAULT '0',
  `subid` varchar(50) NOT NULL COMMENT 'subaccount',
  `tag` varchar(100) NOT NULL,
  `referer` varchar(300) NOT NULL,
  `del` smallint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1 - unsubscribed',
  `sended` int(11) NOT NULL DEFAULT '0',
  `views` int(11) NOT NULL DEFAULT '0',
  `clicks` int(11) NOT NULL DEFAULT '0',
  `subs_type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `empty` int(11) NOT NULL DEFAULT '0',
  `cc` varchar(5) NOT NULL,
  `region` varchar(100) NOT NULL,
  `city` varchar(50) NOT NULL,
  `last_hash_adv` varchar(300) NOT NULL,
  `conversion` int(11) NOT NULL DEFAULT '0',
  `money` decimal(10,3) NOT NULL DEFAULT '0.000',
  `timezone` varchar(100) NOT NULL,
  `sender_id` varchar(300) NOT NULL,
  `browser_id` int(11) NOT NULL DEFAULT '0',
  `ip_range` int(5) NOT NULL DEFAULT '0',
  `comment` varchar(300) NOT NULL,
  `page_title` varchar(300) NOT NULL COMMENT 'заголовок страницы откуда подписался',
  `user_from` varchar(300) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `token` (`token`),
  KEY `brand` (`brand`,`model`),
  KEY `cc` (`cc`),
  KEY `del` (`del`),
  KEY `os` (`os`),
  KEY `admin_id` (`admin_id`),
  KEY `ip_range` (`ip_range`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Структура таблицы `sysstat`
--

CREATE TABLE IF NOT EXISTS `sysstat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `type` int(11) NOT NULL DEFAULT '0',
  `data` longtext NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `date` (`date`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='system stat' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `sysstat_hour`
--

CREATE TABLE IF NOT EXISTS `sysstat_hour` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `hour` int(11) NOT NULL DEFAULT '0',
  `data` longtext NOT NULL,
  `getads_cache` int(11) NOT NULL DEFAULT '0',
  `getads_requests` int(11) NOT NULL DEFAULT '0',
  `getads_time` decimal(10,3) NOT NULL DEFAULT '0.000',
  `is_ads` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `date` (`date`,`hour`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='почасовая системная стата' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `temp_table`
--

CREATE TABLE IF NOT EXISTS `temp_table` (
  `name` varchar(50) NOT NULL,
  `value` longtext NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `temp_table`
--

INSERT INTO `temp_table` (`name`, `value`) VALUES
('browser_stat', ''),
('copypage', ''),
('country_stat', ''),
('last_journal_id', ''),
('tags', '');

-- --------------------------------------------------------

--
-- Структура таблицы `total_stat`
--

CREATE TABLE IF NOT EXISTS `total_stat` (
  `admin_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(20) NOT NULL,
  `value` decimal(15,3) NOT NULL DEFAULT '0.000',
  PRIMARY KEY (`name`,`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Структура таблицы `user_activity_stat`
--

CREATE TABLE IF NOT EXISTS `user_activity_stat` (
  `date` date NOT NULL,
  `type` smallint(1) NOT NULL DEFAULT '0',
  `requests` int(11) NOT NULL DEFAULT '0',
  `views` int(11) NOT NULL DEFAULT '0',
  `clicks` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `date` (`date`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='стата по типам активности юзеров';

--
-- Ограничения внешнего ключа таблицы `macros_langs`
--
ALTER TABLE `macros_langs`
  ADD CONSTRAINT `FK_macros_langs_macros` FOREIGN KEY (`macros_id`) REFERENCES `macros` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
  
 INSERT INTO `settings` (`id`, `admin_id`, `name`, `value`, `created`) VALUES (NULL, '0', 'check_inputs_blockip', '1', CURRENT_TIMESTAMP), (NULL, '0', 'check_inputs_blockuser', '1', CURRENT_TIMESTAMP), (NULL, '0', 'check_inputs_alert', '1', CURRENT_TIMESTAMP), (NULL, '0', 'check_inputs', '1', CURRENT_TIMESTAMP), (NULL, '0', 'check_inputs_count', '3', CURRENT_TIMESTAMP);
 
ALTER TABLE `admins` ADD `good_user` SMALLINT( 1 ) NOT NULL DEFAULT '0' COMMENT '1 - verified';

ALTER TABLE `subscribers` ADD `country` VARCHAR( 50 ) NOT NULL AFTER `cc`;
 
 
