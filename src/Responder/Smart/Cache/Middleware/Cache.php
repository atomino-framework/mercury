<?php namespace Atomino\Mercury\Responder\Smart\Cache\Middleware;

use Atomino\Mercury\Pipeline\Handler;
use Atomino\Mercury\Responder\Smart\Cache\CacheInterface;
use Atomino\Mercury\Responder\Smart\Cache\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Cache\ItemInterface;

class_alias(\Symfony\Contracts\Cache\CacheInterface::class, CacheInterface::class);

class Cache extends Handler {

	private int $cacheRequest = -1;
	private static null|EventDispatcher $eventDispatcher = null;

	public function __construct(EventDispatcher $eventDispatcher, private CacheInterface $storage) {
		self::$eventDispatcher = $eventDispatcher;
	}

	static function SetCache(int $interval) { self::$eventDispatcher?->dispatch(new Event($interval), Event::request); }

	public function handle(Request $request): Response {
		self::$eventDispatcher->addListener(Event::request, function (Event $event) { $this->cacheRequest = $event->interval; });
		return $this->storage->get(
			crc32($request->getRequestUri()),
			function (ItemInterface $item) use ($request): Response {
				$response = $this->next($request);
				$item->expiresAfter($this->cacheRequest);
				return $response;
			}
		);
	}

}