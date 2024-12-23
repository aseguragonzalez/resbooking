<?php

declare(strict_types=1);

namespace App\Domain\Shared;

final class Phone
{
	public function __construct(private readonly string $phone)
	{
		if (!preg_match('/^\(\d{2}\) \d{4,5}-\d{4}$/', $phone)) {
			throw new \InvalidArgumentException('Invalid phone number');
		}
	}

	public function __toString(): string
	{
		return $this->phone;
	}

	public function equals(Phone $phone): bool
	{
		return $this->phone === $phone->phone;
	}

	public function value(): string
	{
		return $this->phone;
	}
}
