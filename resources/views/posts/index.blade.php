@extends('layouts.app')

@section('content')
<div class="row">
    <!-- Blog Entries Column -->
    <div class="col-md-8">
        <h1 class="my-4">
            @if(request('search'))
                Результаты поиска по: "{{ request('search') }}"
            @elseif(request('category'))
                Посты в категории: "{{ $categories->firstWhere('slug', request('category'))->name }}"
            @else
                Последние посты
            @endif
        </h1>

        @forelse ($posts as $post)
            <div class="card mb-4">
                <div class="card-body d-flex flex-column">
                    <h2 class="card-title h4">{{ $post->title }}</h2>
                    <p class="card-text flex-grow-1">{{ Str::limit($post->body, 200) }}</p>
                    <div class="mt-auto">
                        <a href="{{ route('posts.show', $post) }}" class="btn btn-primary">Читать далее &rarr;</a>
                    </div>
                </div>
                <div class="card-footer text-muted">
                    <small>
                        Опубликовано {{ $post->created_at->diffForHumans() }} автором
                        <a href="#">{{ $post->user->name }}</a>
                        <br>
                        Категории:
                        @foreach ($post->categories as $category)
                            <a href="/?category={{ $category->slug }}" class="badge bg-secondary text-decoration-none">{{ $category->name }}</a>
                        @endforeach
                    </small>
                </div>
            </div>
        @empty
            <div class="card">
                <div class="card-body">
                    <p class="card-text">Пока нет ни одного поста, соответствующего вашему запросу.</p>
                </div>
            </div>
        @endforelse

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
