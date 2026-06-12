<?php

namespace App\Enums;

enum ReadingPlanStatus: string
{
    case Pending = 'pending';
    case Completed = 'completed';
    case Overdue = 'overdue';

    public function label(): string
    {
        return match ($this) {
            self::Pending => '未完了',
            self::Completed => '完了',
            self::Overdue => '期限切れ',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Pending => 'bg-yellow-100 text-yellow-800',
            self::Completed => 'bg-green-100 text-green-800',
            self::Overdue => 'bg-red-100 text-red-800',
        };
    }
}
