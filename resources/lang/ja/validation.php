<?php

return [
    'required' => ':attributeを入力してください',
    'email' => ':attributeはメール形式で入力してください',
    'string' => ':attributeは文字列で入力してください',
    'integer' => ':attributeは整数で入力してください',
    'exists' => ':attributeが不正です',
    'max' => [
        'string' => ':attributeは:max文字以内で入力してください',
        'numeric' => ':attributeは:max以下で入力してください',
    ],
    'min' => [
        'string' => ':attributeは:min文字以上で入力してください',
        'numeric' => ':attributeは:min以上で入力してください',
    ],
    'confirmed' => 'パスワードと一致しません',

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
    ],
];
