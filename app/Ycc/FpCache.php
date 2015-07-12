<?php namespace App\Ycc;

use Illuminate\Container\Container;
use Symfony\Component\HttpFoundation\Response;

class FpCache
{
	protected $app;

	public function __construct(Container $app)
	{
		$this->app = $app;
	}

	public function __call($method, $arguments)
	{
		$class = $this;

		$parts = array('cache', 'viewparser');
		foreach ($parts as $part) {
			$part = $this->app['Fpcache.'.$part];
			if (method_exists($part, $method)) {
				$class = $part;
				break;
			}
		}

		return call_user_func_array(array($class, $method), $arguments);
	}

	public function start()
	{
		if ($this->app['Fpcache.component']->inUse() &&
			$this->app['Fpcache.cache']->hit()) 
		{
                        return $this->app['Fpcache']->renderCache();
                }

	}

	public function finish($response = null)
	{
		if ($this->app['Fpcache.component']->inUse()) {
			if (
				!is_null($response) && (
					$response->isRedirection() ||
					$response->isNotFound() ||
					$response->isServerError() ||
					$response->isForbidden()
				)
			) {
				return false;
			}
			$content = $response ? $response->getContent() : ob_end_flush();
			$this->app['Fpcache.cache']->cache($content);
			return true;
		}
		return false;
	}

	public function getResponse($content = null)
	{
		if (!$content) {
			$content = $this->app['Fpcache.cache']->retrieveCache();
		}

		return new Response($content);
	}

	public function renderCache($content = null)
	{
		$this->getResponse($content)->send();
		exit;
	}

	public function computeKey($page = null)
	{
		if (!$page) {
			$page = $this->app['Fpcache.component']->getCurrentUrl();
		}

		$parts[] = $page;
		$parts[] = $this->app['request']->getMethod();

		return md5(implode('/', $parts));
	}
}
