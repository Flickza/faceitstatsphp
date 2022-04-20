<!doctype html>
<html lang="en">
  <head>
  <?php
  function getIp() {
    $ip = $_SERVER['REMOTE_ADDR'];
 
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
 
    return $ip;
}
  ?>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" type="text/css" href="https://pixinvent.com/stack-responsive-bootstrap-4-admin-template/app-assets/css/bootstrap-extended.min.css">
    <link rel="stylesheet" type="text/css" href="https://pixinvent.com/stack-responsive-bootstrap-4-admin-template/app-assets/fonts/simple-line-icons/style.min.css">
    <link rel="stylesheet" type="text/css" href="https://pixinvent.com/stack-responsive-bootstrap-4-admin-template/app-assets/css/colors.min.css">
    <link rel="stylesheet" type="text/css" href="https://pixinvent.com/stack-responsive-bootstrap-4-admin-template/app-assets/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css?family=Montserrat&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="styles.css">
   <title>Faceit Player Statistics</title>
<?php
$playerStats = [];

if(isset($_POST['username']))
{
$username = $_POST['username'];
$authorization = "Authorization: Bearer 0c22113c-50f1-4b93-9829-7960b23580f6";
$url = "https://open.faceit.com/data/v4/players?nickname=$username";
$ch = curl_init();
//  Initiate curl
// Will return the response, if false it print the response
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// Set the url
curl_setopt($ch, CURLOPT_URL,$url);
// SET AUTH
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization ));
// Execute
$result = curl_exec($ch);

$playerArray = json_decode($result, true);

if($result)
{
    $player_id = $playerArray['player_id'];
    $nickname = $playerArray['nickname'];
    $avatar = $playerArray['avatar'];
    $country = $playerArray['country'];
    $last_infraction = $playerArray['infractions']['last_infraction_date'];
    $afk = $playerArray['infractions']['afk'];
    $leaver = $playerArray['infractions']['leaver'];
    $qm_not_checkedin = $playerArray['infractions']['qm_not_checkedin'];
    $qm_not_voted = $playerArray['infractions']['qm_not_voted'];
    $steam = $playerArray['platforms']['steam'];
    $region = $playerArray['games']['csgo']['region'];
    $skilllevel = $playerArray['games']['csgo']['skill_level'];
    $faceitelo = $playerArray['games']['csgo']['faceit_elo'];
} else {
  echo "Player was not found.";
}


$statsurl = "https://open.faceit.com/data/v4/players/$player_id/stats/csgo";
curl_setopt($ch, CURLOPT_URL,$statsurl);
$result = curl_exec($ch);
$playerStats = json_decode($result, true);
$totalMVPs = 0;
$recentResults = [];
if($playerStats)
{
    $lifetimeKD = $playerStats['lifetime']['Average K/D Ratio'];
    $lifetimeMatches = $playerStats['lifetime']['Matches'];
    $lifetimeWinrate = $playerStats['lifetime']['Win Rate %'];
    $lifetimeWinstreak = $playerStats['lifetime']['Longest Win Streak'];
    $lifetimeAvgKD = $playerStats['lifetime']['Average K/D Ratio'];
    $lifetimeAvgHS = $playerStats['lifetime']['Average Headshots %'];
    $lifetimeWins = $playerStats['lifetime']['Wins'];
    $lifetimeLosses = $lifetimeMatches - $lifetimeWins;
    for ($i=0; $i < 8; $i++) { 
        #var_dump($playerStats['segments'][$i]['stats']['MVPs']);
        $totalMVPs += $playerStats['segments'][$i]['stats']['MVPs'];
    }

} else {
  echo "Player was not found.";
}
curl_close($ch);
}

?>
  </head>

  <body>


  <div class="collapse" id="navbarToggleExternalContent">
    <div class="bg-dark p-4">
    <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
        <li class="nav-item">
            <a href="index.php" class="nav-link">Check Stats</a>
        </li>
        <li class="nav-item">
            <a href="tournaments.php" class="nav-link">Check Faceit Tournaments</a>
        </li>
    </ul>
    </div>
  </div>
  <nav class="navbar navbar-dark bg-dark">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarToggleExternalContent" aria-controls="navbarToggleExternalContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
  </nav>

  <div class="container-fluid">
<div class="row">
<div class="col-lg">
<center>
<form method="POST" action="" class="form-inline" style="margin-top: 1.5rem;">
  <div class="form-group">
    <input type="Username" id="inputUsername6" class="form-control mx-sm-3" name="username" aria-describedby="UsernameHelpInline">
    <button type="submit" class="btn btn-info">Check Stats</button>
  </div>
</form>
</center>
</div>
</div>
</div>
  <?php
  if($playerStats and $playerArray)
  {
      ?>
<div class="grey-bg container">
  <section id="minimal-statistics">
    <div class="row" style="display: inline;">
      <div class="col-12 mt-3 mb-1">
      <img src="<?php echo $avatar; ?>" class="rounded-circle" alt="<?php echo $nickname. "'s Avatar"; ?>" height="100px" width="100px">
       <h4 class="text-uppercase">Stats for player <a href="https://www.faceit.com/en/players/<?php echo $username;?>" target="_blank" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Go to faceit profile."><?php echo $username ?></a></h4>
        <p class="stats">Recent Result
        <br>
        <span class="recentLW">
        <?php     
        
        for ($i=0; $i < 5; $i++) { 
        if($playerStats['lifetime']['Recent Results'][$i] == 0)
        {
            echo "<span style='color: #c6443e;'>L </span>";
        } else {
            echo "<span style='color: #2daf7f;'>W </span>";
        }
    }
     ?>
        </span></p>
      </div>
    </div>
    <br>
      <div class="default-box">
                <h2 style="margin-bottom: 0rem;">Main Stats</h2>
                <div class="row" style="margin-left: 0px; margin-right: 0px;">
                    <div class="col-6 col-sm-6 col-md-4 col-lg-2">
                        <small class="text-muted">ELO</small>
                        <h5><?php echo $faceitelo; ?></h5>
                    </div>
                    <div class="col-6 col-sm-6 col-md-4 col-lg-2">
                        <small class="text-muted">MATCHES<span style="font-size: 9px;"> / WON / LOST</span></small>
                        <h5><?php echo $lifetimeMatches; ?><span style="font-size: 9px;"> / <?php echo $lifetimeWins; ?> / <?php echo $lifetimeLosses; ?></span></h5>
                    </div>
                    <div class="col-6 col-sm-6 col-md-4 col-lg-2">
                        <small class="text-muted">WIN RATE</small>
                        <h5><?php echo $lifetimeWinrate; ?></h5>
                    </div>
                    <div class="col-6 col-sm-6 col-md-4 col-lg-2">
                        <small class="text-muted">K / D</small>
                        <h5><?php echo $lifetimeKD; ?></h5>
                    </div>
                    <div class="col-6 col-sm-6 col-md-4 col-lg-2">
                        <small class="text-muted">HS RATE</small>
                        <h5><?php echo $lifetimeAvgHS; ?></h5>
                    </div>
                    <div class="col-6 col-sm-6 col-md-4 col-lg-2">
                        <small class="text-muted">LONGEST WIN STREAK</small>
                        <h5><?php echo $lifetimeWinstreak; ?></h5>
                    </div>
                </div>
            </div>
            <div class="default-box">
                <h2 style="margin-bottom: 0rem;">Infractions</h2>
                <div class="row" style="margin-left: 0px; margin-right: 0px;">
                    <div class="col-6 col-sm-6 col-md-4 col-lg-2">
                        <small class="text-muted">Last Infraction</small>
                        <h5><?php echo $last_infraction; ?></h5>
                    </div>
                    <div class="col-6 col-sm-6 col-md-4 col-lg-2">
                        <small class="text-muted">AFK</small>
                            <h5><?php echo $afk; ?></h5>
                        <small class="text-muted">Leaver</small>
                            <h5><?php echo $leaver; ?></h5>
                        </div>
                    <div class="col-6 col-sm-6 col-md-4 col-lg-2">
                        <small class="text-muted">Not Checked in</small>
                            <h5><?php echo $qm_not_checkedin; ?></h5>
                        <small class="text-muted">NOT VOTED</small>
                            <h5><?php echo $qm_not_voted; ?></h5>
                            </div>
                    </div>
                </div>
            </div>
  </section>
<?php
  }
?>

<center style="margin-top: 20%;">
<a href="http://www.faceit.com"><img src="https://developers.faceit.com/static/media/RGB-FACEIT-Logo-Bright.c3553633.png" alt="" width="300"></a>
</center>




    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
  </body>
</html>