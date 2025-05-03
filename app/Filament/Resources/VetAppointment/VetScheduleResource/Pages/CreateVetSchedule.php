<?php

namespace App\Filament\Resources\VetAppointment\VetScheduleResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\VetAppointment\VetScheduleResource;

class CreateVetSchedule extends CreateRecord
{
    protected static string $resource = VetScheduleResource::class;

    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

   
}
