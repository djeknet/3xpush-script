<?php

// кирилица

class PageParser
{
    const PAGES_DIR = 'landings/';

    private $pageUrl;
    private $replaces;
    private $charset;

    private $pageId;
    private $copiedFiles = array();
    private $proxy;
    private $errors = array();

    private $siteUrl;
    private $pathFull;

    private $allowedExt = array(
        'png', 'css', 'jpg', 'js', 'gif', 'bmp', 'jpeg', 'eot', 'woff', 'ttf',
    );

    private $allowedInCss = array(
        'png', 'jpg', 'gif', 'bmp', 'jpeg', 'eot', 'woff', 'ttf', 'css',
    );

    private $removedFiles = false;

    static $debugMode = false; // Режим тестирования

    public function __construct($url, $replaces = array(), $siteUrl = '')
    {
        ini_set('max_execution_time', 1000);

        $this->siteUrl = $siteUrl;
        $this->pageUrl = $url;
        $this->replaces = $replaces;
    }

    public function setRemoveFiles($remove)
    {
        $this->removedFiles = $remove;
    }

    public function setPageId($pageId)
    {
        $this->pageId = $pageId;
    }

    public function getPathFull()
    {
        return $this->pathFull;
    }

    public function useProxy($proxy)
    {
        $this->proxy = $proxy;
    }

    private function _fileCopy($fileUrl, $parentUrl = null, $nesting = 0)
    {
        if ($nesting >= 100) exit('level 100 nesting reached');

        if (!file_exists(
            $path = ROOT_DIR .
                self::PAGES_DIR .
                $this->pageId . '/data/'
        )) {
            mkdir($path);
        }

        $ext = @strtolower(end(explode('.', $fileUrl)));

        if ($parentUrl) {
            $allowed = $this->allowedInCss;
        } else {
            $allowed = $this->allowedExt;
        }

        if (!in_array($ext, $allowed)) {
            return false;
        }

        $finf = $this->_getFileInfo($fileUrl, $parentUrl);

        if (in_array($finf['realUrl'], $this->copiedFiles)) return;
        $this->copiedFiles[] = $finf['realUrl'];

        $cont = $this->_getFileContents($finf['realUrl']);

        if (!$cont) {
            return;
        }

        $t = explode('.', $finf['baseName']);
        $ext = end($t);

        // remove php tags
        if (in_array($ext, array('css', 'js'))) {
            $cont = preg_replace('/<\?(php)?([\w\W\s]*)\?>/', '', $cont);
        }

        $cont = preg_replace('/\<\!\-\-([\w\W\n]*)\-\-\>/U', '', $cont);

        if ($ext == 'css') {

            // remove comments
            $cont = preg_replace('/\/\*([\n\w\W]*)\*\//U', '', $cont);

            $findFiles = $this->_findFiles($cont);

            foreach ($findFiles as $fileUrl) {

                try {
                    $fileUrlCopy = $this->_fileCopy($fileUrl, $finf['realUrl'], $nesting++);
                    PageParser::debug(__LINE__ . ' fileUrl ' . $fileUrl . ' fileUrlCopy ' . $fileUrlCopy);
                    $cont = str_replace(
                        $fileUrl,
                        $fileUrlCopy,
                        $cont
                    );
                } catch (Exception $e) {
                    $this->errors[] = $e->getMessage();
                }
            }
        }

        while (file_exists($path . $finf['baseName'])) {
            $finf['baseName'] = rand(10000, 99999) . '_' . $finf['baseName'];
        }

        $finf['baseName'] = md5($finf['baseName']) . '_' . str_replace('.' . $ext, '__.' . $ext, $finf['baseName']);

        file_put_contents($path . $finf['baseName'], $cont);

        if (!$parentUrl) {
//            return '../landings/' . $this->pageId . '/data/' . $finf['baseName'];
            $url = '/' . self::PAGES_DIR . $this->pageId . '/data/' . $finf['baseName'];
            return $url;
        } else {
            PageParser::debug(__LINE__ . ' basename ' . $finf['baseName']);
            return $finf['baseName'];
        }
    }

    private function _normalizeUrl($url)
    {
        $url = str_replace('http://', '', $url);
        $exp = explode('/', $url);

        $host = 'http://' . array_shift($exp) . '/';
        $result = array();

        foreach ($exp as $elem) {
            if ($elem == '.') continue;

            if ($elem == '..') {
                array_pop($result);
                continue;
            }

            $result[] = $elem;
        }

        return $host . implode('/', $result);
    }

    function url_exists($url)
    {
        $result = get_headers($url, 1);

        if (stripos($result[0], '404')) {
            return false;
        }

        return true;
    }

    private function _getRealUrl($url)
    {
        $scheme = parse_url($this->pageUrl);

        if (substr($this->pageUrl, -1) != '/') {

            $realUrl = dirname($this->pageUrl) . '/' . $url;

            if (!$this->url_exists($realUrl)) {
                $realUrl = $scheme['scheme'] . '://' . $scheme['host'] . '/' . $url;
            }

        } else {
            $realUrl = $this->pageUrl . $url;
        }

        return $realUrl;
    }

    private function _getFileInfo($url, $parentUrl = null)
    {
        $baseName = basename($url);
        $scheme = parse_url($this->pageUrl);
        $url = trim($url);

        if (stripos($url, 'http') === false) {

            if (substr($url, 0, 2) !== '//') {

                if (substr($url, 0, 1) == '/') {

                    $realUrl = $scheme['scheme'] . '://' . $scheme['host'] . $url;
                    PageParser::debug(__LINE__ . ' url: ' . $url . ' realUrl ' . $realUrl . "\n");

                } else {

                    if ($parentUrl) {
                        $realUrl = dirname($parentUrl) . '/' . $url;
                        PageParser::debug(__LINE__ . ' url: ' . $url . ' realUrl ' . $realUrl . "\n");
                    } else {
                        $realUrl = $this->_getRealUrl($url);
                        PageParser::debug(__LINE__ . ' url: ' . $url . ' realUrl ' . $realUrl . "\n");
                    }
                }

            } else {

                if (substr($url, 0, 2) == '//') {
                    $url = $scheme['scheme'] . ':' . $url;
                } else {
                    $url = dirname($this->pageUrl) . '/' . $url;
                }

                $realUrl = $url;
                PageParser::debug(__LINE__ . ' url: ' . $url . ' realUrl ' . $realUrl . "\n");

            }

        } else {
            $realUrl = $url;
        }

        // normalize path
        //$realUrl = $this->_normalizeUrl($realUrl);

        return array(
            'baseName' => $baseName,
            'realUrl' => $realUrl,
        );
    }

    private function _getFileContents($fileUrl, $withHeaders = false)
    {
        $opts = array(
            'http' => array(
                'timeout' => 5,
            )
        );

        if ($this->proxy) {
            $opts['http']['proxy'] = 'tcp://' . $this->proxy;
        }

        if ($this->pageUrl != $fileUrl) {
            //return '';
        }

        $context = stream_context_create($opts);
        $content = @file_get_contents($fileUrl, false, $context);

        if ($content === false) {
            throw new Exception('File ' . $fileUrl . ' cant be downloaded');
        }

        if ($withHeaders) {
            return array(
                'content' => $content,
                'headers' => $http_response_header,
            );
        }

        return $content;
    }

    public function pageCopyAndGetId()
    {
//        do {
//            $this->pageId = substr(md5(microtime()),rand(0,26), 5);
//        } while (file_exists($path = ROOT_DIR . self::PAGES_DIR . $this->pageId . '/'));
//
//        mkdir($path);

        $path = ROOT_DIR . self::PAGES_DIR . $this->pageId . '/';

        if (is_dir($path)) {
            $this->deleteDirectory($path);
        }

        if (!is_dir(ROOT_DIR . self::PAGES_DIR)) {
            mkdir(ROOT_DIR . self::PAGES_DIR);
        }

        $result = mkdir($path);

        if ($result) {

            try {
                $cont = $this->_getFileContents($this->pageUrl, true);
            } catch (Exception $e) {
                return array('status' => 'bad', 'errors' => array($e->getMessage()));
            }

            foreach ($cont['headers'] as $header) {
                if (preg_match('/charset=([^"\';]+)/', $header, $charsetMatch)) {
                    $this->charset = $charsetMatch[1];
                }
            }

            $cont = $cont['content'];

            if (!$this->charset) {
                if (preg_match('/<meta.*?charset=([^\"\']+)/', $cont, $charsetMatch)) {
                    $this->charset = $charsetMatch[2];
                }

                if (preg_match('/<meta ([\w\W]*)charset=([\w\W]+)(\"|\')/U', $cont, $charsetMatch)) {
                    $this->charset = str_replace(array('"', "'"), '', $charsetMatch[2]);
                }
            }

            if ($this->charset && strtolower($this->charset) != 'windows-1251') {
                // $cont = @iconv($this->charset, 'windows-1251//IGNORE', $cont);
                $cont = mb_convert_encoding($cont, 'windows-1251', $this->charset);
            }

            $cont = str_replace(array('utf-8', 'UTF-8'), 'windows-1251', $cont);

            foreach ($this->replaces as $replace) {
                $cont = str_replace(
                    iconv('utf-8', 'windows-1251', $replace['what']),
                    iconv('utf-8', 'windows-1251', $replace['to']),
                    $cont
                );
            }

            if (preg_match('/<base([^\>]) href=(\'|")([\w\W]*)(\'|")/U', $cont, $match)) {
                $baseUrl = $match[3];

                if (strpos($baseUrl, 'http://') !== false) {
                    $this->pageUrl = $baseUrl;
                } elseif (substr($baseUrl, 0, 1) == '/') {
                    $parsedUrl = parse_url($this->pageUrl);
                    $this->pageUrl = $parsedUrl['scheme'] . ':// ' . $parsedUrl['host'] . $baseUrl;
                } elseif (substr($this->pageUrl, -1, 1) == '/') {
                    $this->pageUrl .= $baseUrl;
                } else {
                    $this->pageUrl = dirname($this->pageUrl) . '/' . $baseUrl;
                }

                $cont = preg_replace('/<base([\w\W\s]*)>/U', '', $cont);
            }

            $cont = preg_replace('/<\?(php)?([\w\W\s]*)\?>/U', '', $cont);

            if ($this->removedFiles) {
                $cont = $this->remove_js($cont);
            }

            $findFiles = $this->_findFiles($cont);
            PageParser::debug($findFiles);

            foreach ($findFiles as $fileUrl) {

                try {
                    $fileUrlCopy = $this->_fileCopy($fileUrl);
                    PageParser::debug(__LINE__ . ' fileUrl ' . $fileUrl . ' fileUrlCopy ' . $fileUrlCopy);
                    $cont = str_replace(
                        $fileUrl,
                        $fileUrlCopy,
                        $cont
                    );

                    $cont = str_replace('https:h', 'h', $cont);

                } catch (Exception $e) {
                    $this->errors[] = $e->getMessage();
                }
            }

            $this->pathFull = $path . 'index.html';
            PageParser::debug(__LINE__ . ' path ' . $this->pathFull);
            file_put_contents($this->pathFull, $cont);

        }


        return array(
            'status' => 'ok',
            'errors' => $this->errors,
            'id' => $this->pageId,
        );
    }

    private function _findFiles($content)
    {
        PageParser::debug(__METHOD__);

        preg_match_all('/(http\:\/\/)?([\w\-\_\+\*\/\.\(\)]+)\.(' . implode('|', $this->allowedExt) . ')/', $content, $matches);

        usort($matches[0], function ($a, $b) {
            return strlen($b) - strlen($a);
        });

        $matches[0] = array_map('trim', $matches[0]);

        foreach ($matches[0] as $k => &$file) {

            if (substr($file, 0, 1) == '(') {
                unset($matches[0][$k]);
                continue;
            }

            if (substr($file, 0, 4) == 'url(') {
                $file = substr($file, 4);
            }
        }

        return array_unique(array_filter($matches[0]));
    }

    function remove_js($htmlContent)
    {
        $htmlContent = preg_replace('#<script[^>]*>.*?</script>#is', '', $htmlContent);
        return $htmlContent;

        preg_match_all('#<script(.*?)>(.*?)</script>#is', $htmlContent, $html);

        for ($i = 0; $i < count($html[0]); $i++) {
            $script = $html[0][$i];

            if (stripos($script, 'src')) {
                $htmlContent = str_replace($script, '', $htmlContent);
            }
        }

        return $htmlContent;
    }

    private function deleteDirectory($dir)
    {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (!$this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }

        }

        return rmdir($dir);
    }

    public static function debug($var)
    {
        if (self::$debugMode == false)
            return false;
        //error_log(print_r($var, 1));
        var_dump($var);
    }

}
