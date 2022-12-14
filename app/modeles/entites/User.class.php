<?php

/**
 * Classe de l'entité User
 *
 */
class User
{
  private $user_id;
  private $user_lastName;
  private $user_firstName;
  private $user_email;
  private $user_password;
  private $user_status_id;
  private $user_address;
  private $user_city;
  private $user_zipCode;

  const PROFIL_ADMINISTRATEUR = "administrateur";
  const PROFIL_USER = "user";

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
  public function getUser_id()       { return $this->user_id; }
  public function getUser_lastName() { return $this->user_lastName; }
  public function getUser_firstName(){ return $this->user_firstName; }
  public function getUser_email()    { return $this->user_email; }
  public function getUser_password() { return $this->user_password; }
  public function getUser_status_id(){ return $this->user_status_id; }
  public function getUser_address()  { return $this->user_address; }
  public function getUser_city()     { return $this->user_city; }
  public function getUser_zipCode()  { return $this->user_zipCode; }
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
  public function setUser_id($user_id) {
    $this->user_id = $user_id; 
  }    

  /**
   * Mutateur de la propriété user_lastName 
   * @param string $user_lastName
   * @return $this
   */    
  public function setUser_lastName($user_lastName) {
    unset($this->erreurs['user_lastName']);
    $user_lastName = trim($user_lastName);
    
    if (!$user_lastName ) {
      $this->erreurs['user_lastName'] = "Champs obligatoire.";
    }

    $this->user_lastName = $user_lastName; 
    return $this;

  }

  /**
   * Mutateur de la propriété user_firstName 
   * @param string $user_firstName
   * @return $this
   */    
  public function setUser_firstName($user_firstName) {
    unset($this->erreurs['user_firstName']);
    $user_firstName = trim($user_firstName);
   
    if (!$user_firstName) {
      $this->erreurs['user_firstName'] = "Champs obligatoire.";
    }
    $this->user_firstName = $user_firstName; 
    return $this;
  }

  /**
   * Mutateur de la propriété user_email
   * @param string $user_email
   * @return $this
   */    
  public function setUser_email($user_email) {
    unset($this->erreurs['user_email']);
    $user_email = trim(strtolower($user_email));
    if (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
      $this->erreurs['user_email'] = "Format invalide.";
    }
    $this->user_email = $user_email;
    
    return $this;
  }

  /**
   * Mutateur de la propriété user_status_id
   * @param string $user_status_id
   * @return $this
   */    
  public function setUser_status_id($user_status_id) {
    $this->user_status_id = $user_status_id; 
  }


   /**
   * Mutateur de la propriété user_password
   * @param string $user_password
   * @return $this
   */    
  public function setUser_password($user_password) {
   unset($this->erreurs['user_password']);

   $user_password = trim($user_password);
   
    $regex = "/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/";

    if (!preg_match($regex, $user_password)) {
      $this->erreurs['user_password'] = "Doit contenir au moins 8 caractères avec une lettre majuscule, une lettre minuscule, un chiffre et un de ces caractères spéciaux # ? ! @ $ % ^ & * -.";
    }
   
    $this->user_password = $user_password; 
    return $this;

  
  }

  
   /**
   * Mutateur de la propriété user_address
   * @param string $user_address
   * @return $this
   */    
  public function setUser_address($user_address) {
    $this->user_address = $user_address; 
  }

  
   /**
   * Mutateur de la propriété user_city
   * @param string $user_city
   * @return $this
   */    
  public function setUser_city($user_city) {
    $this->user_city = $user_city; 
  }

  
   /**
   * Mutateur de la propriété user_zipCode
   * @param string $user_zipCode
   * @return $this
   */    
  public function setUser_zipCode($user_zipCode) {
    $this->user_zipCode = $user_zipCode; 
  }

}