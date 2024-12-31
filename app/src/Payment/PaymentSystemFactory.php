<?php

namespace App\Payment;

use App\System\AciSystem;
use App\System\Shift4System;
use App\System\PaymentSystemInterface;

class PaymentSystemFactory
{
	public static function create(string $system): PaymentSystemInterface
	{
		return match ($system) {
			'aci' => new AciSystem(),
			'shift4' => new Shift4System(),
			default => throw new \InvalidArgumentException('Invalid payment system specified.'),
		};
	}
}