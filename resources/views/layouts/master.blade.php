<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
		<title>{{ $title }}</title>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
		@yield('localStyles', '')
	</head>
	<body>
		<div id="wrapper">
			@if(Auth::check())
				<div class="user-panel">Currently logged in as {{ Auth::user()->name }}. Click <a href="/logout">HERE</a> to logout.
					Click <a href="/user/{{ Auth::user()->id }}/password">HERE</a> to reset your password.
				</div>
			@endif
			<main id="main" role="main" style="display: block;">
				<div id="mainContent" class="mainContent">
					<div id="mainText" class="mainContent">
						@if(Session::has('saved'))
							<div class="container-fluid flash-message">
								<div class="row">
									<div class="col-md-8 col-md-offset-2">
										<div class="panel panel-default">
											<div class="panel-heading">Server Message</div>
											<div class="panel-body bg-success">
												<p>{{ Session::get('saved') }}</p>
											</div>
										</div>
									</div>
								</div>
							</div>
						@endif
						@if(Session::has('error'))
							<div class="container-fluid flash-message">
								<div class="row">
									<div class="col-md-8 col-md-offset-2">
										<div class="panel panel-default">
											<div class="panel-heading">Server Message</div>
											<div class="panel-body bg-danger">
												<p>{{ Session::get('error') }}</p>
											</div>
										</div>
									</div>
								</div>
							</div>
						@endif
						@yield('content')
					</div>
				</div>
			</main>
			<!--END MAIN-->
		</div>
		<script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
		@yield('localScripts', '')
	</body>
</html>
