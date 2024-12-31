<?php

namespace App\System;

class Shift4System implements PaymentSystemInterface
{
	public function generatePayload(array $data): array
	{
		return [
			'auth_key' => 'test_shift4_auth_key',
			'card_number' => $data['card_number'],
			'amount' => $data['amount'],
			'currency' => $data['currency'],
			'card_exp_year' => $data['card_exp_year'],
			'card_exp_month' => $data['card_exp_month'],
			'card_cvv' => $data['card_cvv'],
		];
	}
}