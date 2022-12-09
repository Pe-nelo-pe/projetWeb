
-- -----------------------------------------------------
-- Schema projetWeb
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `projetWeb` DEFAULT CHARACTER SET utf8 ;
USE `projetWeb` ;

-- -----------------------------------------------------
-- Table `projetWeb`.`status`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `projetWeb`.`status` (
  `status_id` INT NOT NULL AUTO_INCREMENT,
  `status_def` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`status_id`),
  UNIQUE INDEX `status_id_UNIQUE` (`status_id` ASC)  )
;


-- -----------------------------------------------------
-- Table `projetWeb`.`locations`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `projetWeb`.`locations` (
  `location_id` INT NOT NULL AUTO_INCREMENT,
  `location_name` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`location_id`),
  UNIQUE INDEX `location_id_UNIQUE` (`location_id` ASC)  )
;


-- -----------------------------------------------------
-- Table `projetWeb`.`conditions`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `projetWeb`.`conditions` (
  `condition_id` INT NOT NULL AUTO_INCREMENT,
  `condition_def` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`condition_id`),
  UNIQUE INDEX `condition_id_UNIQUE` (`condition_id` ASC)  )
;


-- -----------------------------------------------------
-- Table `projetWeb`.`rareness`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `projetWeb`.`rareness` (
  `rareness_id` INT NOT NULL AUTO_INCREMENT,
  `rareness_def` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`rareness_id`),
  UNIQUE INDEX `rareness_id_UNIQUE` (`rareness_id` ASC)  )
;



CREATE TABLE IF NOT EXISTS `projetWeb`.`users` (
  `user_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_firstName` VARCHAR(45) NOT NULL,
  `user_lastName` VARCHAR(45) NOT NULL,
  `user_email` VARCHAR(45) NOT NULL,
  `user_password` VARCHAR(255) NOT NULL,
  `user_address` VARCHAR(45) NULL,
  `user_city` VARCHAR(45) NULL,
  `user_zipCode` VARCHAR(45) NULL,
  `user_status_id` INT NOT NULL,
  UNIQUE INDEX `iduser_UNIQUE` (`user_id` ASC) ,
  UNIQUE INDEX `user_email_UNIQUE` (`user_email` ASC) ,
  INDEX `fk_user_status1_idx` (`user_status_id` ASC) ,
  CONSTRAINT `fk_user_status1`
    FOREIGN KEY (`user_status_id`)
    REFERENCES `projetWeb`.`status` (`status_id`)
    )

;

-- -----------------------------------------------------
-- Table `projetWeb`.`auction_status`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `projetWeb`.`auction_status` (
  `auction_status_id` INT NOT NULL AUTO_INCREMENT,
  `auction_status_name` VARCHAR(45) NULL,
  PRIMARY KEY (`auction_status_id`),
  UNIQUE INDEX `auction_status_id_UNIQUE` (`auction_status_id` ASC)  )
;


-- -----------------------------------------------------
-- Table `projetWeb`.`auctions`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `projetWeb`.`auctions` (
  `auction_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `auction_name` VARCHAR(155) NOT NULL,
  `auction_description` LONGTEXT NOT NULL,
  `auction_startDate` DATETIME NOT NULL,
  `auction_finishDate` DATETIME NOT NULL,
  `auction_lordChoice` TINYINT NULL,
  `auction_price` DECIMAL NULL,
  `auction_user_id` INT NOT NULL,
  `auction_status_id` INT NOT NULL,
  INDEX `fk_auction_user1_idx` (`auction_user_id` ASC)  ,
  INDEX `fk_auctions_auction_status1_idx` (`auction_status_id` ASC)  ,
  UNIQUE INDEX `auction_id_UNIQUE` (`auction_id` ASC)  ,
  CONSTRAINT `fk_auction_user1`
    FOREIGN KEY (`auction_user_id`)
    REFERENCES `projetWeb`.`users` (`user_id`)
    ,
  CONSTRAINT `fk_auctions_auction_status1`
    FOREIGN KEY (`auction_status_id`)
    REFERENCES `projetWeb`.`auction_status` (`auction_status_id`)
    )
;

CREATE TABLE IF NOT EXISTS `projetWeb`.`stamps` (
  `stamp_id` INT NOT NULL AUTO_INCREMENT,
  `stamp_name` VARCHAR(155) NOT NULL,
  `stamp_price` DECIMAL NOT NULL,
  `stamp_description` LONGTEXT NULL,
  `stamp_date` YEAR NULL,
  `stamp_certified` TINYINT NULL,
  `stamp_format` VARCHAR(45) NULL,
  `stamp_color` VARCHAR(45) NULL,
  `stamp_location_id` INT NULL,
  `stamp_condition_id` INT NULL,
  `stamp_rareness_id` INT NULL,
  `stamp_auction_id` INT NULL,
  `stamp_user_id` INT NOT NULL,
  PRIMARY KEY (`stamp_id`),
  INDEX `fk_stamps_locations1_idx` (`stamp_location_id` ASC) ,
  INDEX `fk_stamps_conditions1_idx` (`stamp_condition_id` ASC) ,
  INDEX `fk_stamps_rareness1_idx` (`stamp_rareness_id` ASC) ,
  INDEX `fk_stamps_auctions1_idx` (`stamp_auction_id` ASC) ,
  UNIQUE INDEX `stamp_id_UNIQUE` (`stamp_id` ASC) ,
  INDEX `fk_stamps_users1_idx` (`stamp_user_id` ASC) ,
  CONSTRAINT `fk_stamps_locations1`
    FOREIGN KEY (`stamp_location_id`)
    REFERENCES `projetWeb`.`locations` (`location_id`)
    ,
  CONSTRAINT `fk_stamps_conditions1`
    FOREIGN KEY (`stamp_condition_id`)
    REFERENCES `projetWeb`.`conditions` (`condition_id`)
    ,
  CONSTRAINT `fk_stamps_rareness1`
    FOREIGN KEY (`stamp_rareness_id`)
    REFERENCES `projetWeb`.`rareness` (`rareness_id`)
    ,
  CONSTRAINT `fk_stamps_auctions1`
    FOREIGN KEY (`stamp_auction_id`)
    REFERENCES `projetWeb`.`auctions` (`auction_id`)
    ,
  CONSTRAINT `fk_stamps_users1`
    FOREIGN KEY (`stamp_user_id`)
    REFERENCES `projetWeb`.`users` (`user_id`)
    )
;



-- -----------------------------------------------------
-- Table `projetWeb`.`category_news`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `projetWeb`.`category_news` (
  `category_news_id` INT NOT NULL AUTO_INCREMENT,
  `category_news_name` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`category_news_id`),
  UNIQUE INDEX `category_news_id_UNIQUE` (`category_news_id` ASC)  )
;


-- -----------------------------------------------------
-- Table `projetWeb`.`news`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `projetWeb`.`news` (
  `news_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `news_title` VARCHAR(100) NOT NULL,
  `news_content` VARCHAR(1000) NOT NULL,
  `news_author` VARCHAR(45) NOT NULL,
  `news_date` DATETIME NULL,
  `news_category_id` INT NOT NULL,
  INDEX `fk_news_category_news_idx` (`news_category_id` ASC)  ,
  CONSTRAINT `fk_news_category_news`
    FOREIGN KEY (`news_category_id`)
    REFERENCES `projetWeb`.`category_news` (`category_news_id`)
   )
;


-- -----------------------------------------------------
-- Table `projetWeb`.`bids`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `projetWeb`.`bids` (
  `bid_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `bid_amount` DECIMAL NOT NULL,
  `bid_user_id` INT NOT NULL,
  `bid_auction_id` INT NOT NULL,
  UNIQUE INDEX `bet_id_UNIQUE` (`bid_id` ASC)  ,
  INDEX `fk_bids_users1_idx` (`bid_user_id` ASC)  ,
  INDEX `fk_bids_auctions1_idx` (`bid_auction_id` ASC)  ,
  CONSTRAINT `fk_bids_users1`
    FOREIGN KEY (`bid_user_id`)
    REFERENCES `projetWeb`.`users` (`user_id`)
    ,
  CONSTRAINT `fk_bids_auctions1`
    FOREIGN KEY (`bid_auction_id`)
    REFERENCES `projetWeb`.`auctions` (`auction_id`)
    )
;


-- -----------------------------------------------------
-- Table `projetWeb`.`liked_news`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `projetWeb`.`liked_news` (
  `likedNews_user_id` INT NOT NULL,
  `likedNews_news_id` INT NOT NULL,
  PRIMARY KEY (`likedNews_user_id`, `likedNews_news_id`),
  INDEX `fk_liked_news_news1_idx` (`likedNews_news_id` ASC)  ,
  CONSTRAINT `fk_liked_news_user1`
    FOREIGN KEY (`likedNews_user_id`)
    REFERENCES `projetWeb`.`users` (`user_id`)
    ,
  CONSTRAINT `fk_liked_news_news1`
    FOREIGN KEY (`likedNews_news_id`)
    REFERENCES `projetWeb`.`news` (`news_id`)
   )
;


-- -----------------------------------------------------
-- Table `projetWeb`.`liked_auctions`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `projetWeb`.`liked_auctions` (
  `likedAuction_user_id` INT NOT NULL,
  `likedAuction_auction_id` INT NOT NULL,
  PRIMARY KEY (`likedAuction_user_id`, `likedAuction_auction_id`),
  INDEX `fk_liked_auction_auction1_idx` (`likedAuction_auction_id` ASC)  ,
  CONSTRAINT `fk_liked_auction_user1`
    FOREIGN KEY (`likedAuction_user_id`)
    REFERENCES `projetWeb`.`users` (`user_id`)
    ,
  CONSTRAINT `fk_liked_auction_auction1`
    FOREIGN KEY (`likedAuction_auction_id`)
    REFERENCES `projetWeb`.`auctions` (`auction_id`)
    )
;


-- -----------------------------------------------------
-- Table `projetWeb`.`images`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `projetWeb`.`images` (
  `image_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `image_link` VARCHAR(1000) NOT NULL,
  `image_stamp_id` INT NOT NULL,
  UNIQUE INDEX `image_id_UNIQUE` (`image_id` ASC)  ,
  INDEX `fk_images_stamps1_idx` (`image_stamp_id` ASC)  ,
  CONSTRAINT `fk_images_stamps1`
    FOREIGN KEY (`image_stamp_id`)
    REFERENCES `projetWeb`.`stamps` (`stamp_id`)
   )
;







INSERT INTO `auction_status` (`auction_status_id`, `auction_status_name`) VALUES (NULL, 'archive'), (NULL, 'active'), (NULL, 'coming');

INSERT INTO `category_news` (`category_news_id`, `category_news_name`) VALUES (NULL, 'auction'), (NULL, 'stamp'), (NULL, 'bridge');


INSERT INTO `conditions` (`condition_id`, `condition_def`) VALUES (NULL, 'Neuf'), (NULL, 'Légèrement usé'), (NULL, 'Usé'), (NULL, 'Estampé'), (NULL, 'Troué');

INSERT INTO `locations` (`location_id`, `location_name`) VALUES (NULL, 'Canada'), (NULL, 'États-Unis d\'Amérique'), (NULL, 'France'), (NULL, 'Allemagne'), (NULL, 'Italie'), (NULL, 'Angleterre'), (NULL, 'Mexique'), (NULL, 'Indes'), (NULL, 'Chine'), (NULL, 'Russie'), (NULL, 'Australie'), (NULL, 'Afrique du Sud'), (NULL, 'Iran'), (NULL, 'Turquie'), (NULL, 'Brésil');

INSERT INTO `rareness` (`rareness_id`, `rareness_def`) VALUES (NULL, 'Très rare'), (NULL, 'Rare'), (NULL, 'Moyen rare'), (NULL, 'Peu rare'), (NULL, 'Commun');


INSERT INTO `status` (`status_id`, `status_def`) VALUES (NULL, 'member'), (NULL, 'admin');