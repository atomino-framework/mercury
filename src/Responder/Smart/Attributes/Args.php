<?php namespace Atomino\Mercury\Responder\Smart\Attributes;

use Atomino\Neutrons\Attr;
use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Args extends Attr {
	public function __construct(
		protected string $title = 'Atomino',
		protected string $language = 'HU',
		protected string $class = '',
		protected string $favicon = '/~favicon/'
	) {
	}

	public function set(&$args) {
		if (!array_key_exists('title', $args) || is_null($args['title'])) $args['title'] = $this->title;
		if (!array_key_exists('language', $args) || is_null($args['language'])) $args['language'] = $this->language;
		if (!array_key_exists('class', $args) || is_null($args['class'])) $args['class'] = $this->class;
		if (!array_key_exists('favicon', $args) || is_null($args['favicon'])) $args['favicon'] = $this->favicon;
	}

}
