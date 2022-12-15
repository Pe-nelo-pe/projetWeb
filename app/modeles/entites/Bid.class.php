<?php

/**
 * Classe de l'entité Auction
 *
 */
class Bid
{
  private $bid_id;
  private $bid_auction_id;
  private $bid_user_id;
  private $bid_amount;


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
  public function getBid_id()         { return $this->bid_id; }
  public function getBid_auction_id() { return $this->bid_auction_id; }
  public function getBid_user_id()    { return $this->bid_user_id; }
  public function getBid_amount()     { return $this->bid_amount; }
  
  public function getErreurs()        { return $this->erreurs; }
  
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
   * Mutateur de la propriété bid_id 
   * @param int $bid_id
   * @return $this
   */    
  public function setBid_id($bid_id) {
    $this->bid_id = $bid_id; 
  }    
  /**
    * Mutateur de la propriété bid_auction_id 
    * @param int $bid_auction_id
    * @return $this
    */    
  public function setBid_auction_id($bid_auction_id) {
    $this->bid_auction_id = $bid_auction_id;
    return $this;
  } 

  /**
   * Mutateur de la propriété bid_user_id 
   * @param int $bid_user_id
   * @return $this
   */    
  public function setBid__user_id($bid_user_id) {
    $this->bid_user_id = $bid_user_id; 
  } 
  

  /**
   * Mutateur de la propriété bid_amount 
   * @param int $bid_amount
   * @return $this
   */    
  public function setBid_amount($bid_amount) {
    unset($this->erreurs['bid_amount']);

    $id = $this->bid_auction_id ;

    $oRequetesSQL = new RequetesSQL;
    $maxValue = $oRequetesSQL->getMaxBid($id);
    $minValue = $oRequetesSQL->getMinAuction($id);

    $maxValue = $maxValue["maxAmount"];
    $minValue = $minValue["minAmount"];

    if($bid_amount <= $maxValue){
      $this->erreurs['bid_amount'] = "La nouvelle mise doit être plus haute que la dernière mise faite";
    } else if($bid_amount < $minValue){
      $this->erreurs['bid_amount'] = "La nouvelle mise doit être plus haute que la mise minimale";

    }
    $this->bid_amount = $bid_amount; 
  } 

}