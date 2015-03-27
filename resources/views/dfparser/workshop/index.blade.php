@extends('app')

@section('content')
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">Home</div>


            <div class="panel-body">
                @if($data)
                    @foreach ($data as $item)
                        <div class="item">
                            @foreach ($item as $key=>$value)
                                    @include('dfparser.workshop.partials.item', ['name' => $key, 'item' => $value])

                            @endforeach
                        </div>
                    @endforeach
                @else
                    Nothing be found from that entry.
                @endif
            </div>

        </div>
    </div>
@endsection