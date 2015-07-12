<?php namespace App\Ycc;

use Illuminate\Contracts\Routing\TerminableMiddleware;
use Closure;
use Fpcache;

class TerminateMiddleware implements TerminableMiddleware {
	public function handle($request, Closure $next)
	{
		return $next($request);
	}

	public function terminate($request, $response)
	{
		Fpcache::finish($response);
	}
}
