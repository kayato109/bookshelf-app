<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * ジャンル情報を API レスポンス形式に変換するリソース.
 */
class GenreResource extends JsonResource
{
    /**
     * 配列形式に変換
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}
