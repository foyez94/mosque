@extends('admin.includes.app')
@section('content')





@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

@if (session()->has('success'))
<div class="alert alert-success">
    @if(is_array(session('success')))
    <ul>
        @foreach (session('success') as $message)
        <li>{{  $message }}</li>
        @endforeach
    </ul>
    @else
    {{ session('success') }}
    @endif
</div>
@endif





<div class="container-fluid m-0 p-0">


    <div class="card mb-4 shadow">

        <div class="card-header py-3 bg-abasas-dark">
            <nav class="navbar  ">

                <div class="navbar-brand"><span id="eventList"> Committee List</span> </div>

                <div>
                    <form method="get">
                        <div class="form-row align-items-center">
                            <div class="col-auto">
                                <label for="member_type">Member Type<span style="color: red"> *</span></label>
                                <select class="form-control " value="" name="member_type" id="member_type"
                                required>
                                @if($memberTypeId == 0)
                                <option selected="selected" value="0"> All</option>
                                @else 
                                    <option  value="0"> All</option>
                                @endif
                                @foreach ($member_types as $member_type)
                                
                                @if ($memberTypeId == $member_type->id )
                                    <option selected="selected" value="{{$member_type->id}}"> {{$member_type->name}}</option>
                                @else 
                                 <option value="{{$member_type->id}}"> {{$member_type->name}}</option>
                                @endif
                                @endforeach
                                
                            </select>
                            </div>


                            <div id="addSessionSelect">

                            </div>





                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary mt-3">Filter</button>
                            </div>
                        </div>
                    </form>
                </div>


            </nav>
        </div>
        <div class="card-body">


            <div class="table-responsive">



                <table class="table table-striped table-bordered" id="dataTable" width="100%" cellspacing="0">


                    <thead class="bg-abasas-dark">

                        <tr>

                            <th> #</th>
                            <th>Name</th>
                            <th>Designation</th>
                            <th>Phone</th>
                            <th>Action</th>

                        </tr>
                    </thead>
                    <tfoot class="bg-abasas-dark">
                        <tr>

                            <th> #</th>
                            <th>Name</th>
                            <th>Designation</th>
                            <th>Phone</th>
                            <th>Action</th>

                        </tr>

                    </tfoot>

                    <tbody id="sortable">

                        @php
                        $itr=1;
                        @endphp
                        @foreach ($committees as $committee)


                        <tr class="data-row" db_id="{{ $committee->id  }}" db_position="{{ $committee->position }}" data-item-index={{$itr-1}}>
                            <td class="iteration">{{$itr++}}</td>
                            <td class="word-break">{{ $committee->name }}</td>
                            <td class="word-break">{{ $committee->designation->name }}</td>
                            <td class="word-break">{{ $committee->phone }}</td>



                            <td class="align-middle">
                                <button title="Edit" type="button" class="dataEditItemClass btn btn-success btn-sm" id="data-edit-button"    > <i
                                    class="fa fa-edit" aria-hidden="false"> </i></button>


                                <form method="POST" action="{{route('admin.committee.destroy',$committee->id)}}"
                                    id="delete-form-{{ $committee->id}}" style="display:none; ">
                                    {{csrf_field() }}
                                    {{ method_field("delete") }}
                                </form>




                                <button title="Delete" class="dataDeleteItemClass btn btn-danger btn-sm" onclick="if(confirm('are you sure to delete this')){
                    document.getElementById('delete-form-{{ $committee->id }}').submit();
                }
                else{
                    event.preventDefault();
                }
                " class="btn btn-danger btn-sm btn-raised">
                                    <i class="fa fa-trash" aria-hidden="false">

                                    </i>
                                </button>




                            </td>


                        </tr>
                        @endforeach

                    </tbody>
                </table>

                <button type="button" id="committeePositionSubmit" class="btn bg-abasas-dark"> Save Position </button>
            </div>


        </div>


    </div>
</div>







<!-- Attachment Modal -->
<div class="modal fade" id="data-edit-modal" tabindex="-1" role="dialog" aria-labelledby="edit-modal-label"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-abasas-dark">
                <h5 class="modal-title " id="edit-modal-label ">
                    Edit Committee </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="attachment-body-content">
                <form id="data-edit-form" class="form-horizontal" method="POST" >
                    @csrf
                    @method('put')



                    <div class="form-group">
                        <label class="col-form-label" for="modal-update-hidden-id"> Id<span style="color: red"> *</span> </label>
                        <input type="text" name="id" class="form-control" id="editId" required readonly>
                    </div>

                    <div class="form-group">
                        <label class="col-form-label" for="EditName"> Name<span style="color: red"> *</span></label>
                        <input type="text" name="name" class="form-control" id="EditName"  required>
                    </div>

                    <div class="form-group">
                        <label class="col-form-label" for="editDesignation"> Designation<span style="color: red"> *</span> </label>
                        <select class="form-control form-control" name="designation_id" id="editDesignation"  required >

                        </select>
                    </div>


                    <div class="form-group">
                        <label class="col-form-label" for="editPhone"> Phone<span style="color: red"> *</span></label>
                        <input type="number" name="phone" class="form-control" id="editPhone" required >
                    </div>






                    <div class="form-group">
                        <input type="submit" id="submit-button" value="Submit"
                            class="form-control btn btn-success">
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>
<!-- /Attachment Modal --> 











<script>

$(document).ready(function(){

    insertMemberType();
    $('#member_type').change(function(){

        insertMemberType();

    }); 
    function insertMemberType(){
        var memberType=  $('#member_type').val() ;
        if(memberType == 3){

            var sessionhtml = '';
            sessionhtml +='<div class="col-auto"> ';
            sessionhtml +='    <label for="session_id">Active Session<span style="color: red"> *</span></label>';
            sessionhtml +='    <select class="form-control form-control" value="" name="session_id" id="session_id"';
            sessionhtml +='       required>';
            sessionhtml +='        @foreach ($sessions as $session)'; 
            sessionhtml +='                    @if ($sessionId == $session->id ) ';
            sessionhtml +='                        <option selected="selected" value="{{$session->id}}"> {{$session->name}}</option>';
            sessionhtml +='                    @else ';
            sessionhtml +='                      <option value="{{$session->id}}"> {{$session->name}}</option>';
            sessionhtml +='                     @endif';
            sessionhtml +='        @endforeach';
            sessionhtml +='    </select>';
            sessionhtml +='</div>';


            $('#addSessionSelect').html(sessionhtml);
        }
        else{
            var sessionhtml = '';
            $('#addSessionSelect').html(sessionhtml);
        }
    } 




    $("#sortable").sortable({
        update: function (event, ui) {
            $(this).children().each(function (index) {
                $(this).attr('db_position', index + 1);
            });

        }
    });




    $("#committeePositionSubmit").on('click', function () {
        var positionArray = {
            "_token": $("#csrfToken").val().trim()

        };-
        $("#sortable").children().each(function (index) {
            var id = $(this).attr('db_id').trim()
            var position = $(this).attr('db_position').trim();
            positionArray[id] = position;
        });
        saveSettings(positionArray);

    });


    function saveSettings(positionArray) {
        var url = $("#homeRoute").val().trim() + "/admin/committee_position";
        console.log(url);
        $.ajax({
            url: url,
            data: positionArray,
            type: 'post',
            success: function (data) {
                console.log(data); 
                location.reload(true);
            },
            error: function (data) {
                console.log(data); 
                alert('Error');
            }
        });
    }




    

        $(document).on('click', "#data-edit-button", function () {

            $(this).addClass(
            'edit-item-trigger-clicked'); //useful for identifying which trigger was clicked and consequently grab data from the correct row and not the wrong one.

            var options = {
                'backdrop': 'static'
            };
            $('#data-edit-modal').modal(options)

        });

        // on modal show
        $('#data-edit-modal').on('show.bs.modal', function () {
            var el = $(".edit-item-trigger-clicked");
            var row = el.closest(".data-row");
            var index = row.attr('data-item-index').trim();
            var id = row.attr('db_id').trim();
            var datas = @json($committees);
            var designations = @json($designations);
            var committee = datas[index];
           
           
            var home = "{{route('home')}}";
            var link = "admin/committee"
            var action = home.trim() + '/' + link.trim() + '/' + id;
            $("#data-edit-form").attr('action', action);

            $('#editId').val(committee["id"]);
            $('#EditName').val(committee["name"]);
            $('#editPhone').val(committee["phone"]);
            
            var html = '';

            designations.forEach(function (designation, item) {
                    //   alert(viewCategoryId+'   '+i.name);
                    if (committee.designation_id == designation.id) {
                        html += '   <option  selected="selected"  value="  ' + designation.id + ' ">  ' + designation.name + '    </option>';
                    } else {
                        html += '   <option value="  ' + designation.id + ' "> ' + designation.name + '</option>';
                    }

            });
            $("#editDesignation").html(html);



        });

        // on modal hide
        $('#data-edit-modal').on('hide.bs.modal', function () {
            $('.edit-item-trigger-clicked').removeClass('edit-item-trigger-clicked')
            $("#edit-form").trigger("reset");
        });




});




</script>




@endsection
