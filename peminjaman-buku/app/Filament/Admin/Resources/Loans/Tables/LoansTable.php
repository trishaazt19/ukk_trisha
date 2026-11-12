<?php

namespace App\Filament\Admin\Resources\Loans\Tables;

use App\Models\Loan;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class LoansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Peminjam')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('book.title')
                    ->searchable(),
                TextColumn::make('loan_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('due_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('returned_at')
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => $state === 'dipinjam' ? 'warning' : 'success'),
                TextColumn::make('fine')
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
                  Action::make('kembalikan')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->requiresConfirmation()
                    ->visible(fn (Loan $record) => $record->status === 'dipinjam')
                    ->action(function (Loan $record) {
                        DB::transaction(function () use ($record) {
                            $loan = Loan::query()->lockForUpdate()->findOrFail($record->id);
                            if ($loan->status !== 'dipinjam') {
                                return;
                            }

                            $book = $loan->book()->lockForUpdate()->firstOrFail();
                            $lateDays = max(0, $loan->due_date->diffInDays(now()->startOfDay(), false));
                            $loan->update([
                                'returned_at' => now()->toDateString(),
                                'status' => 'dikembalikan',
                                'fine' => $lateDays * 1000,
                            ]);
                            $book->increment('stock');
                        });
                        Notification::make()->title('Buku sudah dikembalikan')->success()->send();
                    }),
            ]);
    }
}
