<?php namespace Atomino\Mercury\Responder\Api\Attributes;

use Atomino\Bundle\Authenticate\Authenticator;
use Atomino\Neutrons\Attr;
use Attribute;
use JetBrains\PhpStorm\Pure;

#[Attribute(Attribute::TARGET_METHOD)]
class Auth extends Attr {

	#[Pure] public function __construct(public string|false|null $role = null) { }
	public function authCheck(Authenticator $authenticator): bool { return $this->role === false || $authenticator->isAuthenticated(); }
	public function roleCheck(Authenticator $authenticator): bool {
		return
			is_null($this->role) ||
			($this->role === $authenticator->isAuthenticated()) ||
			$authenticator->get()->hasRole($this->role);
	}

}