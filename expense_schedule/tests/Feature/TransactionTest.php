<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Transaction;
use PHPUnit\Framework\Attributes\Test;

class TransactionTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    #[Test]
    public function it_can_add_an_income_transaction(): void
    {
        $data = [
            'title' => 'Зарплата',
            'amount' => 50000.00,
            'type' => 'income',
        ];

        $this->post(route('transactions.store'), $data)
            ->assertRedirect(route('transactions.index'));

        $this->assertDatabaseHas('transactions', $data);
    }

    #[Test]
    public function it_can_add_an_expense_transaction(): void
    {
        $data = [
            'title' => 'Аренда',
            'amount' => 25000.00,
            'type' => 'expense',
        ];

        $this->post(route('transactions.store'), $data)
            ->assertRedirect(route('transactions.index'));

        $this->assertDatabaseHas('transactions', $data);
    }

    #[Test]
    public function it_can_delete_a_transaction(): void
    {
        $transaction = Transaction::factory()->create();

        $this->delete(route('transactions.destroy', $transaction))
            ->assertRedirect(route('transactions.index'));

        $this->assertDatabaseMissing('transactions', ['id' => $transaction->id]);
    }

    #[Test]
    public function it_correctly_calculates_the_balance(): void
    {
        Transaction::factory()->create(['type' => 'income', 'amount' => 1000]);
        Transaction::factory()->create(['type' => 'income', 'amount' => 500]);
        Transaction::factory()->create(['type' => 'expense', 'amount' => 300]);
        Transaction::factory()->create(['type' => 'expense', 'amount' => 200]);

        $response = $this->get(route('transactions.index'));

        $response->assertOk();
        $response->assertViewHas('balance', 1000.00); // 1500 - 500
        $response->assertViewHas('totalIncome', 1500.00);
        $response->assertViewHas('totalExpense', 500.00);
    }

    #[Test]
    public function it_requires_a_title_amount_and_type_to_create_a_transaction(): void
    {
        $this->post(route('transactions.store'), ['title' => ''])
            ->assertSessionHasErrors('title');

        $this->post(route('transactions.store'), ['amount' => ''])
            ->assertSessionHasErrors('amount');

        $this->post(route('transactions.store'), ['type' => ''])
            ->assertSessionHasErrors('type');
    }
}
