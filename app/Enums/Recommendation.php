<?php

namespace App\Enums;

enum Recommendation: string
{
    case Convoquer = 'convoquer';
    case Attente = 'attente';
    case Rejeter = 'rejeter';

    public function label(): string
    {
        return match ($this) {
            self::Convoquer => 'À convoquer',
            self::Attente => 'En attente',
            self::Rejeter => 'À rejeter',
        };
    }
}
