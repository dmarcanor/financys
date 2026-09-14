<?php

declare(strict_types=1);

namespace Tests\Unit\Src\Shared\Domain;

use PHPUnit\Framework\Attributes\DataProvider;
use Shared\Domain\Currency;
use Shared\Domain\Symbols;
use Tests\TestCase;
use ValueError;

class CurrencyTest extends TestCase
{
    #[DataProvider('normalizationCases')]
    public function test_it_should_normalize_amount(string $input, string $expected): void
    {
        expect((new Currency(Symbols::USD, $input))->amount())->toBe($expected);
    }

    public function test_is_negative_when_amount_is_negative(): void
    {
        expect((new Currency(Symbols::USD, '-10'))->isNegative())->toBeTrue();
        expect((new Currency(Symbols::USD, '-0.001'))->isNegative())->toBeTrue();
    }

    public function test_is_not_negative_for_zero_or_positive_amounts(): void
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

    public static function normalizationCases(): array
    {
        return [
            'whole number' => ['5', '5.00000000'],
            'short fraction' => ['5.1', '5.10000000'],
            'exact eight decimals' => ['5.12345678', '5.12345678'],
            'more than eight decimals' => ['5.123456789', '5.12345678'],
            'negative truncated towards zero' => ['-5.123456789', '-5.12345678'],
            'leading zeros' => ['0005', '5.00000000'],
            'trailing dot' => ['5.', '5.00000000'],
            'missing integer part' => ['.5', '0.50000000'],
            'plus sign' => ['+5', '5.00000000'],
            'truncated to zero' => ['0.000000005', '0.00000000'],
            'negative zero' => ['-0', '0.00000000'],
        ];
    }
}