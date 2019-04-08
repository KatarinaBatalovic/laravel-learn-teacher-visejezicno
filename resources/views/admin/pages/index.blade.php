@extends('admin.layout.main')

@section('seo-title')
<title>{{ __('All pages') }} {{ config('app.seo-separator') }} {{ config('app.name') }}</title>
@endsection

@section('custom-css')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@endsection

@section('content')
<!-- Page Heading -->
<h1 class="h3 mb-4 text-gray-800">{{ __('Pages') }}</h1>

<div id='errors-wrapper'>
@include('admin.layout.partials.messages')
</div>
    
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <a href="{{ route('pages.index') }}">Root</a>
            @if(!is_null($page))
            {{ $page->breadcrumbs() }}
            @endif
           
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="rows" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th class="pera">#</th>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Active</th>
                        <th>Options</th>
                    </tr>
                </thead>
                <tbody id="sortable">
                    @if(count($rows) > 0)
                        @foreach($rows as $value)
                            <tr id="{{ $value->id }}">
                                <td class="pera">
                                    {{ $value->order_number }}
                                </td>
                                <td>
                                    <img src='{{ $value->getImage("s") }}'>
                                </td>
                                <td>
                                    {{ $value->title }}
                                    <a data-placement="top" title='Sub pages' href='{{ route("pages.index", ["page" => $value->id]) }}' class="btn btn-sm btn-warning tooltip-custom"><i class="fas fa-caret-square-down fa-sm fa-fw"></i> ({{ count($value->pages) }})</a>
                                </td>
                                <td class="text-center text-white">
                                    @if($value->active == 1)
                                    <a href='{{ route("pages.changestatus", ["page" => $value->id]) }}' class='btn btn-sm btn-success'>{{ __('Active')}}</a>
                                    @else
                                    <a href='{{ route("pages.changestatus", ["page" => $value->id]) }}' class='btn btn-sm btn-danger'>{{ __('Inactive')}}</a>
                                    @endif
                                </td>
                                <td class="text-center text-white">
                                    <a data-placement="top" title='Edit page' href='{{ route("pages.edit", ["page" => $value->id]) }}' class="btn btn-sm btn-primary tooltip-custom">{{ __('Edit') }}</a>
                                    <a data-placement="top" title='Preview page' href="{{ route('pages.show', ['page'=> $value->id, 'slug' => Str::slug($value->title, '-') ]) }}" class="btn btn-sm btn-success tooltip-custom"><i class="fas fa-eye fa-sm fa-fw"></i></a>
                                    <a data-placement="top" title='Delete page {{ $value->title }}' data-name='{{ $value->title }}' data-toggle="modal" data-target="#deleteModal" data-href='{{ route("pages.delete", ["page" => $value->id]) }}' class="btn btn-sm btn-danger tooltip-custom">{{ __('Delete') }}</a>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
        
        <div id='form-state' class="d-none">
            <form class="text-right" method="post" action="{{ route('pages.neworder') }}">
                @csrf
                
                @if(!is_null($page))
                <input type="hidden" name="page_id" value="{{ $page->id }}">
                @else
                <input type="hidden" name="page_id" value="0">
                @endif
                
                <input type="hidden" value="" id="input-new-order-state" name="neworder">
                <button class="btn btn-success">Change pages order</button>
            </form>
        </div>
        
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Delete page</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure that you want to delete page <span id='name-on-modal'></span>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <a id='delete-button-on-modal' type="button" class="btn btn-danger">Delete</a>
            </div>
        </div>
    </div>
</div>

@endsection

@section('custom-js')
<!-- Page level plugins -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
// Call the dataTables jQuery plugin
$(document).ready(function() {
    $('#form-state button').on('click', function(e){
        e.preventDefault();
        $.ajax({
            url: "{{route('pages.neworder')}}",
            type: 'post',
            data: {
                'page_id': $('form [name=page_id]').val(),
                'neworder': $('#input-new-order-state').val(),
                '_token' : $('form [name=_token]').val()
            },
            dataType: 'html'
        }).done(function(data){
            $('#errors-wrapper').html(data);
        }).fail(function(jqXHR, error, message){
            alert(message);
        }).always(function(){
            
        });
    });
});


$('#deleteModal').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget);
    var name = button.data('name');
    var deleteUrl = button.data('href');
    
    $("#name-on-modal").html("<b>"+name+"</b>");
    $("#delete-button-on-modal").attr('href', deleteUrl);
});

$(function () {
  $('.tooltip-custom').tooltip()
})

$( function() {
    $( "#sortable" ).sortable({
        update: function (event, ui){
            $("#input-new-order-state").val($('#sortable').sortable("toArray"));
            $("#form-state").removeClass('d-none');
        }
    });
    $( "#sortable" ).disableSelection();
} );





</script>

@endsection

