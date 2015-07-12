# laravel_fullpagecache
Full page cache + Lazy load for laravel5

Setup
1. Put Ycc folder into your App folder

2. Edit config/app.php
	put 'App\Ycc\FpCacheServiceProvider', into provider;
	put 'Fpcache'  => 'App\Ycc\Facades\Fpcache', into alias
	
3. Edit 'app/Http/Kernel.php'
	put 'App\Ycc\TerminateMiddleware', into global middleware. $middleware
	
4. Config 'app/Ycc/config/fpcache.php'
	You can enable/disable full page cache. Also can whitelist and blacklist.

