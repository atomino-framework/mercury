<?php namespace Atomino\Mercury;

use Symfony\Component\RateLimiter\RateLimiterFactory;

interface RateLimiterInterface {
	public function get(string $key, int $limit, int $interval): RateLimiterFactory;
}