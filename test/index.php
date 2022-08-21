<?php

    require_once ('SunCache.php'); // Call 'SunCache' class

    $config = [
        'cacheDir'      => 'suncache', // cache folder path
        'fileExtension' => 'scf', // cache file extension
        'storageTime'   => 24*60*60, // cache storage time (seconds)
        'excludeFiles'  => ['file1.php', 'file2.php'], // exclude files from caching (with extensions)
        'contentMinify' => true, // cahe content minification
        'showTime'      => true, // show page load time
        'sefUrl'        => false // website sef url status
    ];

    $cache = new SunCache(true, $config); // Cache files using parameters in an external array

    /*
    // Cache files using parameters in an internal array
    $cache = new SunCache(true, ['cacheDir' => 'suncache', 'fileExtension' => 'scf', 'storageTime' => 60*60, 'contentMinify' => true]);
    */


    /*
    // Cache files using default parameters (set in class)
    $cache = new SunCache(true);
    */


    /*
    // Cache files using default parameters except the time (limited time caching)
    $cache = new SunCache(true, ['storageTime' => 3600]);
    */


    /*
    // Example for deleting all cached files (empty cache folder)
    $cache = new SunCache(false, $config);
    $cache->emptyCache();
    */


    /*
    // Example for deleting specific cached file
    $cache = new SunCache(false, $config);
    $cache->deleteCache('cachedFileName'); // write only file name (without extension)
    */


    /*
    // Example for deleting specific cached files
    $cache = new SunCache(false, $config);
    $cache->deleteCache(['cachedFileName1', 'cachedFileName2', 'cachedFileName3']); // write only file names in an array (without extension)
    */
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SunCache</title>
  </head>
  <body>
    <h3>Welcome to SunCache Default Page!</h3>
    <p>Uncomment the lines to see other examples.</p>
    <p><?php echo 'Dynamic Content: '.date('H:i:s'); ?></p>
    <p><?php echo 'Memory Usage: '.$sitemap->memoryUsage().' mb.'; ?></p>
    <p><?php echo 'Page Loading Duration: '.$sitemap->showDuration().' s.'; ?></p>
  </body>
</html>
