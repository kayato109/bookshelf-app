<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first(); // 山田太郎

        $books = [
            [
                'title' => '吾輩は猫である',
                'author' => '夏目漱石',
                'isbn' => '9784101010014',
                'published_date' => '1905-01-01',
                'description' => '『吾輩は猫である』（わがはいはねこである）は、夏目漱石の長編小説であり、処女小説である。',
                'genres' => ['小説'],
            ],
            [
                'title' => '人を動かす',
                'author' => 'D・カーネギー',
                'isbn' => '9784422100524',
                'published_date' => '1936-10-01',
                'description' => 'デール・カーネギーの不朽の名著。人間関係の原則を実話と事例で説き、人に好かれ、人の心を動かすための行動と自己変革を促す。',
                'genres' => ['ビジネス', '自己啓発'],
            ],
            [
                'title' => 'リーダブルコード',
                'author' => 'Dustin Boswell',
                'isbn' => '9784873115658',
                'published_date' => '2012-06-23',
                'description' => 'より良いコードを書くためのシンプルで実践的なテクニック (Theory in practice)。',
                'genres' => ['技術書'],
            ],
            [
                'title' => '7つの習慣',
                'author' => 'スティーブン・R・コヴィー',
                'isbn' => '9784863940246',
                'published_date' => '2013-08-30',
                'description' => '「私たちの人格は、習慣の総体である」 世界的ベストセラー『7つの習慣』は、人生の成功と幸せを実現するための普遍的な原則を提示します。',
                'genres' => ['ビジネス', '自己啓発'],
            ],
            [
                'title' => '坊っちゃん',
                'author' => '夏目漱石',
                'isbn' => '9784101010021',
                'published_date' => '1906-04-01',
                'description' => '夏目漱石が松山中学在任当時の体験を背景とした初期の代表作。',
                'genres' => ['小説'],
            ],
            [
                'title' => 'サピエンス全史',
                'author' => 'ユヴァル・ノア・ハラリ',
                'isbn' => '9784309226712',
                'published_date' => '2016-09-08',
                'description' => 'この書籍はホモ・サピエンスについて扱い、石器時代から21世紀までの人類の歴史を概観するものである。自然科学、特に進化生物学の観点からもそのテーマが語られる。',
                'genres' => ['歴史', '科学'],
            ],
            [
                'title' => 'Clean Code',
                'author' => 'Robert C. Martin',
                'isbn' => '9784048930598',
                'published_date' => '2017-12-18',
                'description' => 'コードを書き、読み、洗練する 本書のケーススタディを注意深く読むことで、コードを洗練していく過程で行うべき判断について学ぶことができます。',
                'genres' => ['技術書'],
            ],
            [
                'title' => '嫌われる勇気',
                'author' => '岸見一郎・古賀史健',
                'isbn' => '9784478025819',
                'published_date' => '2013-12-13',
                'description' => 'アルフレッド・アドラーの「アドラー心理学」を哲学者（哲人）と若者の会話を通して、分かりやすく解説をしている。',
                'genres' => ['自己啓発'],
            ],
            [
                'title' => '火花',
                'author' => '又吉直樹',
                'isbn' => '9784163902302',
                'published_date' => '2015-03-11',
                'description' => '初出は『文學界』2015年2月号（文藝春秋）[1]。掲載時より現役人気お笑いタレントの手がけた純文学小説として話題を呼び、文芸誌である同誌が増刷されるヒットとなった。',
                'genres' => ['小説'],
            ],
            [
                'title' => 'FACTFULNESS',
                'author' => 'ハンス・ロスリング',
                'isbn' => '9784822289607',
                'published_date' => '2019-01-11',
                'description' => '人間はいくつもの本能や思い込みにより、世界を間違った形で認識してしまうことがあります。こうした誤認を防ぐための考え方が「ファクトフルネス」です。',
                'genres' => ['ビジネス', '科学'],
            ],
            [
                'title' => 'コンテナ物語',
                'author' => 'マルク・レビンソン',
                'isbn' => '9784822251468',
                'published_date' => '2007-01-18',
                'description' => '生産地と消費地を結ぶ箱。コンテナは、輸送の世界に革命を起こしました。',
                'genres' => ['ビジネス', '歴史'],
            ],
        ];

        foreach ($books as $index => $data) {
            $book = Book::firstOrCreate(
                ['isbn' => $data['isbn']],
                [
                    'user_id' => $user->id,
                    'title' => $data['title'],
                    'author' => $data['author'],
                    'published_date' => $data['published_date'],
                    'description' => $data['description'],
                    'image_url' => "https://placehold.co/200x300/e2e8f0/475569?text=" . ($index + 1),
                ]
            );

            $genreIds = Genre::whereIn('name', $data['genres'])->pluck('id');
            $book->genres()->sync($genreIds);
        }
    }
}