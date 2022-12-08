<?php

/**
 * Classe de l'entité Auction
 *
 */
class Auction
{
  private $auction_id;
  private $auction_name;
  private $auction_description;
  private $auction_startDate;
  private $auction_finishDate;
  private $auction_price;
  private $auction_user_id;
  private $auction_status_id;




  private $erreurs = array();

   

  /**
   * Constructeur de la classe
   * @param array $proprietes, tableau associatif des propriétés 
   *
   */ 
  public function __construct($proprietes = []) {
    $t = array_keys($proprietes);
    foreach ($t as $nom_propriete) {
      $this->__set($nom_propriete, $proprietes[$nom_propriete]);
    } 
  }

  /**
   * Accesseur magique d'une propriété de l'objet
   * @param string $prop, nom de la propriété
   * @return property value
   */     
  public function __get($prop) {
    return $this->$prop;
  }

  // Getters explicites nécessaires au moteur de templates TWIG
  public function getAuction_id()       { return $this->auction_id; }
  public function getAuction_name() { return $this->auction_name; }
  public function getAuction_description(){ return $this->auction_description; }
  public function getAuction_startDate() { return $this->auction_startDate; }
  public function getAuction_finishDate() { return $this->auction_finishDate; }
  public function getAuction_price()    { return $this->auction_price; }
  public function getAuction_user_id()  { return $this->auction_user_id; }
  public function getAuction_status_id()  { return $this->auction_status_id; }
  public function getErreurs()       { return $this->erreurs; }
  
  /**
   * Mutateur magique qui exécute le mutateur de la propriété en paramètre 
   * @param string $prop, nom de la propriété
   * @param $val, contenu de la propriété à mettre à jour    
   */   
  public function __set($prop, $val) {
    $setProperty = 'set'.ucfirst($prop);
    $this->$setProperty($val);
  }

  /**
   * Mutateur de la propriété auction_id 
   * @param int $auction_id
   * @return $this
   */    
  public function setAuction_id($auction_id) {
    $this->auction_id = $auction_id; 
  }    

 /**
   * Mutateur de la propriété auction_name 
   * @param int $auction_name
   * @return $this
   */    
  public function setAuction_name($auction_name) {
    unset($this->erreurs['auction_name']);

     $auction_name = trim($auction_name);
  //  $regex = "/[^@!*\\+\\=?]+/i";

     if (!$auction_name) {
       $this->erreurs['auction_name'] = "Champs obligatoire.";
    } 
  //else if (!preg_match($regex, $auction_name)) {
  //     $this->erreurs['auction_name'] = "Ne doit contenir les symboles suivants: @ ! * + = ?";
  //   }
    $this->auction_name = ucwords(strtolower($auction_name));
    $this->auction_name = $auction_name; 
    return $this;
 
  }

  /**
   * Mutateur de la propriété auction_description 
   * @param int $auction_description
   * @return $this
   */    
  public function setAuction_description($auction_description) {
    unset($this->erreurs['auction_description']);

     if (!$auction_description) {
      $this->erreurs['auction_description'] = "Champs obligatoire.";
    }
    $this->auction_description = $auction_description; 
  }    


  /**
   * Mutateur de la propriété auction_price 
   * @param int $auction_price
   * @return $this
   */    
  public function setAuction_price($auction_price) {
    unset($this->erreurs['auction_price']);

     if (!$auction_price) {
      $this->erreurs['auction_price'] = "Champs obligatoire.";
    } else if(!(is_numeric($auction_price)) || !($auction_price > 0)){
      $this->erreurs['auction_price'] = "Doit être supérieur à 0.";
    }
    $this->auction_price = $auction_price; 
  }    

   /**
   * Mutateur de la propriété auction_startDate 
   * @param int $auction_startDate
   * @return $this
   */    
  public function setAuction_startDate($auction_startDate) {
    $currentDate = date('Y-m-d');

    unset($this->erreurs['auction_startDate']);

    if($auction_startDate < $currentDate){
       $this->erreurs['auction_startDate'] = "La date minimum doit être aujourd'hui.";
    }
    
    $this->auction_startDate = $auction_startDate; 
  }   

    /**
   * Mutateur de la propriété auction_finishDate 
   * @param int $auction_finishDate
   * @return $this
   */    
  public function setAuction_finishDate($auction_finishDate) {
    $date = $this->auction_startDate;
    $minDate = date('Y-m-d', strtotime($date.'+7 day'));
   
    unset($this->erreurs['auction_finishDate']);

    if($auction_finishDate < $minDate){
       $this->erreurs['auction_finishDate'] = "Les enchères doivent durée au moins 7 jours.";
    }

    $this->auction_finishDate = $auction_finishDate;
  }  

   /**
   * Mutateur de la propriété auction_user_id 
   * @param int $auction_user_id
   * @return $this
   */    
  public function setAuction_user_id($auction_user_id) {
    $this->auction_user_id = $auction_user_id; 
  }   

   /**
   * Mutateur de la propriété auction_status_id 
   * @param int $auction_status_id
   * @return $this
   */    
  public function setAuction_status_id($auction_status_id) {
    unset($this->erreurs['auction_status_id']);

    if( !($auction_status_id > 0)){
        $this->erreurs['auction_status_id'] = "Doit être un chiffre supérieur à 0.";
      }
      $this->auction_status_id = $auction_status_id; 
  }   




}
