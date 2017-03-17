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
	<form method="post">
		{!! csrf_field() !!}
		<div><em>The Name field should be fairly static. You can think of it as the name of a channel.</em></div>
		<div>
			<label for="name">Name</label>
			<input type="text" name="name" value="{{ old('name', $name) }}" />
		</div>
		<div><em>These next items should be updated each session.</em></div>
		<div>
			<label for="title">Title</label>
			<input type="text" name="title" value="{{ old('title', $streamTitle) }}" />
		</div>
		<div>
			<label for="byline">Byline</label>
			<input type="text" name="byline" value="{{ old('byline', $byline) }}" />
		</div>
		<div>
			<input type="submit" />
		</div>
	</form>
@stop

