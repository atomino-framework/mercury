<?php namespace Atomino\Mercury;

use Atomino\Core\Runner\HttpRunnerInterface;
use Atomino\Mercury\Pipeline\Handler;
use Atomino\Mercury\Pipeline\Pipeline;
use Symfony\Component\HttpFoundation\Request;
use function Atomino\debug;

class HttpRunner implements HttpRunnerInterface {

	const DEBUG_CHANNEL_HTTP_REQUEST = 'HTTP';

	public function __construct(Request $request, private Pipeline $pipeline) {}

	public function run(): void { $this->pipeline->handle(); }

	public function pipe($handler, $arguments = null): static {
		$this->pipeline->pipe($handler, $arguments);
		return $this;
	}
}
