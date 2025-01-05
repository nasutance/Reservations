@extends('layouts.main')

@section('title', 'Liste des représentations')

@section('content')

    <h1>Liste des représentations</h1>

    <ul>
        @foreach($representations as $representation)
            <li>
                <datetime>{{ substr($representation->schedule, 0, -3) }}</datetime> - {{ $representation->show->title }}
                @if($representation->location)
                    - <span>{{ $representation->location->designation }}</span>
                @elseif($representation->show->location)
                    - <span>{{ $representation->show->location->designation }}</span>
                @else
                    - <em>Pas de lieu</em>
                @endif
                <a href="{{ route('representation.show', $representation->id) }}">&#128065;</a>
            </li>
        @endforeach
    </ul>

@endsection
