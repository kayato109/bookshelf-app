<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * レビュー情報を API レスポンス形式に変換するリソース.
 */
class ReviewResource extends JsonResource
{
    /**
     * 配列形式に変換
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'user_name' => $this->whenLoaded('user', fn () => $this->user->name),
            'rating' => $this->rating,
            'comment' => $this->comment,
            'created_at' => $this->created_at->format('Y-m-d'),
        ];
    }
}
