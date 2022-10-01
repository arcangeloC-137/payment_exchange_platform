<?php
  require('session.php');
  $homePage = $loginPage = $logPage = "";
  $pagaPage = "class='paginaCorrente'";
  require("header.php");
?>

<div class="paginaPaga">

  <?php include("connectionDB.php");

  if (mysqli_connect_errno()){
      echo "<div class='erroreDB'>
        <h1>Oops! C'&egrave; stato un problema nella connessione al database. Ci scusiamo per il disagio! dbError: 1</h1>\n
      </div>";
    }
  else{

    if($_SESSION['utenteLoggato']==false){
                $queryNegozi = "SELECT count(negozio) AS negozi
                          FROM usr
                          WHERE negozio = 1";

                $queryUtenti = "SELECT count(DISTINCT nick) AS utenti
                                FROM usr
                                WHERE negozio = 0";

                $numNegozi = mysqli_query($con, $queryNegozi);
                $numUtenti = mysqli_query($con, $queryUtenti);

                if(!$numNegozi || !$numUtenti){
                  echo "<h3 id='erroreQuery'>Errore nell'esecuzione della richiesta! Ci scusiamo per il disagio. qError:1</h3>";
                }else{
                    ?>


                    <div class="infoNegozioUtenteNonLogato">

                      <div class="labelPaga">
                        <h3>ATTENZIONE! Per poter effettuare un pagamento esegui prima il <a href="login.php">login</a>!</h3>
                      </div>

                      <div class="bloccoPagaUtenteNonLogato">

                        <div class="bloccoPagaNumeroNegozi">

                          <div class="boxImmagineNegozi">
                            <img src="negozi.png" alt="logo negozi">
                          </div>
                          <h4><?php echo mysqli_fetch_assoc($numNegozi)["negozi"]; ?> negozi a tua disposizione!</h4>
                        </div>

                        <div class="bloccoPagaNumeroUtenti">

                          <div class="boxImmagineUtenti">
                            <img src="utenti.png" alt="logo utenti">
                          </div>
                          <h4><?php echo mysqli_fetch_assoc($numUtenti)["utenti"]; ?> utenti utilizzano gi&agrave; la nostra piattaforma!</h4>
                        </div>
                      </div>
                    </div>

                    <?php

                  	mysqli_free_result($numNegozi);
                    mysqli_free_result($numUtenti);
                }

                mysqli_close($con);

              }else{

                ?>

                <div class="bloccoPagamentoUtenteLoggato">

                  <div class="labelPagamentoUtenteLoggato">
                    <h3>Scegli un destinatario, inserisci l'importo e clicca su PROCEDI per completare la transazione.</h3>
                  </div>

                  <form class="pagamento" action="<?php echo basename($_SERVER['PHP_SELF']); ?>" method="post">

                    <div class="listaPossibiliDestinatari">

                      <?php

                      if($_SESSION['tipoUtente'] == 0){ //utente semplice

                        $query = "SELECT nome, nick FROM `usr` WHERE negozio = 1 ";
                        $stmt= mysqli_prepare($con, $query);

                      }elseif ($_SESSION['tipoUtente'] == 1) { //negoziante

                        $nck = $_SESSION['nickUtente'];
                        $query = "SELECT nome, nick FROM `usr` WHERE nick!=? ";
                        $stmt= mysqli_prepare($con, $query);
                        mysqli_stmt_bind_param($stmt, "s", $nck);

                      }else{
                        echo "<h3>ERRORE. L'utente non corrisponde a nessuno client type.</h3>";
                        exit;
                      }

                      if(!mysqli_stmt_execute($stmt))
                          echo "<h3 id='erroreQuery'>Errore nell'esecuzione della richiesta! Ci scusiamo per il disagio. qError:2</h3>";
                      else
                      {
                          mysqli_stmt_bind_result($stmt, $name, $nick);
                          ?><table id='tabellaDestinatari'>

                            <div class="labelPagamentoUtenteLoggatoPrint">
                              <h3>Elenco dei possibili destinatari:</h3>
                            </div>
                                  <tr>
                                    <th>Nome Utente</th>
                                    <th>Nickname</th>
                                    <th></th>
                                  </tr>
                          <?php
                          while($row = mysqli_stmt_fetch($stmt)){
                              echo "<tr class='righeTabella' id='$nick'>\n
                              <td>$name</td>\n
                              <td>$nick</td>\n
                              <td><input type='radio' value='$nick' name='utSel' class='utenteSelezionato'></td>\n
                              </tr>\n";
                          }

                          mysqli_stmt_close($stmt);
                      }
                      ?></table><?php

                      mysqli_close($con);

                       ?>
                    </div>

                    <?php

                        if($_SERVER["REQUEST_METHOD"] == "POST"){

                          $importoCheck = 0;
                          $importoCheck = $_POST['txtSommaPagamento'];
                          $importo = 0;
                          $newSaldoMittente = 0;

                          $regEx = '/^\d+(\,\d{1,2})?$/';
                          $msg = "";

                          if(!preg_match($regEx, $importoCheck)){
                            $msg = "<h4 style='color: red;'>Il campo IMPORTO deve contenere solo numeri con al pi&ugrave; due cifre dopo la virgola</h4>";
                          }else{

                            $r1 = '/^\d+(\,\d{1})$/';
                            $r2 = '/^\d+(\,\d{2})$/';

                            if(preg_match($r1, $importoCheck)){
                              $importo = substr_replace($importoCheck,"","-2", "1")*10;
                            }elseif(preg_match($r2, $importoCheck)){
                              $importo = substr_replace($importoCheck,"","-3", "1");
                            }else{
                              $importo = $importoCheck*100;
                            }

                            if($importo<=0){
                              $msg = "<h4 style='color: red;'>L'importo deve essere maggiore di zero!</h4>";
                            }elseif($_SESSION['saldo']<$importo) {
                            $msg = "<h4 style='color: red;'>Saldo insufficiente. Riprova con un importo minore!</h4>";

                            }else{

                              if(!isset($_POST['utSel'])){
                                $msg = "<h4 style='color: red;'>Selezionare un destinatario prima di procedere.</h4>";
                              }else{

                                $connection = mysqli_connect("172.17.0.87", "uReadWrite", "SuperPippo!!!", "pagamenti");
                                if (mysqli_connect_errno()){
                                    echo "<div class='erroreDB'>
                                      <h1>Oops! C'&egrave; stato un problema nella connessione al database. Ci scusiamo per il disagio! dbError:2</h1>\n
                                    </div>";
                                  }
                                else{

                                  $destinatario = $_POST['utSel'];

                                  //CONTROLLO CHE IL MITTENTE NON CERCHI, IN QUALCHE MODO, DI INVIARE DENARO AD UTENTI NON DI SUA COMPETENZA

                                  $trovato = false;
                                  $queryControlloUtenteSel = "SELECT nick, negozio FROM usr WHERE nick=?";
                                  $stmtControlloUtenteSel = mysqli_prepare($connection, $queryControlloUtenteSel);
                                  mysqli_stmt_bind_param($stmtControlloUtenteSel, "s", $destinatario);
                                  $resultControlloUtenteSel = mysqli_stmt_execute($stmtControlloUtenteSel);

                                  if(!$resultControlloUtenteSel)
                                      echo "<h3 id='erroreQuery'>Errore nell'esecuzione della richiesta! Ci scusiamo per il disagio. qError:3</h3>";
                                  else
                                    mysqli_stmt_bind_result($stmtControlloUtenteSel, $nickCheck, $typeCheck);
                                    while($row = mysqli_stmt_fetch($stmtControlloUtenteSel)){

                                      if(strcmp($nickCheck, $_SESSION['nickUtente'])==0){
                                        $trovato = true;
                                      }
                                      if($_SESSION['tipoUtente']==0 && $typeCheck==0){
                                        $trovato = true;
                                      }

                                    }

                                  mysqli_stmt_close($stmtControlloUtenteSel);


                                  if($trovato==true){
                                    $msg = "<h4 style='color: red;'>Il destinatario selezionato non &egrave; tra quelli indicati.</h4>";
                                  }else{

                                      // 1 TROVO IL SALDO ED IL NOME DEL DESTINATARIO

                                      $queryGetSaldoDestinatario = "SELECT saldo, nome FROM `usr` WHERE nick = ?";
                                      $stmtGetSaldoDestinatario = mysqli_prepare($connection, $queryGetSaldoDestinatario);
                                      mysqli_stmt_bind_param($stmtGetSaldoDestinatario, "s", $destinatario);
                                      $resultGetSaldoDestinatario = mysqli_stmt_execute($stmtGetSaldoDestinatario);

                                      if(!$resultGetSaldoDestinatario){
                                          echo "<h3 id='erroreQuery'>Errore nell'esecuzione della richiesta! Ci scusiamo per il disagio. qError:4</h3>";
                                      }else{
                                        mysqli_stmt_bind_result($stmtGetSaldoDestinatario, $saldoDestinatario, $nD);
                                        while($row = mysqli_stmt_fetch($stmtGetSaldoDestinatario)){
                                          $newSaldoDestinatario = $saldoDestinatario+$importo;
                                          $nomeDestinatario = $nD;
                                          $_SESSION['nomeDestinatario'] = $nomeDestinatario;
                                        }
                                      }

                                      mysqli_stmt_close($stmtGetSaldoDestinatario);


                                      // 2 FACCIO UPDATE SUL MITTENTE
                                      $mittente = $_SESSION['nickUtente'];
                                      $newSaldoMittente = $_SESSION['saldo']-$importo;
                                      $_SESSION['saldoUtente'] = number_format($newSaldoMittente/100,2,',','.');

                                      $queryUpdateMittente = "UPDATE usr SET saldo = ? WHERE nick = ?";
                                      $stmtMittente = mysqli_prepare($connection, $queryUpdateMittente);
                                      mysqli_stmt_bind_param($stmtMittente, "is", $newSaldoMittente, $mittente);
                                      $resultMittente = mysqli_stmt_execute($stmtMittente);

                                      if(!$resultMittente){
                                          echo "<h3 id='erroreQuery'>Errore nell'esecuzione della richiesta! Ci scusiamo per il disagio. qError:5</h3>";
                                      }

                                      mysqli_stmt_close($stmtMittente);

                                      // 3 FACCIO UPDATE SUL DESTINATARIO
                                      $queryUpdateDestinatario = "UPDATE usr SET saldo = ? WHERE nick = ?";
                                      $stmtDestinatario = mysqli_prepare($connection, $queryUpdateDestinatario);
                                      mysqli_stmt_bind_param($stmtDestinatario, "is", $newSaldoDestinatario, $destinatario);
                                      $resultDestinatario = mysqli_stmt_execute($stmtDestinatario);

                                      if(!$resultDestinatario){
                                          echo "<h3 id='erroreQuery'>Errore nell'esecuzione della richiesta! Ci scusiamo per il disagio. qError:6</h3>";
                                      }

                                      mysqli_stmt_close($stmtDestinatario);

                                      // 4 FACCIO UPDATE SULLA TABELLA LOG
                                      $time = time();
                                      $data = date("Y-m-d", $time);
                                      $ora = date("H:i:s", $time);
                                      $timestamp = $data." ".$ora;

                                      $queryID = "SELECT id FROM `log`";
                                      $resultID = mysqli_query($connection, $queryID);
                                      if(!$resultID){
                                          echo "<h3 id='erroreQuery'>Errore nell'esecuzione della richiesta! Ci scusiamo per il disagio. qError:7</h3>";
                                      }
                                      else
                                      {
                                          $id = 1;
                                          while($row = mysqli_fetch_assoc($resultID)){
                                            if($row>$id){
                                              $id++;
                                            }
                                          }
                                          mysqli_free_result($resultID);
                                      }

                                      $queryUpdateLog = "INSERT INTO log (id, src, dst, importo, data) VALUES (?, ?, ?, ?, ?)";
                                      $stmtUpdateLog = mysqli_prepare($connection, $queryUpdateLog);
                                      mysqli_stmt_bind_param($stmtUpdateLog, "issis", $id, $mittente, $destinatario, $importo, $timestamp);
                                      $resultUpdateLog = mysqli_stmt_execute($stmtUpdateLog);

                                      if(!$resultUpdateLog){
                                         echo "<h3 id='erroreQuery'>Errore nell'esecuzione della richiesta! Ci scusiamo per il disagio. qError:8</h3>";
                                      }else{

                                        mysqli_stmt_close($stmtUpdateLog);
                                        $_SESSION['pagamento'] = 1;
                                        $_SESSION['timestamp'] = $timestamp;
                                        $_SESSION['importo'] = $importo;
                                        $_SESSION['saldo'] = $newSaldoMittente;

                                          echo "<script>
                                                  window.location.replace('confermaPagamento.php');
                                                </script>";


                                    }

                                  }

                              }
                              mysqli_close($connection);
                                }
                              }
                            }

                          echo $msg;
                        }

                     ?>

                        <div class="inputSommaPagamento">
                          <h4>Importo (in &euro;): </h4>
                          <input type="text" name="txtSommaPagamento" value="" placeholder="(es. 10,00, 250, 15,5)" maxlength="10">
                        </div>

                    <div class="inputProcediPagamento">
                      <input type="submit" name="procedi" class="btnProcedi" value="PROCEDI">
                    </div>

                  </form>

                </div>

                <?php
              }
  ?>

<?php } ?>

</div>

<?php require("footer.php") ?>
