<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Str;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\NumberColumn;
use Mediconesystems\LivewireDatatables\DateColumn;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;

class LivewireDatatables extends LivewireDatatable
{
    // public function render()
    // {
    //     return view('livewire.livewire-datatables');
    // }

    public $model = User::class;
    public $exportable = true;

    function columns()
    {
        return [
            NumberColumn::name('id')->label('ID')->sortBy('id'),
            Column::name('username')->label('USERNAME'),
            Column::name('email')->label('EMAIL'),
            Column::name('email_verified_at')->label('EMAIL_VERIFIED_AT'),
            Column::name('user_role')->label('USER ROLE'),
            Column::name('user_status')->label('USER STATUS'),
            Column::name('user_platform')->label('USER PLATFORM'),
            Column::name('created_at')->label('CREATED_AT'),
            Column::name('updated_at')->label('MODIFIED_AT'),
        ];
    }
}
