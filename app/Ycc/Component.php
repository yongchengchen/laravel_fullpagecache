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

		$whitelist = $fpcacheconfig['whitelist'];
		$blacklist = $fpcacheconfig['blacklist'];

		if (empty($whitelist) && empty($blacklist)) {
			return true;
		} else {
			if (!empty($blacklist) && $this->matches($blacklist)) {
				return false;
			}
			if (!empty($whitelist) && $this->matches($whitelist)) {
				return true;	
			}
		}
		return false;
	}

	public function matches($pages)
	{
		$page    = $this->getCurrentUrl();
		$pattern = '#'.implode('|', $pages).'#';

		return (bool) preg_match($pattern, $page);
	}

	public function getCurrentUrl()
	{
		$path  = '/'.ltrim($this->app['request']->path(), '/');
		$query = $this->app['request']->getQueryString();

		return $query ? $path.'?'.$query : $path;
	}
}
