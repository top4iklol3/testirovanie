@extends('layouts.app')

@section('content')
<div class="card mt-5 shadow-sm">
    <div class="card-body">
        <h1 class="card-title text-center mb-4">Менеджер Задач</h1>

        {{-- Форма для добавления новой задачи --}}
        <form action="{{ route('tasks.store') }}" method="POST" class="mb-4">
            @csrf
            <div class="input-group">
                <input type="text" name="title" class="form-control" placeholder="Добавить новую задачу..." required>
                <button class="btn btn-primary" type="submit">Добавить</button>
            </div>
        </form>

        {{-- Список задач --}}
        <ul class="list-group list-group-flush">
            @forelse ($tasks as $task)
                <li class="list-group-item d-flex justify-content-between align-items-center task-item {{ $task->completed ? 'completed' : '' }}">

                    {{-- Форма для пометки задачи как выполненной/невыполненной --}}
                    <form action="{{ route('tasks.complete', $task) }}" method="POST" class="d-flex align-items-center">
                        @csrf
                        @method('PATCH')
                        <input type="checkbox" class="form-check-input me-3" onchange="this.form.submit()" {{ $task->completed ? 'checked' : '' }}>
                        <span class="task-title">{{ $task->title }}</span>
                    </form>

                    {{-- Кнопки управления --}}
                    <div>
                        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editTaskModal-{{ $task->id }}">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </li>

                <!-- Модальное окно для редактирования -->
                <div class="modal fade" id="editTaskModal-{{ $task->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Редактировать задачу</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="{{ route('tasks.update', $task) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-body">
                                    <input type="text" name="title" class="form-control" value="{{ $task->title }}" required>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                                    <button type="submit" class="btn btn-primary">Сохранить</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <li class="list-group-item text-center">У вас пока нет задач.</li>
            @endforelse
        </ul>
    </div>
</div>
@endsection
