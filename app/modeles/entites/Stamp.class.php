<?php

/**
 * Classe de l'entité Stamp
 *
 */
class Stamp
{
  private $stamp_id;
  private $stamp_name;
  private $stamp_description;
  private $stamp_price;
  private $stamp_date;
  private $stamp_certified;
  private $stamp_format;
  private $stamp_color;
  private $stamp_location_id;
  private $stamp_image_id;
  private $stamp_condition_id;
  private $stamp_rareness_id;
  private $stamp_auction_id;



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
  public function getStamp_id()       { return $this->stamp_id; }
  public function getStamp_name() { return $this->stamp_name; }
  public function getStamp_description(){ return $this->stamp_description; }
  public function getStamp_price()    { return $this->stamp_price; }
  public function getStamp_date() { return $this->stamp_date; }
  public function getStamp_certified(){ return $this->stamp_certified; }
  public function getStamp_format()  { return $this->stamp_format; }
  public function getStamp_color()     { return $this->stamp_color; }
  public function getStamp_location_id()  { return $this->stamp_location_id; }
  public function getStamp_image_id()  { return $this->stamp_image_id; }
  public function getStamp_condition_id()  { return $this->stamp_condition_id; }
  public function getStamp_rareness_id()  { return $this->stamp_rareness_id; }
  public function getStamp_auction_id()  { return $this->stamp_auction_id; }
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
   * Mutateur de la propriété user_id 
   * @param int $user_id
   * @return $this
   */    
  public function setStamp_id($stamp_id) {
    
    $this->stamp_id = $stamp_id; 
  }    

 /**
   * Mutateur de la propriété stamp_name 
   * @param int $stamp_name
   * @return $this
   */    
  public function setStamp_name($stamp_name) {

     unset($this->erreurs['stamp_name']);

    $stamp_name = trim($stamp_name);
    $regExp = '/^[a-zÀ-ÖØ-öø-ÿ]{2,}( [a-zÀ-ÖØ-öø-ÿ]{2,})*$/i';

    if (!$stamp_name) {
      $this->erreurs['stamp_name'] = "Champs obligatoire.";
    }else if (!preg_match($regExp, $stamp_name)) {
      $this->erreurs['stamp_name'] = "Au moins 2 caractères alphabétiques pour chaque mot.";
    }
    $this->stamp_name = ucwords(strtolower($stamp_name));
    $this->stamp_name = $stamp_name; 
    return $this;
 
  }

  /**
   * Mutateur de la propriété stamp_description 
   * @param int $stamp_description
   * @return $this
   */    
  public function setStamp_description($stamp_description) {
     unset($this->erreurs['stamp_description']);

    //  if (!$stamp_description) {
    //   $this->erreurs['stamp_description'] = "Champs obligatoire.";
    // }

    $this->stamp_description = $stamp_description;
  }    


  /**
   * Mutateur de la propriété stamp_price 
   * @param int $stamp_price
   * @return $this
   */    
  public function setStamp_price($stamp_price) {
    unset($this->erreurs['stamp_price']);

     if (!$stamp_price) {
      $this->erreurs['stamp_price'] = "Champs obligatoire.";
    } else if(!(is_numeric($stamp_price)) || !($stamp_price > 0)){
      $this->erreurs['stamp_price'] = "Doit être supérieur à 0.";
    }
    $this->stamp_price = $stamp_price; 
  }    

   /**
   * Mutateur de la propriété user_id 
   * @param int $user_id
   * @return $this
   */    
  public function setStamp_date($stamp_date) {
    $currentDate = date('Y');

    $stamp_date = intval($stamp_date);
    
    unset($this->erreurs['stamp_date']);

    if(!is_int($stamp_date)){
      $this->erreurs['stamp_date'] = "Doit être être un chiffre entier";
    } 
    else if( $stamp_date < 1840 || $stamp_date > $currentDate){
      $this->erreurs['stamp_date'] = "Doit être 1840 et ".$currentDate;
    }
    $this->stamp_date = $stamp_date; 
  }   

   /**
   * Mutateur de la propriété stamp_certified 
   * @param int $stamp_certified
   * @return $this
   */    
  public function setStamp_certified($stamp_certified) {
    $this->stamp_certified = $stamp_certified; 
  }   

   /**
   * Mutateur de la propriété stamp_format 
   * @param int $stamp_format
   * @return $this
   */    
  public function setStamp_format($stamp_format) {
    
    $this->stamp_format = $stamp_format; 
  }   

   /**
   * Mutateur de la propriété stamp_color 
   * @param int $stamp_color
   * @return $this
   */    
  public function setStamp_color($stamp_color) {
    $this->stamp_color = $stamp_color; 
  }   

   /**
   * Mutateur de la propriété stamp_location_id 
   * @param int $stamp_location_id
   * @return $this
   */    
  public function setStamp_location_id($stamp_location_id) {
    unset($this->erreurs['stamp_location_id']);
echo $stamp_location_id.'<br><br>';
     if (!isset($stamp_location_id)) {
      echo'<br><br> vide <br><br>';
      $this->erreurs['stamp_location_id'] = "Champs obligatoire.";
    }
    $this->stamp_location_id = $stamp_location_id; 
  }   

   /**
   * Mutateur de la propriété stamp_image_id 
   * @param int $stamp_image_id
   * @return $this
   */    
  public function setStamp_image_id($stamp_image_id) {
    unset($this->erreurs['stamp_image_id']);

     if (!$stamp_image_id) {
      $this->erreurs['stamp_image_id'] = "Champs obligatoire.";
    }
    $this->stamp_image_id = $stamp_image_id; 
  }   

   /**
   * Mutateur de la propriété stamp_condition_id 
   * @param int $stamp_condition_id
   * @return $this
   */    
  public function setStamp_condition_id($stamp_condition_id) {
    unset($this->erreurs['stamp_condition_id']);

     if (!$stamp_condition_id) {
      $this->erreurs['stamp_condition_id'] = "Champs obligatoire.";
    }
    $this->stamp_condition_id = $stamp_condition_id; 
  }   

   /**
   * Mutateur de la propriété stamp_rareness_id 
   * @param int $stamp_rareness_id
   * @return $this
   */    
  public function setStamp_rareness_id($stamp_rareness_id) {
    unset($this->erreurs['stamp_rareness_id']);

     if (!$stamp_rareness_id) {
      $this->erreurs['stamp_rareness_id'] = "Champs obligatoire.";
    }
    $this->stamp_rareness_id = $stamp_rareness_id; 
  }   

   /**
   * Mutateur de la propriété stamp_auction_id 
   * @param int $stamp_auction_id
   * @return $this
   */    
  public function setStamp_auction_id($stamp_auction_id) {
    unset($this->erreurs['stamp_auction_id']);

     if (!$stamp_auction_id) {
      $this->erreurs['stamp_auction_id'] = "Champs obligatoire.";
    }
    $this->stamp_auction_id = $stamp_auction_id; 
  }   



}
