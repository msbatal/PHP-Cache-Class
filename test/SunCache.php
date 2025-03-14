<?php

/**
 * SunCache Class
 *
 * @category  Page Caching
 * @package   SunCache
 * @author    Mehmet Selcuk Batal <batalms@gmail.com>
 * @copyright Copyright (c) 2020, Sunhill Technology <www.sunhillint.com>
 * @license   https://opensource.org/licenses/lgpl-3.0.html The GNU Lesser General Public License, version 3.0
 * @link      https://github.com/msbatal/PHP-Cache-Class
 * @version   3.0.0
 */

class SunCache
{

    /**
     * Cache credentials
     * @var array
     */
    private $cacheParams = [];

    /**
     * Cache system (enabled or disabled)
     * @var boolean
     */
    private $cacheSystem = true;

    /**
     * Cache folder path
     * @var string
     */
    private $cacheDir = 'suncache';

    /**
     * Cache file name
     * @var string
     */
    private $cacheFile = null;

    /**
     * Cache file extension
     * @var string
     */
    private $fileExtension = 'scf';

    /**
     * Cache storage time (seconds)
     * @var integer
     */
    private $storageTime = 24 * 60 * 60;

    /**
     * Exclude files from caching (file_name.ext)
     * @var array
     */
    private $excludeFiles = [];

    /**
     * Cache status (will cache or not)
     * @var boolean
     */
    private $willCache = true;

    /**
     * Cache status (cached or not)
     * @var boolean
     */
    private $cacheStatus = false;

    /**
     * Start time (miliseconds)
     * @var integer
     */
    private $startTime = null;

    /**
     * Cache content minification
     * @var boolean
     */
    private $contentMinify = true;

    /**
     * Show page load time
     * @var boolean
     */
    private $showTime = true;

    /**
     * Website sef url status (uses or not)
     * @var boolean
     */
    private $sefUrl = false;

    /**
     * @param boolean $cacheSystem
     * @param array $cacheParams
     */
    public function __construct($cacheSystem = true, $cacheParams = null) {
        set_exception_handler(function ($exception) {
            echo '<b>[SunClass] Exception:</b> ' . $exception->getMessage();
        });
        $this->cacheSystem = $cacheSystem;
        if ($this->cacheSystem == true) { // if cache system enabled
            if (is_array($cacheParams)) { // cache files using parameters in the array
                foreach ($cacheParams as $key => $value) {
                    $this->cacheParams[$key] = $value;
                    if (isset($key) && !is_null($value)) {
                        $this->$key = $value;
                    } else {
                        $this->$key = $this->cacheParams[$key];
                    }
                }
                $this->htaccess();
            }
            if (is_array($this->excludeFiles) && count($this->excludeFiles) > 0) {
                $activePage = explode('/', $_SERVER['SCRIPT_FILENAME']); // get the active page
                foreach ($this->excludeFiles as $key => $value) {
                    if (in_array(end($activePage), $this->excludeFiles)) {
                        $this->willCache = false; // exclude the active page if in the array
                        break;
                    }
                }
            }
            if ($this->willCache == true) { // if the active page will cache
                if (!file_exists(dirname(__FILE__) . '/' . $this->cacheDir)) {
                    mkdir(dirname(__FILE__) . '/' . $this->cacheDir, 0777); // create directory if not exists
                }
                if ($this->showTime) { // if load time will show on the bottom of the page (hidden)
                    list($time[1], $time[0]) = explode(' ', microtime());
                    $this->startTime = $time[1] + $time[0];
                }
                if ($this->sefUrl == true) { // if website uses sef url
                    $extension = '.html';
                } else {
                    $extension = '.php';
                }
                $file = basename($_SERVER['REQUEST_URI'], $extension);
                $this->cacheFile = dirname(__FILE__) . '/' . $this->cacheDir . '/' . $file . '_' . substr(md5($_SERVER['REQUEST_URI']), 0, 6) . '.' . $this->fileExtension; // define the cache file
                if (time() - $this->storageTime < @filemtime($this->cacheFile)) { // if the storage time has not expired
                    if (filesize($this->cacheFile) > 0) { // if not cache file empty
                        readfile($this->cacheFile); // read cached file
                        $this->cacheStatus = true; // page cached
                        ob_end_flush(); // clear output buffer
                        exit();
                    } else {
                        unlink($this->cacheFile); // delete cached file
                    }
                } else { // if the storage time has expired
                    if (file_exists($this->cacheFile)) { // if cache file exists
                        unlink($this->cacheFile); // delete cached file
                    }
                    ob_start(); // start caching
                }
            }
        } else {
            $this->cacheDir = $cacheParams['cacheDir'];
            $this->fileExtension = $cacheParams['fileExtension'];
            $this->htaccess();
        }
    }

    /**
     * Finish Caching
     */
    public function __destruct() {
        if ($this->cacheSystem == true && $this->willCache == true) { // if cache system enabled and page will cache
            if ($this->cacheStatus == false) { // if page not cached before
                $content = ob_get_contents();
                if (!empty(trim($content))) { // if content not empty
                    $this->writeCache($this->contentMinify ? $this->minify($content) : $content); // write content into the cache file
                }
            }
            if ($this->showTime) { // if request to show time (bottom of the cached file, hidden)
                list($time[1], $time[0]) = explode(' ', microtime());
                $finish = $time[1] + $time[0];
                $duration = number_format(($finish - $this->startTime), 6);
                echo "<!-- Load Duration: {$duration} s. -->"; // add description
            }
            ob_end_flush(); // finish caching
        }
    }

    /**
     * Create htaccess file
     */
    private function htaccess() {
        $htaccessFile = dirname(__FILE__) . '/' . $this->cacheDir . '/.htaccess';
        if (!file_exists($htaccessFile)) { // if htaccess file not exists
            file_put_contents($htaccessFile, "order allow,deny\ndeny from all\nOptions All -Indexes"); // create htaccess file
        }
    }

    /**
     * Minify the page
     *
     * @param string $content
     * @return string
     */
    private function minify($content = null) {
        $replace = array(
            '/\>[^\S ]+/s' => '>',
            '/[^\S ]+\</s' => '<',
            '/([\t ])+/s' => ' ',
            '/^([\t ])+/m' => '',
            '/([\t ])+$/m' => '',
            '~//[a-zA-Z0-9 ]+$~m' => '',
            '/[\r\n]+([\t ]?[\r\n]+)+/s' => "\n",
            '/\>[\r\n\t ]+\</s' => '><',
            '/}[\r\n\t ]+/s' => '}',
            '/}[\r\n\t ]+,[\r\n\t ]+/s' => '},',
            '/\)[\r\n\t ]?{[\r\n\t ]+/s' => '){',
            '/,[\r\n\t ]?{[\r\n\t ]+/s' => ',{',
            '/\),[\r\n\t ]+/s' => '),',
            '~([\r\n\t ])?([a-zA-Z0-9]+)="([a-zA-Z0-9_/\\-]+)"([\r\n\t ])?~s' => '$1$2=$3$4'
        );
        $content = preg_replace(array_keys($replace), array_values($replace), $content);
        return $content;
    }

    /**
     * Write cached content
     *
     * @param string $content
     */
    private function writeCache($content) {
        if (empty(trim($content))) {
            return; // prevent writing empty content
        }
        $cacheFile = fopen($this->cacheFile, 'w'); // open cache file with 'write' mode
        if ($cacheFile == false) {
            throw new Exception('Cache file "'.$this->cacheFile.'" cannot be opened.');
        }
        $content .= "<!-- Cache Expiration: {$this->storageTime} s. -->"; // add storage time
        fwrite($cacheFile, $content); // write content into the cache file
        fclose($cacheFile); // close cache file
    }

    /**
     * Delete all cached files
     * 
     * @throws exception
     */
    public function emptyCache() {
        if (!file_exists($this->cacheDir)){
            throw new Exception('Cache directory "'.$this->cacheDir.'" does not exist.');
        } else {
            $cacheDir = opendir($this->cacheDir); // open cache directory
            while (($cacheFile = readdir($cacheDir)) !== false) { // read cache directory
                if (!is_dir($cacheFile) && $cacheFile != '.htaccess') { // if content is a file
                    unlink($this->cacheDir . '/' . $cacheFile); // delete cached file
                }
            }
            closedir($cacheDir); // close cache directory
        }
    }

    /**
     * Delete cached file(s)
     *
     * @param string|array $files
     */
    public function deleteCache($files = null) {
        if (is_array($files) && count($files) > 0) {
            $fileArray = $files;
        } else {
            $fileArray = array($files);
        }
        foreach ($fileArray as $file) {
            if (!empty($file)) {
                $list = glob(dirname(__FILE__) . '/' . $this->cacheDir . '/*' . $file . '*.' . $this->fileExtension);
                foreach ($list as $item) {
                    unlink($item); // delete cached file
                }
            }
        }
    }

}

?>
