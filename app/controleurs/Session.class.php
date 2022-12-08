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
    //   //'l' => ['nom'=>'listerUsers', 'droits'=>[User::PROFIL_ADMINISTRATEUR]],
      'a' => ['nom'=>'addUser'],
      'as' => ['nom'=>'afterSign'],
      'c' => ['nom'=>'vAccount'],
    //   'm' => ['nom'=>'modifierUser', 'droits'=>[User::PROFIL_ADMINISTRATEUR]],
    //   's' => ['nom'=>'supprimerUser', 'droits'=>[User::PROFIL_ADMINISTRATEUR]],
    //   //'gf'=> ['nom'=>'gestionFilms', 'droits'=>[User::PROFIL_ADMINISTRATEUR, User::PROFIL_EDITEUR]],
       's' => ['nom'=>'connecter'],
       'd' => ['nom'=>'deconnecter'],
    //   //'mmdp' => ['nom'=>'modificationMDP', 'droits'=>[User::PROFIL_ADMINISTRATEUR]],
     ]
  ];
    

  private $classRetour = "fait";
  private $messageRetourAction = "";

  /**
   * Constructeur qui initialise le contexte du contrôleur  
   */  
  public function __construct() {
    $this->entite    = $_GET['entite']    ?? 'user';
    $this->action    = $_GET['action']    ?? 'as';
    $this->user_id = $_GET['user_id'] ?? null;
    //$this->film_id  = $_GET['film_id']  ?? null;
    $this->oRequetesSQL = new RequetesSQL;
  }

  /**
   * Gérer l'interface d'administration 
   */  
  public function gestionConnexion() {
  
    // if (isset($_SESSION['oUser'])) {
    //   $this->oUser = $_SESSION['oUser'];
    //   if (isset($this->methodes[$this->entite])) {
    //     if (isset($this->methodes[$this->entite][$this->action])) {
    //       $methode = $this->methodes[$this->entite][$this->action]['nom'];
    //       echo $methode;
    //       // if(isset($this->methodes[$this->entite][$this->action]['droits'])){
    //       //   $droits = $this->methodes[$this->entite][$this->action]['droits'];
    //       //   foreach ($droits as $value) {
    //       //     if($value === $this->oUser->user_profil){
    //             $this->$methode();
    //             //exit;
    //       //     }
    //       //     throw new Exception(self::FORBIDDEN);
    //       //   }
    //       // } 
    //       // else {
    //       //   $this->$methode();
    //       // }
    //     } else {
    //       throw new Exception("L'action $this->action de l'entité $this->entite n'existe pas.");
    //     }
    //   } else {
    //     throw new Exception("L'entité $this->entite n'existe pas.");
    //   }
    //  } else if($this->methodes[$this->entite][$this->action]['nom'] == 'addUser') {
    
    //   $this-> adduser();
    // } else {
      
    //   $this->$methode();
    // }

     //if (isset($_SESSION['oUser'])) {

     // $this->oUser = $_SESSION['oUser'];
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
    //} else {
      $this->connecter();
    }
  //}

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
              'titre'         => 'Connexion',
              'messageErreurConnexion' => $messageErreurConnexion
            ),
            'gabarit-frontend');
  }

  /**
   * Déconnecter un user
   */
  public function deconnecter() {
    unset ($_SESSION['oUser']);
    $this->afterSign();
  }





  /**
   * Lister les users
    */
  // public function listerUsers() {

  //   $users = $this->oRequetesSQL->getUsers();

  //   (new Vue)->generer('vAdminUsers',
  //           array(
  //             'oUser'        => $this->oUser,
  //             'titre'               => 'Gestion des users',
  //             'users'        => $users,
  //             'classRetour'         => $this->classRetour, 
  //             'messageRetourAction' => $this->messageRetourAction
  //           ),
  //           'gabarit-admin');
  // }


    /**
   * Lister les users
   */
  public function afterSign() {
    if (isset($_SESSION['oUser'])) {
     $this->oUser = $_SESSION['oUser'];
    }

    //$users = $this->oRequetesSQL->getUsers();

    (new Vue)->generer('accueil',
            array(
              'oUser'               => $this->oUser,
              'titre'               => 'Accueil',
              //'users'        => $users,
              'classRetour'         => $this->classRetour, 
              'messageRetourAction' => $this->messageRetourAction
            ),
            'gabarit-frontend');
  }


  public function vAccount() {
    if (isset($_SESSION['oUser'])) {
     $this->oUser = $_SESSION['oUser'];
    }

    $user = $this->oUser;

  //$users = $this->oRequetesSQL->getUsers();

    (new Vue)->generer('vAccount',
          array(
//            'oUser'               => $this->oUser,
            'titre'               => 'Votre compte',
            'user'                => $user,
            'classRetour'         => $this->classRetour, 
            'messageRetourAction' => $this->messageRetourAction
          ),
          'gabarit-frontend');
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
          $this->classRetour = "erreur";
          $this->messageRetourAction = "Ajout du membre non effectué.";
        }
        $this->afterSign(); 
       exit;
      }
    }
    
    (new Vue)->generer('vSignUp',
            array(
              'oUser'         => $this->oUser, 
              'titre'        => 'Inscription',
              'user'          => $user,
              'erreurs'      => $erreurs
            ),
            'gabarit-frontend');
  }

  // /**
  //  * Modifier un user identifié par sa clé dans la propriété user_id
  //  */
  // public function modifierUser() {
  //   if (count($_POST) !== 0) {
  //     $user = $_POST;
  //     $oUser = new User($user);
  //     $erreurs = $oUser->erreurs;
  //     if (count($erreurs) === 0) {
  //       if($this->oRequetesSQL->modifierUser([
  //         'user_id'     => $oUser->user_id,
  //         'user_nom'    => $oUser->user_nom,
  //         'user_prenom' => $oUser->user_prenom,
  //         'user_courriel' => $oUser->user_courriel,
  //         'user_profil' => $oUser->user_profil
  //       ])) {
  //         $this->messageRetourAction = "Modification de l'user numéro $this->user_id effectuée.";
  //       } else {
  //         $this->classRetour = "erreur";
  //         $this->messageRetourAction = "modification de l'user numéro $this->user_id non effectuée.";
  //       }
  //       $this->listerUsers();
  //       exit;
  //     }

  //   } else {
  //     $user  = $this->oRequetesSQL->getUser($this->user_id);
  //     $erreurs = [];
  //   }
    
  //   (new Vue)->generer('vAdminUserModifier',
  //           array(
  //             'oUser' => $this->oUser,
  //             'titre'        => "Modifier l'user numéro $this->user_id",
  //             'user'  => $user,
  //             'erreurs'      => $erreurs
  //           ),
  //           'gabarit-admin');
  // }
  
  // /**
  //  * Supprimer un user identifié par sa clé dans la propriété user_id
  //  */
  // public function supprimerUser() {
  //   if ($this->oRequetesSQL->supprimerUser($this->user_id)) {
  //     $this->messageRetourAction = "Suppression de l'user numéro $this->user_id effectuée.";
  //   } else {
  //     $this->classRetour = "erreur";
  //     $this->messageRetourAction = "Suppression de l'user numéro $this->user_id non effectuée.";
  //   }
  //   $this->listerUsers();
  // }

  
}