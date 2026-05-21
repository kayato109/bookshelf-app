<?php

return [
    'required' => ':attributeを入力してください',
    'email' => ':attributeはメール形式で入力してください',
    'string' => ':attributeは文字列で入力してください',
    'integer' => ':attributeは整数で入力してください',
    'max' => [
        'string' => ':attributeは:max文字以内で入力してください',
        'numeric' => ':attributeは:max以下で入力してください',
    ],
    'min' => [
        'string' => ':attributeは:min文字以上で入力してください',
        'numeric' => ':attributeは:min以上で入力してください',
    ],
    'size' => [
        'string' => ':attributeは:size桁で入力してください',
    ],
    'confirmed' => 'パスワードと一致しません',

    'date' => ':attributeは有効な日付形式で入力してください',
    'url' => ':attributeは正しい形式で入力してください',
    'unique' => '入力された:attributeは既に登録されています',
    'exists' => '選択された:attributeが存在しません',

    'array' => ':attributeは配列で入力してください',

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
