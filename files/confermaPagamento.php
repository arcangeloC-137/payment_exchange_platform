<?php
  require('session.php');
  $pagaPage = $homePage = $loginPage = "";
  $logPage = "";
  require("header.php")
?>

<div class="paginaConfermaPagamento">

  <?php
  if($_SESSION['utenteLoggato']==false){ ?>
  <div class="bloccoAvvisoConfermaPagamento">
    <h3>ATTENZIONE! Questa area &egrave;
    disponibile solo dopo aver effettuato un pagamento.</h3>
    <h4>Per poter visualizzare questa pagina esegui prima il <a href="login.php">login</a>!</h4>
  </div>

<?php }else{

  if($_SESSION['pagamento']==0){
    ?>
    <div class="bloccoAvvisoConfermaPagamento">
      <h3>ATTENZIONE! Questa area &egrave; disponibile solo dopo aver effettuato un pagamento.</h3>
      <h4>Per poter visualizzare questa pagina esegui prima un <a href="paga.php">pagamento</a>!</h4>
    </div>

  <?php
}elseif($_SESSION['pagamento']==1){

    ?>

    <div class="bloccoConfermaPagamento">

      <div class="labelPagamentoConSuccesso">
        <h2 style="color:green;">Pagamento effettuato con successo!</h2>
      </div>

      <div class="bloccoTabellaPagamento">
        <table id="tabellaConfermaPagamento">
          <tr>
            <td class="dati"><b>Destinatario: </b></td>
            <td class="risultati"><?php echo $_SESSION['nomeDestinatario']; ?></td>
          </tr>
          <tr>
            <td class="dati"><b>Mittente: </b></td>
            <td class="risultati"><?php echo $_SESSION['nomeUtente']; ?></td>
          </tr>
          <tr>
            <td class="dati"><b>Importo: </b></td>
            <td class="risultati"><?php echo number_format($_SESSION['importo']/100,2,',','.');?>&euro;</td>
          </tr>
          <tr>
            <td class="dati"><b>Data e ora:</b></td>
            <td class="risultati"><?php echo $_SESSION['timestamp']; ?></td>
          </tr>
        </table>
      </div>

        <form class="btnTornaAlLog" action="log.php" method="post">
          <input type="submit" name="btnHome" value="CONTINUA">
        </form>

    </div>

     <?php
     $_SESSION['pagamento'] = 0;
     $_SESSION['timestamp'] = "";
     $_SESSION['nomeDestinatario'] = "";
     $_SESSION['importo'] = 0;
  }
}
 ?>
</div>

<?php require("footer.php") ?>
