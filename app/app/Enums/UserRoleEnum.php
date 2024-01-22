<?php

namespace App\Enums;

enum UserRoleEnum:string{
    case PLATEFORME = 'responsable_plateforme';
    case BRANCHE =  'reponsable_branche';
    case FILIALE =  'responsable_filiale';
}
