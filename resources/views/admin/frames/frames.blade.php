@extends('admin.layout')

@section('admin-title') Frames @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Frames' => 'admin/data/frames']) !!}

<h1>Frames</h1>

<p>
    This is a list of frames in the game. Frames exist in tandem with their paired item, via which they can be aquired by users and used to unlock the associated frame for characters.
</p>

<p>
    For convenience, these are the frame sizes are configured for this site:
</p>
<ul>
    <li>
        Default: {{ Config::get('lorekeeper.settings.frame_dimensions.width') }}px x {{ Config::get('lorekeeper.settings.frame_dimensions.height') }}px. {!! $defaultFrame ? 'Has' : '<strong class="text-danger">No</strong>' !!} default frame!
    </li>
    @if(isset($sizes['species']))
        @foreach($sizes['species'] as $size)
            <li>
                {!! $size['species']->displayName !!}: {{ $size['width'] }}px x {{ $size['height'] }}px. {!! $size['default_frame'] ? 'Has' : '<strong>No</strong>' !!} default frame!
                @if(isset($sizes['subtype'][$size['species']->id]))
                    <ul>
                        @foreach($sizes['subtype'][$size['species']->id] as $subtypeSize)
                            <li>
                                {!! $subtypeSize['subtype']->displayName !!}: {{ $subtypeSize['width'] }}px x {{ $subtypeSize['height'] }}px. {!! $subtypeSize['default_frame'] ? 'Has' : '<strong class="text-danger">No</strong>' !!} default frame!
                            </li>
                        @endforeach
                    </ul>
                @endif
            </li>
        @endforeach
    @endif
</ul>

<p>It's imperative that default frames exist for all configured frame sizes.</p>

<div class="text-right mb-3">
    <a class="btn btn-primary" href="{{ url('admin/data/frame-categories') }}"><i class="fas fa-folder"></i> Frame Categories</a>
    <a class="btn btn-primary" href="{{ url('admin/data/frames/create') }}"><i class="fas fa-plus"></i> Create New Frame</a>
</div>

<div>
    {!! Form::open(['method' => 'GET', 'class' => 'form-inline justify-content-end']) !!}
        <div class="form-group mr-3 mb-3">
            {!! Form::text('name', Request::get('name'), ['class' => 'form-control', 'placeholder' => 'Name']) !!}
        </div>
        <div class="form-group mb-3">
            {!! Form::submit('Search', ['class' => 'btn btn-primary']) !!}
        </div>
    {!! Form::close() !!}
</div>

@if(!count($frames))
    <p>No frames found.</p>
@else
    {!! $frames->render() !!}

        <div class="row ml-md-2 mb-4">
          <div class="d-flex row flex-wrap col-12 pb-1 px-0 ubt-bottom">
            <div class="col-3 col-md-2 font-weight-bold">Is Default</div>
            <div class="col-3 col-md-4 font-weight-bold">Name</div>
            <div class="col-3 col-md-2 font-weight-bold">Species</div>
            <div class="col-3 col-md-2 font-weight-bold">Subtype</div>
          </div>
          @foreach($frames as $frame)
          <div class="d-flex row flex-wrap col-12 mt-1 pt-2 px-0 ubt-top">
            <div class="col-3 col-md-2"> {!! $frame->is_default ? '<i class="text-success fas fa-check"></i>' : '-' !!} </div>
            <div class="col-3 col-md-4"> {{ $frame->name }} </div>
            <div class="col-3 col-md-2"> {!! $frame->species ? $frame->species->displayName : '-' !!} </div>
            <div class="col-3 col-md-2"> {!! $frame->subtype ? $frame->subtype->displayName : '-' !!} </div>
            <div class="col col-md text-right">
              <a href="{{ url('admin/data/frames/edit/'.$frame->id) }}"  class="btn btn-primary py-0 px-2">Edit</a>
            </div>
          </div>
          @endforeach
        </div>

    {!! $frames->render() !!}
@endif

@endsection

@section('scripts')
@parent
@endsection
