<?php

declare(strict_types=1);

namespace Tests\Feature\Financys\Account;

use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Unit\Financys\Account\Domain\AccountMother;

final class AccountPutControllerTest extends TestCase
{
    use DatabaseTransactions;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->createUser();
    }

    public function test_it_should_update_a_account_put_with_a_account_id(): void
    {
        $originalAccount = AccountMother::create(userId: $this->user->id);
        $modifiedAccount = AccountMother::create(
            id: $originalAccount->id(),
            userId: $originalAccount->userId()
        );

        Account::factory()->create([
            'id' => $originalAccount->id(),
            'user_id' => $originalAccount->userId(),
            'name' => $originalAccount->name(),
            'code' => $originalAccount->code(),
            'balance' => $originalAccount->balance()->amount(),
            'currency' => $originalAccount->balance()->symbol(),
        ]);

        $response = $this
            ->withHeader('Authorization', "Bearer {$this->token()}")
            ->put("api/account/{$originalAccount->id()}", [
                'code' => $modifiedAccount->code(),
                'name' => $modifiedAccount->name(),
            ]);

        $json = $response->json();

        expect($json)->toHAveKeys(['error', 'body']);
        expect($json['error'])->toBe([]);
        expect($json['body'])->toBe([]);
        expect($response->status())->toBe(200);
    }

    public function test_it_should_return_response_with_error_account_not_found(): void
    {
        $originalAccount = AccountMother::create(userId: $this->user->id);
        $modifiedAccount = AccountMother::create(
            id: $originalAccount->id(),
            userId: $originalAccount->userId()
        );

        $response = $this
            ->withHeader('Authorization', "Bearer {$this->token()}")
            ->put("api/account/{$originalAccount->id()}", [
                'code' => $modifiedAccount->code(),
                'name' => $modifiedAccount->name(),
            ]);

        $json = $response->json();

        expect($json)->toHAveKeys(['error', 'body']);
        expect($json['error'])->toBe(["Account with ID {$originalAccount->id()} not found."]);
        expect($json['body'])->toBe([]);
        expect($response->status())->toBe(404);
    }

    public function test_it_should_return_not_found_for_an_account_owned_by_another_user(): void
    {
        $originalAccount = AccountMother::create(userId: $this->user->id);
        $modifiedAccount = AccountMother::create(
            id: $originalAccount->id(),
            userId: $originalAccount->userId()
        );

        Account::factory()->create([
            'id' => $originalAccount->id(),
            'user_id' => $originalAccount->userId(),
            'name' => $originalAccount->name(),
            'code' => $originalAccount->code(),
            'balance' => $originalAccount->balance()->amount(),
            'currency' => $originalAccount->balance()->symbol(),
        ]);

        $otherUser = User::factory()->create();
        $token = auth()->login($otherUser);

        $response = $this
            ->withHeader('Authorization', "Bearer {$token}")
            ->put("api/account/{$originalAccount->id()}", [
                'code' => $modifiedAccount->code(),
                'name' => $modifiedAccount->name(),
            ]);

        $json = $response->json();

        expect($json)->toHAveKeys(['error', 'body']);
        expect($json['error'])->toBe(["Account with ID {$originalAccount->id()} not found."]);
        expect($json['body'])->toBe([]);
        expect($response->status())->toBe(404);
    }
}
