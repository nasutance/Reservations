<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * Classe Artist – représente un artiste (acteur, metteur en scène, etc.)
 * Cette classe est liée à la table 'artists' dans la base de données.
 *
 * @property int $id
 * @property string $firstname
 * @property string $lastname
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ArtistType> $artistTypes
 * @property-read int|null $artist_types_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Type> $types
 * @property-read int|null $types_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist whereFirstname($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist whereLastname($value)
 * @mixin \Eloquent
 * @property int|null $troupe_id
 * @property-read \App\Models\Troupe|null $troupe
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist whereTroupeId($value)
 */
	class Artist extends \Eloquent {}
}

namespace App\Models{
/**
 * Classe ArtistType – Représente l'association entre un artiste et un type (fonction).
 * 
 * Ex. : "Daniel Marcelin est un scénographe"
 * C'est un modèle pivot entre les entités Artist et Type.
 *
 * @property int $id
 * @property int $artist_id
 * @property int $type_id
 * @property-read \App\Models\Artist $artist
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ArtistTypeShow> $artistTypeShow
 * @property-read int|null $artist_type_show_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Show> $shows
 * @property-read int|null $shows_count
 * @property-read \App\Models\Type $type
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArtistType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArtistType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArtistType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArtistType whereArtistId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArtistType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArtistType whereTypeId($value)
 * @mixin \Eloquent
 */
	class ArtistType extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $artist_type_id
 * @property int $show_id
 * @property-read \App\Models\ArtistType $artistType
 * @property-read \App\Models\Show $show
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArtistTypeShow newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArtistTypeShow newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArtistTypeShow query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArtistTypeShow whereArtistTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArtistTypeShow whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ArtistTypeShow whereShowId($value)
 * @mixin \Eloquent
 */
	class ArtistTypeShow extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property string $postal_code
 * @property string $locality
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Location> $locations
 * @property-read int|null $locations_count
 * @method static \Database\Factories\LocalityFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Locality newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Locality newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Locality query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Locality whereLocality($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Locality wherePostalCode($value)
 * @mixin \Eloquent
 */
	class Locality extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $slug
 * @property string $designation
 * @property string $address
 * @property string $locality_postal_code
 * @property string|null $website
 * @property string|null $phone
 * @property-read \App\Models\Locality $locality
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Representation> $representations
 * @property-read int|null $representations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Show> $shows
 * @property-read int|null $shows_count
 * @method static \Database\Factories\LocationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereDesignation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereLocalityPostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereWebsite($value)
 * @mixin \Eloquent
 */
	class Location extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $type
 * @property string $price
 * @property string $description
 * @property string $start_date
 * @property string|null $end_date
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Reservation> $reservations
 * @property-read int|null $reservations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Show> $shows
 * @property-read int|null $shows_count
 * @method static \Database\Factories\PriceFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Price newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Price newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Price query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Price whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Price whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Price whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Price wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Price whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Price whereType($value)
 * @mixin \Eloquent
 */
	class Price extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $location_id
 * @property int $show_id
 * @property Carbon $schedule
 * @property-read \App\Models\Location|null $location
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Price> $prices
 * @property-read int|null $prices_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Reservation> $reservations
 * @property-read int|null $reservations_count
 * @property-read \App\Models\Show $show
 * @method static \Database\Factories\RepresentationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Representation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Representation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Representation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Representation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Representation whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Representation whereSchedule($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Representation whereShowId($value)
 * @mixin \Eloquent
 */
	class Representation extends \Eloquent implements \Spatie\Feed\Feedable {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property string $status
 * @property \Illuminate\Support\Carbon $booking_date
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Representation> $representations
 * @property-read int|null $representations_count
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\ReservationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereBookingDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation withoutTrashed()
 * @mixin \Eloquent
 */
	class Reservation extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property int $show_id
 * @property string $review
 * @property int $stars
 * @property int $validated
 * @property string $created_at
 * @property string|null $updated_at
 * @property-read \App\Models\Show $show
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereReview($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereShowId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereStars($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Review whereValidated($value)
 * @mixin \Eloquent
 */
	class Review extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $role
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereRole($value)
 * @mixin \Eloquent
 */
	class Role extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $slug
 * @property string $title
 * @property string|null $description
 * @property string|null $poster_url
 * @property int $duration
 * @property string $created_in
 * @property int|null $location_id
 * @property bool $bookable
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ArtistTypeShow> $artistTypeShow
 * @property-read int|null $artist_type_show_count
 * @property-read \App\Models\Location|null $location
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Price> $prices
 * @property-read int|null $prices_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Representation> $representations
 * @property-read int|null $representations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Review> $reviews
 * @property-read int|null $reviews_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $tags
 * @property-read int|null $tags_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Video> $videos
 * @property-read int|null $videos_count
 * @method static \Database\Factories\ShowFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Show newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Show newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Show onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Show query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Show whereBookable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Show whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Show whereCreatedIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Show whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Show whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Show whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Show whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Show whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Show wherePosterUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Show whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Show whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Show whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Show withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Show withoutTrashed()
 * @mixin \Eloquent
 */
	class Show extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $tag
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Show> $shows
 * @property-read int|null $shows_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereTag($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class Tag extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $logo_url
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Artist> $artists
 * @property-read int|null $artists_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Troupe newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Troupe newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Troupe query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Troupe whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Troupe whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Troupe whereLogoUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Troupe whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Troupe whereUpdatedAt($value)
 */
	class Troupe extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $type
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Artist> $artists
 * @property-read int|null $artists_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Type newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Type newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Type query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Type whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Type whereType($value)
 * @mixin \Eloquent
 */
	class Type extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string|null $login
 * @property string|null $firstname
 * @property string $lastname
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $langue
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Reservation> $reservations
 * @property-read int|null $reservations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Review> $reviews
 * @property-read int|null $reviews_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFirstname($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLangue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastname($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutTrashed()
 * @mixin \Eloquent
 */
	class User extends \Eloquent implements \Illuminate\Contracts\Auth\MustVerifyEmail, \Filament\Models\Contracts\HasName {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $title
 * @property string $video_url
 * @property int $show_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Show $show
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video whereShowId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Video whereVideoUrl($value)
 * @mixin \Eloquent
 */
	class Video extends \Eloquent {}
}

