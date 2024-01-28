<?php

namespace App\Enums;

enum UserRoleEnum:string{
    case PLATEFORME = 'responsable_plateforme';
    case PROJECT =  'reponsable_branche';
    case GROUP =  'responsable_filiale';
}
