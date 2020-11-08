
@extends('admin.includes.app')
@section('content')



<!-- Begin Page Content -->
<div class="container-fluid">
    <div class="card mb-4 shadow">


        <div class="card-header py-3 bg-abasas-dark">
            <nav class="navbar navbar-dark ">
                <a class="navbar-brand">Edit Event</a>
            </nav>
        </div>
        <div class="card-body">



            <form method="POST" id="createEventForm" action="{{ route('admin.event.update',$event->id) }}">
                @csrf
                @method('put')


                <div class="row">
                    <div class="form-group col-md-4 col-sm-12  ">
                        <label for="title"> Event Title<span style="color: red"> *</span></label>
                        <input type="text" name="title" class="form-control" id="title" placeholder="Event Title" value="{{ $event->title }}" required>
                    </div>

                    
                    <div class="form-group col-md-4 col-sm-12">
                        <label for="category_id">Event Category<span style="color: red"> *</span></label> 
                       <select class="form-control form-control" value="" name="category_id" id="catagory_id" required>
                           @foreach ($categories as $category)
                           @if ( $category->id == $event->category_id) 
                               <option selected="selected"  value="{{$category->id}}"> {{$category->name}}</option>  
                           @else
                               <option value="{{$category->id}}"> {{$category->name}}</option>
                           @endif
                           @endforeach
                       </select>
                   </div>

                    
                    <div class="form-group col-md-4 col-sm-12 ">
                        <label for="vanu"> Vanu<span style="color: red"> *</span></label>
                        <input type="text" name="vanu" class="form-control" id="vanu" placeholder="Vanu" value="{{ $event->vanu }}" required>
                    </div>

                    <div class="form-group col-md-4 col-sm-12 ">
                        <label for="date"> Date<span style="color: red"> *</span></label>
                        <input type="date" name="date" class="form-control" id="date" value="{{ $event->date }}" required>
                    </div>




                    <div class="form-group col-md-4 col-sm-12 ">
                        <label for="start_time"> Start Time<span style="color: red"> *</span></label>
                        <input type="time" name="start_time" class="form-control" id="startTime" value="{{ $event->start_time }}" required>
                    </div>

                    <div class="form-group col-md-4 col-sm-12 ">
                        <label for="end_time"> End Time<span style="color: red"> *</span></label>
                        <input type="time" name="end_time" class="form-control" id="endTime" value="{{ $event->end_time }}" required>
                    </div>

                    <div class="form-group col-md-4 col-sm-12 ">
                        <label for="description"> Description<span style="color: red"> *</span></label>
                        <textarea class="form-control" id="description" name="description" rows="4"   > {{ $event->description }}</textarea>
                    </div>







                </div>

                <button type="submit" id="createEventSubmit" class="btn bg-abasas-dark"> Submit</button>



            </form>



        </div>
    </div>


</div>


 
@endsection