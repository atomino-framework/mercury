<?php namespace Atomino\Mercury\Middleware;

use Atomino\Mercury\Pipeline\Handler;
use Atomino\Mercury\Pipeline\Pipeline;
use DI\Container;
use JetBrains\PhpStorm\Pure;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ExceptionHandler extends Handler {
	public function __construct(private Logger $logger, Container $container) { }

	#[Pure] static public function setup(array|string $exceptionHandler) { return parent::args(get_defined_vars()); }

	public function handle(Request $request): Response {
		try {
			$response = $this->next($request);
		} catch (\Throwable $exception) {
			$this->logger->error($exception->getMessage(), [$request->getMethod() . ' ' . $request->getSchemeAndHttpHost() . $request->getPathInfo() . (($q = $request->getQueryString()) ? "?" . $q : '')]);
			$exceptionHandler = $this->arguments->get('exceptionHandler');
			if (!is_array($exceptionHandler)) $exceptionHandler = [$exceptionHandler];
			$response = $this->container->make(Pipeline::class)->pipe(...$exceptionHandler)->next($request);
		}
		return $response;
	}
}