<?php

/**
 * Classe Routeur
 * analyse l'uri et exécute la méthode associée  
 *
 */

class Routeur {

  private $routes = [
    ["",          "Frontend", "viewHome"],
    ["accueil",   "Frontend", "viewHome"],
    ["session",   "Session",  "gestionConnexion"],
    ["auctions",  "Auctions", "gererAuction"],
 
  ];

  protected $oRequetesSQL; 


  const BASE_URI = '/projetWeb/'; 

  const FORBIDDEN       = 'HTTP 403';
  const ERROR_NOT_FOUND = 'HTTP 404';

  /**
   * Constructeur qui valide l'URI,
   * instancie un contrôleur et exécute une méthode de ce contrôleur,
   * chaque URI valide est associé à un contrôleur et une méthode de ce contrôleur
   */
  public function __construct() {
    try {

  
      $uri =  $_SERVER['REQUEST_URI'];
      if (strpos($uri, '?')) $uri = strstr($uri, '?', true);

      foreach ($this->routes as $route) { // 

        $routeUri     = self::BASE_URI.$route[0];
        $routeClasse  = $route[1];
        $routeMethode = $route[2];
        
        if ($routeUri ===  $uri) {
       
          $oControleur = new $routeClasse;
          $oControleur->$routeMethode();  
          exit;
        }
      }
 
      throw new Exception(self::ERROR_NOT_FOUND);
    }
    catch (Error | Exception $e) {
      $this->erreur($e);
    }
  }

  /**
   * Méthode qui envoie un compte-rendu d'erreur
   * @param Exception $e 
   */
  public function erreur($e) {
    $message = $e->getMessage();
    if ($message == self::FORBIDDEN) {
      header("HTTP/1.1 403 Forbidden", 403);
    } else if ($message == self::ERROR_NOT_FOUND) {
      header('HTTP/1.1 404 Not Found');
      (new Vue)->generer('vErreur404', [], 'gabarit-erreur');
    } else {
      header('HTTP/1.1 500 Internal Server Error');
      (new Vue)->generer('vErreur500',
            ['message' => $message, 'fichier' => $e->getFile(), 'ligne' => $e->getLine()],
            'gabarit-erreur');
    }
    exit;
  }
}