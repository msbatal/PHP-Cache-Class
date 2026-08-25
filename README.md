# PHP Cache Class

SunCache is a simple, fast, and powerful PHP dynamic cache class that uses the file system for caching.

`Technical Document:` https://www.deepwiki.com/msbatal/PHP-Cache-Class

[![Ask DeepWiki](https://deepwiki.com/badge.svg)](https://deepwiki.com/msbatal/PHP-Cache-Class)

<hr>

### Table of Contents

- **[Initialization](#initialization)**
- **[Caching Files](#caching-files)**
- **[Limited Time Caching](#limited-time-caching)**
- **[Caching with Minified Content](#caching-with-minified-content)**
- **[Caching with SEF URL](#caching-with-sef-url)**
- **[Exclude Some Files from Caching](#exclude-some-files-from-caching)**
- **[Caching with Cookie Variants](#caching-with-cookie-variants)**
- **[Delete All Cached Files](#delete-all-cached-files)**
- **[Delete a Specific Cached File](#delete-a-specific-cached-file)**
- **[Delete Specific Cached Files](#delete-specific-cached-files)**

### Installation

Download all files (except Test directory), change the htaccess.txt file's name to the .htaccess, and move it to your cache directory. Htaccess file will be created automatically if it does not exist.

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
    'varyCookies'   => ['lang'], // cookie names that make the cache vary (a separate file per value)
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

### Caching with Cookie Variants

Some pages render different content depending on a cookie value, but caching by URL alone would serve that same cached copy to every visitor regardless of their own cookie. For example, a multi-language site where the language is stored in a cookie instead of the URL. `varyCookies` fixes this: give it a list of cookie names, and the class stores a separate cached file for each distinct combination of their values.

```php
$cache = new SunCache(true, ['varyCookies' => ['lang']]);
```

With this, a visitor whose `lang` cookie is `en` gets a different cached file than one whose `lang` cookie is `fr`, even though both requested the exact same URL. You can combine multiple cookies too:

```php
$cache = new SunCache(true, ['varyCookies' => ['lang', 'currency']]);
```

**Rule of thumb:** only add a cookie here if the number of values it can take stays small and fixed no matter how many visitors your site gets; a language code, a currency, an A/B test group, and similar. If a cookie's value is unique per visitor (a session ID, a "logged in" flag tied to a specific account, a user ID) don't put it in `varyCookies`; it would create one cached file per visitor and the cache directory would just keep growing without ever being reused. For that kind of per-visitor state, add the file to `excludeFiles` instead, or keep the personalized part out of the cached HTML entirely and fill it in on the client side (e.g., with a small script reading `localStorage` or making its own request).

**Restrict variants to known values (recommended for cookies a visitor can set):** the plain form above trusts whatever value the cookie carries. Since request cookies are fully attacker-controlled, someone could send thousands of made-up `lang` values just to make the class write a new cache file for each one, filling up your disk. Give a cookie name an array of allowed values instead of listing it as a plain string, and any value outside that list collapses into a single shared "other" bucket instead of creating a new variant:

```php
$cache = new SunCache(true, ['varyCookies' => ['lang' => ['en', 'es', 'fr', 'de']]]);
```

Plain cookie names (no allow-list) still work and are fine for cookies your own server sets and controls; just know that an unrestricted cookie name accepts any value up to a short length cap.

```php
$cache = new SunCache(true, ['excludeFiles' => ['account.php', 'cart.php']]); // personalized pages: don't cache at all
```

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

You should send the file name (without extension and underscore, for example: use `blog` instead of `blog_cd12f5.scf`) as a string parameter to the delete method.

If you send `test` as a file name, the class will delete all files containing `test` term (ex. test, test123, test_xxx, xxx_test_yyy, etc.).

```php
$cache->deleteCache('news'); // will be deleted all files contain 'news' term => xxx_news_yyy.zzz
$cache->deleteCache('blog'); // will be deleted all files contain 'blog' term => xxx_blog_yyy.zzz
```

### Delete Specific Cached Files

This method deletes some specific cached files in the cache directory.

```php
$cache = new SunCache(false, $config);
$cache->deleteCache(['cachedFileName1', 'cachedFileName2', 'cachedFileName3']); // file names (array)
```

Don't forget to create a new object with `false` parameter.

You should send all file names (without extension and underscore) as an array parameter to the delete method. If you send `test` as a file name, the class will delete all files containing `test` term (ex. test, test123, test_xxx, xxx_test_yyy, etc.).

```php
$cache->deleteCache(['news', 'blog']); // will be deleted all files contain 'news' or 'blog' terms => xxx_news_yyy.zzz // xxx_blog_yyy.zzz
```
