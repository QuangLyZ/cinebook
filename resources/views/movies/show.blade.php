@extends('layouts.app')

@section('title', $movie->name ?? 'Movie')

@section('content')
  <div class="row">
    <div class="col-md-8">
      <h1>{{ $movie->name }}</h1>
      <p><strong>Genre:</strong> {{ $movie->genre }}</p>
      <p><strong>Duration:</strong> {{ $movie->duration }} minutes</p>
      <p>{{ $movie->description }}</p>
    </div>
    <div class="col-md-4">
      <h5>Trailer</h5>
      @if($movie->trailer_link)
        <div class="ratio ratio-16x9">
          <iframe src="{{ $movie->trailer_link }}" allowfullscreen></iframe>
        </div>
      @else
        <p>No trailer available</p>
      @endif
    </div>
  </div>
@endsection
