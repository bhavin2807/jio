<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $u = $_POST['email'];

    if (strpos($u, "@") !== false) {
        $user = $u;
    } else {
        $user = "+91" . $u;
    }

    $pass = $_POST['pass'];
}

$headers = array(
    'Content-Type:application/json',
    'x-api-key: l7xx938b6684ee9e4bbe8831a9a682b8e19f',
    'app-name: RJIL_JioTV'
);

$username = $user;
$password = $pass;

$payload = array(
    'identifier' => "$username",
    'password' => "$password",
    'rememberUser' => 'T',
    'upgradeAuth' => 'Y',
    'returnSessionDetails' => 'T',
    'deviceInfo' => array(
        'consumptionDeviceName' => 'samsung SM-G930F',
        'info' => array(
            'type' => 'android',
            'platform' => array(
                'name' => 'SM-G930F',
                'version' => '5.1.1'
            ),
            'androidId' => '3022048329094879'
        )
    )
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.jio.com/v3/dip/user/unpw/verify');
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_USERAGENT, 'Dalvik/2.1.0 (Linux; U; Android 5.1.1; SM-G930F Build/LMY48Z)');
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_AUTOREFERER, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_ENCODING, 'UTF-8');
curl_setopt($ch, CURLOPT_TIMEOUT, 20);
$result = curl_exec($ch);
curl_close($ch);

$j = json_decode($result, true);

$k = $j["ssoToken"];
if ($k != "") {
    // echo $k;
    file_put_contents("assets/data/creds.json", $result);
    $sign = "LOGGED IN SUCCESSFULLY !";
} else {
    $sign = "WRONG PHONE NO. OR PASSWORDS.<br> PLEASE TRY AGAIN.";
}

?>

<html>


<style>
input:hover{
border-color:orangered;}

	body{

	align-items:center;
	text-align:center;
	 background-image:url("https://img.freepik.com/free-photo/abstract-futuristic-background-with-3d-design_1361-3532.jpg?w=2000");
       
padding-top:0rem;

	}

	.screen{ border:2px solid white;
	height:auto;
	border-radius:6px;
	}

	input {width:75%;
	            height:47px;
	           border:3px solid black ;
	border-radius:6px;
	font-size:16px;
	}
	fieldset{color:white;}


		}
	img{margin-top:1rem;}



$fuschia: #ff0081;
$button-bg: $fuschia;
$button-text-color: #fff;
$baby-blue: #f8faff;

body{
  font-size: 16px;
  font-family: 'Helvetica', 'Arial', sans-serif;
  text-align: center;
  background-color: $baby-blue;
}
.bubbly-button{
  font-family: 'Helvetica', 'Arial', sans-serif;
  display: inline-block;
  font-size: 1em;
  padding: 1em 2em;
  margin-top: 100px;
  margin-bottom: 60px;
  -webkit-appearance: none;
  appearance: none;
  background-color: $button-bg;
  color: $button-text-color;
  border-radius: 4px;
  border: none;
  cursor: pointer;
  position: relative;
  transition: transform ease-in 0.1s, box-shadow ease-in 0.25s;
  box-shadow: 0 2px 25px rgba(255, 0, 130, 0.5);

  &:focus {
    outline: 0;
  }

  &:before, &:after{
    position: absolute;
    content: '';
    display: block;
    width: 140%;
    height: 100%;
    left: -20%;
    z-index: -1000;
    transition: all ease-in-out 0.5s;
    background-repeat: no-repeat;
  }

  &:before{
    display: none;
    top: -75%;
    background-image:
      radial-gradient(circle, $button-bg 20%, transparent 20%),
    radial-gradient(circle,  transparent 20%, $button-bg 20%, transparent 30%),
    radial-gradient(circle, $button-bg 20%, transparent 20%),
    radial-gradient(circle, $button-bg 20%, transparent 20%),
    radial-gradient(circle,  transparent 10%, $button-bg 15%, transparent 20%),
    radial-gradient(circle, $button-bg 20%, transparent 20%),
    radial-gradient(circle, $button-bg 20%, transparent 20%),
    radial-gradient(circle, $button-bg 20%, transparent 20%),
    radial-gradient(circle, $button-bg 20%, transparent 20%);
  background-size: 10% 10%, 20% 20%, 15% 15%, 20% 20%, 18% 18%, 10% 10%, 15% 15%, 10% 10%, 18% 18%;
  //background-position: 0% 80%, -5% 20%, 10% 40%, 20% 0%, 30% 30%, 22% 50%, 50% 50%, 65% 20%, 85% 30%;
  }

  &:after{
    display: none;
    bottom: -75%;
    background-image:
    radial-gradient(circle, $button-bg 20%, transparent 20%),
    radial-gradient(circle, $button-bg 20%, transparent 20%),
    radial-gradient(circle,  transparent 10%, $button-bg 15%, transparent 20%),
    radial-gradient(circle, $button-bg 20%, transparent 20%),
    radial-gradient(circle, $button-bg 20%, transparent 20%),
    radial-gradient(circle, $button-bg 20%, transparent 20%),
    radial-gradient(circle, $button-bg 20%, transparent 20%);
  background-size: 15% 15%, 20% 20%, 18% 18%, 20% 20%, 15% 15%, 10% 10%, 20% 20%;
  //background-position: 5% 90%, 10% 90%, 10% 90%, 15% 90%, 25% 90%, 25% 90%, 40% 90%, 55% 90%, 70% 90%;
  }

  &:active{
    transform: scale(0.9);
    background-color: darken($button-bg, 5%);
    box-shadow: 0 2px 25px rgba(255, 0, 130, 0.2);
  }

  &.animate{
    &:before{
      display: block;
      animation: topBubbles ease-in-out 0.75s forwards;
    }
    &:after{
      display: block;
      animation: bottomBubbles ease-in-out 0.75s forwards;
    }
  }
}


@keyframes topBubbles {
  0%{
    background-position: 5% 90%, 10% 90%, 10% 90%, 15% 90%, 25% 90%, 25% 90%, 40% 90%, 55% 90%, 70% 90%;
  }
    50% {
      background-position: 0% 80%, 0% 20%, 10% 40%, 20% 0%, 30% 30%, 22% 50%, 50% 50%, 65% 20%, 90% 30%;}
 100% {
    background-position: 0% 70%, 0% 10%, 10% 30%, 20% -10%, 30% 20%, 22% 40%, 50% 40%, 65% 10%, 90% 20%;
  background-size: 0% 0%, 0% 0%,  0% 0%,  0% 0%,  0% 0%,  0% 0%;
  }
}

@keyframes bottomBubbles {
  0%{
    background-position: 10% -10%, 30% 10%, 55% -10%, 70% -10%, 85% -10%, 70% -10%, 70% 0%;
  }
  50% {
    background-position: 0% 80%, 20% 80%, 45% 60%, 60% 100%, 75% 70%, 95% 60%, 105% 0%;}
 100% {
    background-position: 0% 90%, 20% 90%, 45% 70%, 60% 110%, 75% 80%, 95% 70%, 110% 10%;
  background-size: 0% 0%, 0% 0%,  0% 0%,  0% 0%,  0% 0%,  0% 0%;
  }
}
button:hover{background-color:yellow;
cursor:pointer;
}

	



	</style>
<head>

    <title>JIOTV LOGIN </title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="assets/css/signin.css" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="author" content="Techie Sneh">
    <meta name="copyright" content="This Created by Techie Sneh">
    <link rel="shortcut icon" type="image/x-icon" href="https://i.ibb.co/37fVLxB/f4027915ec9335046755d489a14472f2.png">
    <meta name="robots" content="all" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">


</head>

<body>
    <div class="container">


                <div class="social-login">
                    <br>


                </div>
                <div class="screen"> <br>
            <div class="screen__content">
                <form class="login" action="<?php $_PHP_SELF ?>" method="POST">
                    <div class="login__field">
                        <i class="login__icon fas fa-user"></i>
                        <input type="text" class="login__input" placeholder="&nbsp;&nbsp;&nbsp;Jio Number / Email" name="email"><br><br>
                    </div>
                    <div class="login__field">
                        <i class="login__icon fas fa-lock"></i>
                        <input type="password" class="login__input" placeholder="&nbsp;&nbsp;&nbsp; Password" name="pass"><br>
            <br>        </div>
                    <button class="button login__submit" type="submit" style="width:170px;
		height:47px; border-radius:6px;">
                        <span class="button__text" style="color:Black;"><b>LOGIN</b></span>
                        <i class="button__icon fas fa-chevron-right"></i>
                    </button>
                </form>
                <div class="copyright">

                    <h3 style="color:white;">TechMaxPro</h3>
                </div>
                <div class="logsucc" style="color:white;background-color:orangered;"><b><?php echo $sign; ?></b></div>
            </div>

            <div class="screen__background">
                <span class="screen__background__shape screen__background__shape3"></span>
                <span class="screen__background__shape screen__background__shape2"></span>
                <span class="screen__background__shape screen__background__shape1"></span></fieldset>
                <br>



            </div>

        </div>
            
    </div>





    <script>
var animateButton = function(e) {

  e.preventDefault;
  //reset animation
  e.target.classList.remove('animate');

  e.target.classList.add('animate');
  setTimeout(function(){
    e.target.classList.remove('animate');
  },700);
};

var bubblyButtons = document.getElementsByClassName("bubbly-button");

for (var i = 0; i < bubblyButtons.length; i++) {
  bubblyButtons[i].addEventListener('click', animateButton, false);
}
</script>
</body>

</html>
