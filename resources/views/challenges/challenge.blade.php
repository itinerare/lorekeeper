@extends('challenges.layout')

@section('challenges-title') Home @endsection

@section('content')
{!! breadcrumbs(['Challenges' => 'challenges', $challenge->name => 'challenges/'.$challenge->id]) !!}

<h1>
    {{ $challenge->name }}
    @if(Auth::check() && Auth::user()->canChallenge && (isset($showRegister) ? $showRegister : true))
        <a href="#" class="float-right btn btn-success register-challenge-button" data-id="{{ $challenge->id }}">Register</a>
    @endif
</h1>

<div class="card mb-4">
    <div class="card-body">
        {!! $challenge->parsed_description !!}
        @if($challenge->rules)
            <h4>Rules</h4>
            {!! nl2br(htmlentities($challenge->rules)) !!}
        @endif
    </div>
</div>

<h4>Prompts</h4>
<ul class="list-group list-group-flush">
    @foreach($challenge->data as $prompt)
        <li class="list-group-item">
            <h6>{{ $prompt['name'] }}</h6>
            @if(isset($prompt['description']))
                {!! nl2br(htmlentities($prompt['description'])) !!}
            @endif
        </li>
    @endforeach
</ul>

@endsection

@section('scripts')
@parent
    @if(Auth::check())
        <script>
        $( document ).ready(function() {
            $('.register-challenge-button').on('click', function(e) {
                e.preventDefault();
                loadModal("{{ url('challenges/new/') }}/" + $(this).data('id'), 'Register');
            });
        });
        </script>
    @endif
@endsection
