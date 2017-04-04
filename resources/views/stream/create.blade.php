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
			<input type="text" name="name" value="{{ old('name') }}" />
		</div>
		<div><em>These are generated using Facebook's Developer Graph Explorer tool and are for streaming to Facebook Live</em></div>
		<div>
			<label for="fbPageID">Facebook Page ID</label>
			<input type="text" name="fbPageID" value="{{ old('fbPageID') }}" />
		</div>
		<div>
			<label for="fbPageToken">Facebook Page Token</label>
			<input type="text" name="fbPageToken" value="{{ old('fbPageToken') }}" />
		</div>
		<div><em>These next items should be updated each session.</em></div>
		<div>
			<label for="title">Title</label>
			<input type="text" name="title" value="{{ old('title') }}" />
		</div>
		<div class="textarea">
			<label for="description">Description</label>
			<textarea name="description">{{ old('description') }}</textarea>
		</div>
		<div>
			<input type="submit" />
		</div>
	</form>
@stop
@section('localStyles')
<style>
	div.textarea {
		display:flex;
	}
	div.textarea label {
		margin-right: 10px;
	}
</style>
@stop
