@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>{{ $course->title }} Leaderboard</h2>

        <h3>Course Leaderboard</h3>
        <x-leaderboard-table :leaderboard="$finalLeaderboard" />

        <h3>Global Leaderboard</h3>
        <x-leaderboard-table :leaderboard="$finalGlobalLeaderboard" />

        <h3>Country Leaderboard</h3>
        <x-leaderboard-table :leaderboard="$finalCountryLeaderboard" />
    </div>
@endsection
