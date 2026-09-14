<?php

declare(strict_types=1);

namespace Tests\Unit\Src\Shared\Domain;

use Shared\Domain\Currency;
use Shared\Domain\Symbols;
use Tests\TestCase;
use ValueError;

class CurrencyTest extends TestCase
{
    public function test_it_should_pad_whole_number_to_eight_decimals(): void
    {
        expect((new Currency(Symbols::USD, '5'))->amount())->toBe('5.00000000');
    }

    public function test_it_should_pad_short_fraction_to_eight_decimals(): void
    {
        expect((new Currency(Symbols::USD, '5.1'))->amount())->toBe('5.10000000');
    }

    public function test_it_should_keep_exactly_eight_decimals(): void
    {
        expect((new Currency(Symbols::USD, '5.12345678'))->amount())->toBe('5.12345678');
    }

    public function test_it_should_truncate_more_than_eight_decimals(): void
    {
        expect((new Currency(Symbols::USD, '5.123456789'))->amount())->toBe('5.12345678');
    }

    public function test_it_should_truncate_negative_amounts_towards_zero(): void
    {
        expect((new Currency(Symbols::USD, '-5.123456789'))->amount())->toBe('-5.12345678');
    }

    public function test_it_should_clean_leading_zeros(): void
    {
        expect((new Currency(Symbols::USD, '0005'))->amount())->toBe('5.00000000');
    }

    public function test_it_should_normalize_trailing_dot_notation(): void
    {
        expect((new Currency(Symbols::USD, '5.'))->amount())->toBe('5.00000000');
    }

    public function test_it_should_normalize_amount_without_integer_part(): void
    {
        expect((new Currency(Symbols::USD, '.5'))->amount())->toBe('0.50000000');
    }

    public function test_it_should_normalize_plus_signed_amount(): void
    {
        expect((new Currency(Symbols::USD, '+5'))->amount())->toBe('5.00000000');
    }

    public function test_it_should_truncate_to_zero(): void
    {
        expect((new Currency(Symbols::USD, '0.000000005'))->amount())->toBe('0.00000000');
    }

    public function test_it_should_treat_negative_zero_as_zero(): void
    {
        expect((new Currency(Symbols::USD, '-0'))->amount())->toBe('0.00000000');
    }

    public function test_it_should_detect_negative_amounts(): void
    {
        expect((new Currency(Symbols::USD, '-10'))->isNegative())->toBeTrue();
        expect((new Currency(Symbols::USD, '-0.001'))->isNegative())->toBeTrue();
    }

    public function test_it_should_not_detect_zero_or_positive_as_negative(): void
    {
        expect((new Currency(Symbols::USD, '10'))->isNegative())->toBeFalse();
        expect((new Currency(Symbols::USD, '0'))->isNegative())->toBeFalse();
        expect((new Currency(Symbols::USD, '-0'))->isNegative())->toBeFalse();
    }

    public function test_it_should_reject_malformed_amount(): void
    {
        $this->expectException(ValueError::class);

        new Currency(Symbols::USD, 'abc');
    }
}