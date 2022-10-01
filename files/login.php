<?php
  require("session.php");
  $loginPage = "class='paginaCorrente'";
  $homePage = $logPage = $pagaPage = "";
  require("header.php");
  if($_SESSION['utenteLoggato']==false){
?>

<?php

  include("connectionDB.php");
    if (mysqli_connect_error()){
      echo "<div class='erroreDB'> <h1>Oops! C'&egrave; stato un problema nella connessione al database. Ci scusiamo per il disagio!</h1>\n</div>";
    }else{ ?>

    <div class="labelLogin">
      <h3>Effettua il login per accedere ai pagamenti!</h3>
      <h4>(Un cookie verr&agrave; utilizzato per salvare il nick del tuo ultimo accesso!)</h4>
    </div>

    <div class="paginaLogin">

      <div class="bloccoAutenticazione">

        <form class="autenticazione" action=<?php echo $_SERVER['PHP_SELF']; ?> method="POST" onreset="clearNickname();">

        <div class="inputLogin">

          <?php

          // fonte: w3schools.com, "form validation". Utilizzo questa funzione per pulire i dati in input dall'utente
          function test_input($data) {
            $data = trim($data); //rimuovo i caratteri non necessari, come spazi extra, tab e newline
            $data = htmlspecialchars($data);
            //tale funzione consente di convertire caratteri speciali in entità HTML, ovvero sostituisce i caratteri < e > con "&lt;" e "&gt;".
            //In questo modo impedisce di sfruttare delle injection di codice HTML o JavaScrpt
            return $data;
          }

          if($_SERVER["REQUEST_METHOD"] == "POST"){

            $nickname = test_input($_POST['txtNickname']);
            $password = test_input($_POST['txtPassword']);

            if(!isset($nickname)){
              echo "<h4>Completare tutti i dati del form!</h4>";

            }else{

              if(!isset($password)){
                echo "<h4>Completare tutti i dati del form!</h4>";
              }else{


                    $query = "SELECT nick, pwd, saldo, negozio, nome FROM usr WHERE nick=? AND pwd=?";

                    $stmt= mysqli_prepare($con, $query);
                    mysqli_stmt_bind_param($stmt, "ss", $nickname, $password);

                    if(mysqli_stmt_execute($stmt)){
                      mysqli_stmt_bind_result($stmt, $nkname, $passwd, $saldoUtente, $type, $nomeUtente);

                      while($row = mysqli_stmt_fetch($stmt)){

                        // il DB sembra essere case insensitive, dunque inserisco un ulteriore controllo
                        if(strcmp($nickname, $nkname)==0 && strcmp($password, $passwd)==0){

                          $_SESSION['saldoUtente'] = number_format(($saldoUtente/100),2,',','.');
                          $_SESSION['saldo'] = $saldoUtente;
                          $_SESSION['nickUtente'] = $nickname;
                          $_SESSION['utenteLoggato'] = true;
                          $_SESSION['tipoUtente'] = $type;
                          $_SESSION['nomeUtente'] = $nomeUtente;

                          mysqli_stmt_close($stmt);
                          echo "<script>
                                  window.location.replace('paga.php');
                                </script>";
                          exit;
                        }

                  }

                  echo "<h4 style='color: red;'>Nickname e/o password errati!</h4>";


                }else{
                  echo "<h3 id='erroreQuery'>Errore nell'esecuzione della richiesta! Ci scusiamo per il disagio.</h3>";
                }

                mysqli_close($con);

              }
            }

          }
          ?>

          <h4>Nickname</h4>
          <?php
          if(!isset($_COOKIE["lastNickname"])){
             ?>
             <input type="text" id="txtNickname" name="txtNickname" placeholder="Nickname..." value="" required maxlength="16">
             <?php
          }else{ ?>

           <input type="text" id="txtNickname" name="txtNickname" placeholder="Nickname..." value="<?php echo $_COOKIE['lastNickname']; ?>" required maxlength="16">

         <?php } ?>
          <h4>Password</h4>
          <input type="password" name="txtPassword" placeholder="Password..." value="" required maxlength="16">

        </div>

        <div class="btnAutenticazione">

          <input type="submit" name="btnOK" value="OK">
          <input type="reset" name="btnPulisci" value="PULISCI">


        </div>

      </form>

      </div>
    </div>

  <?php
    }
}else{ ?>

  <div class="bloccoAvvisoLoginForzato">
    <h3>ATTENZIONE! Il login &egrave; gi&agrave; stato effettuato. Per connettersi nuovamente effettuare prima il <a href="logout.php">LOGOUT.</a> </h3>
  </div>

  <?php
}
 require("footer.php");?>
