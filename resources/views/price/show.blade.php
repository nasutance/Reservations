@extends('layouts.app')
@section('title', 'Fiche d\'un prix')
@section('content')
    <h1>Tarif {{ ucfirst($price->type) }}</h1>
    <p>{{ $price->description }}</p>
    <p>{{ $price->price }} €</p>
    <p>Validité : du {{ $price->startDate }} au {{ $price->endDate }}</p>
    <nav><a href="{{ route('price.index') }}">Retour à l'index</a></nav>
@endsection 
