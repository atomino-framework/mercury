<?php namespace Atomino\Mercury\Responder\Smart;

use Atomino\Mercury\Responder\Smart\Attributes\Args;
use Atomino\Mercury\Responder\Smart\Attributes\Cache;
use Atomino\Mercury\Responder\Smart\Attributes\CSS;
use Atomino\Mercury\Responder\Smart\Attributes\Init;
use Atomino\Mercury\Responder\Smart\Attributes\JS;
use Atomino\Mercury\Responder\Responder;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Response;
use Twig\Cache\FilesystemCache;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

abstract class SmartResponder extends Responder {

	protected Environment $twig;
	protected ?string $template = null;
	protected \ReflectionClass $ref;

	#[ArrayShape([
		'js'              => 'array',
		'css'             => 'array',
		'frontendVersion' => 'int',
		'class'           => 'string',
		'language'        => 'string',
		'title'           => 'string',
		'data'            => ParameterBag::class,
		'url'             => 'string',
		'host'            => 'string',
	])]
	protected array $smart = [];

	public function setTitle(string $title) { $this->smart["title"] = $title; }
	public function setClass(string $class) { $this->smart["class"] = $class; }
	public function setLanguage(string $language) { $this->smart["language"] = $language; }
	public function getSmartDataBag() { return $this->smart["data"]; }

	protected function respond(Response $response): Response {
		$this->smart = [
			'js'              => [],
			'css'             => [],
			'frontendVersion' => 0,
			'class'           => null,
			'language'        => null,
			'title'           => null,
			'data'            => new ParameterBag(),
			'url'             => $this->request->getUri(),
			'host'            => $this->request->getSchemeAndHttpHost(),
		];

		$this->setup();
		$this->prepare($response);

		$smart = $this->smart;
		$smart['data'] = base64_encode(json_encode($this->smart['data']->all()));

		return $response->setContent(
			$this->twig->render(
				$this->template,
				[
					'smartpage' => $smart,
					'viewmodel' => (array)$this,
				]
			)
		);
	}

	abstract protected function prepare(Response $response);

	private function setup() {
		$smartResponderReflection = new \ReflectionClass(static::class);
		$initAttr = Init::get($smartResponderReflection);
		/** @var SmartResponderConfig $config */
		$config = $this->container->get(SmartResponderConfig::class);

		$this->template = $this->request->attributes->get('isMobile') ? $initAttr->mobileTemplate : $initAttr->template;
		$environment = $initAttr->environment;
		$this->smart['frontendVersion'] = $config['frontend-version'];

		$loader = new FilesystemLoader();
		$namespaces = $config('twig.namespaces');
		if (array_key_exists($environment, $namespaces)) $namespaces['__main__'] = $namespaces[$environment];
		$namespaces['smartpage'] = __DIR__ . '/@resource';
		foreach ($namespaces as $namespace => $path) $loader->addPath($path, $namespace);
		$twigDebug = $config('twig.debug');
		$this->twig = new Environment($loader, ['debug' => $twigDebug, 'auto_reload' => $twigDebug]);
		$this->twig->setCache($config('twig.cache'));

		Args::get($smartResponderReflection)?->set($this->smart);
		foreach (JS::all($smartResponderReflection) as $JS) $JS->set($this->smart);
		foreach (CSS::all($smartResponderReflection) as $CSS) $CSS->set($this->smart);
		Cache::get($smartResponderReflection);
	}
}






