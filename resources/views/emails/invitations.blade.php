<!DOCTYPE html>
<html>
<head>
    <title>Bid365 | Auction Invitation</title>
</head>
<body>
	Hello <strong>{{ $name }}</strong>,

	<p>&nbsp; </p>
	<p>I would like to invite you for participating into the auction <b>{{$title}}</b> that are conducted on {{$startdate}}. Please click on <a href="{{$url}}">JOIN NOW</a> for your participation.</p>
	<p>If you are not able to click on above link, please use try with this url {{$url}}</p>
	<p>&nbsp; </p>
	@if($password)
		<p>Your account details are as follows:</p>
		<p>Login: {{$invite_email}}</p>
		<p>Password: {{$password}}</p>
		<p>
	@endif
	<p>&nbsp; </p>
	<p>&nbsp; </p>

	<p> Thank You</p>
	<p>Best Regards</p>
	<p>Bid365 Team</p>
	<p>&nbsp; </p>
	<p>&nbsp; </p>
</body>
</html>