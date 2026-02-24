@extends('layouts.app')

@section('content')
<h1 class="text-center mb-4">Учет Расходов</h1>

<!-- Статистика -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card card-income shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Доходы</h5>
                <p class="card-text fs-4 text-success">+ {{ number_format($totalIncome, 2, ',', ' ') }} ₽</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-expense shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Расходы</h5>
                <p class="card-text fs-4 text-danger">- {{ number_format($totalExpense, 2, ',', ' ') }} ₽</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-balance shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Баланс</h5>
                <p class="card-text fs-4">{{ number_format($balance, 2, ',', ' ') }} ₽</p>
            </div>
        </div>
    </div>
</div>

<!-- Форма добавления и список транзакций -->
<div class="card shadow-sm">
    <div class="card-body">
        <h3 class="card-title mb-3">Добавить транзакцию</h3>
        <form action="{{ route('transactions.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <input type="text" name="title" class="form-control" placeholder="Название" required>
                </div>
                <div class="col-md-3">
                    <input type="number" name="amount" step="0.01" class="form-control" placeholder="Сумма" required>
                </div>
                <div class="col-md-3">
                    <select name="type" class="form-select" required>
                        <option value="expense">Расход</option>
                        <option value="income">Доход</option>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary w-100">Добавить</button>
                </div>
            </div>
        </form>

        <hr class="my-4">

        <h3 class="mb-3">Последние операции</h3>
        <ul class="list-group list-group-flush">
            @forelse ($transactions as $transaction)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <span class="fw-bold">{{ $transaction->title }}</span>
                        <small class="d-block text-muted">{{ $transaction->created_at->format('d.m.Y H:i') }}</small>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="fs-5 me-3 fw-bold {{ $transaction->type == 'income' ? 'text-success' : 'text-danger' }}">
                            {{ $transaction->type == 'income' ? '+' : '-' }} {{ number_format($transaction->amount, 2, ',', ' ') }} ₽
                        </span>
                        <form action="{{ route('transactions.destroy', $transaction) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">&times;</button>
                        </form>
                    </div>
                </li>
            @empty
                <li class="list-group-item text-center">Транзакций пока нет.</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
