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
	

5. Put public/js/lazypart.js to your app's js folder

Configuration

app/Ycc/config/fpcache.php        

        'enabled' => true,                           //enable or disable this module

        'blacklist'       => array("/auth/login"),   //not cached pages

        'whitelist'         => array(),              //pages you only for whitelist

        'lifetime'     => 100,                       //cached content lifetime

        'jslib'     => '<script src="/js/lazypart.js"></script>',   //lazyload js lib configuration
        
 
 Sample View templating
 
 Here's a sample view template, you just put 
    @lazyview('uniq-id-of-block')
    @endlazyview
 and this block will be lazyload by the module automaticly
 
 you can change app.blade.php 
 
                                 @lazyview('user-info')
                                <ul class="nav navbar-nav navbar-right">
                                        @if (Auth::guest())
                                                <li><a href="{{ url('/auth/login') }}">Login</a></li>
                                                <li><a href="{{ url('/auth/register') }}">Register</a></li>
                                        @else
                                                <li class="dropdown">
                                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">{{ Auth::user()->name }} <span class="caret"></span></a>
                                                        <ul class="dropdown-menu" role="menu">
                                                                <li><a href="{{ url('/auth/logout') }}">Logout</a></li>
                                                        </ul>
                                                </li>
                                        @endif
                                </ul>
                                @endlazyview





