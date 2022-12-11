<?php

/**
 * Classe de l'entité Stamp
 *
 */
class Image
{
  private $image_id;
  private $image_name;
  private $image_link;
 


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
  public function getImage_id()       { return $this->image_id; }
  public function getImage_name()       { return $this->image_name; }
  public function getImage_link() { return $this->image_link; }
 
  
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
  public function setImage_id($image_id) {
    
    $this->image_id = $image_id; 
  }    

  /**
   * Mutateur de la propriété user_id 
   * @param int $user_id
   * @return $this
   */    
  public function setImage_name($image_name) {
    
    $this->image_name = $image_name; 
  }  

 /**
   * Mutateur de la propriété stamp_name 
   * @param int $stamp_name
   * @return $this
   */    
  public function setImage_link($image_link) {

    unset($this->erreurs['image_link']);

    if (!$image_link) {
      $this->erreurs['image_link'] = "Champs obligatoire.";
    }
    
    $this->image_link = $image_link; 
    return $this;
 
  }

}