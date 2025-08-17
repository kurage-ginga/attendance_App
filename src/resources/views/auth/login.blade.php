@extends('layouts.app')

@section('content')
<div class="w-full max-w-lg mx-auto mt-16 p-10 bg-white shadow-md">
    <h2 class="text-2xl font-bold text-center mb-8">ログイン</h2>

    @if ($errors->any())
        <div class="mb-6 text-red-600 text-sm">
            <ul class="list-disc pl-6">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-6">
            <label for="email" class="block font-semibold mb-2">メールアドレス</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                class="w-full border border-gray-400 px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-black"
                required autofocus>
        </div>

        <div class="mb-8">
            <label for="password" class="block font-semibold mb-2">パスワード</label>
            <input id="password" type="password" name="password"
                class="w-full border border-gray-400 px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-black"
                required>
        </div>

        <button type="submit" class="w-full bg-black text-white font-semibold py-3 rounded">ログインする</button>
    </form>

    <div class="mt-6 text-center">
        <a href="{{ route('register') }}" class="text-blue-600 underline">会員登録はこちら</a>
    </div>
</div>
@endsection