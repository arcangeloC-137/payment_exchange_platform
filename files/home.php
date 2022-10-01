<?php require('session.php');
  $homePage = "class='paginaCorrente'";
  $loginPage = $logPage = $pagaPage = "";
  require("header.php");
?>


<div class="labelPresentazione">
   <h3>Benvenuto in ThunderPay, la piattaforma digitale per i tuoi pagamenti online!</h3>
</div>

<noscript>
  <h4 id="labelNoscript">
   ATTENZIONE! Il sito utilizza il linguaggio JavaScript per migliorare
   l'esperienza dell'utente. Il tuo browser non supporta, o ha disabilitato,
   JavaScript, perci&ograve; alcune funzionalit&agrave; protrebbero non essere supportate!
  </h4>
</noscript>


<!-- Blocchi per consigli sulla navigaione della pagina -->
  <div class="blocchiHome">

    <!-- LOGIN -->
    <div class="bloccoLogin" >
      <a href="login.php">

      <div class="boxImmagineHome">
        <img src="loginIcon.png" alt="logo login">
      </div>
      <h4>Effettua il Login per poter accedere ai tuoi dati.</h4>

      </a>
    </div>

    <!-- LOG -->
    <div class="bloccoLog">
      <a href="log.php">

      <div class="boxImmagineHome">
          <img src="logIcon.png" alt="logo Log">
      </div>
      <h4>Visita la sezione Log per consultare la tabella dei pagameti.</h4>

      </a>
    </div>

    <!-- PAGA -->
    <div class="bloccoPaga">
      <a href="paga.php">

      <div class="boxImmagineHome">
        <img src="pagaIcon.png" alt="logo paga">
      </div>
      <h4>Esegui le tue transazioni tramite la sezione Paga.</h4>

      </a>
    </div>
</div>

<?php require("footer.php") ?>
