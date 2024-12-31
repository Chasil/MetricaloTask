<?php

namespace App\System;

class AciSystem implements PaymentSystemInterface
{
	public function generatePayload(array $data): array
	{
		return [
			'auth_key' => 'test_aci_auth_key',
			'entity_id' => 'test_entity_id',
			'payment_brand' => 'VISA',
			'currency' => $data['currency'],
			'amount' => $data['amount'],
			'card_number' => $data['card_number'],
			'card_exp_year' => $data['card_exp_year'],
			'card_exp_month' => $data['card_exp_month'],
			'card_cvv' => $data['card_cvv'],
		];
	}
}