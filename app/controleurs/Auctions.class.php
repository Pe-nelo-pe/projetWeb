<?php

/**
 * Classe Contrôleur des requêtes pour les enchères
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
      'sd'=> ['nom'=>'singleDetails'],
  
    ]
  ];
    

 
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


  /**
   * Gérer l'interface d'auction 
   */ 
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
    } elseif (!isset($_SESSION['oUser'])){
      if (isset($this->methodes[$this->entite][$this->action])) {
          $methode = $this->methodes[$this->entite][$this->action]['nom'];
          $this->$methode();
        }
      $this->catalogueAuctions();
    }
  }
 

  /**
   * Lister les enchères d'un utilisateur
   */
  public function listAuctionsByUser() {
    if (isset($_SESSION['oUser'])) {
      $user = $this->oUser = $_SESSION['oUser'];
    }

    $auctions = $this->oRequetesSQL->getAuctionsByUser($user->user_id);

    (new Vue)->generer('vListAuctions',
            array(
              'user'                => $user,
              'titre'               => 'Liste de vos enchères',
              'auctions'            => $auctions,
              'messageRetourAction' => $this->messageRetourAction
            ),
            'gabarit-frontend');
  }


  /**
   * Lister les enchères du catalogue
   */
  public function catalogueAuctions() {
    if (isset($_SESSION['oUser'])) {
      $user = $this->oUser = $_SESSION['oUser'];
    }
   
    $auctions = $this->oRequetesSQL->getAuctions();

    (new Vue)->generer('vCatalogue',
            array(
              'user'       => $user,
              'auctions'   => $auctions,
              
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

      $postDivised = $this->splitArrayByKey($_POST,"auction_status_id",true);
    
      $auction = $postDivised[0];
      $stamp = $postDivised[1];
      
      $oAuction = new Auction($auction); 
      $erreursA = $oAuction->erreurs; 

      $oStamp = new Stamp($stamp); 
      $erreursS = $oStamp->erreurs;

      if($_FILES['userfile']['error'] === 4){
        $erreursI = 'Champs obligatoire';

      }

      if (count($erreursA) === 0 && count($erreursS) === 0 && $_FILES['userfile']['error'] != 4) { 
        
        $auction_id = $this->oRequetesSQL->addAuction([
          'auction_name'        => $oAuction->auction_name,
          'auction_description' => $oAuction->auction_description,
          'auction_startDate'   => $oAuction->auction_startDate,
          'auction_finishDate'  => $oAuction->auction_finishDate,
          'auction_price'       => $oAuction->auction_price,
          'auction_user_id'     => $user->user_id,
          'auction_status_id'   => $oAuction->auction_status_id
        ]);

        $stamp_id = $this->oRequetesSQL->addStamp([
          'stamp_name' => $oStamp->stamp_name, 
          'stamp_description'  => $oStamp->stamp_description,  
          'stamp_price'        => $oStamp->stamp_price, 
          'stamp_date'         => $oStamp->stamp_date,
          'stamp_certified'    => $oStamp->stamp_certified, 
          'stamp_format'       => $oStamp->stamp_format, 
          'stamp_color'        => $oStamp->stamp_color, 
          'stamp_location_id'  => $oStamp->stamp_location_id,  
          'stamp_condition_id' => $oStamp->stamp_condition_id,
          'stamp_rareness_id'  => $oStamp->stamp_rareness_id, 
          'stamp_auction_id'   => $auction_id,
          'stamp_user_id'      => $user->user_id,
        ]);


        $nom_fichier = $_FILES['userfile']['name'];
        $fichier = $_FILES['userfile']['tmp_name'];

        $url_img = "assets/imgs/stamps/".$nom_fichier;
        
        if(move_uploaded_file($fichier, $url_img)){
          $img_id = $this->oRequetesSQL->addImg([
            'image_link'    => $url_img,
            'image_name'    => $nom_fichier,
            'image_stamp_id'=> $stamp_id
          ]);
        }

        $nom_fichier = $_FILES['userfile2']['name'];
        $fichier = $_FILES['userfile2']['tmp_name'];

        $url_img = "assets/imgs/stamps/".$nom_fichier;
        
        if(move_uploaded_file($fichier, $url_img)){
          $img_id = $this->oRequetesSQL->addImg([
            'image_link'    => $url_img,
            'image_name'    => $nom_fichier,
            'image_stamp_id'=> $stamp_id
          ]);
        } 

        $nom_fichier = $_FILES['userfile3']['name'];
        $fichier = $_FILES['userfile3']['tmp_name'];

        $url_img = "assets/imgs/stamps/".$nom_fichier;
        
        if(move_uploaded_file($fichier, $url_img)){
          $img_id = $this->oRequetesSQL->addImg([
            'image_link'    => $url_img,
            'image_name'    => $nom_fichier,
            'image_stamp_id'=> $stamp_id
          ]);
        }
          
        $nom_fichier = $_FILES['userfile4']['name'];
        $fichier = $_FILES['userfile4']['tmp_name'];

        $url_img = "assets/imgs/stamps/".$nom_fichier;
        
        if(move_uploaded_file($fichier, $url_img)){
          $img_id = $this->oRequetesSQL->addImg([
            'image_link'    => $url_img,
            'image_name'    => $nom_fichier,
            'image_stamp_id'=> $stamp_id
          ]);
        }

          $this->messageRetourAction = "Enchère ajoutée.";
          $this->listAuctionsByUser(); 
          exit;
        }
      }
 
    (new Vue)->generer('vAddAuction',
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
   * Modifier une enchère 
   */
  public function updateAuction() {
    if (isset($_SESSION['oUser'])) {
     $user = $this->oUser = $_SESSION['oUser'];
    }
 
    $auctions  = $this->oRequetesSQL->getAuction($this->auction_id);
    $dataAuction = $auctions[0];
   
    $location = $this->oRequetesSQL->getLocations();
    $rareness = $this->oRequetesSQL->getRareness();
    $condition = $this->oRequetesSQL->getConditions();
    
    $stamp = [];
    $erreursA = [];
    $erreursS = [];

    if (count($_POST) !== 0) {
  
      $postDivised = $this->splitArrayByKey($_POST,"auction_status_id",true);
    
      $auction = $postDivised[0];
      $stamp = $postDivised[1];
        
      $oAuction = new Auction($auction); 
      $erreursA = $oAuction->erreurs; 

      $oStamp = new Stamp($stamp); 
      $erreursS = $oStamp->erreurs;

      if (count($erreursA) === 0 && count($erreursS) === 0) { 
        
        $this->oRequetesSQL->updateAuction([
          'auction_id'          => $this->auction_id,
          'auction_name'        => $oAuction->auction_name,
          'auction_description' => $oAuction->auction_description,
          'auction_startDate'   => $oAuction->auction_startDate,
          'auction_finishDate'  => $oAuction->auction_finishDate,
          'auction_price'       => $oAuction->auction_price,
          'auction_user_id'     => $user->user_id,
          'auction_status_id'   => $oAuction->auction_status_id
        ]);

         $this->oRequetesSQL->updateStamp([
          'stamp_id'          => $dataAuction['stamp_id'], 
          'stamp_name'        => $oStamp->stamp_name, 
          'stamp_description' => $oStamp->stamp_description,  
          'stamp_price'       => $oStamp->stamp_price, 
          'stamp_date'        => $oStamp->stamp_date,
          'stamp_certified'   => $oStamp->stamp_certified, 
          'stamp_format'      => $oStamp->stamp_format, 
          'stamp_color'       => $oStamp->stamp_color, 
          'stamp_location_id' => $oStamp->stamp_location_id,  
          'stamp_condition_id'=> $oStamp->stamp_condition_id,
          'stamp_rareness_id' => $oStamp->stamp_rareness_id, 
          'stamp_auction_id'  => $this->auction_id,
          'stamp_user_id'     => $user->user_id,
        ]);

        $this->messageRetourAction = "Modifications faites.";
        $this->listAuctionsByUser(); 
        exit; 
      }

    }else {
      $auctions  = $this->oRequetesSQL->getAuction($this->auction_id);
    }

    (new Vue)->generer('vUpdateAuction',
            array(
              'titre'       => 'Modifier une enchère',
              'user'        => $user,
              'auction'     => $auctions[0],
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
   * Supprimer une enchère 
   */
  public function deleteAuction() {

    if (isset($_SESSION['oUser'])) {
     $this->oUser = $_SESSION['oUser'];

      if ($this->oRequetesSQL->deleteAuction($this->auction_id)) {
        $this->messageRetourAction = "Suppression du lot $this->auction_id effectuée.";
      } else {
      $this->messageRetourAction = "Suppression du lot $this->auction_id non effectuée.";
      }
      $this->listAuctionsByUser();
    }
  }


   /**
   * Affiche une enchère avec ses détails
   */
  public function singleDetails() {
    $user=[];
    if (isset($_SESSION['oUser'])) {
      $user = $this->oUser = $_SESSION['oUser'];
    }
    $auction = $this->oRequetesSQL->getAuction($this->auction_id);
    $bids = $this->oRequetesSQL->getBids($this->auction_id);
 
    (new Vue)->generer('vDetail',
            array(
              'user'                => $user,
              'auction'             => $auction[0],
              'bids'                => $bids,
              'messageRetourAction' => $this->messageRetourAction
            ),
            'gabarit-frontend');
  }



  /*
DESCRIPTION: This function splits an array into chunks based on a key value.
 
ARGUMENTS
@Param  $array  array  The Input array  REQUIRED.
@Param  $needle mixed  The key value to split $array on.
@Param  $preserve_keys Boolean  If true, the function preserves the keys after the split
 
RETURNS
    A multi-dimensional array containing the chunks after splitting $array.
    Null if either key value doesn't exist or the chunk length is greater $array length.
*/
function splitArrayByKey($array,$needle,$preserve_keys = false)
{
    //Get the array of keys.
    $array_keys = array_keys($array);
    
    //Search the $needle in the array of keys.
    $split_index = array_search($needle, $array_keys);
 
    //If the keys exists
    if($split_index !== false) 
    {
        //Make array chunks with each chunk containing less than or equal to ($split_index+1) elements.
        $partitioned_array = array_chunk($array,$split_index+1,$preserve_keys);
 
        return $partitioned_array;
    }
 
    //If the key doesn't exist.
    else
    {
        return null;
    }
}
}