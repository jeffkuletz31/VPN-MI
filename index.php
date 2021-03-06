<?php
/**
 * stormbitvpn/index.php
 *
 * @package default
 */


//load user data if it exists, otherwise make it exist, and check if CRL is set up.
if (!file_exists("/etc/openvpn/vpndata.json")) {file_put_contents("/etc/openvpn/vpndata.json", "{}");}
if (!file_exists("/etc/openvpn/easy-rsa/2.0")) {exec("/etc/openvpn/easy-rsa/2.0/usermgmt.sh revoked-user revoked@user");exec("/etc/openvpn/easy-rsa/2.0/usermgmt.sh revoked-user --delete");}
// Load our stuff
$vpndata=json_decode(file_get_contents("/etc/openvpn/vpndata.json"), true);


/**
 * shows a login page
 *
 * @return unknown
 */
function showLogin() {
	$body='<h1>Login!</h1><hr>
<form action="?" type="multipart/form-data" method="POST">
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
 <td><input type="radio" name="action" value="login" checked>Already registered <input type="radio" name="action" value="register">Register me!<td>
</td></tr>
<tr><td>
<input type="submit" class="btn btn-default" value="Login/Register!">
</td></tr>
</table>
</form>
If you are registering, <b>please use the email your PayPal account is tied to!</b> If we can\'t tell who sent us money and your PayPal email\'s not your registration email, we can\'t give you a VPN key!<br><br>
By registering for and using this service you agree to a few basic rules:
<ol><li>No illegal operations or hacking! That means any kind of hacking attempts to other sites, our sites, our users on our sites, or <b>anything illegal in the USA, Germany, and the UK.</b> Again, key and email permanently revoked, no exceptions.</li>
<li>No attempting to sign up for multiple VPN accounts. You can connect multiple devices under one key.</li>
<li>Don\'t download things over 1GB through the VPN. We pay money to host the server with a set amount of bandwidth; we don\'t need to have the server shut down because you wanted 15GB of text files. Again, permanent key revocation, no exeptions.</li></ol>
These rules are subject to change at any time, and it is your duty to know! If you get your key revoked and you don\'t like it, that\'s too bad!';
	return $body;
}


/**
 *
 * @param unknown $username
 * @param unknown $password
 * @param unknown $reging
 * @return unknown
 */
function doLogin($username, $password, $reging) {
	global $vpndata;
	$token=hash("ripemd160", $username."-".$password);
	if ($reging===false) {
		//check user/pass
		if (isset($vpndata['users'][$token])) {
			if ($vpndata['users'][$token]['username']==$username && $vpndata['users'][$token]['password']==$password) {
				$token=hash("ripemd160", $username."-".$password);
				header("HTTP/1.1 302 Found");
				if ($vpndata['users'][$token]['authority'] == 0) {
					header("Location: ?token=".$token."&action=getkey");
					$body='<h1>Registered! <a href="?action=getkey&amp;token='.$token.'>Get your key!</a></h1>';
				}else {
					header("Location: ?token=".$token."&action=showadmin");
					$body='<h1>Welcome, Administrator '.$username.'! <a href="?action=showadmin&amp;token='.$token.'>Admin Control Panel</a></h1>';
				}
			}else {
				$body='<h1>Login incorrect!</h1>';
			}
		}else {$body='<h1>Login incorrect!</h1>';}
	}elseif ($reging===true) {
		if (!isset($vpndata['users'][$token])) {
			$token=hash("ripemd160", $username."-".$password);
			//store user data into array
			$vpndata['users'][$token]['username']=$username;  //WHOA GEEZ NO WAY
			$vpndata['users'][$token]['password']=$password;  //sha256'd, don't worry
			$vpndata['users'][$token]['regtime']=time(); //for possible expiration of unactivated accounts? idk
			$vpndata['users'][$token]['status']='1';  //0 is active, 1 is ungenerated key, 2 is revoked/banned.
			$vpndata['users'][$token]['token']=$token;
			$vpndata['users'][$token]['authority']='0';  //0 is user, 1 is admin
			$body='<h1>You\'ve been registered! You need to send a payment to us in order for us to activate your account and generate your keys.</h1><br>Questions? <a href="irc://irc.stormbit.net/neo">See us on IRC!</a>';
		}else {
			$body='<h1>Registration failed! That email\'s already in use! Please use your Paypal account\'s associated address, otherwise we can\'t give you your VPN key.</h1>';
			//tell the user the email used to register is in use
		}
	}else {
		$body='<h1>Login failed! <a href="?">Try again?</a></h1>';
	}
	return $body;
}


/**
 *
 * @param unknown $task
 * @param unknown $token
 * @return unknown
 */
function doAdmin($task, $token) {
	global $vpndata;

	return $body;
}


/**
 *
 * @param unknown $body
 */
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
<link href="/resources/bootstrap.css" rel="stylesheet">
<style>body{padding-top: 50px;background-color: transparent;} .mainbody{padding: 40px 15px;background-color: transparent;}</style>
<link href="/style.css" rel="stylesheet">
</head>
<body>
	<div class="navbar navbar-default navbar-fixed-top">
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
				<ul class="nav navbar-nav navbar-right">
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="glyphicon glyphicon-cog"></span><b class="caret"></b></a>
							<ul class="dropdown-menu">
							<li><a href="http://stormbit.net/">Main Page</a></li>
							<li><a href="http://goo.gl/EQixaU">Donate</a></li>
							<li><a href="http://openvpn.net/index.php/open-source/downloads.html">Recommended VPN Client</a></li>
							<li class="divider"></li>
							<li><a href="?action=getkey&amp;token='.htmlspecialchars($token).'">Get Key/Config</a></li>
							<li><a href="?">Log Out</a></li>
							<li class="divider"></li>
							<li><a href="?token='.htmlspecialchars($token).'&amp;action=showadmin">Admin CP</a></li>
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
		<script src="/resources/jquery.js"></script>
		<script src="/resources/bootstrap.js"></script>
	</div>
</body>
</html>';
	print $html;
}


/**
 * Source: http://darklaunch.com/2009/05/06/php-normalize-newlines-line-endings-crlf-cr-lf-unix-windows-mac
 *
 * @param unknown $s
 * @return unknown
 */
function normalize($s) {
	// Normalize line endings
	// Convert all line-endings to Windows format
	$s = str_replace("\r\n", "\n", $s);
	$s = str_replace("\r", "\n", $s);
	// Don't allow out-of-control blank lines
	$s = preg_replace("/\n{2,}/", "\n\n", $s);
	//Windows-ify
	$s = str_replace("\n", "\r\n", $s);
	return $s;
}


//Main flow control section. Handles what the user wants, and how we give it to them.
if (!isset($_REQUEST["token"])) {
	if (isset($_REQUEST["username"]) && isset($_REQUEST["password"])) {
		//user has submitted the form for registering or logging in
		//sha256 this shit <_<
		$password=hash("sha256", $_REQUEST["password"]);
		$username=filter_var($_REQUEST["username"], FILTER_SANITIZE_EMAIL);
		$token=hash("ripemd160", $username."-".$password);
		if ($_REQUEST["action"]=="register") {
			//user attempting to register
			$body=doLogin($username, $password, true);
		}elseif ($_REQUEST["action"]=="login") {
			//user attempting to login
			$body=doLogin($username, $password, false);
		}else {$body='<h1>You\'re not logged in! <a href="?">Wanna?</a></h1>';}
	}else {$body=showLogin();}
	// }else{$body='<h1>You\'re not logged in! <a href="?">Wanna?</a></h1>';}
}elseif (isset($_REQUEST["token"])) {
	// Error checking and security junk
	if ($_REQUEST["token"]==null) {$body="<h1>Token invalid!</h1>";genHtml($body);exit();}
	$token=filter_var($_REQUEST['token'], FILTER_SANITIZE_STRING, FILTER_SANITIZE_SPECIAL_CHARS);
	if (isset($_REQUEST["action"])&& $_REQUEST["action"] == null ) {$body="<h1>No action specified!</h1>";}else {
		if ($_REQUEST["action"]=="getkey") {
			//user requesting a key. Check their access privileges.
			if ($vpndata['users'][$_REQUEST["token"]]['status']=='1') {
				$body='<h1>Key is not active yet!</h1>';
			}elseif ($vpndata['users'][$_REQUEST["token"]]['status']=='2') {
				$body='<h1 color="red">Account Banned!</h1>';
			}else {
				//User is go for key, check if key actually is there.
				$usercrt="/etc/openvpn/easy-rsa/2.0/keys/".$token.".crt";
				$userkey="/etc/openvpn/easy-rsa/2.0/keys/".$token.".key";
				$cacrt="/etc/openvpn/easy-rsa/2.0/keys/ca.crt";
				$cfgtmpl="/etc/openvpn/easy-rsa/2.0/template.ovpn";
				if (!file_exists($usercrt)) {$body="<h1>User Cert not found!</h1>";genHtml($body);exit();}
				if (!file_exists($userkey)) {$body="<h1>User Key not found!</h1>";genHtml($body);exit();}
				if (!file_exists($cacrt)) {$body="<h1>CA File Not Found!</h1>";genHtml($body);exit();}
				if (!file_exists($cfgtmpl)) {$body="<h1>Stock Config Not Found!</h1>";genHtml($body);exit();}
				header('Content-Type: application/octet-stream');
				header("Content-Transfer-Encoding: Binary");
				header("Content-disposition: attachment; filename=\"" . $token . ".ovpn\"");
				$ovpn=file_get_contents($cfgtmpl);
				$ovpn=$ovpn.'
<ca>
';
				$ovpn=$ovpn.file_get_contents($cacrt);
				$ovpn=$ovpn.'
</ca>
<key>
';
				$ovpn=$ovpn.file_get_contents($userkey);
				$ovpn=$ovpn.'
</key>
<cert>
';
				$ovpn=$ovpn.file_get_contents($usercrt);
				$ovpn=$ovpn.'
</cert>
';
				print normalize($ovpn);
				exit();

			}
		}elseif ($_REQUEST["action"]=="showadmin") {
			if ($vpndata['users'][$token]['authority'] != 1) {$body="<h1>You're not an admin!</h1>";genHtml($body);exit();}
			$body='<form action="?token='.$token.'&amp;action=doadmin" method="POST">';
			$body=$body.'<h2>Inactive Users</h2><br><table class="table table=striped">';
			foreach ($vpndata['users'] as &$listuser) {
				if ($listuser['status']==1) {
					//print Unchanged, Activate or Deny options
					$body=$body.'<tr>
					<td>'.$listuser["username"].'</td>
					<td><input type="radio" name="'.$listuser['token'].'-status" value="'.$listuser['token'].'-one" checked>Unchanged</td>
					<td><input type="radio" name="'.$listuser['token'].'-status" value="'.$listuser['token'].'-zero">Approve</td>
					<td><input type="radio" name="'.$listuser['token'].'-status" value="'.$listuser['token'].'-two">Deny</td></tr>';
				}
			}
			$body=$body.'</table><br><h2>Active Users</h2><br><table class="table table=striped">';
			foreach ($vpndata['users'] as &$listuser) {
				if ($listuser['status']==0) {
					//print Active or Banned options
					$body=$body.'<tr>
					<td>'.$listuser["username"].'</td>
					<td><input type="radio" name="'.$listuser['token'].'-status" value="'.$listuser['token'].'-zero" checked>Active</td>
					<td><input type="radio" name="'.$listuser['token'].'-status" value="'.$listuser['token'].'-two">Banned</td></tr>';
				}
			}
			$body=$body.'</table><br><button type="submit" class="btn btn-default">Submit</button>';
		}elseif ($_REQUEST["action"]=="doadmin") {
			if ($vpndata['users'][$token]['authority'] != 1) {$body="<h1>You're not an admin!</h1>";genHtml($body);exit();}
			foreach ($_REQUEST as $requestvar) {
				$requestvare=explode('-', $requestvar);
				if (isset($vpndata['users'][$requestvare[0]]["status"])) {
					if ($requestvare[1]=='zero') {
						$vpndata['users'][$requestvare[0]]["status"]=0;
						exec("/etc/openvpn/easy-rsa/2.0/usermgmt.sh ".$requestvare[0]." \"".$vpndata['users'][$requestvare[0]]['username']."\"");
					}elseif ($requestvare[1]=='one') {
						$vpndata['users'][$requestvare[0]]["status"]=1;
					}elseif ($requestvare[1]=='two') {
						$vpndata['users'][$requestvare[0]]["status"]=2;
						exec("/etc/openvpn/easy-rsa/2.0/usermgmt.sh ".$requestvare[0]." --delete");
					}
				}
			}
			header("HTTP/1.1 302 Found");
			header("Location: ?token=".$token."&action=showadmin");
		}else {$body='<h1>Error!</h1>';}
	}
}
//if(isset($doGen)){if($doGen!==false){genHtml($body);}}else{
genHtml($body);
//}
file_put_contents("/etc/openvpn/vpndata.json", json_encode($vpndata));

?>
