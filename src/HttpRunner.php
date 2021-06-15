<?php namespace Atomino\Mercury;

use Atomino\Core\Runner\HttpRunnerInterface;
use Atomino\Mercury\Pipeline\Handler;
use Atomino\Mercury\Pipeline\Pipeline;

class HttpRunner implements HttpRunnerInterface {

	private Pipeline $pipeline;

	public function __construct() {
		$this->pipeline = new Pipeline();
	}

	public function run(): void {
		$this->pipeline->execute();
	}

	public function pipe($handler, $arguments = null):static{
		$this->pipeline->pipe($handler, $arguments);
		return $this;
	}
}
