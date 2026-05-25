<?php

return [
    'required' => ':attribute を入力してください',
    'email' => ':attribute はメール形式で入力してください',
    'string' => ':attribute は文字列で入力してください',
    'integer' => ':attribute は整数で入力してください',

    'max' => [
        'string' => ':attribute は :max 文字以内で入力してください',
        'numeric' => ':attribute は :max 以下で入力してください',
    ],

    'min' => [
        'string' => ':attribute は :min 文字以上で入力してください',
        'numeric' => ':attribute は :min 以上で入力してください',
    ],

    'size' => [
        'string' => ':attribute は :size 桁で入力してください',
    ],

    'confirmed' => 'パスワードと一致しません',

    'date' => ':attribute は有効な日付形式で入力してください',
    'url' => ':attribute は正しい形式で入力してください',
    'unique' => '入力された :attribute は既に登録されています',
    'exists' => '選択された :attribute が存在しません',

    'array' => ':attribute は配列で入力してください',

    'attributes' => [
        // Fortify
        'name' => 'お名前',
        'email' => 'メールアドレス',
        'password' => 'パスワード',

        // API
        'keyword' => 'キーワード',
        'genre_id' => 'ジャンルID',
        'page' => 'ページ番号',
        'per_page' => '1ページあたり件数',
        'user_id' => 'ユーザーID',
        'title' => 'タイトル',
        'author' => '著者名',
        'isbn' => 'ISBN',
        'published_date' => '出版日',
        'description' => '説明',
        'image_url' => '画像URL',
        'genres' => 'ジャンル',
        'genres.*' => 'ジャンルID',
    ],
];
