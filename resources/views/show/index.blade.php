@extends('layouts.main')

@section('title', 'Liste des spectacles')

@section('content')

    <h1>Liste des spectacles</h1>

    {{--Formulaire de tri / filtres --}}
    <form method="GET" action="{{ route('show.index') }}" style="margin-bottom: 2em;">
        <input type="text" name="q" placeholder="Mot-clé" value="{{ request('q') }}">

        <input type="number" name="min_duration" placeholder="Durée min" value="{{ request('min_duration') }}">
        <input type="number" name="max_duration" placeholder="Durée max" value="{{ request('max_duration') }}">

        <input type="text" name="postal_code" placeholder="Code postal" value="{{ request('postal_code') }}">

        <select name="sort">
            <option value="">-- Tri --</option>
            <option value="title" @selected(request('sort') === 'title')>Titre</option>
            <option value="duration" @selected(request('sort') === 'duration')>Durée</option>
        </select>

        <select name="direction">
            <option value="asc" @selected(request('direction') === 'asc')>Asc</option>
            <option value="desc" @selected(request('direction') === 'desc')>Desc</option>
        </select>

        <button type="submit">Filtrer</button>
        <a href="{{ route('show.index') }}">Réinitialiser</a>
    </form>

    <ul>
        @foreach($shows as $show)
            <li>
                <a href="{{ route('show.show', $show->id) }}">{{ $show->title }}</a>

                @if(!$show->bookable)
                    <em>Réservation indisponible</em>
                @endif

                @if($show->representations->count() == 1)
                    - <span>1 représentation</span>
                @elseif($show->representations->count() > 1)
                    - <span>{{ $show->representations->count() }} représentations</span>
                @else
                    - <em>aucune représentation</em>
                @endif
            </li>
        @endforeach
    </ul>

    {{-- 📄 Pagination --}}
    {{ $shows->appends(request()->query())->links() }}

@endsection
