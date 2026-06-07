<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RepresentationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'       => $this->id,
            'schedule' => $this->schedule,

            // Lieu de la représentation (si chargé)
            'location' => $this->whenLoaded('location', fn () => [
                'id'          => $this->location->id,
                'designation' => $this->location->designation,
            ]),

            // Spectacle lié (si chargé)
            'show'     => $this->whenLoaded('show', fn () => [
                'id'    => $this->show->id,
                'title' => $this->show->title,
            ]),
        ];
    }
}
