<?php namespace App\Ycc\Facades;

use Illuminate\Support\Facades\Facade;
use App\Ycc\FpCacheServiceProvider;

class Fpcache extends Facade {
	protected static function getFacadeAccessor() 
	{ 
		if (!static::$app) {
			static::$app = new Container();
			$provider = new FpCacheServiceProvider(static::$app);
			$provider->register();
		}
		return "Fpcache";
	}
}
