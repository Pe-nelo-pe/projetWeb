<?php

/**
 * Classe Contrôleur des requêtes de l'application admin
 */

class Admin extends Routeur {

  private $entite;
  private $action;
  private $user_id;

  private $oUser;

  private $methodes = [
    'user' => [
      'a' => ['nom'=>'ajouterUser', 'droits'=>[User::PROFIL_ADMINISTRATEUR]],
      'm' => ['nom'=>'modifierUser', 'droits'=>[User::PROFIL_ADMINISTRATEUR]],
      's' => ['nom'=>'supprimerUser', 'droits'=>[User::PROFIL_ADMINISTRATEUR]],
      'd' => ['nom'=>'deconnecter'],
    ]
  ];
    

  private $classRetour = "fait";
  private $messageRetourAction = "";

  /**
   * Constructeur qui initialise le contexte du contrôleur  
   */  
  public function __construct() {
    $this->entite    = $_GET['entite']    ?? 'user';
    $this->action    = $_GET['action']    ?? 'l';
    $this->user_id = $_GET['user_id'] ?? null;
    $this->film_id  = $_GET['film_id']  ?? null;
    $this->oRequetesSQL = new RequetesSQL;
  }

  /**
   * Gérer l'interface d'administration 
   */  
  public function gererAdmin() {
    if (isset($_SESSION['oUser'])) {
      $this->oUser = $_SESSION['oUser'];
      if (isset($this->methodes[$this->entite])) {
        if (isset($this->methodes[$this->entite][$this->action])) {
          $methode = $this->methodes[$this->entite][$this->action]['nom'];
          if(isset($this->methodes[$this->entite][$this->action]['droits'])){
            $droits = $this->methodes[$this->entite][$this->action]['droits'];
            foreach ($droits as $value) {
              if($value === $this->oUser->user_profil){
                $this->$methode();
                exit;
              }
              throw new Exception(self::FORBIDDEN);
            }
          } 
          else {
            $this->$methode();
          }
        } else {
          throw new Exception("L'action $this->action de l'entité $this->entite n'existe pas.");
        }
      } else {
        throw new Exception("L'entité $this->entite n'existe pas.");
      }
    } else {
      $this->connecter();
    }
  }

  /**
   * Connecter un user
   */
  public function connecter() {
    $messageErreurConnexion = ""; 
    if (count($_POST) !== 0) {
      $user = $this->oRequetesSQL->connecter($_POST);
      if ($user !== false) {
        $_SESSION['oUser'] = new User($user);
        $this->oUser = $_SESSION['oUser'];
      } else {
        $messageErreurConnexion = "Courriel ou mot de passe incorrect.";
      }
    }
    
    (new Vue)->generer('vAdminUserConnecter',
            array(
              'titre'                  => 'Connexion',
              'messageErreurConnexion' => $messageErreurConnexion
            ),
            'gabarit-admin-min');
  }

  /**
   * Déconnecter un user
   */
  public function deconnecter() {
    unset ($_SESSION['oUser']);
    $this->connecter();
  }


   /**
   * Modification du mot de passe par le bouton générer mdp
   */
  public function modificationMDP() {

    $oUser = new User(["user_id"=>$this->user_id]);
    //$oUser->genererMdp();

    if ($this->oRequetesSQL->modificationMDP(['user_id'=> $oUser->user_id, 'user_mdp'=> $oUser->user_mdp])) {
       
       $newMDP= $oUser->user_mdp;
       
       $oUser = $this->oRequetesSQL->getUser($this->user_id);
       $oUser["user_mdp"] = $newMDP;

      // $retour = (new GestionCourriel)->envoyerMdp($oUser);
       
       $this->messageRetourAction = "Modification du mot de passe de l'user numéro $this->user_id effectuée. Courriel envoyé à ". $oUser["user_courriel"]. ".<br>";
      // if (ENV === "DEV")  $this->messageRetourAction .= "<a href=\"$retour\">Message dans le fichier $retour</a>";
       
    } else {
      $this->classRetour = "erreur";
      $this->messageRetourAction = "Modification du mot de passe de l'user numéro $this->user_id non effectuée.";
    }
    
    $this->listerUsers();

    
  }



  /**
   * Lister les users
   */
  public function listerUsers() {

    $users = $this->oRequetesSQL->getUsers();

    (new Vue)->generer('vAdminUsers',
            array(
              'oUser'        => $this->oUser,
              'titre'               => 'Gestion des users',
              'users'        => $users,
              'classRetour'         => $this->classRetour, 
              'messageRetourAction' => $this->messageRetourAction
            ),
            'gabarit-admin');
  }

  /**
   * Ajouter un user
   */
  public function ajouterUser() {
    $user  = [];
    $erreurs = [];
    if (count($_POST) !== 0) {
      $user = $_POST;
      $oUser = new User($user); 
      $erreurs = $oUser->erreurs;
      if (count($erreurs) === 0) { 
        //$oUser->genererMdp();
        $user_id = $this->oRequetesSQL->ajouterUser([
          'user_nom'    => $oUser->user_nom,
          'user_prenom' => $oUser->user_prenom,
          'user_courriel' => $oUser->user_courriel,
          'user_profil' => $oUser->user_profil,
          'user_mdp' => $oUser->user_mdp
        ]);
        if ( $user_id > 0) { 
         // $retour = (new GestionCourriel)->envoyerMdp($oUser);
          $this->messageRetourAction = "Ajout de l'user numéro $user_id effectuée. Courriel envoyé à " . $oUser->user_courriel. ".<br>";
         // if (ENV === "DEV")  $this->messageRetourAction .= "<a href=\"$retour\">Message dans le fichier $retour</a>";
        } else {
          $this->classRetour = "erreur";
          $this->messageRetourAction = "Ajout de l'user non effectué.";
        }
        $this->listerUsers(); 
        exit;
      }
    }
    
    (new Vue)->generer('vAdminUserAjouter',
            array(
              'oUser' => $this->oUser,
              'titre'        => 'Ajouter un user',
              'user'  => $user,
              'erreurs'      => $erreurs
            ),
            'gabarit-admin');
  }

  /**
   * Modifier un user identifié par sa clé dans la propriété user_id
   */
  public function modifierUser() {
    if (count($_POST) !== 0) {
      $user = $_POST;
      $oUser = new User($user);
      $erreurs = $oUser->erreurs;
      if (count($erreurs) === 0) {
        if($this->oRequetesSQL->modifierUser([
          'user_id'     => $oUser->user_id,
          'user_nom'    => $oUser->user_nom,
          'user_prenom' => $oUser->user_prenom,
          'user_courriel' => $oUser->user_courriel,
          'user_profil' => $oUser->user_profil
        ])) {
          $this->messageRetourAction = "Modification de l'user numéro $this->user_id effectuée.";
        } else {
          $this->classRetour = "erreur";
          $this->messageRetourAction = "modification de l'user numéro $this->user_id non effectuée.";
        }
        $this->listerUsers();
        exit;
      }

    } else {
      $user  = $this->oRequetesSQL->getUser($this->user_id);
      $erreurs = [];
    }
    
    (new Vue)->generer('vAdminUserModifier',
            array(
              'oUser' => $this->oUser,
              'titre'        => "Modifier l'user numéro $this->user_id",
              'user'  => $user,
              'erreurs'      => $erreurs
            ),
            'gabarit-admin');
  }
  
  /**
   * Supprimer un user identifié par sa clé dans la propriété user_id
   */
  public function supprimerUser() {
    if ($this->oRequetesSQL->supprimerUser($this->user_id)) {
      $this->messageRetourAction = "Suppression de l'user numéro $this->user_id effectuée.";
    } else {
      $this->classRetour = "erreur";
      $this->messageRetourAction = "Suppression de l'user numéro $this->user_id non effectuée.";
    }
    $this->listerUsers();
  }

  
  /**
   * Lister les films
   */
  public function gestionFilms() {
   
    (new Vue)->generer('vAdminUsers',
            array(
              'oUser'        => $this->oUser,
              'titre'               => 'Gestion des films',
              'messageRetourAction' => "Développement en cours"
            ),
            'gabarit-admin');
  }

  /**
   * Lister les films
   */
  public function listerFilms() {
    throw new Exception("Développement en cours.");
  }

  /**
   * Ajouter un film
   */
  public function ajouterFilm() {
    throw new Exception("Développement en cours.");
  }

  /**
   * Modifier un film identifié par sa clé dans la propriété film_id
   */
  public function modifierFilm() {
    throw new Exception("Développement en cours.");
  }
  
  /**
   * Supprimer un film identifié par sa clé dans la propriété film_id
   */
  public function supprimerFilm() {
    throw new Exception("Développement en cours.");
  }
}