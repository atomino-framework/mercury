<?php namespace Atomino\Mercury\Responder\Smart\Attributes;

use Atomino\Mercury\Responder\Smart\SmartResponderEnv;
use Atomino\Neutrons\Attr;
use Attribute;
use Twig\Cache\CacheInterface;
use function Atomino\dic;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class Init extends Attr {
	public function __construct(public string $environment, public string $template) { }
}
