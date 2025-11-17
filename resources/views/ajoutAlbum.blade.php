@extends("templates.app")

@section('content')
<form method="POST" action="/albums/add">
@csrf
    <input type="text" name="titre" class="border border-1 border-blue w-100">
    <input type="submit">
</form>
@endsection
