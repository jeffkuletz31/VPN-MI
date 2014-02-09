<?php
//header("HTTP/1.1 302 Found");
//header("Location: http://example.com/");

//shows a login page
function showLogin() {
	$body='<h1>Login!</h1><hr>
<form action="?" method="POST">
<table>
<tr>
 <td>Paypal E-Mail:</td>
 <td><input type="text" name="username"></td>
</tr><tr>
 <td>Password:</td>
 <td><input type="password" name="password"></td>
</tr>
<tr>
 <td>New user?</td>
 <td><input type="radio" name="action" value="login" checked>Already registered <input type="radio" name="action" value="register">Register me!<td></td>
</td></tr>
<tr><td>
<input type="submit" value="Login/Register!">
</td></tr>
</table>
</form>
<h2>If you are registering, please use the email your paypal account is tied to! If we can\'t tell who sent us money and your paypal email\'s not your registration email, we can\'t give you a VPN key!
By registering for and using this service you agree to a few basic rules:
<ol><li>No illegal operations or hacking! That means any kind of hacking attempts to other sites, our sites, our users on our sites, or anything illegal in the USA, Germany, and the UK. Again, key and email permanently revoked, no exceptions.</li>
<li>No attempting to sign up for multiple VPN accounts. You can connect multiple devices under one key.
Don\'t download things over 1GB through the VPN. We pay money to host the server with a set amount of bandwidth; we don\'t need to have the server shut down because you wanted 15GB of text files. Again, permanent key revocation, no exeptions.</li></ol>
These rules are subject to change at any time, and it is your duty to know! If you get caught with your pants down and don\'t like it, too bad.';
	return $body;
}

function doLogin($user,$pass,$reging) {
	if($reging===false){
		//check user/pass
		if ($vpndata['users'][$username]['username']==$username && $vpndata['users'][$username]['password']==$password){
		$token=hash("ripemd160",$username"-"$password);
		$body='Login successful! <a href='Click here to continue.';
		}else{
		$body='Login incorrect!';
	}elseif($reging===true){
		if(!isset($vpndata['users'][$username])){
			$token=hash("ripemd160",$username"-"$password)
			//store user data into array
			$vpndata['users'][$username]['username']=$username;  //
			$vpndata['users'][$username]['password']=$password;  //sha256'd, don't worry
			$vpndata['users'][$username]['regtime']=time(); //for possible expiration of unactivated accounts?
			$vpndata['users'][$username]['status']='1';  //0 is active, 1 is ungenerated key, 2 is revoked/banned.
			$body='<h1>Registered! <a href="?action=getkey&token='.$token.'>';
			
		}else{
			$body='<h1>Registration failed! That email\'s already in use! Please use your Paypal account\'s associated address, otherwise we can\'t give you your VPN key.'
			//tell the user the email used to register is in use
		}
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
	global $token;
	global $isAdmin;
	//header
	$html='<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>StormBit VPN User Interface</title>
	<link href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css" rel="stylesheet">
	<style>body { padding-top: 50px; } .mainbody { padding: 40px 15px; text-align: center; } .nocenter { text-align: left; }</style>
</head>
<body>
	<div class="navbar navbar-inverse navbar-fixed-top">
		<div class="container">
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="#">StormBit VPN</a>
			</div>
			<div class="collapse navbar-collapse">
				<ul class="nav navbar-nav">
					<li class="dropdown navbar-right">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-cog"></span><b class="caret"></b></a>
							<ul class="dropdown-menu">
							<li><a href="http://stormbit.net/">Main Page</a></li>
							<li><a href="http://goo.gl/EQixaU">Donate</a></li>
							<li><a href="http://openvpn.net/index.php/open-source/downloads.html">Recommended VPN Client</a></li>
							<li class="divider"></li>';
//	if(isset($token)){ //commented out for testing
		$html=$html.'
							<li><a href="?action=getkey&token='.htmlspecialchars($token).'">Get Key/Config</a></li>
							<li><a href="?">Log Out</a></li>';
//}
// for some reason this whole dropdown won't move to the right side of the menubar, or even show its links. :(
		//show the admin CP?
		if ($isAdmin===true){
		$html=$html.'
							<li class="divider"></li>
							<li><a href="#">Admin CP</a></li>';
		}
	$html=$html.'
						</ul>
					</li>
				</ul>
			</div>
			<!--/.nav-collapse -->
		</div>
	</div>
	<div class="container">
		<div class="mainbody">
			<div class="nocenter">
';

	//actual body text (forms, links, etc)
	$html=$html.$body;

	//footer
	$html=$html.'
			</div>
		</div>
		<script src="//code.jquery.org/jquery-1.10.1.min.js"></script>
		<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>
	</div>
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
			$username=filter_var($_REQUEST["username"],FILTER_SANITIZE_EMAIL);
			if($_REQUEST["action"]=="register"){
				//user attempting to register
				$body=doLogin($username,$password,true);
			}elseif($_REQUEST["action"]=="login"){
				//user attempting to login
				$body=doLogin($username,$password,false);
			}else{$body='<h1>You\'re not logged in! <a href="?">Wanna?</a></h1>';}
		}else{$body=showLogin();genHtml($body);}
//	}else{$body='<h1>You\'re not logged in! <a href="?">Wanna?</a></h1>';}
}elseif(isset($_REQUEST["token"])){

//actual other junk here
}

?>