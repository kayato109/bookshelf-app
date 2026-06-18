<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * 書籍情報を API レスポンス形式に変換するリソース.
 */
class BookResource extends JsonResource
{
    /**
     * 配列形式に変換
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'author' => $this->author,
            'isbn' => $this->isbn,
            'published_date' => optional($this->published_date)->toDateString(),
            'description' => $this->description,
            'image_url' => $this->image_url,

            'genres' => GenreResource::collection(
                $this->whenLoaded('genres')
            ),

            'avg_rating' => $this->reviews_avg_rating,
            'reviews_count' => $this->reviews_count,

            'reviews' => ReviewResource::collection(
                $this->whenLoaded('reviews')
            ),
        ];
    }
}
