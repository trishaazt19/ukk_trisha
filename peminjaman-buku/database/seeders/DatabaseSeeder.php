<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
      
User::firstOrCreate(
    ['email' => 'trisha@gmail.com'],
    [
        'name' => 'admin',
        'password' => 'password',
        'role' => 'admin',
    ]
);

      
User::firstOrCreate(
    ['email' => 'siswa@perpus.test'],
    [
        'name' => 'Yuki',
        'password' => 'password',
        'role' => 'siswa',
        'kelas' => 'XI RPL 3',
    ]
);


        $books = [
            [
                'title' => 'Laskar Pelangi',
                'author' => 'Andrea Hirata',
                'publisher' => 'Bentang Pustaka',
                'year' => 2005,
                'stock' => 4,
            ],
            [
                'title' => 'Si putih',
                'author' => 'Tere Liye',
                'publisher' => 'Gramedia Pustaka Utama',
                'year' => 2014,
                'stock' => 3,
            ],
            [
                'title' => 'Negeri 10 onde onde',
                'author' => 'Ahmad Fuadi',
                'publisher' => 'Gramedia Pustaka Utama',
                'year' => 2009,
                'stock' => 5,
            ],
            [
                'title' => 'Laut Bernyanyi',
                'author' => 'Leila S. Chudori',
                'publisher' => 'Kepustakaan Populer Gramedia',
                'year' => 2017,
                'stock' => 3,
            ],
        ];

        foreach ($books as $book) {
            Book::updateOrCreate(
                ['title' => $book['title']],
                $book,
            );
        }

        // Replace the old placeholder while keeping any existing loan references intact.
        Book::where('title', 'Belajar Laravel')->update([
            'title' => 'Pulang',
            'author' => 'Leila S. Chudori',
            'publisher' => 'Kepustakaan Populer Gramedia',
            'year' => 2012,
            'stock' => 3,
        ]);
    }
}
