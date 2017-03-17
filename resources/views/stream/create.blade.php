@extends('layouts.master')
@section('content')
	<h1>{{ $title }}</h1>
	@if (count($errors) > 0)
		<div class="alert alert-danger">
			<ul>
				@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
	@endif
	<form method="post" action="/stream/create">
		{!! csrf_field() !!}
		<div>
			<label for="name">Name</label>
			<input type="text" name="name" value="{{ old('name') }}" />
		</div>
		<div>
			<label for="description">Description</label>
			<textarea name="description">{{ old('description') }}</textarea>
		</div>
		<div>
			<input type="submit" />
		</div>
	</form>
@stop
