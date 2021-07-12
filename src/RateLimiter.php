<?php namespace Atomino\Mercury;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\RateLimiter\LimiterInterface;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\RateLimiter\Storage\CacheStorage;

class RateLimiter implements RateLimiterInterface {
	public function __construct(private CacheItemPoolInterface $cache) { }
	public function get(string $key, int $limit, int $interval): RateLimiterFactory {
		return (new RateLimiterFactory([
			'id'       => $key,
			'policy'   => 'sliding_window',
			'limit'    => $limit,
			'interval' => $interval . ' seconds',
		], new CacheStorage($this->cache)));
	}
}