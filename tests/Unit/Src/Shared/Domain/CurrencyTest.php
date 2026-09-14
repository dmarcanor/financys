<?php

declare(strict_types=1);

namespace Tests\Unit\Src\Shared\Domain;

use Shared\Domain\Currency;
use Shared\Domain\Symbols;
use Tests\TestCase;
use ValueError;

class TestableCurrency extends Currency
{
    public function amount(): string
    {
        return $this->amount;
    }

    public function isNegative(): bool
    {
        return parent::isNegative();
    }
}

class CurrencyTest extends TestCase
{
    private function currency(string $amount): TestableCurrency
    {
        return new TestableCurrency(Symbols::USD, $amount);
    }

    public function test_it_should_pad_whole_number_to_eight_decimals(): void
    {
        expect($this->currency('5')->amount())->toBe('5.00000000');
    }

    public function test_it_should_pad_short_fraction_to_eight_decimals(): void
    {
        expect($this->currency('5.1')->amount())->toBe('5.10000000');
    }

    public function test_it_should_keep_exactly_eight_decimals(): void
    {
        expect($this->currency('5.12345678')->amount())->toBe('5.12345678');
    }

    public function test_it_should_truncate_more_than_eight_decimals(): void
    {
        expect($this->currency('5.123456789')->amount())->toBe('5.12345678');
    }

    public function test_it_should_truncate_negative_amounts_towards_zero(): void
    {
        expect($this->currency('-5.123456789')->amount())->toBe('-5.12345678');
    }

    public function test_it_should_clean_leading_zeros(): void
    {
        expect($this->currency('0005')->amount())->toBe('5.00000000');
    }

    public function test_it_should_normalize_trailing_dot_notation(): void
    {
        expect($this->currency('5.')->amount())->toBe('5.00000000');
    }

    public function test_it_should_normalize_amount_without_integer_part(): void
    {
        expect($this->currency('.5')->amount())->toBe('0.50000000');
    }

    public function test_it_should_normalize_plus_signed_amount(): void
    {
        expect($this->currency('+5')->amount())->toBe('5.00000000');
    }

    public function test_it_should_truncate_to_zero(): void
    {
        expect($this->currency('0.000000005')->amount())->toBe('0.00000000');
    }

    public function test_it_should_treat_negative_zero_as_zero(): void
    {
        expect($this->currency('-0')->amount())->toBe('0.00000000');
    }

    public function test_is_negative_when_amount_is_negative(): void
    {
        expect($this->currency('-10')->isNegative())->toBeTrue();
    }

    public function test_is_negative_for_negative_amount_with_zero_integer_part(): void
    {
        expect($this->currency('-0.001')->isNegative())->toBeTrue();
    }

    public function test_is_not_negative_when_amount_is_positive(): void
    {
        expect($this->currency('10')->isNegative())->toBeFalse();
    }

    public function test_is_not_negative_for_zero_or_negative_zero(): void
    {
        expect($this->currency('0')->isNegative())->toBeFalse();
        expect($this->currency('-0')->isNegative())->toBeFalse();
    }

    public function test_it_should_reject_malformed_amount(): void
    {
        $this->expectException(ValueError::class);

        $this->currency('abc');
    }
}