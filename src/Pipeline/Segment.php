<?php namespace Atomino\Mercury\Pipeline;

use DI\Container;
use Symfony\Component\HttpFoundation\ParameterBag;
use function Atomino\inject;

class Segment {
	public function __construct(
		private Handler|string $handler,
		private array|null $arguments,
		private Pipeline $pipeLine,
		private Container $container
	) {
	}

	public function getHandler(): Handler {
		$handler = is_string($this->handler) ? $this->container->get($this->handler) : $this->handler;
		$handler->setContainer($this->container);
		inject($handler, 'arguments', new ParameterBag($this->arguments ?? []));
		inject($handler, 'pipeLine', $this->pipeLine);
		return $handler;
	}
}