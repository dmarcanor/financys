<?php

declare(strict_types=1);

namespace Tests\Unit\Src\Account\Domain;

use Financys\Account\Domain\AccountBalance;
use Shared\Domain\NegativeCurrency;
use Shared\Domain\Symbols;
use Tests\TestCase;

class AccountBalanceTest extends TestCase
{
    public function test_it_should_pad_whole_number_to_eight_decimals(): void
    {
        $balance = AccountBalance::create(Symbols::USD, '5');

        expect($balance->amount())->toBe('5.00000000');
    }

    public function test_it_should_pad_short_fraction_to_eight_decimals(): void
    {
        $balance = AccountBalance::create(Symbols::USD, '5.1');

        expect($balance->amount())->toBe('5.10000000');
    }

    public function test_it_should_truncate_more_than_eight_decimals(): void
    {
        $balance = AccountBalance::create(Symbols::USD, '5.123456789');

        expect($balance->amount())->toBe('5.12345678');
    }

    public function test_it_should_clean_leading_zeros(): void
    {
        $balance = AccountBalance::create(Symbols::USD, '0005');

        expect($balance->amount())->toBe('5.00000000');
    }

    public function test_it_should_throw_negative_currency_when_balance_is_negative(): void
    {
        $this->expectException(NegativeCurrency::class);

        AccountBalance::create(Symbols::USD, '-100');
    }

    public function test_it_should_not_throw_when_negative_zero(): void
    {
        $balance = AccountBalance::create(Symbols::USD, '-0');

        expect($balance->amount())->toBe('0.00000000');
    }
}