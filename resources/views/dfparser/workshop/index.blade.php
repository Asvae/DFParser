@extends('app')

@section('content')
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">Home</div>


            <div class="panel-body">
                @if($data)
                {!! $data !!}
                @else
                    Nothing be found from that entry.
                @endif
            </div>

        </div>
    </div>
@endsection