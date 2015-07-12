<?php namespace App\Ycc;

use Illuminate\Container\Container;

class CacheCore
{
	protected $key;

	protected $app;

	public function __construct(Container $app, $key)
	{
		$this->app  = $app;
		$this->key = $key;
	}

	public function retrieveCache()
	{
		return $this->app['cache']->get($this->key);
	}

	public function hit()
	{
		return $this->app['cache']->has($this->key);
	}

	public function cache($content)
	{
		if (!$content) {
			return false;
		}

		// put timestamp 
		$content .= PHP_EOL.'<!-- '.date('Y-m-d H:i:s').' cached-->';

		return $this->app['cache']->put(
			$this->key, $content, $this->getLifetime()
		);
	}

	public function flush()
	{
	}

	public function getLifetime()
	{
		return (int) $this->app['config']->get('fpcache')['lifetime'] ?: 999 * 999;
	}

	public function getKey()
	{
		return $this->key;
	}
}
