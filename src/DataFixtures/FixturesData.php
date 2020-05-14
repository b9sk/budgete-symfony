<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 13.05.20
 * Time: 9:49
 */

namespace App\DataFixtures;


class FixturesData
{
    function getCurrency()
    {
        return [
            ["Symbol" => "Br", "Name" => "Belarus Ruble",     "Code" => "by"],
            ["Symbol" => "₽",  "Name" => "Russian Ruble",     "Code" => "ru"],
            ["Symbol" => "₴",  "Name" => "Ukrainian Hryvnia", "Code" => "ua"],
            ["Symbol" => "₸",  "Name" => "Kazakhstan Tenge",  "Code" => "kz"],
            ["Symbol" => "лв", "Name" => "Uzbekistani Som",   "Code" => "uz"],
            ["Symbol" => "$",  "Name" => "US Dollar",         "Code" => "us"],
        ];
    }
}