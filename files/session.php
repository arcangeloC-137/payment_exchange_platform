<?php
  $session = true;

  if( session_status() === PHP_SESSION_DISABLED  )
    $session = false;
  elseif( session_status() !== PHP_SESSION_ACTIVE ){
    session_start();

    if(!isset($_SESSION['nickUtente']))
      $_SESSION['nickUtente'] = "ANONIMO";

    if(!isset($_SESSION['saldoUtente']))
      $_SESSION['saldoUtente'] = number_format(0,2,',','.');

    if(!isset($_SESSION['saldo']))
      $_SESSION['saldo'] = 0;

    if(!isset($_SESSION['utenteLoggato']))
      $_SESSION['utenteLoggato'] = false;

    if(!isset($_SESSION['tipoUtente']))
      $_SESSION['tipoUtente'] = -1;

    if(!isset($_SESSION['nomeUtente'])){
      $_SESSION['nomeUtente'] = "";
    }

    if(!isset($_SESSION['pagamento'])){
      $_SESSION['pagamento'] = 0;
    }

    if(!isset($_SESSION['timestamp'])){
      $_SESSION['timestamp'] = "";
    }

    if(!isset($_SESSION['nomeDestinatario'])){
      $_SESSION['nomeDestinatario'] = "";
    }

    if(!isset($_SESSION['importo'])){
      $_SESSION['importo'] = 0;
    }

  }

 ?>
