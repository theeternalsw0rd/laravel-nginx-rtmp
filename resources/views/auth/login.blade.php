<?php $title = "Login"; ?>
@extends('layouts.master')
@include('layouts.formcss')
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
	<form method="post" action="/login">
		{!! csrf_field() !!}
		<div>
			<label for="email">Email</label>
			<input type="email" name="email" value="{{ old('email') }}" />
		</div>
		<div>
			<label for="password">Password</label>
			<input type="password" name="password" value="" />
		</div>
		<div>
			<input type="submit" />
		</div>
	</form>
@section('content')
@stop
