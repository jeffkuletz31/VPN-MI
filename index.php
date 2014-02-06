<?php
//header("HTTP/1.1 302 Found");
//header("Location: http://example.com/");

//shows a login page
function showLogin() {
	$body='<form action="?" method="POST">
	E-Mail (Must be valid):<input type="text" name="username"><br>
	Password (Stored safe):<input type="password" name="password"></form>';
	return $body;
}

function doLogin($user,$pass,$reging) {
	if($reging===false){
	}elseif{
		
	}else{
		$body='<h1>Login failed! <a href="?">Try again?</a></h1>';
	}
	return $body;
}

function showAdmin($token) {

	return $body;
}

function doAdmin($task,$token) {

	return $body;
}

function genHtml($body) {
	//header
	$html='<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>StormBit VPN User Interface</title>
<link href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css" rel="stylesheet">
<style>body { padding-top: 50px; } .mainbody { padding: 40px 15px; text-align: center; }</style>
</head>
<body>
<div class="navbar navbar-inverse navbar-fixed-top">
<div class="container">
<div class="navbar-header">
<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
<span class="icon-bar">
</span>
<span class="icon-bar">
</span>
<span class="icon-bar">
</span>
</button>
<a class="navbar-brand" href="?">StormBit VPN</a>
</div>
<div class="collapse navbar-collapse">
<ul class="nav navbar-nav">
<li><a href="http://stormbit.net/">Main Page</a></li>
<li><a href="http://goo.gl/EQixaU">Donate</a></li>
<li><a href="http://openvpn.net/index.php/open-source/downloads.html">Recommended VPN Client</a></li>
</ul>
</div>
<!--/.nav-collapse -->
</div>
</div>
<div class="container">
<div class="mainbody">
<div align="left">';

	//actual body text (forms, links, etc)
	$html=$html.$body;

	//footer
	$html=$html.'</div>
</div>
<script src="//code.jquery.org/jquery-1.10.1.min.js">
</script>
<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.3/js/bootstrap.min.js">
</script>
</body>
</html>';
	print $html;
}

//Main flow control section. Handles what the user wants, and how we give it to them.
if(!isset($_REQUEST["token"])){
		if(isset($_REQUEST["username"]) && isset($_REQUEST["password"])){
			//user has submitted the form for registering or logging in
			//sha256 this shit <_<
			$password=hash("sha256",$_REQUEST["password"]);
			$username=urlencode(filter_var($_REQUEST["username"],FILTER_SANITIZE_EMAIL));
			if($_REQUEST["action"]=="register"){
				//user attempting to register
				doLogin($username,$password,true);
			}elseif($_REQUEST["action"]=="login"){
				//user attempting to login
				doLogin($username,$password,false);
			}else{$body='<h1>You\'re not logged in! <a href="?">Wanna?</a></h1>';}
		}else{$body='<h1>Login failed! <a href="?">Try again?</a></h1>';}
//	}else{$body='<h1>You\'re not logged in! <a href="?">Wanna?</a></h1>';}
}else{
//actual other junk here
}
genHtml($body);
?>