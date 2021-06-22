<?php namespace Atomino\Mercury\Pipeline;

use DI\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Pipeline extends Handler implements Nextable {

	/** @var Segment[] */
	private array $queue = [];

	public function __construct(private Request|null $request = null, protected Container $container) { }

	public function add(...$segments) {
		foreach ($segments as $segment) $this->pipe(...$segment);
		return $this;
	}

	public function pipe(string|Handler $handler, array|null $arguments = null): static {
		array_push($this->queue, new Segment($handler, $arguments, $this, $this->container));
		return $this;
	}

	public function clear(): Pipeline {
		$this->queue = [];
		return $this;
	}

	public function handle(Request|null $request = null): Response|null { return $this->next($request); }

	public function next(Request|null $request): Response|null {
		if(!is_null($request)) $this->request = $request;
		return (count($this->queue) === 0) ? null : array_shift($this->queue)->getHandler()->handle($this->request);
	}
}
