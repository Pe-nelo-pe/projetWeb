<?php

/**
 * Classe Contrôleur des requêtes de l'application admin
 */

class Auction extends Routeur {

  private $entite;
  private $action;
  private $user_id;

  private $oUser;

  private $methodes = [
    'auction' => [
      'l' => ['nom'=>'listerUsers'],
      'a' => ['nom'=>'addAuction'],
      'm' => ['nom'=>'modifierUser' ],
      's' => ['nom'=>'supprimerUser'],
  
    ]
  ];
    

  private $classRetour = "fait";
  private $messageRetourAction = "";

  /**
   * Constructeur qui initialise le contexte du contrôleur  
   */  
  public function __construct() {
    $this->entite    = $_GET['entite']    ?? 'auction';
    $this->action    = $_GET['action']    ?? 'l';
    $this->user_id = $_GET['user_id'] ?? null;
    $this->oRequetesSQL = new RequetesSQL;
 
  }


  public function gererAuction() {
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
      $this->listing();
    }
  }
 

   /**
   * Modification du mot de passe par le bouton générer mdp
   */
  public function modificationMDP() {

    $oUser = new User(["user_id"=>$this->user_id]);
    $oUser->genererMdp();

    if ($this->oRequetesSQL->modificationMDP(['user_id'=> $oUser->user_id, 'user_mdp'=> $oUser->user_mdp])) {
       
       $newMDP= $oUser->user_mdp;
       
       $oUser = $this->oRequetesSQL->getUser($this->user_id);
       $oUser["user_mdp"] = $newMDP;

       $retour = (new GestionCourriel)->envoyerMdp($oUser);
       
       $this->messageRetourAction = "Modification du mot de passe de l'user numéro $this->user_id effectuée. Courriel envoyé à ". $oUser["user_courriel"]. ".<br>";
       if (ENV === "DEV")  $this->messageRetourAction .= "<a href=\"$retour\">Message dans le fichier $retour</a>";
       
    } else {
      $this->classRetour = "erreur";
      $this->messageRetourAction = "Modification du mot de passe de l'user numéro $this->user_id non effectuée.";
    }
    
    $this->listerUsers();

    
  }



  /**
   * Lister les users
   */
  public function listerUsers() {

    $users = $this->oRequetesSQL->getUsers();

    (new Vue)->generer('vAdminUsers',
            array(
              'oUser'        => $this->oUser,
              'titre'               => 'Gestion des users',
              'users'        => $users,
              'classRetour'         => $this->classRetour, 
              'messageRetourAction' => $this->messageRetourAction
            ),
            'gabarit-admin');
  }

  /**
   * Ajouter une enchère
   */
  public function addAuction() {

    $auction  = [];
    $erreurs = [];
    if (count($_POST) !== 0) {
      $auction = $_POST;
      $oAuction = new Auction($auction); 
      $erreurs = $oAuction->erreurs;
      if (count($erreurs) === 0) { 
        $auction_id = $this->oRequetesSQL->addAuction([
          'auction_name'    => $oAuction->auction_name,
          'auction_description' => $oAuction->auction_description,
          'auction_courriel' => $oAuction->user_courriel,
          'auction_profil' => $oAuction->user_profil,
          'auction_mdp' => $oAuction->user_mdp
        ]);
        if ( $user_id > 0) { 
          $this->messageRetourAction = "Ajout de l'user numéro $user_id effectuée. Courriel envoyé à " . $oUser->user_courriel. ".<br>";
        } else {
          $this->classRetour = "erreur";
          $this->messageRetourAction = "Ajout de l'user non effectué.";
        }
        $this->listerUsers(); 
        exit;
      }
    }
    
    (new Vue)->generer('vAuctionAdd',
            array(
             // 'oUser' => $this->oUser,
              'titre'        => 'Ajouter une enchère',
              //'user'  => $user,
              'erreurs'      => $erreurs
            ),
            'gabarit-frontend');
  }

  /**
   * Modifier un user identifié par sa clé dans la propriété user_id
   */
  public function modifierUser() {
    if (count($_POST) !== 0) {
      $user = $_POST;
      $oUser = new User($user);
      $erreurs = $oUser->erreurs;
      if (count($erreurs) === 0) {
        if($this->oRequetesSQL->modifierUser([
          'user_id'     => $oUser->user_id,
          'user_nom'    => $oUser->user_nom,
          'user_prenom' => $oUser->user_prenom,
          'user_courriel' => $oUser->user_courriel,
          'user_profil' => $oUser->user_profil
        ])) {
          $this->messageRetourAction = "Modification de l'user numéro $this->user_id effectuée.";
        } else {
          $this->classRetour = "erreur";
          $this->messageRetourAction = "modification de l'user numéro $this->user_id non effectuée.";
        }
        $this->listerUsers();
        exit;
      }

    } else {
      $user  = $this->oRequetesSQL->getUser($this->user_id);
      $erreurs = [];
    }
    
    (new Vue)->generer('vAdminUserModifier',
            array(
              'oUser' => $this->oUser,
              'titre'        => "Modifier l'user numéro $this->user_id",
              'user'  => $user,
              'erreurs'      => $erreurs
            ),
            'gabarit-admin');
  }
  
  /**
   * Supprimer un user identifié par sa clé dans la propriété user_id
   */
  public function supprimerUser() {
    if ($this->oRequetesSQL->supprimerUser($this->user_id)) {
      $this->messageRetourAction = "Suppression de l'user numéro $this->user_id effectuée.";
    } else {
      $this->classRetour = "erreur";
      $this->messageRetourAction = "Suppression de l'user numéro $this->user_id non effectuée.";
    }
    $this->listerUsers();
  }

  

}