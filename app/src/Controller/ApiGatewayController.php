<?php

namespace App\Controller;

use App\Payment\PaymentSystemFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiGatewayController extends AbstractController
{
	public function __construct(
		private readonly HttpClientInterface $httpClient,
		private readonly array $paymentSystems
	) {}

	#[Route('/app/example/{system}', name: 'app_api_gateway', methods: 'POST')]
	public function handleRequest(string $system, Request $request): Response
	{
		$jsonPayload = json_decode($request->getContent(), true);

		if (!$jsonPayload || !isset($jsonPayload['amount'], $jsonPayload['currency'], $jsonPayload['card_number'], $jsonPayload['card_exp_year'], $jsonPayload['card_exp_month'], $jsonPayload['card_cvv'])) {
			return $this->json(['error' => 'Invalid or missing input parameters'], Response::HTTP_BAD_REQUEST);
		}

		if (!array_key_exists($system, $this->paymentSystems)) {
			return $this->json(['error' => 'No system found'], Response::HTTP_BAD_REQUEST);
		}

		try {
			$paymentSystem = PaymentSystemFactory::create($system);
			$payload = $paymentSystem->generatePayload($jsonPayload);

			$response = $this->httpClient->request(
				'POST',
				$this->paymentSystems[$system]['url'],
				['json' => $payload]
			);

			$statusCode = $response->getStatusCode();
			$content = $response->toArray(false);

			return $this->json([
				'transaction_id' => $content['transaction_id'] ?? 'N/A',
				'created_at' => $content['created_at'] ?? date('Y-m-d H:i:s'),
				'amount' => $jsonPayload['amount'],
				'currency' => $jsonPayload['currency'],
				'card_bin' => substr($jsonPayload['card_number'], 0, 6),
			], $statusCode);
		} catch (\Throwable $e) {
			return $this->json([
				'error' => 'An error occurred while processing the request.',
				'details' => $e->getMessage(),
			], Response::HTTP_INTERNAL_SERVER_ERROR);
		}
	}
}