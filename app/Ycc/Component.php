<?php namespace App\Ycc;

use Illuminate\Container\Container;

class Component
{
	protected $app;

	public function __construct(Container $app)
	{
		$this->app = $app;
	}

	public function inUse()
	{
		$enabled = (boolean)$this->app['config']->get('fpcache')['enabled'];
		return $enabled && $this->canCachePage();
	}

	public function canCachePage()
	{
		$fpcacheconfig = $this->app['config']->get('fpcache');
		if ($this->app['request']->isXmlHttpRequest() || $this->app['request']->getMethod() !== 'GET') {
			return false;
		}
		if ($this->app['request']->request->get("_load_no_cache_diff_")){ return false;}

		$whitelist = $this->app['cache']->remember("fpcache.whitelist", 10, function() use($fpcacheconfig){
			return '#|'.implode('|', $fpcacheconfig['whitelist']).'|#';
		});
		$blacklist = $this->app['cache']->remember("fpcache.blacklist", 10, function() use($fpcacheconfig){
			return '#|'.implode('|', $fpcacheconfig['blacklist']).'|#';
		});

		if ($this->hit($blacklist)) {
			return false;
		}
		if ($this->hit($whitelist)) {
			return true;	
		}
		return true;
	}

	protected function hit($pages)
	{
		return (bool) strpos($pages, "|" . $this->getCurrentUrl() . "|");
	}

	public function getCurrentUrl($more=null)
	{
		$path  = '/'.ltrim($this->app['request']->path(), '/');
		$query = $this->app['request']->getQueryString();

		if ($query || $more){
			$query .= "&$more";
		}
		return $query ? $path.'?'.$query : $path;
	}
}
