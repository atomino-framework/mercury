<?php namespace Atomino\Mercury\Middleware;

use Atomino\Mercury\Pipeline\Handler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Emitter extends Handler {
	public function handle(Request $request): Response|null {
		$response = $this->next($request);
		if (is_null($response))  $response = new Response(null, 404);
		$response->send();
		return $response;
	}
}