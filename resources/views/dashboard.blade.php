<?php
/**
 * @var \Illuminate\Database\Eloquent\Collection<int, App\Models\CourseEnrollment> $myEnrollments
 */
?>
@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <h2 class="card-header">My courses</h2>
                    <div class="card-body">
                        <ul>
                            @foreach($myEnrollments as $myEnrollment)
                                <li>
                                    <a href="{{ route('courseEnrollments.show', ['course' => $myEnrollment->course]) }}">
                                        {{ $myEnrollment->course->title }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
