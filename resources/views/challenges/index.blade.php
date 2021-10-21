@extends('challenges.layout')

@section('challenges-title') Home @endsection

@section('content')
{!! breadcrumbs(['Challenges' => 'challenges']) !!}

<h1>Challenges</h1>

<div>
    {!! Form::open(['method' => 'GET', 'class' => '']) !!}
        <div class="form-inline justify-content-end">
            <div class="form-group ml-3 mb-3">
                {!! Form::select('sort', [
                    'difficulty'           => 'Sort by Name',
                    'difficulty-reverse'   => 'Sort by Name (Reverse)',
                    'newest'               => 'Newest First',
                    'oldest'               => 'Oldest First'
                ], Request::get('sort') ? : 'category', ['class' => 'form-control']) !!}
            </div>
            <div class="form-group ml-3 mb-3">
                {!! Form::submit('Search', ['class' => 'btn btn-primary']) !!}
            </div>
        </div>
    {!! Form::close() !!}
</div>

{!! $challenges->render() !!}

@foreach($challenges as $challenge)
    <div class="card mb-4">
        <div class="card-header">
            <h3>
                {!! $challenge->displayName !!}
                @if(Auth::check() && Auth::user()->canChallenge && (isset($showRegister) ? $showRegister : true))
                    <a href="#" class="float-right btn btn-success register-challenge-button" data-id="{{ $challenge->id }}">Register</a>
                @endif
            </h3>
        </div>
        <div class="card-body">
            {!! $challenge->parsed_description !!}
            @if($challenge->rules)
                <h4>Rules</h4>
                {!! nl2br(htmlentities($challenge->rules)) !!}
            @endif
        </div>
        <ul class="list-group list-group-flush">
            <li class="list-group-item">
                <a class="card-title h4 collapse-title"  data-toggle="collapse" href="#prompts-{{ $challenge->id }}"> Prompts</a>
                <div id="prompts-{{ $challenge->id }}" class="collapse">
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
                </div>
            </li>
        </ul>
    </div>
@endforeach

{!! $challenges->render() !!}
<div class="text-center mt-4 small text-muted">{{ $challenges->total() }} result{{ $challenges->total() == 1 ? '' : 's' }} found.</div>

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
