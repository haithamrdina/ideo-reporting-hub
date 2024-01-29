<?php

namespace App\Enums;

enum UserRoleEnum:string{
    case PLATEFORME = 'responsable_plateforme';
    case PROJECT =  'reponsable_branche';
    case GROUP =  'responsable_filiale';


    public function description(): string
    {
        return match($this)
        {
            self::PLATEFORME => 'Responsable plateforme',
            self::PROJECT => 'Responsable branche',
            self::GROUP => 'Responsable filiale',
        };
    }
}
