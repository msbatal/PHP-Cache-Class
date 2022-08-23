# PHP Cache Class

SunCache is a simple, fast, and powerful PHP dynamic cache class that uses the file system for caching.

<hr>

### Table of Contents

- **[Initialization](#initialization)**
- **[Caching Files](#caching-files)**
- **[Limited Time Caching](#limited-time-caching)**
- **[Caching with Minified Content](#caching-with-minified-content)**
- **[Caching with SEF URL](#caching-with-sef-url)**
- **[Exclude Some Files from Caching](#exclude-some-files-from-caching)**
- **[Delete All Cached Files](#delete-all-cached-files)**
- **[Delete a Specific Cached File](#delete-a-specific-cached-file)**
- **[Delete Specific Cached Files](#delete-specific-cached-files)**

### Installation

Download all files (except Test directory), change the htaccess.txt file's name to the .htaccess (important), and move it to your cache directory.

To utilize this class, first import SunCache.php into your project, and require it.
SunCache requires PHP 5.5+ to work.

```php
require_once ('SunCache.php');
```

### Initialization

Simple initialization with default parameters:

```php
$cache = new SunCache(true);
```

Advanced initialization:

```php
$config = [
    'cacheDir'      => 'suncache', // cache folder path
    'fileExtension' => 'scf', // cache file extension
    'storageTime'   => 24*60*60, // cache storage time (seconds)
    'excludeFiles'  => ['file1.php', 'file2.php'], // exclude files from caching (with extensions)
    'contentMinify' => true, // cahe content minification
    'showTime'      => true, // show page load time
    'sefUrl'        => false // website sef url status
];
$cache = new SunCache(true, $config);
```

All config parameters are optional.

It will use default parameters that are set in the class if you don't specify the parameters while creating the object.

### Caching Files

Cache files using parameters in an internal array

```php
$cache = new SunCache(true, ['cacheDir' => 'suncache', 'fileExtension' => 'scf', 'storageTime' => 60*60, 'contentMinify' => true]);
```

Cache files using default parameters (set in class)

```php
$cache = new SunCache(true);
```

### Limited Time Caching

Cache files using default parameters except the time (limited time caching)

```php
$cache = new SunCache(true, ['storageTime' => 3600]); // or 60*60 (seconds)
```

### Caching with Minified Content

Cache files with minified content (remove all unwanted characters)

```php
$cache = new SunCache(true, ['contentMinify' => true]);
```

This may cause some problems with javascript code works (if you use js codes on the same page, inline, or top/bottom of the page).

### Caching with SEF URL

Cache files with SEF URL (if you use HTML extensions instead of PHP)

```php
$cache = new SunCache(true, ['sefUrl' => true]);
```

### Exclude Some Files from Caching

Exclude some specific files from caching.

```php
$cache = new SunCache(true, ['excludeFiles'  => ['file1.php', 'file2.php']]);
```

Don't forget to send the file names (with `php` extension) in an array parameter.

### Delete All Cached Files

This method deletes all cached files in the cache directory.

```php
$cache = new SunCache(false, $config);
$cache->emptyCache();
```

Don't forget to create a new object with `false` parameter.

### Delete a Specific Cached File

This method deletes a specific cached file in the cache directory.

```php
$cache = new SunCache(false, $config);
$cache->deleteCache('cachedFileName'); // file name (string)
```

Don't forget to create a new object with `false` parameter.

You should send the file name (without extension) as a string parameter to the delete method.

### Delete Specific Cached Files

This method deletes some specific cached files in the cache directory.

```php
$cache = new SunCache(false, $config);
$cache->deleteCache(['cachedFileName1', 'cachedFileName2', 'cachedFileName3']); // file names (array)
```

Don't forget to create a new object with `false` parameter.

You should send all file names (without extensions) as an array parameter to the delete method. If you send `filename` as a file name, the class will delete all files containing `filename` term (ex. filename, filename1, filename_xxx, xxx_filename_yyy, etc.).
