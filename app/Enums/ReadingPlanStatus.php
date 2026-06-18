<?php

namespace App\Enums;

/**
 * 読書計画の状態を表す Enum.
 *
 * - pending   : 未完了
 * - completed : 完了
 * - overdue   : 期限切れ
 */
enum ReadingPlanStatus: string
{
    case Pending = 'pending';
    case Completed = 'completed';
    case Overdue = 'overdue';

    /**
     * 日本語ラベルを返す.
     */
    public function label(): string
    {
        return match ($this) {
            self::Pending => '未完了',
            self::Completed => '完了',
            self::Overdue => '期限切れ',
        };
    }

    /**
     * 状態に応じた Tailwind CSS のバッジクラスを返す.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::Pending => 'bg-yellow-100 text-yellow-800',
            self::Completed => 'bg-green-100 text-green-800',
            self::Overdue => 'bg-red-100 text-red-800',
        };
    }
}
