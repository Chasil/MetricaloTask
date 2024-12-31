<?php

namespace App\Service;

class PaymentPayloadBuilder
{
	public function buildPayload(array $data, array $systemPayload): array
	{
		foreach (['amount', 'currency', 'card_number', 'card_exp_year', 'card_exp_month', 'card_cvv'] as $key) {
			if (!isset($data[$key])) {
				throw new \InvalidArgumentException(sprintf('Missing required parameter: %s', $key));
			}
		}

		$basePayload = [
			'amount' => $data['amount'],
			'currency' => $data['currency'],
			'card_number' => $data['card_number'],
			'card_exp_year' => $data['card_exp_year'],
			'card_exp_month' => $data['card_exp_month'],
			'card_cvv' => $data['card_cvv'],
		];

		return array_merge($basePayload, $systemPayload);
	}
}