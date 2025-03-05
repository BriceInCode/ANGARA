<?php

namespace App\Enums;

enum SessionStatus: string
{
    case EN_ATTENTE = 'en_attente';
    case ACTIF = 'actif';
    case EXPIRE = 'expire';
    case REVOQUE = 'revoked';
}
