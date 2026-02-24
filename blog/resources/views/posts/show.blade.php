@extends('layouts.app')

@section('content')
<div class="row">
    <!-- Post Content Column -->
    <div class="col-lg-8">
        <div class="card p-4">
            <!-- Title -->
            <h1 class="mt-4 mb-3">{{ $post->title }}</h1>

            <!-- Author -->
            <p class="lead">
                автор <a href="#">{{ $post->user->name }}</a>
            </p>

            <hr>

            <!-- Date/Time and Categories -->
            <p>
                <small class="text-muted">
                    Опубликовано {{ $post->created_at->format('d F Y \в H:i') }} |
                    Категории:
                    @foreach ($post->categories as $category)
                        <a href="/?category={{ $category->slug }}" class="badge bg-secondary text-decoration-none">{{ $category->name }}</a>
                    @endforeach
                </small>
            </p>

            <hr>

            <!-- Post Content -->
            <div class="post-content">
                {!! nl2br(e($post->body)) !!}
            </div>

            <hr>

            <!-- Action Buttons -->
            @auth
                @can('update', $post)
                    <a href="{{ route('posts.edit', $post) }}" class="btn btn-info">Редактировать</a>
                @endcan
                @can('delete', $post)
                    <form action="{{ route('posts.destroy', $post) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Удалить</button>
                    </form>
                @endcan
            @endauth
        </div>

        <hr>

        <!-- Comments -->
        <div class="card my-4">
            <h5 class="card-header">Комментарии:</h5>
            <div class="card-body">
                @auth
                    <form action="{{ route('comments.store', $post) }}" method="POST" class="mb-4">
                        @csrf
                        <div class="form-group">
                            <textarea class="form-control" name="body" rows="3" placeholder="Присоединяйтесь к обсуждению..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary mt-2">Отправить</button>
                    </form>
                @else
                    <p>Пожалуйста, <a href="{{ route('login') }}">войдите</a>, чтобы оставить комментарий.</p>
                @endauth

                @forelse ($post->comments->sortByDesc('created_at') as $comment)
                    <div class="d-flex mb-4">
                        <div class="flex-shrink-0"><img class="rounded-circle" src="https://dummyimage.com/50x50/ced4da/6c757d.jpg" alt="..." /></div>
                        <div class="ms-3">
                            <div class="fw-bold">{{ $comment->user->name }} <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small></div>
                            {{ $comment->body }}
                        </div>
                    </div>
                @empty
                    <p>Комментариев пока нет. Будьте первым!</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Sidebar Widgets Column -->
    <div class="col-md-4">
        <div class="card my-4">
            <h5 class="card-header">Поиск</h5>
            <div class="card-body">
                <form action="/">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Найти..." value="{{ request('search') }}">
                        <button class="btn btn-outline-secondary" type="submit">Найти</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="card my-4">
            <h5 class="card-header">Категории</h5>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    @foreach ($categories as $category)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <a href="/?category={{ $category->slug }}" class="text-decoration-none">{{ $category->name }}</a>
                            <span class="badge bg-primary rounded-pill">{{ $category->posts_count }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
