@extends('challenges.layout')

@section('challenges-title') Log #{{ $log->id }} @endsection

@section('content')
{!! breadcrumbs(['Challenges' => 'challenges', 'Log #'.$log->id => 'challenges/view/'.$log->id]) !!}

<h1>
    <span class="float-right badge badge-{{ $log->status == 'Old' ? 'secondary' : 'success' }}">{{ $log->status }}</span>
    Challenge Log #{{ $log->id }}
</h1>

<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <h4>{!! $log->challenge->is_active ? $log->challenge->displayName : $log->challenge->name !!}</h4>
                {!! $log->challenge->parsed_description !!}
                @if($log->challenge->rules)
                    <h4>Rules</h4>
                    {!! nl2br(htmlentities($log->challenge->rules)) !!}
                @endif
            </div>
            <div class="col-md">
                @if($log->staff)
                    <h6>Processed By:</h6>
                    <p>{!! $log->staff->displayName !!}</p>
                @endif
                @if($log->submission)
                    <h6>Submission:</h6>
                    <p><a href="{{ $log->submission->viewUrl }}">#{{ $log->submission->id }}</a></p>
                @endif
            </div>
        </div>
        @if(isset($log->staff_comments))
            <hr/>
            <h5>Staff Comments ({!! $log->staff->displayName !!})</h5>
            {!! nl2br(htmlentities($log->staff_comments)) !!}
        @endif
        @if(Auth::user()->hasPower(['manage_submissions']) && $log->isComplete && !$log->isOld)
            <hr/>
            <h5>[Admin]
            @if($log->isComplete)
                {!! $log->status != 'Old' ? 'Process Log</h5>' : 'Stats</h5>' !!}
                @if($log->status != 'Old')
                    <p>Accepting this log does not do anything inherently, aside from indicating that the log has been acknowledged as complete and processed (therefore marking it non-current). Awarding of any rewards is done via the standard submission flow.</p>
                    {!! Form::open(['url' => 'admin/challenges/edit/'.$log->id, 'id' => 'submissionForm']) !!}
                    <div class="form-group">
                        {!! Form::label('staff_comments', 'Staff Comments (Optional)') !!}
                        {!! Form::textarea('staff_comments', $log->staff_comments, ['class' => 'form-control']) !!}
                    </div>
                    <div class="float-right">
                        <a href="#" class="btn btn-success mr-2" id="acceptButton">Accept</a>
                    </div>
                    {!! Form::close() !!}
                @endif
            @endif
        @endif
    </div>
</div>

@if(!$log->isOld && Auth::user()->id == $log->user->id)
    <p>
        This is your challenge log. Your log is currently
    @if(!$log->isComplete)
        active! Complete each prompt by clicking on the corresponding tab and filling out the form. You can edit a prompt's entry as much as you like over the course of your challenge. Note that each prompt is edited separately-- so take care to submit work on one before editing another!</p>
    @else
        complete! Submit your log via the button below. It will take you to the submission form with the submission type and url pre-filled. Follow the challenge's directions regarding any other information that may need to be included in your submission form.</p>
        <div class="text-center">
            <a class="btn btn-success mb-4" href="{{ url('/submissions/new?prompt_id='.Settings::get('challenges_prompt').'&url='.$log->url) }}">Submit Log</a>
        </div>
    @endif
@else
    <p>This is the record of this log, {{ $log->isOld ? 'preserved for archival purposes' : 'which is currently in progress'}}.</p>
@endif

<div class="card">
    <div class="card-header bg-faded">
        <ul class="nav nav-tabs card-header-tabs">
        @foreach($log->challenge->data as $key=>$prompt)
            <li class="nav-item"><a class="nav-link mx-2" href="#prompt-{{ $key }}" data-toggle="tab"><i class="text-{{ isset($log->data[$key]) ? 'success far fa-circle' : 'danger fas fa-times'  }} fa-fw mr-2"></i> <strong>{{ $loop->iteration }}. {{ $prompt['name'] }}</strong></a></li>
        @endforeach
        </ul>
    </div>

    <div class="card-body tab-content">
        <div id="default" class="tab-pane active show card-block pb-0" style="height: auto; overflow: auto;">
            <p>Select a prompt via the tabs above!</p>
        </div>
    @foreach($log->challenge->data as $key=>$prompt)
        <div id="prompt-{{ $key }}" class="tab-pane card-block pb-0" style="height: auto; overflow: auto;">
            @if(!$log->isOld)
                {!! Form::open(['url' => 'challenges/edit/'.$log->id]) !!}
                @if(isset($prompt['description']))
                    <p>
                        The description for this prompt is:
                        {!! nl2br(htmlentities($prompt['description'])) !!}
                    </p>
                @endif

                <p>You must submit <strong>either</strong> a URL or text depending on the content of the prompt!</p>
                <div class="form-group">
                    {!! Form::text('prompt_url['.$key.']', isset($log->data[$key]['url']) ? $log->data[$key]['url'] : null, ['class' => 'form-control', 'placeholder' => 'URL']) !!}
                </div>
                <div class="form-group">
                    {!! Form::textarea('prompt_text['.$key.']', isset($log->data[$key]['text']) ? $log->data[$key]['text'] : null, ['class' => 'form-control']) !!}
                </div>

                {!! Form::hidden('log_id', $log->id) !!}

                <div class="text-right">
                    {!! Form::submit('Submit', ['class' => 'btn btn-primary']) !!}
                </div>
                {!! Form::close() !!}
            @else
                @if(isset($log->data[$key]))
                    @if(isset($prompt['description']))
                        <p>
                            The description for this prompt is:
                            {!! nl2br(htmlentities($prompt['description'])) !!}
                        </p>
                    @endif
                    <h5>{{ Auth::user()->id == $log->user->id ? 'You logged' : 'Entry' }}:</h5>
                    @if(isset($log->data[$key]['url']))
                        URL: <a href="{!! nl2br(htmlentities($log->data[$key]['url'])) !!}">{!! nl2br(htmlentities($log->data[$key]['url'])) !!}</a>
                    @endif
                    @if(isset($log->data[$key]['text']))
                        {!! nl2br(htmlentities($log->data[$key]['text'])) !!}
                    @endif
                @else
                    <p><i>Nothing logged!</i></p>
                @endif
            @endif
        </div>
    @endforeach
    </div>
</div>

@if(Auth::user()->hasPower('manage_submissions') && $log->status != 'Accepted')
    <div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            @if($log->isComplete)
            <div class="modal-content" id="acceptContent">
                <div class="modal-header">
                    <span class="modal-title h5 mb-0">Confirm Acceptance</span>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p>This will accept the log, marking it as "old"/non-current. Awarding points is done via the standard submission flow.</p>
                    <div class="text-right">
                        <a href="#" id="acceptSubmit" class="btn btn-success">Accept</a>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
@endif

@endsection

@section('scripts')
@parent
@if(Auth::user()->hasPower('manage_submissions'))
    <script>

        $(document).ready(function() {
            var $confirmationModal = $('#confirmationModal');
            var $submissionForm = $('#submissionForm');

            var $acceptButton = $('#acceptButton');
            var $acceptContent = $('#acceptContent');
            var $acceptSubmit = $('#acceptSubmit');

            $acceptButton.on('click', function(e) {
                e.preventDefault();
                $confirmationModal.modal('show');
            });

            $acceptSubmit.on('click', function(e) {
                e.preventDefault();
                $submissionForm.attr('action', '{{ url('admin/challenges/edit/'.$log->id) }}/accept');
                $submissionForm.submit();
            });
        });

    </script>
@endif
@endsection
