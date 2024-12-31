<?php

namespace App\Exception;

use Throwable;

class ApiSystemException extends \Exception
{
	public function __construct(string $message = 'Failed to request API', int $code = 0, ?Throwable $previous = null) {
		parent::__construct($message, $code, $previous);
	}
}
