<?php namespace Atomino\Mercury\Responder\Api;

use Atomino\Bundle\Authenticate\Authenticator;
use Atomino\Mercury\Responder\Api\Attributes\Auth;
use Atomino\Mercury\Router\Matcher\PathMatcher;
use Atomino\Mercury\Responder\Api\Attributes\Route;
use Atomino\Mercury\Responder\Responder;
use Symfony\Component\HttpFoundation\Response;

abstract class Api extends Responder {

	const HEAD = 'HEAD';
	const GET = 'GET';
	const POST = 'POST';
	const PUT = 'PUT';
	const PATCH = 'PATCH';
	const DELETE = 'DELETE';
	const PURGE = 'PURGE';
	const OPTIONS = 'OPTIONS';
	const TRACE = 'TRACE';
	const CONNECT = 'CONNECT';

	const VALIDATION_ERROR = 422;
	const TOO_MANY_REQUESTS = 429;


	private Response $response;
	protected function getResponse(): Response { return $this->response; }
	protected function setStatusCode(int $statusCode) { $this->response->setStatusCode($statusCode); }

	public function respond(Response $response): Response {

		$this->response = $response;
		$methods = (new \ReflectionClass(static::class))->getMethods(\ReflectionMethod::IS_PUBLIC);
		$request = $this->request;

		foreach ($methods as $method) {
			if (!is_null($Route = Route::get($method))) {
				if (empty($Route->verb) || in_array($request->getMethod(), $Route->verb)) {

					$pattern = $Route->getPattern($method);

					if((new PathMatcher($pattern, $request, $attributes))->isMatches()){
						$this->initializeHandler($request);

						if ($Auth = Auth::get($method)) {
							if (!$Auth->authCheck($this->container->get(Authenticator::class))) return $response->setStatusCode(Response::HTTP_UNAUTHORIZED);
							if (!$Auth->roleCheck($this->container->get(Authenticator::class))) return $response->setStatusCode(Response::HTTP_FORBIDDEN);
						}

						$result = $method->invoke($this, ...$attributes);
						$response->headers->set('Content-Type', 'application/json');
						$response->setContent(json_encode($result, JSON_UNESCAPED_UNICODE));
						return $response;
					}
				}
			}
		}
		return $response->setStatusCode(Response::HTTP_NOT_FOUND);
	}
}