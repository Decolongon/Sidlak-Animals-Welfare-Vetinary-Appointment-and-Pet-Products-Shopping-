<?php

namespace App\Helpers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class YearFilterHelper
{
    protected int $year;

    // public function __construct()
    // {
    //     $this->year = now()->year;
    // }

    protected string $sessionKey = 'current_year_filter';

    /**
     * Get the current year
     *
     * @return integer
     */
    public function getYear(): int
    {
        return Session::get($this->sessionKey, now()->year);
    }

    /**
     * Set the current year
     *
     * @param integer $year
     * @return integer
     */
    public function setYear(int $year): int
    {
        Session::put($this->sessionKey, $year);
        return $year;
    }
}
