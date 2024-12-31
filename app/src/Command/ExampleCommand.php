<?php

namespace App\Command;

use App\Service\PaymentPayloadBuilder;
use App\Exception\ApiSystemException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:example',
    description: 'Add a short description for your command',
)]
class ExampleCommand extends Command
{
	protected static $defaultName = 'app:example';
	private HttpClientInterface $httpClient;
	private PaymentPayloadBuilder $payloadBuilder;

	public function __construct(
		HttpClientInterface $httpClient,
		PaymentPayloadBuilder $payloadBuilder,
	)
	{
		parent::__construct();
		$this->httpClient = $httpClient;
		$this->payloadBuilder = $payloadBuilder;
	}

	protected function configure(): void {
		$this
			->setDescription('Send a request to ACI or Shift4 system.')
			->addArgument('system', InputArgument::REQUIRED, 'The system to use: "aci" or "shift4".')
			->addArgument('amount', InputArgument::REQUIRED, 'Amount to process.')
			->addArgument('currency', InputArgument::REQUIRED, 'Currency for the transaction.')
			->addArgument('card_number', InputArgument::REQUIRED, 'Card number.')
			->addArgument('card_exp_year', InputArgument::REQUIRED, 'Card expiration year.')
			->addArgument('card_exp_month', InputArgument::REQUIRED, 'Card expiration month.')
			->addArgument('card_cvv', InputArgument::REQUIRED, 'Card CVV.');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int {
		$system = $input->getArgument('system');
		$jsonPayload = [
			'amount' => $input->getArgument('amount'),
			'currency' => $input->getArgument('currency'),
			'card_number' => $input->getArgument('card_number'),
			'card_exp_year' => $input->getArgument('card_exp_year'),
			'card_exp_month' => $input->getArgument('card_exp_month'),
			'card_cvv' => $input->getArgument('card_cvv'),
		];

		$systems = [
			'aci' => 'https://eu-test.oppwa.com/v1/payments',
			'shift4' => 'https://api.shift4.com/charges',
		];

		if (!array_key_exists($system, $systems)) {
			$output->writeln('<error>Invalid system specified. Use "aci" or "shift4".</error>');
			return Command::INVALID;
		}

		try {
			$payload = $this->payloadBuilder->buildPayload($system, $jsonPayload);

			$response = $this->httpClient->request(
				'POST',
				$systems[$system],
				['json' => $payload]
			);

			$content = $response->toArray(false);
			$output->writeln('<info>Transaction Successful:</info>');
			$output->writeln('Transaction ID: ' . ($content['transaction_id'] ?? 'N/A'));
			$output->writeln('Created At: ' . ($content['created_at'] ?? date('Y-m-d H:i:s')));
			$output->writeln('Amount: ' . $jsonPayload['amount']);
			$output->writeln('Currency: ' . $jsonPayload['currency']);
			$output->writeln('Card BIN: ' . substr($jsonPayload['card_number'], 0, 6));

			return Command::SUCCESS;
		} catch (ApiSystemException $e) {
			$output->writeln('<error>An error occurred while communicating with the external system:</error>');
			$output->writeln('<error>' . $e->getMessage() . '</error>');
			return Command::FAILURE;
		} catch (\Exception $e) {
			$output->writeln('<error>An unexpected error occurred:</error>');
			$output->writeln('<error>' . $e->getMessage() . '</error>');
			return Command::FAILURE;
		}
	}
}
