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
    'auction' => [
      'l' => ['nom'=>'listAuctions'],
      'a' => ['nom'=>'addAuction'],
      'm' => ['nom'=>'modifierAuction' ],
      's' => ['nom'=>'supprimerAuction'],
  
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
  public function listAuctions() {

    $auctions = $this->oRequetesSQL->getAuctions();

    (new Vue)->generer('vListAuctions',
            array(
              'oUser'        => $this->oUser,
              'titre'               => 'Gestion des users',
              'auctions'        => $auctions,
              'classRetour'         => $this->classRetour, 
              'messageRetourAction' => $this->messageRetourAction
            ),
            'gabarit-frontend');
  }



  /**
   * Ajouter une enchère
   */
  public function addAuction() {

    $user = $this->oUser;
   
  
    $location = $this->oRequetesSQL->getLocations();
    $rareness = $this->oRequetesSQL->getRareness();
    $condition = $this->oRequetesSQL->getConditions();
    
    
    $auction  = [];
    $stamp = [];
    $erreursA = [];
    $erreursS = [];
   

    if (count($_POST) !== 0) {
      $stamp = array_splice($_POST, 7);
      $auction = $_POST;

      $oAuction = new Auction($auction); 
      $erreursA = $oAuction->erreurs; 

      $oStamp = new Stamp($stamp); 
      $erreursS = $oStamp->erreurs;


      if (count($erreursA) === 0) { 
        $auction_id = $this->oRequetesSQL->addAuction([
          'auction_name'    => $oAuction->auction_name,
          'auction_description' => $oAuction->auction_description,
          'auction_startDate' => $oAuction->auction_startDate,
          'auction_finishDate' => $oAuction->auction_finishDate,
          'auction_price' => $oAuction->auction_price,
          'auction_user_id' => $user->user_id,
          'auction_status_id' => $oAuction->auction_status_id
        ]);

        if($_FILES){
          $nom_fichier = $_FILES['userfile']['name'];
          $fichier = $_FILES['userfile']['tmp_name'];

          $url_img = "assets/imgs/stamps/".$nom_fichier;
          //Ajouter stamptime pour permettre d'avoir des images avec le mm nom
          if(move_uploaded_file($fichier, $url_img)){
            //echo "Fichier copié";
             $img_id = $this->oRequetesSQL->addImg([
              'image_link' => $url_img
             ]);
            
          //} else {
           // echo "fichier non copié";
          }
        }

     
        // $oStamp = new Stamp($stamp); 
        // $erreursS = $oStamp->erreurs;
        if (count($erreursS) === 0) { 
          $stamp_id = $this->oRequetesSQL->addStamp([
            'stamp_name' => $oStamp->stamp_name, 
            'stamp_description' => $oStamp->stamp_description,  
            'stamp_price' => $oStamp->stamp_price, 
            'stamp_date' => $oStamp->stamp_date,
             'stamp_certified' => $oStamp->stamp_certified, 
             'stamp_format' => $oStamp->stamp_format, 
             'stamp_color' => $oStamp->stamp_color, 
             'stamp_location_id' => $oStamp->stamp_location_id, 
             'stamp_image_id' => $img_id, 
             'stamp_condition_id' => $oStamp->stamp_condition_id,
             'stamp_rareness_id' => $oStamp->stamp_rareness_id, 
             'stamp_auction_id' => $auction_id
          ]);




        }
        if ($auction_id > 0 && $stamp_id > 0) { 
          $this->messageRetourAction = "Enchère ajoutée.";
        } else {
          $this->classRetour = "erreur";
          $this->messageRetourAction = "Ajout non effectué.";
        }
        $this->listAuctions(); 
        exit;
      }
    }
    
    (new Vue)->generer('vAuctionAdd',
            array(
              'titre'       => 'Ajouter une enchère',
              'user'        => $user,
              'auction'     => $auction,
              'stamp'       => $stamp,
              'locations'   => $location,
              'conditions'  => $condition,
              'rareness'    => $rareness,
              'erreursA'    => $erreursA,
              'erreursS'    => $erreursS
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