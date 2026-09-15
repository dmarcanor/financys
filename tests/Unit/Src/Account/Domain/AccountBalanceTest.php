<?php

declare(strict_types=1);

namespace Tests\Unit\Src\Account\Domain;

use Financys\Account\Domain\AccountBalance;
use Shared\Domain\NegativeCurrency;
use Shared\Domain\Symbols;
use Tests\TestCase;

class AccountBalanceTest extends TestCase
{
    public function test_it_should_create_balance_with_symbol_and_normalized_amount(): void
    {
        $balance = AccountBalance::create(Symbols::USD, '5');

        expect($balance->amount())->toBe('5.00000000')
            ->and($balance->symbol())->toBe('usd');
    }

    public function test_it_should_accept_bs_symbol(): void
    {
        $balance = AccountBalance::create(Symbols::BS, '10.5');

        expect($balance->amount())->toBe('10.50000000')
            ->and($balance->symbol())->toBe('bs');
    }

    public function test_it_should_throw_negative_currency_when_balance_is_negative(): void
    {
        $this->expectException(NegativeCurrency::class);
        $this->expectExceptionMessage("The account balance -100.000000 usd can't be negative");

        AccountBalance::create(Symbols::USD, '-100');
    }

    public function test_it_should_throw_negative_currency_for_negative_sub_unit_balance(): void
    {
        $this->expectException(NegativeCurrency::class);

        AccountBalance::create(Symbols::USD, '-0.001');
    }

    public function test_it_should_not_throw_when_negative_zero(): void
    {
        $balance = AccountBalance::create(Symbols::USD, '-0');

        expect($balance->amount())->toBe('0.00000000');
    }

    public function test_it_should_normalize_amount_on_create(): void
    {
        $balance = AccountBalance::create(Symbols::USD, '5.123456789');

        expect($balance->amount())->toBe('5.12345678');
    }
}
