<?php

include ("function.php");

$kode = array("GOFOODBOBA07","AYOCOBAGOJEK","COBAINGOJEK");
$file = file_get_contents('token.txt');
$a = array_filter(explode(PHP_EOL,$file));

function nama()
	{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "http://ninjaname.horseridersupply.com/indonesian_name.php");
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	$ex = curl_exec($ch);
	preg_match_all('~(&bull; (.*?)<br/>&bull; )~', $ex, $name);
	return $name[2][mt_rand(0, 14) ];
	}
function register($no)
	{
	$nama = nama();
	$email = str_replace(" ", "", $nama) . mt_rand(100, 999);
	$data = '{"name":"' . $nama . '","email":"' . $email . '@mail.com","phone":"+1' . $no . '","signed_up_country":"ID"}';
	$register = request("/v5/customers", "", $data);
   echo "\n";
   echo "Nama: " . $nama . "\n";
   echo "Email: " . $email . "@mail.com\n";
   echo "\n";
	if ($register['success'] == 1)
		{
		return $register['data']['otp_token'];
		}
	  else
		{
      save("error_log.txt", json_encode($register));
		return false;
		}
	}
function verif($otp, $token)
	{
	$data = '{"client_name":"gojek:cons:android","data":{"otp":"' . $otp . '","otp_token":"' . $token . '"},"client_secret":"83415d06-ec4e-11e6-a41b-6c40088ab51e"}';
	$verif = request("/v5/customers/phone/verify", "", $data);
	if ($verif['success'] == 1)
		{
   echo "\n";
   echo "Token: " . $verif['data']['access_token'] . "\n";
   echo "Saving token...\n";
   echo "\n";
   save("token.txt",$verif['data']['access_token']);
		return $verif['data']['access_token'];
		}
	  else
		{
      save("error_log.txt", json_encode($verif));
		return false;
		}
	}
	function login($no)
	{
	$data = '{"phone":"+1'.$no.'"}';
	$register = request("/v4/customers/login_with_phone", "", $data);

	if ($register['success'] == 1)
		{
		return $register['data']['login_token'];
		}
	  else
		{
      save("error_log.txt", json_encode($register));
		return false;
		}
	}
function veriflogin($otp, $token)
	{
	$data = '{"client_name":"gojek:cons:android","client_secret":"83415d06-ec4e-11e6-a41b-6c40088ab51e","data":{"otp":"'.$otp.'","otp_token":"'.$token.'"},"grant_type":"otp","scopes":"gojek:customer:transaction gojek:customer:readonly"}';
	$verif = request("/v4/customers/login/verify", "", $data);
	if ($verif['success'] == 1)
		{
   echo "\n";
   echo "Token: " . $verif['data']['access_token'] . "\n";
   echo "Saving token...\n";
   echo "\n";
   save("token.txt",$verif['data']['access_token']);
		return $verif['data']['access_token'];
		}
	  else
		{
      save("error_log.txt", json_encode($verif));
		return false;
		}
	}
function claim($token,$x)
	{
	$data = '{"promo_code":"'.$x.'"}';
	$claim = request("/go-promotions/v1/promotions/enrollments", $token, $data);
	if ($claim['success'] == 1)
		{
		return $claim['data']['message'];
		}
	  else
		{
      save("error_log.txt", json_encode($claim));
		return false;
		}
	}
echo "Choose Login or Register? \nRegister = 1 \nLogin = 2\nClaim with Token = 3 ";
echo "\n";
echo "\n";
echo "Option: ";
$type = trim(fgets(STDIN));
if($type == 1){
echo "It's Register Way\n";
echo "Input US Phone Number\n";
echo "Enter Number: ";
$nope = trim(fgets(STDIN));
$register = register($nope);
if ($register == false)
	{
	echo "Failed to Get OTP, Use Unregistered Number!\n";
	}
  else
	{
	echo "Enter Your OTP: ";
	$otp = trim(fgets(STDIN));
	$verif = verif($otp, $register);
	if ($verif == false)
		{
		echo "Failed to Registering Your Number!\n";
		}
	  else
		{
		echo "Ready to Claim... \n";
   echo "\n";
   foreach ($kode as $m => $x){
		$claim = claim($verif,$x);
		if ($claim == false)
			{
     $num = $m + 1;
     echo "[$num] " . $x . "\n";
			echo "Failed to Claim Voucher\n";
     echo "\n";
     sleep (5);
			}
		  else
			{
     $num = $m + 1;
     echo "[$num] " . $x . "\n";
			echo $claim . "\n";
     echo "\n";
     sleep (5);
			}
			}
		}
	}
}else if($type == 2){
echo "It's Login Way\n";
echo "Input US Phone Number\n";
echo "Enter Number: ";
$nope = trim(fgets(STDIN));
$login = login($nope);
if ($login == false)
	{
	echo "Failed to Get OTP!\n";
	}
  else
	{
	echo "Enter Your OTP: ";
	// echo "Enter Number: ";
	$otp = trim(fgets(STDIN));
	$verif = veriflogin($otp, $login);
	if ($verif == false)
		{
		echo "Failed to Login with Your Number!\n";
		}
	  else
		{
		echo "Ready to Claim... \n";
   echo "\n";
   foreach ($kode as $m => $x){
		$claim = claim($verif,$x);
		if ($claim == false)
			{
     $num = $m + 1;
     echo "[$num] " . $x . "\n";
			echo "Failed to Claim Voucher\n";
     echo "\n";
     sleep (5);
			}
		  else
			{
     $num = $m + 1;
     echo "[$num] " . $x . "\n";
			echo $claim . "\n";
     echo "\n";
     sleep (5);
			}
		}
	}
}
}
elseif ($type == 3){
echo "Ready to Claim... \n";
echo "\n";
foreach($a as $n => $a){
$kde = array("GOFOODBOBA07","AYOCOBAGOJEK","COBAINGOJEK");
foreach ($kde as $m => $b){
		$claim = claim($a,$b);
		if ($claim == false)
			{
     $num = $m + 1;
     echo "[$num] " . $b . "\n";
			echo "Failed to Claim Voucher\n";
     echo "\n";
     sleep (5);
			}
		  else
			{
     $num = $m + 1;
     echo "[$num] " . $b . "\n";
			echo $claim . "\n";
     echo "\n";
     sleep (5);
			}
}
}
}
?>
