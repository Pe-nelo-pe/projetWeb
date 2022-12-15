<?php

/**
 * Classe Contrôleur des requêtes des utilisateurs et leur session
 */
 
class Session extends Routeur {
 
  private $entite;
  private $action;
  private $user_id;

  private $oUser;

  private $methodes = [
     'user' => [
        'a' => ['nom'=>'addUser'],
        'as'=> ['nom'=>'afterSign'],
        'c' => ['nom'=>'vAccount'],
        'u' => ['nom'=>'updateUser'],
        's' => ['nom'=>'connecter'],
        'd' => ['nom'=>'deconnecter'],
     ]
  ];
    

  private $messageRetourAction = "";

 

  /**
   * Constructeur qui initialise le contexte du contrôleur  
   */  
  public function __construct() {
    $this->entite    = $_GET['entite']    ?? 'user';
    $this->action    = $_GET['action']    ?? 'as';
    $this->user_id = $_GET['user_id'] ?? null;
    $this->oRequetesSQL = new RequetesSQL;
  }


  /**
   * Gérer l'interface de la session
   */  
  public function gestionConnexion() {
 
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
 
      //$this->connecter();
    }


  /**
   * Connecter un user
   */
  public function connecter() {
    $user = "";
    $messageErreurConnexion = ""; 
    if (count($_POST) !== 0) {
      $user = $this->oRequetesSQL->connecter($_POST);
      if ($user !== false) {
        $_SESSION['oUser'] = new User($user);
        $this->oUser = $_SESSION['oUser'];

        $this->vAccount(); 
        exit;
      } else {

        $messageErreurConnexion = "Courriel ou mot de passe incorrect.";
      }

    }
    
    (new Vue)->generer('vConnexion',
            array(
              'titre'                  => 'Connexion',
              'messageErreurConnexion' => $messageErreurConnexion
            ),
            'gabarit-frontend');
  }


  /**
   * Déconnecter un user
   */
  public function deconnecter() {
    unset ($_SESSION['oUser']);
    
    $frontend = new Frontend();
    $frontend-> viewHome();
  }


  /**
   * Redirection après l'inscription d'une nouvel utilisateur
   */
  public function afterSign() {

    if (isset($_SESSION['oUser'])) {
     $this->oUser = $_SESSION['oUser'];
    }


    (new Vue)->generer('vNewUser',
            array(
              'oUser'               => $this->oUser,
              'messageRetourAction' => $this->messageRetourAction
            ),
            'gabarit-frontend');
  }


  /**
   * Gestion du compte
   */
  public function vAccount() {
    if (isset($_SESSION['oUser'])) {
     $user = $this->oUser = $_SESSION['oUser'];

    (new Vue)->generer('vAccount',
          array(
            'titre'               => 'Votre compte',
            'user'                => $user,
            'messageRetourAction' => $this->messageRetourAction
          ),
          'gabarit-frontend');
    }
  }


  /**
   * Ajouter un user
   */
  public function addUser() {
    $user  = [];
    $erreurs = [];

    if (count($_POST) !== 0) {
      $user = $_POST;
      $oUser = new User($user); 
      $erreurs = $oUser->erreurs;

      if (count($erreurs) === 0) { 
        $user_id = $this->oRequetesSQL->addUser([
          'user_lastName'    => $oUser->user_lastName,
          'user_firstName' => $oUser->user_firstName,
          'user_email' => $oUser->user_email,
          'user_password' => $oUser->user_password,
          'user_address' => $oUser->user_address,
          'user_city' => $oUser->user_city,
          'user_zipCode' => $oUser->user_zipCode 
        ]);
       
        $this->afterSign(); 
       exit;
      }
    }
    
    (new Vue)->generer('vSignUp',
            array(
              'oUser'        => $this->oUser, 
              'titre'        => 'Inscription',
              'user'         => $user,
              'erreurs'      => $erreurs
            ),
            'gabarit-frontend');
  }

  /**
   * Modifier un user
   */
  public function updateUser() {
    if (isset($_SESSION['oUser'])) {
     $users = $this->oUser = $_SESSION['oUser'];
    }
    if (count($_POST) !== 0) {

      $user = $_POST;
      $oUser = new User($user);
      $erreurs = $oUser->erreurs;

      if (count($erreurs) === 0) {
        
        if($this->oRequetesSQL->updateUser([
          'user_id'        => $users->user_id,
          'user_lastName'  => $oUser->user_lastName,
          'user_firstName' => $oUser->user_firstName,
          'user_email'     => $oUser->user_email,
          'user_address'   => $oUser->user_address,
          'user_city'      => $oUser->user_city,
          'user_zipCode'   => $oUser->user_zipCode,
        ])) { 
          $this->messageRetourAction = "Modification effectuée.";
         (new Vue)->generer('vAccount',
            array(
              // 'oUser'   => $this->oUser,
              'titre'   => "Modification du compte",
              'user'    => $users,
              'erreurs' => $erreurs,
              'messageRetourAction' => $this->messageRetourAction
            ),
            'gabarit-frontend');  
        } else {

          $this->messageRetourAction = "Modification non effectuée.";
        }

      }

    } else {
      $user  = $this->oRequetesSQL->getUser($this->user_id);
      $erreurs = [];
    }
    
    (new Vue)->generer('vUpdateUser',
            array(
              // 'oUser'   => $this->oUser,
              'titre'   => "Modification du compte",
              'user'    => $users,
              'erreurs' => $erreurs,
              'messageRetourAction' => $this->messageRetourAction
            ),
            'gabarit-frontend');
  }
  
  
}