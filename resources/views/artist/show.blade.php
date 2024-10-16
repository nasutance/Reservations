@extends('layouts.app')

@section('title', 'Fiche d\'un artiste')

@section('content')

    <h1>{{ $artist->firstname }} {{ $artist->lastname }}</h1>

    <div><a href="{{ route('artist.edit' ,$artist->id) }}">Modifier</a></div>

    <form method="post" action="{{ route('artist.delete', $artist->id) }}" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ?')">
      @csrf
      @method('DELETE')
      <button>Supprimer</button>

    </form>

    <nav><a href="{{ route('artist.index') }}">Retour à l'index</a></nav>

@endsection
