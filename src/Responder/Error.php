<?php namespace Atomino\Mercury\Responder;

use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\Response;

class Error extends Basic {
	#[Pure] static public function setup(int $statuscode) { return parent::args(get_defined_vars()); }

	protected function respond(Response $response): Response|null {
		$response->setStatusCode($this->arguments->get('statuscode'));
		return $response;
	}
}
