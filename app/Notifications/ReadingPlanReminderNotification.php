<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * 読書計画のリマインダー通知（DB 保存）
 */
class ReadingPlanReminderNotification extends Notification
{
    use Queueable;

    public function __construct(private array $data) {}

    /**
     * 通知チャネル
     */
    public function via($notifiable): array
    {
        return ['database'];
    }

    /**
     * DB 保存用データ
     */
    public function toDatabase($notifiable): array
    {
        return $this->data;
    }

    /**
     * 通知データを取得
     */
    public function getData(): array
    {
        return $this->data;
    }
}
