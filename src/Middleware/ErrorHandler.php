<?php namespace Atomino\Mercury\Middleware;

use Atomino\Mercury\Pipeline\Handler;
use Atomino\Mercury\Pipeline\Pipeline;
use DI\Container;
use JetBrains\PhpStorm\Pure;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ErrorHandler extends Handler {
	public function __construct(private Logger $logger, protected Container $container) { }

	#[Pure] static public function setup(array|string $nullHandler, array $statusHandlers = []) { return parent::args(get_defined_vars()); }

	public function handle(Request $request): Response {
		$response = $this->next($request);
		$handler = null;

		if (!is_null($response) && $response->getStatusCode() !== 200) {
			if (array_key_exists($response->getStatusCode(), $this->arguments->get('statusHandlers'))) {
				$statusHandler = $this->arguments->get('statusHandlers')[$response->getStatusCode()];
				if(!is_array($statusHandler)) $statusHandler = [$statusHandler];
				/** @var Handler $handler */
				$handler = $this->container->make(Pipeline::class)->pipe(...$statusHandler);
			} else {
				$response = null;
			}
		}

		if (is_null($response)) {
			/** @var Handler $handler */
			$nullHandler = $this->arguments->get('nullHandler');
			if(!is_array($nullHandler)) $nullHandler = [$nullHandler];
			$handler = $this->container->make(Pipeline::class)->pipe(...$nullHandler);
		}

		if (!is_null($handler)) {
			$this->logger->error("Error " . ($response ? $response->getStatusCode() : 'NULL'), [$request->getMethod() . ' ' . $request->getSchemeAndHttpHost() . $request->getPathInfo() . (($q = $request->getQueryString()) ? "?" . $q : '')]);
			$response = $handler->handle($request);
		}
		return $response;
	}
}
