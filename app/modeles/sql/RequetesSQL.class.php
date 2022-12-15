<?php

/**
 * Classe des requêtes SQL
 *
 */
class RequetesSQL extends RequetesPDO {


/**
   * Connecter un utilisateur
   * @param array $champs, tableau avec les champs utilisateur_courriel et utilisateur_mdp  
   * @return array|false ligne de la table, false sinon 
   */ 
  public function connecter($champs) {
    $this->sql = "SELECT user_id, user_lastName, user_firstName, user_email, user_status_id, user_address, user_zipCode, user_city FROM users WHERE user_email = :user_email AND user_password = SHA2(:user_password, 512)";

    return $this->getLignes($champs, RequetesPDO::UNE_SEULE_LIGNE);
  } 


  /**
   * Ajouter un utilisateur
   * @param array $champs tableau des champs de l'utilisateur 
   * @return string|boolean clé primaire de la ligne ajoutée, false sinon
   */ 
  public function addUser($champs) {
    $this->sql = ' INSERT INTO users SET user_lastName = :user_lastName, user_firstName = :user_firstName, user_email =:user_email, user_password = SHA2(:user_password, 512), user_address = :user_address, user_city = :user_city, user_zipCode = :user_zipCode, user_status_id = 1';
    return $this->CUDLigne($champs); 
  }
  
   /**
   * Modifier un utilisateur
   * @param array $champs tableau avec les champs à modifier et la clé user_id
   * @return boolean true si modification effectuée, false sinon
   */ 
  public function getUser($user_id) {
    $this->sql = 'SELECT user_lastName, user_firstName, user_email, user_address, user_city, user_zipCode from users
    WHERE user_id = :user_id';
    return $this->getLignes(['user_id' => $user_id], RequetesPDO::UNE_SEULE_LIGNE);
  }

  /**
   * Modifier un utilisateur
   * @param array $champs tableau avec les champs à modifier et la clé user_id
   * @return boolean true si modification effectuée, false sinon
   */ 
  public function updateUser($champs) {

    $this->sql = 'UPDATE users SET user_lastName = :user_lastName, user_firstName = :user_firstName, user_email =:user_email, user_address = :user_address, user_city = :user_city, user_zipCode = :user_zipCode
    WHERE user_id = :user_id';
    return $this->CUDLigne($champs);
  }

  /**
   * Supprimer une enchère
   * @param int $auction_id clé primaire
   * @return boolean true si suppression effectuée, false sinon
   */ 
  public function deleteAuction($auction_id) {
    $this->sql = 'DELETE FROM auctions 
      WHERE auction_id = :auction_id';
    return $this->CUDLigne(['auction_id' => $auction_id]); 
  }

  /**
   * Ajouter une enchère
   * @param array $champs tableau des champs de l'enchère 
   * @return string|boolean clé primaire de la ligne ajoutée, false sinon
   */ 
  public function addAuction($champs) {
    $this->sql = ' INSERT INTO auctions SET auction_name = :auction_name, auction_description = :auction_description, auction_startDate =:auction_startDate, auction_finishDate = :auction_finishDate, auction_price = :auction_price, auction_user_id = :auction_user_id, auction_status_id = :auction_status_id';
    return $this->CUDLigne($champs); 
  }


  /**
   * Ajouter une enchère
   * @param array $champs tableau des champs de l'enchère 
   * @return string|boolean clé primaire de la ligne ajoutée, false sinon
   */ 
  public function updateAuction($champs) {
 
    $this->sql = ' UPDATE auctions SET auction_name = :auction_name, auction_description = :auction_description, auction_startDate =:auction_startDate, auction_finishDate = :auction_finishDate, auction_price = :auction_price, auction_user_id = :auction_user_id, auction_status_id = :auction_status_id
    where auction_id = :auction_id';
    return $this->getLignes($champs); 
  }

  
  public function addStamp($champs) {
  
    $this->sql = ' INSERT INTO stamps SET stamp_name = :stamp_name, stamp_description = :stamp_description, stamp_price = :stamp_price, stamp_date = :stamp_date, stamp_certified =:stamp_certified, stamp_format = :stamp_format, stamp_color = :stamp_color, stamp_location_id = :stamp_location_id, stamp_condition_id = :stamp_condition_id, stamp_rareness_id = :stamp_rareness_id, stamp_auction_id = :stamp_auction_id, stamp_user_id = :stamp_user_id ';
    return $this->CUDLigne($champs); 
  }


  public function updateStamp($champs) {
 
    $this->sql = ' UPDATE stamps SET stamp_name = :stamp_name, stamp_description = :stamp_description, stamp_price = :stamp_price, stamp_date = :stamp_date, stamp_certified =:stamp_certified, stamp_format = :stamp_format, stamp_color = :stamp_color, stamp_location_id = :stamp_location_id, stamp_condition_id = :stamp_condition_id, stamp_rareness_id = :stamp_rareness_id, stamp_auction_id = :stamp_auction_id, stamp_user_id = :stamp_user_id 
    where stamp_id = :stamp_id';
    return $this->getLignes($champs); 
  }


  public function addImg($champs){
    $this->sql = ' INSERT INTO images SET image_link =:image_link, image_name = :image_name, image_stamp_id = :image_stamp_id';
    return $this->CUDLigne($champs);
  }


  public function getAuctionsByUser($user_id){
    $this->sql = 'SELECT * FROM auctions
      left join stamps on auction_id = stamp_auction_id
      left join locations on stamp_location_id = location_id
      left join conditions on stamp_condition_id = condition_id
      left join rareness on stamp_rareness_id = rareness_id
      left join images on image_stamp_id = stamp_id
      left join bids on auction_id = bid_auction_id
      WHERE auction_user_id = :user_id 
      group by stamp_id
      order by auction_id DESC
      ';
    return $this->getLignes(['user_id' => $user_id]);
  }


  public function getAuctions(){
    $this->sql = ' SELECT * FROM auctions
      left join stamps on auction_id = stamp_auction_id
      left join locations on stamp_location_id = location_id
      left join conditions on stamp_condition_id = condition_id
      left join rareness on stamp_rareness_id = rareness_id
      left join images on image_stamp_id = stamp_id
      left outer join bids on auction_id = bid_auction_id AND bid_amount = (select max(bid_amount) from bids where auction_id = bid_auction_id) 
      group by stamp_id   
      ';
    return $this->getLignes();
  }


  public function getAuctionsHome(){
    $this->sql = 'SELECT * FROM auctions
      left join stamps on auction_id = stamp_auction_id
      left join locations on stamp_location_id = location_id
      left join conditions on stamp_condition_id = condition_id
      left join rareness on stamp_rareness_id = rareness_id
      left join images on image_stamp_id = stamp_id
      left outer join bids on auction_id = bid_auction_id AND bid_amount = (select max(bid_amount) from bids where auction_id = bid_auction_id)
      where auction_status_id = 2
      group by stamp_id 
      order by auction_id DESC
      LIMIT 3
      ';
    return $this->getLignes();
  }


  public function getAuction($auction_id){
    $this->sql = 'SELECT * FROM auctions
      left join stamps on auction_id = stamp_auction_id
      left join users on user_id = stamp_user_id
      left join locations on stamp_location_id = location_id
      left join conditions on stamp_condition_id = condition_id
      left join rareness on stamp_rareness_id = rareness_id
      left join images on image_stamp_id = stamp_id
      left join bids on auction_id = bid_auction_id AND bid_amount = (select max(bid_amount) from bids where auction_id = bid_auction_id)
      WHERE auction_id = :auction_id
       ';
    return $this->getLignes(['auction_id' => $auction_id]);
  }


  public function addBid($champs){
    $this->sql = 'INSERT INTO bids 
      SET bid_amount = :bid_amount, bid_user_id = :bid_user_id,  bid_auction_id = :bid_auction_id, bid_date = CURRENT_TIMESTAMP()';
    return $this->CUDLigne($champs);
  }


  public function getBids($auction_id){
    $this->sql = 'SELECT * from bids 
      inner join users on bid_user_id = user_id
      where bid_auction_id = :auction_id
      order by bid_id DESC';
    return $this->getLignes(['auction_id' => $auction_id]);
  }

  public function getMaxBid($auction_id){
    $this->sql = 'SELECT max(bid_amount) as maxAmount from bids where bid_auction_id = :auction_id';
    return $this->getLignes(['auction_id' => $auction_id], RequetesPDO::UNE_SEULE_LIGNE);
  }

  public function getMinAuction($auction_id){
    
    $this->sql = 'SELECT auction_price as minAmount from auctions where auction_id = :auction_id';
    return $this->getLignes(['auction_id' => $auction_id], RequetesPDO::UNE_SEULE_LIGNE);
  }

// $this->sql =
//       "SELECT MAX(mise_valeur), mise_utilisateur_id, mise_enchere_id
//     FROM mise
//     where :enchere_id = mise_enchere_id
//    group by mise_enchere_id 

  public function getCountBid($auction_id){
    $this->sql = 'SELECT COUNT(*) from bids
      group by bid_auction_id';
    return $this->getLignes(['auction_id' => $auction_id]);
  }


  public function getLocations(){
    $this->sql = 'SELECT * FROM locations
      order by location_name ASC';
    return $this->getLignes();
  }


  public function getRareness(){
    $this->sql = 'SELECT * FROM rareness
      order by rareness_id ASC';
    return $this->getLignes();
  }


  public function getConditions(){
    $this->sql = 'SELECT * FROM conditions
      order by condition_id ASC';
    return $this->getLignes();
  }
  
}