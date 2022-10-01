<?php
if($_SESSION['utenteLoggato']==true){
  $scadenza = time()+3600*24*5;
  $valCookie = $_SESSION['nickUtente'];
  setcookie("lastNickname", $valCookie, $scadenza, "", "", FALSE, TRUE);
}

?>
<!DOCTYPE html>
<html lang="it" dir="ltr">
  <head>
    <meta charset="utf-8">
    	<meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="author" content="Arcangelo Frigiola">
      <meta name="keywords" content="Pagamenti">
      <link rel="stylesheet" href="styleSheet.css">
      <link rel="shortcut icon" href="thunder.png">
    <title>ThunderPay</title>

    <script>
      function clearNickname(){
        document.getElementById("txtNickname").removeAttribute("value");
      }
    </script>

  </head>

  <body>

    <div id="contenitorePrincipale">

      <!-- inizio dell'header  -->
      <header>

        <div class="nomeSito">
          <a href="home.php">
            <h1>THUNDERPAY</h1>
            <img id="thunder" src="thunder.png" alt="logoThunder">
            <img id="thunderPrint" src="thunderPrint.png" alt="logoThunder">
          </a>
        </div>

        <!-- barra di navigazione -->
          <div class="btnNavbar">
              <?php if($_SESSION['utenteLoggato']==false){ ?>
              <ul>
                <li <?php echo $homePage; ?> ><a href="home.php">HOME</a></li>
                <li <?php echo $loginPage; ?> ><a href="login.php">LOGIN</a></li>
                <li <?php echo $pagaPage; ?>><a href="paga.php">PAGA</a></li>
                <li <?php echo $logPage; ?>><a href="log.php">LOG</a></li>
                <li style="pointer-events:none; opacity:0.6;"><a href="logout.php">LOGOUT</a></li>
              </ul>
            <?php }else{ ?>
                <ul>
                  <li <?php echo $homePage; ?> ><a href="home.php">HOME</a></li>
                  <li <?php echo $loginPage; ?> style="pointer-events:none; opacity:0.6;"><a href="login.php">LOGIN</a></li>
                  <li <?php echo $pagaPage; ?> ><a href="paga.php">PAGA</a></li>
                  <li <?php echo $logPage; ?> ><a href="log.php">LOG</a></li>
                  <li><a href="logout.php">LOGOUT</a></li>
                </ul>
              <?php } ?>
          </div>

          <div class="datiUtente">
            <ul>
              <li>Utente: <?php echo $_SESSION['nickUtente']; ?></li>
              <li>Saldo: <?php echo $_SESSION['saldoUtente']."&euro;"; ?></li>
            </ul>
          </div>

      </header>
