<?php

namespace App\Helpers;

class PostalCodeHelper
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    // public function getPostalCodes(): array
    // {
    //     return [
    //         '6100', // bcd
    //         '6101', //bago
    //         '6107', //binalbagan
    //         '6121', //cadiz
    //         '6126', //calatrava
    //         '6110', //candoni
    //         '6112', // cauayan
    //         '6113', // don salvador benedicto
    //         '6118', // ebm
    //         '6124', //escalante
    //         '6108', // himamaylan
    //         '6106', //hinigaran
    //         '6114', //hinoba-an
    //         '6109', //ilog
    //         '6128', // isabela
    //         '6111', // kabankalan
    //         '6130', // la carlota
    //         '6131', // la castellana
    //         '6120', //manapla
    //         '6132', //moises padilla
    //         '6129', // murcia
    //         '6123', //paraiso fabrica
    //         '6105', //pontevedra
    //         '6102', // pulupandan
    //         '6122', // sagay
    //         '6127', // san carlos
    //         '6104', // san enrique
    //         '6116', // silay
    //         '6112', //siplay
    //         '6115', // talisay
    //         '6125', //toboso
    //         '6103', // valladolid
    //         '6119', //victorias

    //     ];
    // }


    public function getCityPostalCodes(): array
    {
        return [
            'Bacolod City (Capital)' => '6100',
            'Bago City' => '6101',
            'Binalbagan' => '6107',
            'Cadiz City' => '6121',
            'Calatrava' => '6126',
            'Candoni' => '6110',
            'Cauayan' => '6112',
            'Don Salvador Benedicto' => '6113',
            'Enrique B. Magalona (Saravia)' => '6118',
            'City of Escalante' => '6124',
            'City of Himamaylan' => '6108',
            'Hinigaran' => '6106',
            'Hinoba-an (Asia)' => '6114',
            'Ilog' => '6109',
            'Isabela' => '6128',
            'City of Kabankalan' => '6111',
            'La Carlota City' => '6130',
            'La Castellana' => '6131',
            'Manapla' => '6120',
            'Moises Padilla (Magallon)' => '6132',
            'Murcia' => '6129',
            'Paraiso Fabrica' => '6123',
            'Pontevedra' => '6105',
            'Pulupandan' => '6102',
            'Sagay City' => '6122',
            'San Carlos City' => '6127',
            'San Enrique' => '6104',
            'Silay City' => '6116',
            'City of Sipalay' => '6113',
            'City of Talisay' => '6115',
            'Toboso' => '6125',
            'Valladolid' => '6103',
            'City of Victorias' => '6119',
        ];
    }



    public function getPostalCodeByCityName(string $cityName): ?string
    {
        $cityPostalCodes = $this->getCityPostalCodes();

        // Try exact match first
        if (isset($cityPostalCodes[$cityName])) {
            return $cityPostalCodes[$cityName];
        }

        // Try case-insensitive match
        foreach ($cityPostalCodes as $city => $postalCode) {
            if (strtolower($city) === strtolower($cityName)) {
                return $postalCode;
            }
        }

        // Try partial match
        foreach ($cityPostalCodes as $city => $postalCode) {
            if (str_contains(strtolower($city), strtolower($cityName))) {
                return $postalCode;
            }
        }

        return null;
    }
}
