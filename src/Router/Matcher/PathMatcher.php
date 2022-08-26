<?php namespace Atomino\Mercury\Router\Matcher;

use Symfony\Component\HttpFoundation\Request;

class PathMatcher extends Matcher {

	public static function matchAll(array|string $patterns, Request &$request, &$attributes = null) {
		if (!is_array($patterns)) $patterns = [$patterns];
		foreach ($patterns as $pattern) {
			if ((new PathMatcher($pattern, $request, $attributes))->isMatches()) return true;
		}
	}

	public function __construct($pattern, Request &$request, &$attributes = null) {
		$this->parse($pattern, '/');
		$this->match(trim($request->getPathInfo(), '/'));
		if ($this->isMatches()) {
			$attributes = $this->attributes;
			$request = $request->duplicate(
				null,
				null,
				array_merge($request->attributes->all(), $this->attributes),
				null,
				null,
				$this->rest !== false ? array_merge($request->server->all(), ['REQUEST_URI' => $this->rest]) : null
			);
		}
	}
}
