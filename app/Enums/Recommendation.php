<?php

namespace App\Enums;

use InvalidArgumentException;

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

    /**
     * @return array<string, string>
     */
    public static function toSelectArray(): array
    {
        return [
            self::Convoquer->value => self::Convoquer->label(),
            self::Attente->value => self::Attente->label(),
            self::Rejeter->value => self::Rejeter->label(),
        ];
    }

    public static function fromLabel(string $label): self
    {
        return match (true) {
            $label === self::Convoquer->label() => self::Convoquer,
            $label === self::Attente->label() => self::Attente,
            $label === self::Rejeter->label() => self::Rejeter,
            default => throw new InvalidArgumentException("Invalid recommendation label: {$label}"),
        };
    }
}
