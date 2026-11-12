<?php

namespace App\Filament\Admin\Resources\Books\Tables;


use App\Models\Book;
use App\Models\Loan;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class BooksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('author')
                    ->searchable(),
                TextColumn::make('publisher')
                    ->searchable(),
                TextColumn::make('year')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('stock')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                                Action::make('pinjam')
                    ->icon('heroicon-o-book-open')
                    ->requiresConfirmation()
                    ->visible(fn () => ! auth()->user()?->isAdmin())
                    ->disabled(fn (Book $record) => $record->stock < 1)
                    ->action(function (Book $record) {
                        $borrowed = DB::transaction(function () use ($record) {
                            $book = Book::query()->lockForUpdate()->findOrFail($record->id);
                            $activeLoans = Loan::query()
                                ->where('user_id', auth()->id())
                                ->where('status', 'dipinjam')
                                ->count();

                            if ($activeLoans >= 3 || $book->stock < 1) {
                                return false;
                            }

                            Loan::create([
                                'user_id' => auth()->id(),
                                'book_id' => $book->id,
                                'loan_date' => now()->toDateString(),
                                'due_date' => now()->addDays(7)->toDateString(),
                            ]);
                            $book->decrement('stock');

                            return true;
                        });

                        if (! $borrowed) {
                            Notification::make()->title('Batas pinjaman tercapai atau stok buku habis')->danger()->send();
                            return;
                        }
                        Notification::make()->title('Buku berhasil dipinjam')->success()->send();
                    }),

                EditAction::make()->visible(fn () => auth()->user()?->isAdmin()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ])->visible(fn () => auth()->user()?->isAdmin()),
            ]);
    }
}
