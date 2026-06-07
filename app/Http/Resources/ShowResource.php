<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'title'                => $this->title,
            'slug'                 => $this->slug,
            'description'          => $this->description,
            'poster_url'           => $this->poster_url,
            'duration'             => $this->duration,
            'created_in'           => $this->created_in,
            'bookable'             => (bool) $this->bookable,

            // Lieu principal (si chargé)
            'location'             => $this->whenLoaded('location', fn () => [
                'id'          => $this->location->id,
                'designation' => $this->location->designation,
            ]),

            // Représentations (si chargées)
            'representations'      => RepresentationResource::collection(
                $this->whenLoaded('representations')
            ),

            // Nombre de représentations (si withCount utilisé)
            'representations_count' => $this->when(
                isset($this->representations_count),
                $this->representations_count
            ),

            // Tarifs (si chargés)
            'prices'               => $this->whenLoaded('prices', fn () =>
                $this->prices->map(fn ($price) => [
                    'id'    => $price->id,
                    'label' => $price->label ?? $price->type,
                    'price' => $price->price,
                ])
            ),
        ];
    }
}
