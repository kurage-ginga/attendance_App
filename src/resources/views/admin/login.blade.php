@extends('layouts.app')

@section('content')
<div class="w-full max-w-md mx-auto mt-10 p-6 bg-white rounded shadow">
    <h2 class="text-2xl font-bold text-center mb-6">管理者ログイン</h2>

    @if ($errors->any())
        <div class="mb-4 text-red-600 text-sm">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.login') }}">
        @csrf

        <div class="mb-4">
            <label for="email" class="block font-semibold mb-1">メールアドレス</label>
            <input id="email" type="email" name="email" class="w-full border rounded px-3 py-2" required autofocus>
        </div>

        <div class="mb-6">
            <label for="password" class="block font-semibold mb-1">パスワード</label>
            <input id="password" type="password" name="password" class="w-full border rounded px-3 py-2" required>
        </div>

        <button type="submit" class="w-full bg-black text-white py-2 rounded">管理者ログイン</button>
    </form>
</div>
@endsection