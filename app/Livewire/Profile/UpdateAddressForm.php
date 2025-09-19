<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use App\Models\Ecommerce\Address;
use Illuminate\Support\Facades\Auth;
use Woenel\Prpcmblmts\Models\PhilippineCity;
use Woenel\Prpcmblmts\Models\PhilippineBarangay;

class UpdateAddressForm extends Component
{
    public $selectedCity = null;
    public $selectedBarangay = null;
    public $searchCity = '';
    public $searchBrgy = '';

    public $cities = [];
    public $barangays = [];


    public function mount()
    {
        $this->getCities();
        $this->getBarangays();
        $this->loadUserAddress();
    }

    protected function getCities()
    {
        $this->cities = PhilippineCity::where('province_code', '0645')
            //->limit(10)
            ->get();
    }

    protected function getBarangays()
    {
        $this->barangays = PhilippineBarangay::where('city_code', $this->selectedCity)
            //->limit(10)
            ->get();
    }

    protected function getCompleteAddress()
    {
        $userAddress = Address::where('user_id', Auth::id())->first();
        return $userAddress->complete_address;
    }
    protected function loadUserAddress()
    {
        $userAddress = Address::where('user_id', Auth::id())->first();

        if ($userAddress) {

            // Find and set the city code if it exists
            $city = PhilippineCity::where('name', $userAddress->city)->first();
            if ($city) {
                $this->selectedCity = $city->code;

                // Load specific barangay base sa city na gin select n user
                $this->barangays = PhilippineBarangay::where('city_code', $this->selectedCity)->get();

                // Find and set the barangay this only works if user my ga exist na nga daan na address
                $barangay = PhilippineBarangay::where('name', $userAddress->barangay)
                    ->where('city_code', $this->selectedCity)
                    ->first();

                if ($barangay) {
                    $this->selectedBarangay = $barangay->name;
                }
            }
        }
    }

    public function updatedSearchCity()
    {
        $this->cities = PhilippineCity::where('province_code', '0645')
            ->where('name', 'like', '%' . $this->searchCity . '%')
            //->limit(10)
            ->get();
    }

    public function updatedSearchBrgy()
    {
        $this->barangays = PhilippineBarangay::where('city_code', $this->selectedCity)
            ->where('name', 'like', '%' . $this->searchBrgy . '%')
            ->get();
    }

    public function selectCity($value)
    {
        $this->selectedCity = $value;
        $this->barangays = PhilippineBarangay::where('city_code', $this->selectedCity)
            //->limit(10)
            ->get();
        $this->selectedBarangay = null;
    }

    public function selectBrgy($value)
    {
        $this->selectedBarangay = $value;
    }

    public function updateAddress()
    {
        $validated = $this->validate([
            'selectedCity' => 'required',
            'selectedBarangay' => 'required',
        ]);

        $sanitized = $this->sanitizeInput($validated);

       
        $sanitized['selectedCity'] = PhilippineCity::where('code', $sanitized['selectedCity'])->value('name');

        Address::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'city' => $sanitized['selectedCity'],
                'barangay' => $sanitized['selectedBarangay'],
            ]
        );
        session()->flash('message', 'Address updated successfully.');
    }

    protected function validationAttributes()
    {
        return [
            'selectedCity' => 'city',
            'selectedBarangay' => 'barangay',
        ];
    }

    protected function sanitizeInput(array $data): array
    {
        return [
            'selectedCity' => strip_tags($data['selectedCity']),
            'selectedBarangay' => strip_tags($data['selectedBarangay']),
        ];
    }

    public function render()
    {
        return view(
            'livewire.profile.update-address-form',
            [
                'completedAddress' => $this->getCompleteAddress(),
                'barangays' => $this->barangays,
                'cities' => $this->cities
            ]
        );
    }
}
