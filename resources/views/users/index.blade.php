<!DOCTYPE html>
<html>
<head>
	<title>Social User</title>
	<link rel="stylesheet" type="text/css" href="{{asset('/css/app.css')}}">
</head>
<body>
	<div class="container mt-5">
		<table class="table mt-3">
			<h3>User List</h3>
			<thead>
				<tr>
					<th scope="col">#</th>
					<th scope="col">Name</th>
					<th scope="col">Email</th>
					<th scope="col">Phone</th>
				</tr>
			</thead>
			<tbody>
				@foreach($users as $index => $user)
				<tr>
					<th scope="row">{{ $index+1 }}</th>
					<td>{{$user->name}}</td>
					<td>{{$user->email}}</td>
					<td>{{$user->phone}}</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</body>
</html>