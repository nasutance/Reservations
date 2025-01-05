@extends('layouts.main')

@section('title', 'Fiche d\'une représentation')

@section('content')

<article>
    <h1>Représentation du {{ $rep_date }} à {{ $rep_time }}</h1>

    <p><strong>Spectacle:</strong> {{ $representation->show->title }}</p>

    <p><strong>Lieu:</strong>
        @if($representation->location)
            {{ $representation->location->designation }}
        @elseif($representation->show->location)
            {{ $representation->show->location->designation }}
        @else
            <em>à déterminer</em>
        @endif
    </p>
</article>

<nav>
    <a href="{{ route('representation.index') }}">Retour à l'index</a>
</nav>

@endsection
