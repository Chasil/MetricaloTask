<?php

namespace App\System;

interface PaymentSystemInterface
{
	public function generatePayload(array $data): array;
}