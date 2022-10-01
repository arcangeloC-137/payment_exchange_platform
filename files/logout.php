<?php
  require("session.php");
  if($_SESSION['utenteLoggato']==true){
  session_destroy();
  header("Location: home.php");
}else{
  require("header.php");
  ?>
  <div class="bloccoAvvisoLogoutForzato">
    <h3>ATTENZIONE! Per effettuare il Logout &egrave; necessario prima autenticarsi attraverso la sezione <a href="login.php">LOGIN</a>!</h3>
  </div>
  <?php
  require("footer.php");
}
 ?>
