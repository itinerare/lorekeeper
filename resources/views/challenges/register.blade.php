@extends('bingo.layout')

@section('bingo-title') Register @endsection

@section('content')
{!! breadcrumbs(['Community Garden' => 'communitygarden', 'Register' => 'communitygarden/'.$isPersonal? 'personal' : 'community'.'/register']) !!}

{!! Form::open(['url' => 'communitygarden/'.($isPersonal ? 'personal' : 'community').'/register', 'id' => 'submissionForm']) !!}

<h2>Register : {{$isPersonal ? 'Personal' : 'Community'}} Prompt</h2>

@if(!$challenge && $canRegister)
    @if(!$isPersonal)
        <div class="card mb-3">
            <div class="card-body">
                <h4>Community Prompt</h4>
                @if($prompt)
                    <p><strong>Started:</strong> {!! pretty_date(Carbon\Carbon::today()->startOfMonth()->startOfDay()) !!}<br/>
                    <strong>Ends:</strong> {!! pretty_date(Carbon\Carbon::today()->endOfMonth()->endOfDay()) !!}</p>
                    {!! $prompt->parsed_description !!}
                    <p><strong>The following special rules apply for this prompt:</strong></p>
                    @if($prompt->rules)
                        {{ $prompt->rules }}
                    @else
                        <p>None!</p>
                    @endif
                @else
                    <p>There isn't a community prompt running right now!</p>
                @endif
            </div>
        </div>
    @endif

    <p>
        Select your difficulty and specify the details of your prompt here. Once you submit your registration, it will be put into the queue to be confirmed. You will, however, be able to begin immediately-- this confirmation is just to ensure that your prompt is suitable. If you are unsure if your prompt qualifies, feel free to ask before registration! Note that if it does not, your registration will be cancelled, and you will be able to register again. Alternately, you can wait until your registration is confirmed, as while you will be able to log day 1 of your challenge, the timer will not start fully running until it is confirmed.
    </p>
    <p>
        However, note that unless it is cancelled due to your specified prompt being unsuitable, you will not be able to change the details of your challenge once it is registered. Review your registration carefully before submitting!
    </p>

    @if(isset($prompt))
        {!! Form::hidden('prompt_id', $prompt->id) !!}
    @endif

    <div class="form-group">
        {!! Form::label('difficulty', 'Difficulty') !!}
        {!! Form::select('difficulty', [9 => 'Easy (9 Days)',16 => 'Medium (16 Days)',25 => 'Hard (25 Days)'], null, ['class' => 'form-control mr-2 default difficulty-select', 'placeholder' => 'Select Difficulty']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('description', 'Prompt Description') !!} {!! add_help('Enter a description of your intended ' . ($isPersonal ? 'personal' : 'community') . ' prompt (no HTML). This will be reviewed by a staff member before your prompt becomes active.') !!}
        {!! Form::textarea('description', null, ['class' => 'form-control']) !!}
    </div>

    <div class="text-right">
        <a href="#" class="btn btn-primary" id="submitButton">Submit</a>
    </div>
    {!! Form::close() !!}
@else
    <div class="text-center">
        <p>You can't register again yet!</p>
        <p>
            @if($challenge)
                You currently have a running challenge. Go <a href="{{ url('communitygarden/'. ($isPersonal ? 'personal' : 'community')) }}">here</a> to view it!
            @else
                You last registered {!! pretty_date($challengeOld->created_at) !!}. You can register again {!! pretty_date(Carbon\Carbon::today()->startOfMonth()->startOfDay()->addMonth(1)) !!}.
            @endif
        </p>
    </div>
@endif

<div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <span class="modal-title h5 mb-0">Confirm Registration</span>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>This will submit the form and put your {{$isPersonal ? 'personal' : 'community'}} prompt registration into the approval queue. You will not be able to edit the contents after the submission has been made. Click the Confirm button to complete your registration.</p>
                <div class="text-right">
                    <a href="#" id="formSubmit" class="btn btn-primary">Confirm</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@parent 
<script>
    $(document).ready(function() {
        $('.default.difficulty-select').selectize();
        var $submitButton = $('#submitButton');
        var $confirmationModal = $('#confirmationModal');
        var $formSubmit = $('#formSubmit');
        var $submissionForm = $('#submissionForm');
        
        $submitButton.on('click', function(e) {
            e.preventDefault();
            $confirmationModal.modal('show');
        });

        $formSubmit.on('click', function(e) {
            e.preventDefault();
            $submissionForm.submit();
        });
    });
</script>
@endsection