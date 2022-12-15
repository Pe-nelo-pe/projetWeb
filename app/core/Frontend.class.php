<?php

/**
 * Classe Contrôleur des requêtes de l'interface frontend
 * 
 */

class Frontend extends Routeur {


  
  /**
   * Constructeur qui initialise des propriétés de la classe
   * 
   */
  public function __construct() {
    $this->oRequetesSQL = new RequetesSQL;
  }


  /**
   * Affichage accueil
   * 
   */  
  public function viewHome() {
    $user=[];
    if (isset($_SESSION['oUser'])) {
      $user = $this->oUser = $_SESSION['oUser'];
    }
    
    $auctions = $this->oRequetesSQL->getAuctionsHome();
    (new Vue)->generer("accueil",
            array(
              'auctions'  => $auctions,
              'user'  => $user,
            ),
            "gabarits/gabarit-frontend");
  }

}