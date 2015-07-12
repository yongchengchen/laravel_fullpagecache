<?php namespace App\Ycc;

use Closure;
use Illuminate\Container\Container;

class ViewParser
{
	protected $app;
	public function __construct(Container $app)
	{
		$this->app = $app;
	}

	public function injectBlade()
	{
		$blade = $this->app['view']->getEngineResolver()->resolve('blade')->getCompiler();

		if ($this->app['Fpcache.component']->inUse()){
			$blade->extend(function ($view, $blade){
				$pattern = $blade->createOpenMatcher('lazyview');
				$replace = '<?php echo App\Ycc\Facades\Fpcache::lazyview$2, function() { ?>';
				$view    = preg_replace($pattern, $replace, $view);

				// Replace closing tag
				$view = str_replace('@endlazyview', '<?php }); ?>', $view);
				$view = str_replace('Auth::guest()', 'true', $view);  //for cached page,lazyload first always return guest
				return $view;
			});
		}
	}

	public function lazyview($name, $lifetime, $contents = null)
        {
                if (!$contents) {
                        $contents = $lifetime;
                        $lifetime = $this->app['Fpcache.cache']->getLifetime();
                }

		$tagname = $this->formatTagName($name);
                return $this->app['cache']->remember($tagname, $lifetime, function () use ($tagname, $contents) {
                        ob_start();
			echo "<span id='$sectionname'>"; //for lazyload javascript to rewrite this section
                        echo $contents();
			echo "</span>";
                        return ob_get_clean();
                });
        }

	protected function formatTagName($name)
        {
                return 'lazyload-'.$name;
        }


}
