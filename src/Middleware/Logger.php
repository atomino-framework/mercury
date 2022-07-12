<?php namespace Atomino\Mercury\Middleware;

use Atomino\Mercury\HttpRunner;
use Atomino\Mercury\Pipeline\Handler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use function Atomino\debug;

class Logger extends Handler {
	public function handle(Request $request): Response|null {
		debug($request,HttpRunner::DEBUG_CHANNEL_HTTP_REQUEST);
		return $this->next($request);
	}
}
