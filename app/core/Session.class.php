<?php

/**
 * Classe Contrôleur des requêtes de l'application admin
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
   * Gérer l'interface d'administration 
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
 
      $this->connecter();
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

        $this->vAccount($this->oUser); 
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
   * Lister les users
   */
  public function afterSign() {

    if (isset($_SESSION['oUser'])) {
     $this->oUser = $_SESSION['oUser'];
    }


    (new Vue)->generer('welcomeUser',
            array(
              'oUser'               => $this->oUser,
              'messageRetourAction' => $this->messageRetourAction
            ),
            'gabarit-frontend');
  }


  
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
          'user_zipCode' => $oUser->user_zipCode,
        ]);
        if ( $user_id > 0) { 
          $this->messageRetourAction = "Ajout du membre # $user_id effectuée.";
        } else {
          $this->messageRetourAction = "Ajout du membre non effectué.";
        }
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
   * Modifier un user identifié par sa clé dans la propriété user_id
   */
  public function updateUser() {
    if (isset($_SESSION['oUser'])) {
     $users = $this->oUser = $_SESSION['oUser'];
    }
    if (count($_POST) !== 0) {
      //echo "id " .$users->user_id;
      $user = $_POST;
      $oUser = new User($user);
      $erreurs = $oUser->erreurs;
      //print_r($oUser);
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
          echo $oUser->user_firstName;
          print_r($user);
          $_SESSION['oUser']['user_firstName'] = $oUser->user_firstName;
        } else {

          $this->messageRetourAction = "Modification non effectuée.";
        }
        //$this->vAccount();
        //exit;
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