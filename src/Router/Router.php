<?php namespace Atomino\Mercury\Router;


use Atomino\Mercury\Pipeline\Handler;
use Atomino\Mercury\Router\Matcher\HostMatcher;
use Atomino\Mercury\Router\Matcher\PathMatcher;
use Atomino\Mercury\Pipeline\Pipeline;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use function Atomino\inject;

abstract class Router extends Handler {

	private bool $matches = false;

	protected Request $request;

	public function handle(Request $request): Response|null {
		$this->request = $request;
		$this->route();
		return $this->next($this->request);
	}

	abstract protected function route(): void;

	public function __invoke(
		string|null $method = null,
		string|null|array $path = null,
		string|null $host = null,
		string|null $port = null,
		string|null $scheme = null): Pipeline|null {
		if ($this->matches) return null;
		$request = $this->request;
		if (
			(is_null($method) || $method === $this->request->getMethod()) &&
			(is_null($port) || $port == $this->request->getPort()) &&
			(is_null($scheme) || $scheme === $this->request->getScheme()) &&
			(is_null($path) || PathMatcher::matchAll($path, $request)) &&
			(is_null($host) || (new HostMatcher($host, $request))->isMatches())
		) {
			$this->matches = true;
			$this->request = $request;
			return $this->pipeLine->clear();
		}
		return null;
	}

//	static public function create(callable $route): Router {
//		$router = new class extends Router {
//			protected \Closure $routing;
//			protected function route(): void { ($this->routing)($this); }
//		};
//		inject($router, 'routing', \Closure::bind($route, $router, get_class($router)));
//		return $router;
//
//	}
}
