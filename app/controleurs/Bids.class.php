<?php

/**
 * Classe Contrôleur des requêtes de l'application admin
 */

class Auctions extends Routeur {

  private $entite;
  private $action;
  private $user_id;

  private $oUser;

  private $methodes = [
    'bid' => [
      'b' => ['nom'=>'bid'],
      
  
    ]
  ];
    

  private $classRetour = "fait";
  private $messageRetourAction = "";

  /**
   * Constructeur qui initialise le contexte du contrôleur  
   */  
  public function __construct() {
    $this->entite    = $_GET['entite']    ?? 'bid';
    $this->action    = $_GET['action']    ?? 'b';
    $this->user_id = $_GET['user_id'] ?? null;
    $this->auction_id  = $_GET['auction_id']  ?? null;
    $this->oRequetesSQL = new RequetesSQL;
 
  }
public function gererBid() {
    if (isset($_SESSION['oUser'])) {
      $this->oUser = $_SESSION['oUser'];
      if (isset($this->methodes[$this->entite])) {
        if (isset($this->methodes[$this->entite][$this->action])) {
          $methode = $this->methodes[$this->entite][$this->action]['nom'];
          $this->$methode();
     
        } else {
          throw new Exception("L'action $this->action de l'entité $this->entite n'existe pas.");
        }
      } else {
        throw new Exception("L'entité $this->entite n'existe pas.");
      }
    } else {
      $this->catalogueAuctions();
    }
  }

  public function bid(){
    if (isset($_SESSION['oUser'])) {
      $user = $this->oUser = $_SESSION['oUser'];
      $auction = $this->oRequetesSQL->getAuction($this->auction_id);

     if (count($_POST) !== 0) {
        $bid = $_POST;
        $oBid = new Bid($bid); 
        $erreurs = $oBid->erreurs;
        if (count($erreurs) === 0 ) { 
          $bid_id = $this->oRequetesSQL->addBid([
          'bid_amount' => $oBid->bid_amount,
          'bid_user_id' => $user->user_id,
          'bid_auction_id' => $auction->auction_id
          ]);
        }
      }
    echo "<pre>";
    print_r($auction);
    echo "</pre>";
    (new Vue)->generer('modaleBid',
            array(
              'user'        => $user,
              'bid'        => $bid,
              
              'auction'        => $auction,
              'classRetour'         => $this->classRetour, 
              'messageRetourAction' => $this->messageRetourAction
            ),
            'gabarit-frontend');
    }
  }
}