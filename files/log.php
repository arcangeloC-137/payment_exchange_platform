<?php
  require('session.php');
  $pagaPage = $homePage = $loginPage = "";
  $logPage = "class='paginaCorrente'";
  require("header.php")
?>


<div class="paginaLog">

  <?php
  if($_SESSION['utenteLoggato']==false){ ?>
  <div class="bloccoAvvisoAutenticazioneLog">
    <h3>ATTENZIONE! L'elenco dei pagamenti &egrave;
    disponibile solo per gli utenti autenticati.</h3>
    <h4>Per poter visualizzare questa pagina esegui prima il <a href="login.php">login</a>!</h4>
  </div>

<?php }else{ ?>

  <div class="bloccoLogUtenteLoggato">

    <div class="labelLogUtenteLoggato">
      <h3>Scegli le opzioni di ricerca e visualizza i tuoi pagamenti facendo click su CERCA.</h3>
    </div>

    <form class="bloccoCerca" action="<?php $_SERVER['PHP_SELF'] ?>" method="post">

      <select class="selettorePos" name="pszUt">

        <option class="posizioneUtente" value="tutti">Tutti</option>
        <option class="posizioneUtente" value="solo mittente">Solo mittente</option>
        <option class="posizioneUtente" value="solo destinatario">Solo destinatario</option>

      </select>

      <select class="selettorePer" name="dtaPg">

        <option class="dataPagamento" value="ultimo mese">Ultimo mese</option>
        <option class="dataPagamento" value="ultimo trimestre">Ultimo trimestre</option>

      </select>

        <input type="submit" id="btnCerca" name="btnCerca" value="CERCA">
    </form>

    <?php if($_SERVER["REQUEST_METHOD"] == "POST"){

        if(!isset($_POST['pszUt'])){
          $_POST['pszUt'] = "tutti";
        }

        if(!isset($_POST['dtaPg'])){
          $_POST['dtaPg'] = "ultimo mese";
        }

        $posizione = $_POST['pszUt'];
        $data = $_POST['dtaPg'];
        $nickU = $_SESSION['nickUtente'];

        $time = time();
        $mese = date("m", $time);
        $anno = date("Y", $time);

        if($posizione=="tutti"){
          $queryPos = "(src='$nickU' OR dst = '$nickU')";
        }elseif ($posizione=="solo mittente") {
          $queryPos = "src='$nickU'";
        }elseif ($posizione=="solo destinatario") {
          $queryPos = "dst = '$nickU'";
        }

        if($data=="ultimo mese"){
          $queryData = "YEAR(data)='$anno' AND MONTH(data)='$mese'";
        }elseif ($data=="ultimo trimestre") {
          $meseTemp = $mese-2;
          $queryData = "YEAR(data)='$anno' AND MONTH(data)>='$meseTemp'";
        }

        $query = "SELECT * FROM `log` WHERE $queryPos AND $queryData";

        include('connectionDB.php');
        if (mysqli_connect_errno()){
        echo "<div class='erroreDB'>
                <h1>Oops! C'&egrave; stato un problema nella connessione al database. Ci scusiamo per il disagio!</h1>\n
              </div>";
        }else
        {
            $stmt = mysqli_prepare($con, $query);
            $result = mysqli_stmt_execute($stmt);

            if(!mysqli_stmt_execute($stmt)){
                echo "<h3 id='erroreQuery'>Errore nell'esecuzione della richiesta! Ci scusiamo per il disagio.</h3>";
            }else
            {
                mysqli_stmt_bind_result($stmt, $id, $src, $dst, $importo, $dt);
                ?><table id='tabellaPagamentiLog'>

                      <div class="labelLogUtenteLoggatoPrint">
                        <h3>Tabella pagamenti, dettagli ricerca: <?php echo $_POST['pszUt']; ?>, <?php echo $_POST['dtaPg']; ?>.</h3>
                      </div>

                        <tr>
                          <th>Id</th>
                          <th>Mittente</th>
                          <th>Destinatario</th>
                          <th>Importo</th>
                          <th>Data</th>
                        </tr>
                <?php

                while($row = mysqli_stmt_fetch($stmt)){

                  $cash = number_format($importo/100,2,',','.');
                    echo "<tr class='righeTabella'>\n
                    <td>$id</td>\n
                    <td>$src</td>\n
                    <td>$dst</td>\n
                    <td>$cash&euro;</td>\n
                    <td>$dt</td>\n
                    </tr>\n";
                }

                mysqli_stmt_close($stmt);
            }
            ?></table><?php

            mysqli_close($con);
          }
      }?>


  </div>
  <?php
} ?>
</div>

<?php require("footer.php") ?>
