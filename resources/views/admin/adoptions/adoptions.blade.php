@extends('admin.layout')

@section('admin-title') Adoptions @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Adoptions' => 'admin/data/adoptions']) !!}

<h1>Adoptions</h1>

<p>Press edit to add characters to the store.</p> 
<p>Only characters owned by ''Admin'' (user where id = 1) can be added to the center.</p>

@if(!count($adoptions))
    <p>No adoption center found.</p>
@else 
    <table class="table table-sm adoption-table">
        <tbody>
            @foreach($adoptions as $adoption)
                    <td>
                        {!! $adoption->displayName !!}
                    </td>
                    <td class="text-right">
                        <a href="{{ url('admin/data/adoptions/edit/'.$adoption->id) }}" class="btn btn-primary">Edit</a>
                    </td>
            @endforeach
        </tbody>
    </table>
@endif

<a href="{{ url('admin/data/adoptions/stock/edit/'.$adoption->id) }}" class="btn btn-primary">Create Adopt Stock</a>

@endsection