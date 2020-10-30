@extends('home.layout')

@section('home-title') Surrender (#{{ $surrender->id }}) @endsection

@section('home-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Submission Queue' => 'admin/surrenders/pending', 'Surrender (#' . $surrender->id . ')' => $surrender->viewUrl]) !!}

@if($surrender->status == 'Pending')

    <h1>
        Surrender (#{{ $surrender->id }})
        <span class="float-right badge badge-{{ $surrender->status == 'Pending' ? 'secondary' : ($surrender->status == 'Approved' ? 'success' : 'danger') }}">{{ $surrender->status }}</span>
    </h1>

    <div class="mb-1">
        <div class="row">
            <div class="col-md-2 col-4"><h5>User</h5></div>
            <div class="col-md-10 col-8">{!! $surrender->user->displayName !!}</div>
        </div>
        <div class="row">
            <div class="col-md-2 col-4"><h5>URL</h5></div>
            <div class="col-md-10 col-8"><a href="{{ $surrender->url }}">{{ $surrender->url }}</a></div>
        </div>
        <div class="row">
            <div class="col-md-2 col-4"><h5>Submitted</h5></div>
            <div class="col-md-10 col-8">{!! format_date($surrender->created_at) !!} ({{ $surrender->created_at->diffForHumans() }})</div>
        </div>
    </div>
    <h2>Comments</h2>
    <div class="card mb-3"><div class="card-body">{!! nl2br(htmlentities($surrender->notes)) !!}</div></div>
    @if(Auth::check() && $surrender->staff_comments && ($surrender->user_id == Auth::user()->id || Auth::user()->hasPower('manage_submissions')))
        <h2>Staff Comments ({!! $surrender->staff->displayName !!})</h2>
        <div class="card mb-3"><div class="card-body">
		    @if(isset($surrender->parsed_staff_comments))
                {!! $surrender->parsed_staff_comments !!}
            @else
                {!! $surrender->staff_comments !!}
            @endif
		</div></div>
    @endif

    <h2>Character Details</h2>
    <div class="card mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-2 mb-md-0 mb-2 mr-3">
                <img src="{{ $surrender->character->image->thumbnailurl }}"> 
                </div>
                <div class="col-md-2 mb-md-0 mb-2">
                    <a href="{{ $surrender->character->url }}"><h3 class="text-uppercase">{!! $surrender->character->displayname !!}</h3></a>
                        <strong>Species</strong> - {!! $surrender->character->image->species->displayname !!}<br>
                        <strong>Subtype</strong> - {!! $surrender->character->image->subtype_id ? $surrender->character->image->subtype->displayName : 'None' !!}<br>
                        <strong>Rarity</strong> - {!! $surrender->character->image->rarity->displayname !!}<br>
                        <strong>Traits</strong> -
                        <?php $features = $surrender->character->image->features()->with('feature.category')->get(); ?>
                    @if($features->count())
                            @foreach($features as $feature)
                                <div>@if($feature->feature->feature_category_id) <strong>{!! $feature->feature->category->displayName !!}:</strong> @endif {!! $feature->feature->displayName !!} @if($feature->data) ({{ $feature->data }}) @endif</div> 
                            @endforeach
                    @else 
                            <div>No traits listed.</div>
                    @endif
                </div>
                <div class="col-md-6 mb-md-0 mb-2">
                    <h5>User Suggested worth:</h5>
                    {{ $surrender->worth }}
                    <br>
                    <br>
                    <h5>Estimated worth:</h5>
                    <div class="alert alert-warning">The estimated worth will always be the amount granted to the user. If you believe more / less is the worth, edit the grant amount area.</div>
                    {{ $estimate }}
                </div>
            </div>
        </div>
    </div>

    {!! Form::open(['url' => url()->current(), 'id' => 'surrenderForm']) !!}
        <div class="form-group">
            {!! Form::label('grant', 'Grant amount') !!} {!! add_help('This is in case an admin needs to overwrite the estimated worth, in case traits are missing etc.') !!}
            {!! Form::text('grant', $estimate, ['class' => 'form-control']) !!}
        </div>
		<div class="form-group">
            {!! Form::label('staff_comments', 'Staff Comments (Optional)') !!}
			{!! Form::textarea('staff_comments', $surrender->staffComments, ['class' => 'form-control wysiwyg']) !!}
        </div>
        <div class="text-right">
            <a href="#" class="btn btn-danger mr-2" id="rejectionButton">Reject</a>
            <a href="#" class="btn btn-success" id="approvalButton">Approve</a>
        </div>

    {!! Form::close() !!}

    <div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content hide" id="approvalContent">
                <div class="modal-header">
                    <span class="modal-title h5 mb-0">Confirm Approval</span>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p>This will approve the surrender and distribute the above currency to the user.</p>
                    <div class="text-right">
                        <a href="#" id="approvalSubmit" class="btn btn-success">Approve</a>
                    </div>
                </div>
            </div>
            <div class="modal-content hide" id="rejectionContent">
                <div class="modal-header">
                    <span class="modal-title h5 mb-0">Confirm Rejection</span>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <p>This will reject the surrender.</p>
                    <div class="text-right">
                        <a href="#" id="rejectionSubmit" class="btn btn-danger">Reject</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="alert alert-danger">This surrender has already been processed.</div>
    @include('home._submission_content', ['submission' => $surrender])
@endif

@endsection

@section('scripts')
@parent 
@if($surrender->status == 'Pending')
    <script>
        
        $(document).ready(function() {
            var $confirmationModal = $('#confirmationModal');
            var $surrenderForm = $('#surrenderForm');

            var $approvalButton = $('#approvalButton');
            var $approvalContent = $('#approvalContent');
            var $approvalSubmit = $('#approvalSubmit');

            var $rejectionButton = $('#rejectionButton');
            var $rejectionContent = $('#rejectionContent');
            var $rejectionSubmit = $('#rejectionSubmit');
            
            $approvalButton.on('click', function(e) {
                e.preventDefault();
                $approvalContent.removeClass('hide');
                $rejectionContent.addClass('hide');
                $confirmationModal.modal('show');
            });
            
            $rejectionButton.on('click', function(e) {
                e.preventDefault();
                $rejectionContent.removeClass('hide');
                $approvalContent.addClass('hide');
                $confirmationModal.modal('show');
            });

            $approvalSubmit.on('click', function(e) {
                e.preventDefault();
                $surrenderForm.attr('action', '{{ url()->current() }}/approve');
                $surrenderForm.submit();
            });

            $rejectionSubmit.on('click', function(e) {
                e.preventDefault();
                $surrenderForm.attr('action', '{{ url()->current() }}/reject');
                $surrenderForm.submit();
            });
        });

    </script>
@endif
@endsection