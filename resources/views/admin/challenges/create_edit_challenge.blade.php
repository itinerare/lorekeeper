@extends('admin.layout')

@section('admin-title') Challenges @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Challenges' => 'admin/data/challenges', ($challenge->id ? 'Edit' : 'Create').' Challenge' => $challenge->id ? 'admin/data/challenges/edit/'.$challenge->id : 'admin/data/challenges/create']) !!}

<h1>{{ $challenge->id ? 'Edit' : 'Create' }} Challenge
    @if($challenge->id)
        <a href="#" class="btn btn-danger float-right delete-challenge-button">Delete Challenge</a>
    @endif
</h1>

{!! Form::open(['url' => $challenge->id ? 'admin/data/challenges/edit/'.$challenge->id : 'admin/data/challenges/create']) !!}

<h2>Basic Information</h2>

<div class="row">
    <div class="col-md">
        <div class="form-group">
            {!! Form::label('Name') !!} {!! add_help('This will be used to identify the challenge, including to users.') !!}
            {!! Form::text('name', $challenge->name, ['class' => 'form-control']) !!}
        </div>
    </div>
</div>

<div class="form-group">
    {!! Form::label('Description (Optional)') !!} {!! add_help('This is a description of the challenge. It may be practical to include information about challenge-specific rewards here.') !!}
    {!! Form::textarea('description', $challenge->description, ['class' => 'form-control wysiwyg']) !!}
</div>

<div class="form-group">
    {!! Form::label('Rules (Optional)') !!} {!! add_help('Any additional rules for this challenge.') !!}
    {!! Form::textarea('rules', $challenge->rules, ['class' => 'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::checkbox('is_active', 1, $challenge->id ? $challenge->is_active : 1, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
    {!! Form::label('is_active', 'Is Active', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If this is turned off, users will not be able to view this challenge or register for it.') !!}
</div>

<h3>Prompts</h3>

Each row here represents a prompt that the user must complete to submit their challenge log. Users must either provide a URL or text for a prompt to be considered "complete". Note that descriptions for individual prompts should be kept fairly concise, and do not support HTML! Also note that <strong>removing a prompt will remove it from all user challenge logs</strong>, even if you recreate it.

<div id="promptList" class="my-2">
    @if(isset($challenge->data))
        @foreach($challenge->data as $key=>$prompt)
            <div class="my-2">
                {!! Form::hidden('prompt_key[]', $key) !!}
                <div class="input-group mb-2">
                    {!! Form::text('prompt_name[]', $prompt['name'], ['class' => 'form-control', 'placeholder' => 'A short name', 'aria-label' => 'Prompt name', 'aria-describedby' => 'prompt-name-group']) !!}
                    <div class="input-group-append">
                      <button class="btn btn-outline-danger remove-prompt" type="button" id="prompt-name-group">Remove Prompt</button>
                    </div>
                </div>
                {!! Form::textarea('prompt_description[]', $prompt['description'], ['class' => 'form-control mr-2', 'placeholder' => 'A succinct description or instructions (Optional)']) !!}
                <hr/>
            </div>
        @endforeach
    @endif
</div>
<div><a href="#" class="btn btn-primary" id="add-prompt">Add Prompt</a></div>

<div class="text-right">
    {!! Form::submit($challenge->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
</div>

{!! Form::close() !!}

<div class="prompt-row hide my-2">
    {!! Form::hidden('prompt_key[]', null) !!}
    <div class="input-group mb-2">
        {!! Form::text('prompt_name[]', null, ['class' => 'form-control', 'placeholder' => 'A short name', 'aria-label' => 'Prompt name', 'aria-describedby' => 'prompt-name-group']) !!}
        <div class="input-group-append">
          <button class="btn btn-outline-danger remove-prompt" type="button" id="prompt-name-group">Remove Prompt</button>
        </div>
    </div>
    {!! Form::textarea('prompt_description[]', null, ['class' => 'form-control mr-2', 'placeholder' => 'A succinct description or instructions (Optional)']) !!}
    <hr/>
</div>

@endsection

@section('scripts')
@parent
<script>
$( document ).ready(function() {
    $('.delete-challenge-button').on('click', function(e) {
        e.preventDefault();
        loadModal("{{ url('admin/data/challenges/delete') }}/{{ $challenge->id }}", 'Delete Challenge');
    });

    $('#add-prompt').on('click', function(e) {
        e.preventDefault();
        addPromptRow();
    });
    $('.remove-prompt').on('click', function(e) {
        e.preventDefault();
        removePromptRow($(this));
    })
    function addPromptRow() {
        var $clone = $('.prompt-row').clone();
        $('#promptList').append($clone);
        $clone.removeClass('hide prompt-row');
        $clone.find('.remove-prompt').on('click', function(e) {
            e.preventDefault();
            removePromptRow($(this));
        })
        $clone.find('.prompt-select').selectize();
    }
    function removePromptRow($trigger) {
        $trigger.parent().parent().parent().remove();
    }
});

</script>
@endsection
