<?php

namespace App\Enums;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum AppointmentStatusEnum: string implements HasColor, HasIcon, HasLabel
{
   // case Pending = 'pending';
    case Booked = 'approved';
    //case Rejected = 'rejected';
    case Completed = 'completed';

    public function getLabel(): string
    {
        return match ($this) {
            //self::Pending => 'Pending',
            self::Booked => 'Booked',
            //self::Rejected => 'Rejected',
            self::Completed => 'Completed',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            //self::Pending => 'warning',
            self::Booked => 'success',
            //self::Rejected => 'danger',
            self::Completed => 'info',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            //self::Pending => 'heroicon-o-clock',
            self::Booked => 'heroicon-o-bookmark',
            //self::Rejected => 'heroicon-o-x-circle',
            self::Completed => 'heroicon-o-check-circle',
        };
    }
}
