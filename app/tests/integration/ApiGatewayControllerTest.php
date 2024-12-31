<?php

namespace integration;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiGatewayControllerTest extends WebTestCase
{
	public static function validDataProvider(): array
	{
		return [
			['aci', [
				'amount' => '100',
				'currency' => 'EUR',
				'card_number' => '1234567890123456',
				'card_exp_year' => '2025',
				'card_exp_month' => '12',
				'card_cvv' => '123',
			]],
		];
	}

	/**
	 * @dataProvider validDataProvider
	 */
	public function testHandleRequestWithValidData($system, $data)
	{
		$client = static::createClient();
		$client->request(
			'POST',
			'/app/example/' . $system,
			[],
			[],
			['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'Bearer your_token_here'],
			json_encode($data)
		);

		$response = $client->getResponse();
		$this->assertEquals(200, $response->getStatusCode());

		$responseData = json_decode($response->getContent(), true);
		$this->assertArrayHasKey('transaction_id', $responseData);
		$this->assertArrayHasKey('amount', $responseData);
		$this->assertEquals($data['amount'], $responseData['amount']);
	}

	public static function invalidDataProvider(): array
	{
		return [
			['aci', [
				'amount' => '100',
				'currency' => 'EUR',
			]],
		];
	}

	/**
	 * @dataProvider invalidDataProvider
	 */
	public function testHandleRequestWithInvalidData($system, $data)
	{
		$client = static::createClient();
		$client->request(
			'POST',
			'/app/example/' . $system,
			[],
			[],
			['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'Bearer your_token_here'],
			json_encode($data)
		);

		$this->assertEquals(400, $client->getResponse()->getStatusCode());
	}

	public static function invalidSystemProvider(): array
	{
		return [
			['invalid', [
				'amount' => '100',
				'currency' => 'EUR',
				'card_number' => '1234567890123456',
				'card_exp_year' => '2025',
				'card_exp_month' => '12',
				'card_cvv' => '123',
			]],
		];
	}

	/**
	 * @dataProvider invalidSystemProvider
	 */
	public function testHandleRequestWithInvalidSystem($system, $data)
	{
		$client = static::createClient();
		$client->request(
			'POST',
			'/app/example/' . $system,
			[],
			[],
			['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'Bearer your_token_here'],
			json_encode($data)
		);

		$this->assertEquals(400, $client->getResponse()->getStatusCode());
	}
}