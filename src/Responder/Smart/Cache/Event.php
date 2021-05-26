<?php namespace Atomino\Mercury\Responder\Smart\Cache;

class Event extends \Symfony\Contracts\EventDispatcher\Event{
	const request = __CLASS__.'.request';
	public function __construct(public int $interval){}
}