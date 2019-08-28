@extends('donator::mail-template')

@section('content')

Hi,
<br />
{{$detail['status']}}
<br />
{{$detail['message']}}

@endsection