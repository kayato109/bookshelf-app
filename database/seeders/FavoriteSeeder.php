<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class FavoriteSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $books = Book::pluck('id'); // ID のみ取得して軽量化

        if ($users->isEmpty() || $books->isEmpty()) {
            return;
        }

        foreach ($users as $user) {
            $favoriteBookIds = $books
                ->shuffle()
                ->take(rand(3, 5));

            $user->favoriteBooks()->syncWithoutDetaching($favoriteBookIds);
        }
    }
}
