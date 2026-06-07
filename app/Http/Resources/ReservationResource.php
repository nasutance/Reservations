<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'     => $this->id,
            'status' => $this->status,

            // Propriétaire (si chargé — admin uniquement)
            'user'   => $this->whenLoaded('user', fn () => [
                'id'        => $this->user->id,
                'firstname' => $this->user->firstname,
                'lastname'  => $this->user->lastname,
                'email'     => $this->user->email,
            ]),

            // Représentations avec pivot (quantité + tarif)
            'representations' => $this->whenLoaded('representations', fn () =>
                $this->representations->map(fn ($repr) => [
                    'id'       => $repr->id,
                    'schedule' => $repr->schedule,
                    'show'     => $repr->relationLoaded('show') ? [
                        'id'    => $repr->show->id,
                        'title' => $repr->show->title,
                    ] : null,
                    'quantity' => $repr->pivot->quantity ?? null,
                    'price_id' => $repr->pivot->price_id ?? null,
                ])
            ),

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
