<?php

namespace App\Validations;


class CustomRules
{
    public function checkNotNumeric(string $str): bool
    {
        if (is_numeric($str)) {
            return false;
        } else {
            return true;
        }
    }
}
