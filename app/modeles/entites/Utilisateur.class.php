<?php

/**
 * Classe de l'entité Utilisateur
 *
 */
class Utilisateur
{
  private $utilisateur_id;
  private $utilisateur_nom;
  private $utilisateur_prenom;
  private $utilisateur_courriel;
  private $utilisateur_mdp;
  private $utilisateur_profil;

  const PROFIL_ADMINISTRATEUR = "administrateur";
  const PROFIL_EDITEUR = "editeur";
  const PROFIL_UTILISATEUR = "utilisateur";

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
  public function getUtilisateur_id()       { return $this->utilisateur_id; }
  public function getUtilisateur_nom()      { return $this->utilisateur_nom; }
  public function getUtilisateur_prenom()   { return $this->utilisateur_prenom; }
  public function getUtilisateur_courriel() { return $this->utilisateur_courriel; }
  public function getUtilisateur_mdp()      { return $this->utilisateur_mdp; }
  public function getUtilisateur_profil()   { return $this->utilisateur_profil; }
  public function getErreurs()              { return $this->erreurs; }
  
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
   * Mutateur de la propriété utilisateur_id 
   * @param int $utilisateur_id
   * @return $this
   */    
  public function setUtilisateur_id($utilisateur_id) {
    $this->utilisateur_id = $utilisateur_id; // à remplacer avec des contrôles
  }    

  /**
   * Mutateur de la propriété utilisateur_nom 
   * @param string $utilisateur_nom
   * @return $this
   */    
  public function setUtilisateur_nom($utilisateur_nom) {
    unset($this->erreurs['utilisateur_nom']);
    $utilisateur_nom = trim($utilisateur_nom);
    $regExp = '/^[a-zÀ-ÖØ-öø-ÿ]{2,}( [a-zÀ-ÖØ-öø-ÿ]{2,})*$/i';
    if (!preg_match($regExp, $utilisateur_nom)) {
      $this->erreurs['utilisateur_nom'] = "Au moins 2 caractères alphabétiques pour chaque mot.";
    }
    $this->utilisateur_nom = ucwords(strtolower($utilisateur_nom));
    $this->utilisateur_nom = $utilisateur_nom; 
    return $this;

  }

  /**
   * Mutateur de la propriété utilisateur_prenom 
   * @param string $utilisateur_prenom
   * @return $this
   */    
  public function setUtilisateur_prenom($utilisateur_prenom) {
    unset($this->erreurs['utilisateur_prenom']);
    $utilisateur_prenom = trim($utilisateur_prenom);
    $regExp = '/^[a-zÀ-ÖØ-öø-ÿ]{2,}( [a-zÀ-ÖØ-öø-ÿ]{2,})*$/i';
    if (!preg_match($regExp, $utilisateur_prenom)) {
      $this->erreurs['utilisateur_prenom'] = "Au moins 2 caractères alphabétiques pour chaque mot.";
    }
    $this->utilisateur_prenom = ucwords(strtolower($utilisateur_prenom));
    $this->utilisateur_prenom = $utilisateur_prenom; 
    return $this;
  }

  /**
   * Mutateur de la propriété utilisateur_courriel
   * @param string $utilisateur_courriel
   * @return $this
   */    
  public function setUtilisateur_courriel($utilisateur_courriel) {
    unset($this->erreurs['utilisateur_courriel']);
    $utilisateur_courriel = trim(strtolower($utilisateur_courriel));
    if (!filter_var($utilisateur_courriel, FILTER_VALIDATE_EMAIL)) {
      $this->erreurs['utilisateur_courriel'] = "Format invalide.";
    }
    $this->utilisateur_courriel = $utilisateur_courriel;
    
    return $this;
  }

  /**
   * Mutateur de la propriété utilisateur_profil
   * @param string $utilisateur_profil
   * @return $this
   */    
  public function setUtilisateur_profil($utilisateur_profil) {
    $this->utilisateur_profil = $utilisateur_profil; // à remplacer avec des contrôles
  }

  /**
   * Génération d'un mot de passe aléatoire dans la propriété utilisateur_mdp
   * @return $this
   */    
  public function genererMdp() {
    //$mdp = "!a2Rt67&qsd"; // à remplacer par une génération aléatoire
    $length = 10;
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $mdp = substr(str_shuffle($chars),0,$length);
    $this->utilisateur_mdp = $mdp;
    return $this;
  }
}