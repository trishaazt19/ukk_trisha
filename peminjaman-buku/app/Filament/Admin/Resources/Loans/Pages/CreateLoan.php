<?php

namespace App\Filament\Admin\Resources\Loans\Pages;

use App\Filament\Admin\Resources\Loans\LoanResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLoan extends CreateRecord
{
    protected static string $resource = LoanResource::class;
}
