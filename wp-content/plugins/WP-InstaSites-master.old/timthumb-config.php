<?php

if(defined(RP_CACHE_DIR)) {
	define ('FILE_CACHE_DIRECTORY', RP_CACHE_DIR);
}

define ('FILE_CACHE_TIME_BETWEEN_CLEANS', 604800);	// How often the cache is cleaned in seconds (604800 = 7days)
define ('FILE_CACHE_MAX_FILE_AGE', 604800);			// How old does a file have to be to be deleted from the cache in seconds (604800 = 7days)
define ('BROWSER_CACHE_MAX_AGE', 6048000);			// Time to cache in the browser in milliseconds (6048000 = 7days)