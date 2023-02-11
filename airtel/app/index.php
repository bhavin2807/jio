<?php

session_start();

if(!function_exists('getutkn'))
{
    include('cmn.configs.php');
}

$success = "";
$error = "";

if(isset($_GET['reset']))
{
    $_SESSION['ams_mobile'] = "";
}

if(isset($_POST['airtel_one']))
{
    //Validate Mobile Number
    if(!preg_match('/^[0-9]{10}$/', $_POST['airtel_one']))
    {
        $error = "Error: Please Enter Valid Mobile Number !";
    }
    else
    {
        //Get Preflight Token
        $preflightapi = 'https://api.airtel.tv/v4/user/login?appId=MOBILITY';
        $prehead1[] = 'x-atv-did: '.$AIRDVID;
        $prehead1[] = 'content-type: application/json';
        $prepost = '{"requiresOtp": "false"}';
        $process = curl_init($preflightapi);
        curl_setopt($process, CURLOPT_POST, 1);
        curl_setopt($process, CURLOPT_POSTFIELDS, $prepost);
        curl_setopt($process, CURLOPT_HTTPHEADER, $prehead1);
        curl_setopt($process, CURLOPT_HEADER, 0);
        curl_setopt($process, CURLOPT_ENCODING, '');
        curl_setopt($process, CURLOPT_TIMEOUT, 4);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
        $return1 = curl_exec($process);
        curl_close($process);
            $data1 = json_decode($return1, true);
            $freeuid = $data1['uid'];
            $freetoken = $data1['token'];

        //Generate OTP
        $gen_airapi = 'https://api.airtel.tv/v2/user/profile/generateOtp?appId=MOBILITY';
        $genartlpost = '{"msisdn":"'.trim($_POST['airtel_one']).'","msgTxt":"Use {OTP} as your login OTP. OTP is confidential"}';
            $genartlhd[] = 'Accept: application/json, text/plain, */*';
            $genartlhd[] = 'Content-Type: application/json';
            $genartlhd[] = 'Referer: https://www.airtelxstream.in/';
            $genartlhd[] = 'x-atv-did: '.$AIRDVID;
            $genartlhd[] = 'x-atv-utkn: '.getutkn('POST/v2/user/profile/generateOtp?appId=MOBILITY'.$genartlpost, $freetoken, $freeuid);
            $genartlhd[] = 'Origin: https://www.airtelxstream.in';
        $process = curl_init($gen_airapi);
        curl_setopt($process, CURLOPT_POST, 1);
        curl_setopt($process, CURLOPT_POSTFIELDS, $genartlpost);
        curl_setopt($process, CURLOPT_HTTPHEADER, $genartlhd);
        curl_setopt($process, CURLOPT_HEADER, 0);
        curl_setopt($process, CURLOPT_ENCODING, '');
        curl_setopt($process, CURLOPT_TIMEOUT, 8);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
        $return1 = curl_exec($process);
        curl_close($process);
        $kiner = @json_decode($return1, true);
        if($kiner['success'] == true)
        {
            $success = "OTP Sent !";
            $_SESSION['ams_mobile'] = trim($_POST['airtel_one']);
        }
        else
        {
            if(isset($kiner['message']) && !empty($kiner['message']))
            {
                $error = $kiner['message'];
            }
            else
            {
                exit("System Error : ".$return1);
            }
        }

    }
}

//--------------------------------------------------------------------------//

if(isset($_POST['airtel_otp']))
{
    if (!preg_match('/^[0-9]+$/', $_POST['airtel_otp']))
    {
        $error = "Error: Please Enter OTP !";
    }
    else
    {
        //Get Preflight Token
        $preflightapi = 'https://api.airtel.tv/v4/user/login?appId=MOBILITY';
        $prehead1[] = 'x-atv-did: '.$AIRDVID;
        $prehead1[] = 'content-type: application/json';
        $prepost = '{"requiresOtp": "false"}';
        $process = curl_init($preflightapi);
        curl_setopt($process, CURLOPT_POST, 1);
        curl_setopt($process, CURLOPT_POSTFIELDS, $prepost);
        curl_setopt($process, CURLOPT_HTTPHEADER, $prehead1);
        curl_setopt($process, CURLOPT_HEADER, 0);
        curl_setopt($process, CURLOPT_ENCODING, '');
        curl_setopt($process, CURLOPT_TIMEOUT, 4);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
        $return1 = curl_exec($process);
        curl_close($process);
            $data1 = json_decode($return1, true);
            $freeuid = $data1['uid'];
            $freetoken = $data1['token'];

        //Apply OTP
        $dologinapi = 'https://api.airtel.tv/v4/user/login?appId=MOBILITY';
        $dologinpost = '{"msisdn":"'.$_SESSION['ams_mobile'].'","otp":"'.trim($_POST['airtel_otp']).'"}';
        $dologinhead[] = 'x-atv-did: '.$AIRDVID;
        $dologinhead[] = 'content-type: application/json';
        $dologinhead[] = 'x-atv-utkn: '.getutkn('POST/v4/user/login?appId=MOBILITY'.$dologinpost, $freetoken, $freeuid);
        $process = curl_init($dologinapi);
        curl_setopt($process, CURLOPT_POST, 1);
        curl_setopt($process, CURLOPT_POSTFIELDS, $dologinpost);
        curl_setopt($process, CURLOPT_HTTPHEADER, $dologinhead);
        curl_setopt($process, CURLOPT_HEADER, 0);
        curl_setopt($process, CURLOPT_ENCODING, '');
        curl_setopt($process, CURLOPT_TIMEOUT, 10);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
        $return2 = curl_exec($process);
        curl_close($process);
        $data2 = json_decode($return2, true);
        if($data2['success'] == true)
        {
            $uid = $data2['uid'];
            $token = $data2['token'];
            $accesstoken = $data2['authToken'];
            $_SESSION['ams_mobile'] = "";

            $fcdl = @json_encode(array('uid' => $uid,
                                       'token' => $token,
                                       'access_token' => $accesstoken));
            $savime = hideit('encrypt', $fcdl);
            if(file_put_contents('_isidata', $savime))
            {
                $success = "Credentials Generated & Saved Successfully !";
            }
            else
            {
                $error = "Error : Logged In Successfully. But Failed To Save Login Data.";
            }
        }

    }
}

?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <title>Airtel TV Login</title>
    <link rel="shortcut icon" href="https://www.airtelxstream.in/static/fevicon.png"/>
    <style>
        body
        {
            background-color: #000000;
            color: #FFFFFF;
        }
        .card
        {
            background-color: #000000;
            color: #FFFFFF;
        }
        #mahama
        {
            display: none;
        }
    </style>
  </head>
  <body>

  <div class="card">
  <div class="card-header">
    <img src="https://www.airtelxstream.in/static/fevicon.png"/> <b>Airtel TV Login</b>
  </div>
  <div class="card-body">

<?php
if(isset($error))
{
    if(!empty($error))
    {
?>
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
  <strong><?php echo $error; ?></strong>
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
<?php
}}
?>

<?php
if(isset($success))
{
    if(!empty($success))
    {
?>
  <div class="alert alert-success alert-dismissible fade show" role="alert">
  <strong><?php echo $success; ?></strong>
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
<?php
}
}
?>

<?php
if(empty($_SESSION['ams_mobile']))
{
?>
    <form method="post" action="">
        <div class="form-group">
            <input type="number" value="<?php if(isset($_POST['airtel_one'])){ echo trim(strip_tags($_POST['airtel_one'])); } ?>" name="airtel_one" maxlength="10" placeholder="Enter Any Indian Mobile Number" required="" class="form-control" autocomplete="off"/>
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-info" value="Send OTP"/>
        </div>
    </form>

<?php
}
else
{
?>
    <form method="post" action="">
        <div class="form-group">
            <input type="number" value="<?php if(isset($_POST['airtel_otp'])){ echo trim(strip_tags($_POST['airtel_otp'])); } ?>" name="airtel_otp" placeholder="Enter OTP Sent To Mobile Number" required="" class="form-control" autocomplete="off"/>
        </div>
        <div class="form-group">
            <input type="submit" class="btn btn-dark" value="Apply OTP"/>&nbsp;
            <input type="button" class="btn btn-danger" value="Reset" onclick="resetform()"/>
        </div>
    </form>
<?php
}
?>

<?php
if(isset($AIRAUTH['access_token']) && !empty($AIRAUTH['access_token']))
{
?>
<div class="card" style="border: 1px solid #C0C0C0;">
<div class="card-body">
<h5 class="card-title">Generate M3U Playlist (Make Sure You Are Connected To Internet)</h5>
<div class="mt-3">
    <input type="text" readonly="" class="form-control" id="nlbltl" placeholder="Click Build Playlist Button Below" autocomplete="off"/>
</div>
<div class="mt-3" id="wamaha">
    <button class="btn btn-warning btn-sm" style="font-weight: bold;" id="nxsaso">Build Playlist</button>
</div>
<div class="spinner-border text-light mt-3 " role="status" id="mahama"></div>
<div class="mt-3">
    <button class="btn btn-success" onclick="refreshchannellist()"> Refresh Channel List </button>
</div>
</div>
</div>
<?php
}
?>  
</div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

<script>
function resetform()
{
    window.location = "?reset";
}
function refreshchannellist()
{
    $.ajax({
        "url": "channels.php",
        "type": "GET",
        "data": "force_refresh=yes",
        "success":function(data)
        {
            try { data = JSON.parse(data); }catch(err){ } 
            if(data.status == "success" && data.msg.indexOf("CACHED") == -1)
            {
                alert("Channel List Refreshed Successfully");
            }
            else
            {
                alert("Failed To Refresh Channel List");
            }
        },
        "error":function(data)
        {
            alert("Failed To Refresh Channel List");
        }
    });
}

$("#nxsaso").on("click", function(){
    $("#nxsaso").fadeOut();
    $("#mahama").fadeIn();
    let action = "";
    let playlist_url = $("#nlbltl").val();
    $.ajax({
        "url":"genPlaylist.php",
        "data":"action=build",
        "type":"GET",
        "beforeSend":function(xhr)
        {

        },
        "success":function(data)
        {
            try { data = JSON.parse(data); }catch(err){ }
            if(data.status == "success")
            {
                $.ajax({
                    "url":"genPlaylist.php",
                    "data":"action=render",
                    "type":"GET",
                    "beforeSend":function(xhr)
                    {

                    },
                    "success":function(data)
                    {
                        try { data = JSON.parse(data); }catch(err){ }
                        if(data.status == "success")
                        {   
                            $("#nlbltl").val(data.data.playlist_link);
                            $("#mahama").fadeOut();
                        }
                        else
                        {
                            $("#mahama").hide();
                            alert("Error : " + data.msg);
                            $("#nxsaso").fadeIn();
                        }
                    },
                    "error":function(xhr)
                    {
                        $("#mahama").hide();
                        alert("Failed To Connect With Server");
                        $("#nxsaso").fadeIn();
                    }
                });
            }
            else
            {
                $("#mahama").hide();
                alert("Error : " + data.msg);
                $("#nxsaso").fadeIn();
            }
        },
        "error":function(xhr)
        {
            $("#mahama").hide();
            alert("Failed To Connect With Server");
            $("#nxsaso").fadeIn();
        }
    });
});
</script>

  </body>
</html>
