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
		$app = $this->app;
		$blade->extend(function ($view, $blade) use($app) {
			$pattern = $blade->createOpenMatcher('lazyview');

			$newview = $view;
			$pickpattern = rtrim($pattern, "/") . "(.|\\n)*?@endlazyview/";
			if (preg_match_all($pickpattern, $newview, $matchs)){
				$newview = "";
				foreach($matchs[0] as $match){
					$newview .= $match;
				}
				$replace = '<?php echo App\Ycc\Facades\Fpcache::lazytag$2);?>';
				$newview = preg_replace($pattern, $replace, $newview);
				$newview = str_replace('@endlazyview', '</span>', $newview);
			} else {
				$newview = "";
			}

			#$replace = $app['config']->get('fpcache')['jslib'];
			$replace = '<?php echo App\Ycc\Facades\Fpcache::lazytag$2);?>';
			$view    = preg_replace($pattern, $replace, $view);
			$replace = $app['config']->get('fpcache')['jslib'] 
				. " <script>g_lazy.load('" 
				#. $app['Fpcache.component']->getCurrentUrl("_load_no_cache_diff_=1") 
				. "<?php echo App\Ycc\Facades\Fpcache::renewUrl() ?>"
				."');</script></body>";
			$view    = str_replace("</body>", $replace , $view);

			// Replace closing tag
			$view = str_replace('@endlazyview', '</span>', $view);
			$view = str_replace('Auth::guest()', 'true', $view);  //for cached page,lazyload first always return guest
			$view = '<?php if(!App\Ycc\Facades\Fpcache::loadDiff()){ ?>'
				. $view 
				. " <?php } else { echo '<!-- load diff --><br>';?>" 
				. $newview . " <?php } ?>";
		
			return $view;
		});
	}

	public function lazytag($name)
        {
		#if ($this->app['Fpcache.component']->inUse()){
			//for lazyload javascript to rewrite this section
			$name = $this->formatTagName($name);
			if ($this->loadDiff()){
				$lazyApply ="<script>g_lazy.replaceElement('#$name', '#$name"."L');</script>";
				$name .= "L";
				return "$lazyApply<span style='display:none' id='$name'>";
			}
			return "<span id='$name'>";
                #}
        }
        
        public function renewUrl(){
                 return $this->app['Fpcache.component']->getCurrentUrl("_load_no_cache_diff_=1");
        }

	public function loadDiff(){
		return $this->app['request']->request->get("_load_no_cache_diff_");
	}

	protected function formatTagName($name)
        {
                return 'lazyload-'. $name ;
        }
}
