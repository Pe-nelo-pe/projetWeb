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
      'u' => ['nom'=>'updateAuction' ],
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
            'image_name' => $nom_fichier,
            'image_stamp_id'=> $stamp_id
          ]);

        }
        $nom_fichier = $_FILES['userfile2']['name'];
        $fichier = $_FILES['userfile2']['tmp_name'];

        $url_img = "assets/imgs/stamps/".$nom_fichier;
        
        if(move_uploaded_file($fichier, $url_img)){
          $img_id = $this->oRequetesSQL->addImg([
            'image_link' => $url_img,
            'image_name' => $nom_fichier,
            'image_stamp_id'=> $stamp_id
          ]);

        } 
        $nom_fichier = $_FILES['userfile3']['name'];
        $fichier = $_FILES['userfile3']['tmp_name'];

        $url_img = "assets/imgs/stamps/".$nom_fichier;
        
        if(move_uploaded_file($fichier, $url_img)){
          $img_id = $this->oRequetesSQL->addImg([
            'image_link' => $url_img,
            'image_name' => $nom_fichier,
            'image_stamp_id'=> $stamp_id
          ]);
        }
          
        $nom_fichier = $_FILES['userfile4']['name'];
        $fichier = $_FILES['userfile4']['tmp_name'];

        $url_img = "assets/imgs/stamps/".$nom_fichier;
        
        if(move_uploaded_file($fichier, $url_img)){
          $img_id = $this->oRequetesSQL->addImg([
            'image_link' => $url_img,
            'image_name' => $nom_fichier,
            'image_stamp_id'=> $stamp_id
          ]);
        }

          
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
   * Modifier une enchère par sa clé dans la propriété auction_id
   */
  public function updateAuction() {
    if (isset($_SESSION['oUser'])) {
     $user = $this->oUser = $_SESSION['oUser'];
    }
    //$user = $this->oUser;

  
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
//       $stamp = array_splice($_POST, 7);
//       $auction = $_POST;
// // !!!!!!changer pour nom de champs
//       $oAuction = new Auction($auction); 
//       $erreursA = $oAuction->erreurs; 

     

//       $oStamp = new Stamp($stamp); 
//       $erreursS = $oStamp->erreurs;

      // if($_FILES['userfile']['error'] === 4){
      //     $erreursI = 'Champs obligatoire';

      //   }

      // if (count($erreursA) === 0 && count($erreursS) === 0 && $_FILES['userfile']['error'] != 4) { 
        
      //   //$auction_id = 
      //   $this->oRequetesSQL->updateAuction([
      //     'auction_name'    => $oAuction->auction_name,
      //     'auction_description' => $oAuction->auction_description,
      //     'auction_startDate' => $oAuction->auction_startDate,
      //     'auction_finishDate' => $oAuction->auction_finishDate,
      //     'auction_price' => $oAuction->auction_price,
      //     'auction_user_id' => $user->user_id,
      //     'auction_status_id' => $oAuction->auction_status_id
      //   ]);

      //    //$stamp_id = 
      //    $this->oRequetesSQL->updateStamp([
      //     'stamp_name' => $oStamp->stamp_name, 
      //     'stamp_description' => $oStamp->stamp_description,  
      //     'stamp_price' => $oStamp->stamp_price, 
      //     'stamp_date' => $oStamp->stamp_date,
      //     'stamp_certified' => $oStamp->stamp_certified, 
      //     'stamp_format' => $oStamp->stamp_format, 
      //     'stamp_color' => $oStamp->stamp_color, 
      //     'stamp_location_id' => $oStamp->stamp_location_id,  
      //     'stamp_condition_id' => $oStamp->stamp_condition_id,
      //     'stamp_rareness_id' => $oStamp->stamp_rareness_id, 
      //     'stamp_auction_id' => $auction_id,
      //     'stamp_user_id' => $user->user_id,
      //   ]);


      //   $nom_fichier = $_FILES['userfile']['name'];
      //   $fichier = $_FILES['userfile']['tmp_name'];

      //   $url_img = "assets/imgs/stamps/".$nom_fichier;
        
      //   if(move_uploaded_file($fichier, $url_img)){
      //     //$img_id = 
      //     $this->oRequetesSQL->updateImg([
      //       'image_link' => $url_img,
      //       'image_stamp_id'=> $stamp_id
      //     ]);

      //   }
      //   $nom_fichier = $_FILES['userfile2']['name'];
      //   $fichier = $_FILES['userfile2']['tmp_name'];

      //   $url_img = "assets/imgs/stamps/".$nom_fichier;
        
      //   if(move_uploaded_file($fichier, $url_img)){
      //     //$img_id = 
      //     $this->oRequetesSQL->updateImg([
      //       'image_link' => $url_img,
      //       'image_stamp_id'=> $stamp_id
      //     ]);

      //   } 
      //   $nom_fichier = $_FILES['userfile3']['name'];
      //   $fichier = $_FILES['userfile3']['tmp_name'];

      //   $url_img = "assets/imgs/stamps/".$nom_fichier;
        
      //   if(move_uploaded_file($fichier, $url_img)){
      //     //$img_id = 
      //     $this->oRequetesSQL->updateImg([
      //       'image_link' => $url_img,
      //       'image_stamp_id'=> $stamp_id
      //     ]);
      //   }
          
      //   $nom_fichier = $_FILES['userfile4']['name'];
      //   $fichier = $_FILES['userfile4']['tmp_name'];

      //   $url_img = "assets/imgs/stamps/".$nom_fichier;
        
      //   if(move_uploaded_file($fichier, $url_img)){
      //     //$img_id = 
      //     $this->oRequetesSQL->updateImg([
      //       'image_link' => $url_img,
      //       'image_stamp_id'=> $stamp_id
      //     ]);
      //   }

       
          
      //    // if ($auction_id > 0  && $stamp_id > 0 && $img_id > 0) { 
      //       $this->messageRetourAction = "Modifications faites.";
      //       $this->listAuctionsByUser(); 
      //       exit;
      //     //} else {
      //    //   $this->classRetour = "erreur";
      //    //  $this->messageRetourAction = "Ajout non effectué.";
      //    // }
          
      //   }
    }else {
      $auctions  = $this->oRequetesSQL->getAuction($this->auction_id);
    }

 echo '<pre>';
 print_r($auctions);
 echo '</pre>';
    (new Vue)->generer('vUpdateAuction',
            array(
              'titre'       => 'Modifier une enchère',
              'user'        => $user,
              'auctions'     => $auctions,
              'auction'     => $auctions[0],
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