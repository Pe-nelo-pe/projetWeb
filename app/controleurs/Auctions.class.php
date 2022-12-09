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
      'l' => ['nom'=>'listAuctionsByUser'],
      'a' => ['nom'=>'addAuction'],
      'm' => ['nom'=>'modifierAuction' ],
      'd' => ['nom'=>'deleteAuction'],
      'c' => ['nom'=>'catalogueAuctions'],
  
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
    $this->auction_id  = $_GET['auction_id']  ?? null;
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
      $this->catalogueAuctions();
    }
  }
 



  /**
   * Lister les users
   */
  public function listAuctionsByUser() {
    if (isset($_SESSION['oUser'])) {
      $user = $this->oUser = $_SESSION['oUser'];
    }
//var_dump($this->oUser);
$auctions = $this->oRequetesSQL->getAuctionsByUser($user->user_id);


    (new Vue)->generer('vListAuctions',
            array(
              'user'        => $user,
              'titre'               => 'Liste de vos enchères',
              'auctions'        => $auctions,
              'classRetour'         => $this->classRetour, 
              'messageRetourAction' => $this->messageRetourAction
            ),
            'gabarit-frontend');
  }


    /**
   * Lister les users
   */
  public function catalogueAuctions() {
    if (isset($_SESSION['oUser'])) {
      $user = $this->oUser = $_SESSION['oUser'];
    }
   
    $auctions = $this->oRequetesSQL->getAuctions();

    (new Vue)->generer('vCatalogue',
            array(
              'oUser'        => $this->oUser,
              //'titre'               => 'Liste de vos enchères',
              'auctions'        => $auctions
            ),
            'gabarit-frontend');
  }

  /**
   * Ajouter une enchère
   */
  public function addAuction() {
    if (isset($_SESSION['oUser'])) {
     $user = $this->oUser = $_SESSION['oUser'];
    }
    $user = $this->oUser;
 
  
    $location = $this->oRequetesSQL->getLocations();
    $rareness = $this->oRequetesSQL->getRareness();
    $condition = $this->oRequetesSQL->getConditions();
    
    
    $auction  = [];
    $stamp = [];
    $erreursA = [];
    $erreursS = [];
    $erreursI = "";

    $auction_id = [];
    $stamp_id = [];
    $img_id = [];

    if (count($_POST) !== 0) {

       //var_dump($_POST);
  
       //die;
      $stamp = array_splice($_POST, 7);
      $auction = $_POST;
// !!!!!!changer pour nom de champs
      $oAuction = new Auction($auction); 
      $erreursA = $oAuction->erreurs; 

      // $oImage = new Image($_FILES); 
      // $erreursI = $oImage->erreurs;
     

      $oStamp = new Stamp($stamp); 
      $erreursS = $oStamp->erreurs;


//       if (count($erreursA) === 0) { 
//         $auction_id = $this->oRequetesSQL->addAuction([
//           'auction_name'    => $oAuction->auction_name,
//           'auction_description' => $oAuction->auction_description,
//           'auction_startDate' => $oAuction->auction_startDate,
//           'auction_finishDate' => $oAuction->auction_finishDate,
//           'auction_price' => $oAuction->auction_price,
//           'auction_user_id' => $user->user_id,
//           'auction_status_id' => $oAuction->auction_status_id
//         ]);

      
//    //  var_dump($_FILES);
//         if($_FILES['userfile']['error'] === 4){
//           $erreursI = 'Champs obligatoire';

//         } else {

//           $nom_fichier = $_FILES['userfile']['name'];
//           $fichier = $_FILES['userfile']['tmp_name'];

//           $url_img = "assets/imgs/stamps/".$nom_fichier;
//           //Ajouter stamptime pour permettre d'avoir des images avec le mm nom
//           if(move_uploaded_file($fichier, $url_img)){
//              $img_id = $this->oRequetesSQL->addImg([
//               'image_link' => $url_img
//             ]);
//           }  
     
//         }

// //print_r($oStamp);
//         if (count($erreursS) === 0) { 
//           $stamp_id = $this->oRequetesSQL->addStamp([
//             'stamp_name' => $oStamp->stamp_name, 
//             'stamp_description' => $oStamp->stamp_description,  
//             'stamp_price' => $oStamp->stamp_price, 
//             'stamp_date' => $oStamp->stamp_date,
//              'stamp_certified' => $oStamp->stamp_certified, 
//              'stamp_format' => $oStamp->stamp_format, 
//              'stamp_color' => $oStamp->stamp_color, 
//              'stamp_location_id' => $oStamp->stamp_location_id, 
//              'stamp_image_id' => $img_id, 
//              'stamp_condition_id' => $oStamp->stamp_condition_id,
//              'stamp_rareness_id' => $oStamp->stamp_rareness_id, 
//              'stamp_auction_id' => $auction_id
//           ]);
//         }
// print_r($stamp);
// die;
      if($_FILES['userfile']['error'] === 4){
          $erreursI = 'Champs obligatoire';

        }

      if (count($erreursA) === 0 && count($erreursS) === 0 && $_FILES['userfile']['error'] != 4) { 
        
        $auction_id = $this->oRequetesSQL->addAuction([
          'auction_name'    => $oAuction->auction_name,
          'auction_description' => $oAuction->auction_description,
          'auction_startDate' => $oAuction->auction_startDate,
          'auction_finishDate' => $oAuction->auction_finishDate,
          'auction_price' => $oAuction->auction_price,
          'auction_user_id' => $user->user_id,
          'auction_status_id' => $oAuction->auction_status_id
        ]);

         $stamp_id = $this->oRequetesSQL->addStamp([
          'stamp_name' => $oStamp->stamp_name, 
          'stamp_description' => $oStamp->stamp_description,  
          'stamp_price' => $oStamp->stamp_price, 
          'stamp_date' => $oStamp->stamp_date,
          'stamp_certified' => $oStamp->stamp_certified, 
          'stamp_format' => $oStamp->stamp_format, 
          'stamp_color' => $oStamp->stamp_color, 
          'stamp_location_id' => $oStamp->stamp_location_id,  
          'stamp_condition_id' => $oStamp->stamp_condition_id,
          'stamp_rareness_id' => $oStamp->stamp_rareness_id, 
          'stamp_auction_id' => $auction_id,
          'stamp_user_id' => $user->user_id,
        ]);


        $nom_fichier = $_FILES['userfile']['name'];
        $fichier = $_FILES['userfile']['tmp_name'];

        $url_img = "assets/imgs/stamps/".$nom_fichier;
        
        if(move_uploaded_file($fichier, $url_img)){
          $img_id = $this->oRequetesSQL->addImg([
            'image_link' => $url_img,
            'image_stamp_id'=> $stamp_id
          ]);
        }
//print_r($stamp);
//die;
       
          
         // if ($auction_id > 0  && $stamp_id > 0 && $img_id > 0) { 
            $this->messageRetourAction = "Enchère ajoutée.";
            $this->listAuctionsByUser(); 
            exit;
          //} else {
         //   $this->classRetour = "erreur";
         //  $this->messageRetourAction = "Ajout non effectué.";
         // }
          
        }
      }
    //} 
    //}  
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
              'erreursI'    => $erreursI,
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
        //$this->listerUsers();
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
   * Supprimer une enchère par sa clé dans la propriété user_id
   */
  public function deleteAuction() {

    if (isset($_SESSION['oUser'])) {
     $this->oUser = $_SESSION['oUser'];

     if ($this->oRequetesSQL->deleteAuction($this->auction_id)) {
      $this->messageRetourAction = "Suppression du lot $this->auction_id effectuée.";
    } else {
      $this->classRetour = "erreur";
      $this->messageRetourAction = "Suppression du lot $this->auction_id non effectuée.";
    }
    $this->listAuctionsByUser();
    }


    
  }

  

}