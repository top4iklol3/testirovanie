@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h1>Редактировать пост</h1>
            </div>
            <div class="card-body">
                <form action="{{ route('posts.update', $post) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="title" class="form-label">Заголовок</label>
                        <input type="text" class="form-control" id="title" name="title" value="{{ $post->title }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="body" class="form-label">Содержание</label>
                        <textarea class="form-control" id="body" name="body" rows="10" required>{{ $post->body }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Категории</label>
                        @foreach ($categories as $category)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="categories[]" value="{{ $category->id }}" id="category-{{ $category->id }}"
                                    @if ($post->categories->contains($category)) checked @endif
                                >
                                <label class="form-check-label" for="category-{{ $category->id }}">
                                    {{ $category->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                    <button type="submit" class="btn btn-primary">Обновить</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
