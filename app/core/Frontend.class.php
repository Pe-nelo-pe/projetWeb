<?php

/**
 * Classe Contrôleur des requêtes de l'interface frontend
 * 
 */

class Frontend extends Routeur {


  
  /**
   * Constructeur qui initialise des propriétés de la classe
   * 
   */
  public function __construct() {
    $this->oRequetesSQL = new RequetesSQL;
  }


  /**
   * Affichage accueil
   * 
   */  
  public function viewHome() {
    $user=[];
    if (isset($_SESSION['oUser'])) {
      $user = $this->oUser = $_SESSION['oUser'];
    }
    
    $auctions = $this->oRequetesSQL->getAuctionsHome();
    (new Vue)->generer("accueil",
            array(
              'auctions'  => $auctions,
              'user'  => $user,
            ),
            "gabarit-frontend");
  }

  // /**
  //  * Lister les films à l'affiche
  //  * 
  //  */  
  // public function listerAlaffiche() {
  //   $films = $this->oRequetesSQL->getFilms('enSalle');
  //   (new Vue)->generer("vListeFilms",
  //           array(
  //             'titre'  => "À l'affiche",
  //             'films' => $films
  //           ),
  //           "gabarit-frontend");
  // }

  // /**
  //  * Lister les films diffusés prochainement
  //  * 
  //  */  
  // public function listerProchainement() {
  //   $films = $this->oRequetesSQL->getFilms('prochainement');
  //   (new Vue)->generer("vListeFilms",
  //           array(
  //             'titre'  => "Prochainement",
  //             'films' => $films
  //           ),
  //           "gabarit-frontend");
  // }

  // /**
  //  * Voir les informations d'un film
  //  * 
  //  */  
  // public function voirFilm() {
  //   $film = false;
  //   if (!is_null($this->film_id)) {
  //     $film = $this->oRequetesSQL->getFilm($this->film_id);
  //     $realisateurs = $this->oRequetesSQL->getRealisateursFilm($this->film_id);
  //     $pays         = $this->oRequetesSQL->getPaysFilm($this->film_id);
  //     $acteurs      = $this->oRequetesSQL->getActeursFilm($this->film_id);

  //     // si affichage avec vFilm2.twig
  //     // =============================
  //     // $seances      = $this->oRequetesSQL->getSeancesFilm($this->film_id); 

  //     // si affichage avec vFilm.twig
  //     // ============================
  //     $seancesTemp  = $this->oRequetesSQL->getSeancesFilm($this->film_id);
  //     $seances = [];
  //     foreach ($seancesTemp as $seance) {
  //       $seances[$seance['seance_date']]['jour']     = $seance['seance_jour'];
  //       $seances[$seance['seance_date']]['heures'][] = $seance['seance_heure'];
  //     }
  //   }
  //   if (!$film) throw new Exception("Film inexistant.");

  //   (new Vue)->generer("vFilm",
  //           array(
  //             'titre'        => $film['film_titre'],
  //             'film'         => $film,
  //             'realisateurs' => $realisateurs,
  //             'pays'         => $pays,
  //             'acteurs'      => $acteurs,
  //             'seances'      => $seances
  //           ),
  //           "gabarit-frontend");
  // }
}