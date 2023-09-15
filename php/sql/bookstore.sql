SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema bookstore
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS `bookstore`;
CREATE SCHEMA `bookstore` DEFAULT CHARACTER SET utf8 COLLATE utf8_slovenian_ci ;
USE `bookstore` ;

-- -----------------------------------------------------
-- Table `bookstore`.`posta`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `bookstore`.`posta` (
  `posta_stevilka` VARCHAR(30) NOT NULL,
  `posta_ime` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`posta_stevilka`))
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `bookstore`.`uporabnik` 
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `bookstore`.`uporabnik` (
  `uporabnik_id` INT NOT NULL AUTO_INCREMENT,
  `uporabnik_tip` VARCHAR(20) DEFAULT 'stranka',
  `uporabnik_ime` VARCHAR(100) NOT NULL,
  `uporabnik_priimek` VARCHAR(100) NOT NULL,
  `uporabnik_email` VARCHAR(100) NOT NULL,
  `uporabnik_geslo` VARCHAR(255) NOT NULL,
  `uporabnik_telefon` VARCHAR(20) DEFAULT '' ,
  `uporabnik_aktiviran` INT DEFAULT 1,
  `uporabnik_naslov` VARCHAR(255) DEFAULT '',
  `posta_stevilka` VARCHAR(30) NOT NULL,
  PRIMARY KEY (`uporabnik_id`),
  INDEX `fk_uporabnik_posta1_idx` (`posta_stevilka` ASC) VISIBLE,
  CONSTRAINT `fk_uporabnik_posta1`
    FOREIGN KEY (`posta_stevilka`)
    REFERENCES `bookstore`.`posta` (`posta_stevilka`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `bookstore`.`narocilo`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `bookstore`.`narocilo` (
  `narocilo_id` INT NOT NULL AUTO_INCREMENT,
  `narocilo_postavka` FLOAT NULL,
  `narocilo_status` VARCHAR(45) DEFAULT '',
  `uporabnik_id` INT NOT NULL,
  PRIMARY KEY (`narocilo_id`),
  CONSTRAINT `fk_narocilo_uporabnik`
    FOREIGN KEY (`uporabnik_id`)
    REFERENCES `bookstore`.`uporabnik` (`uporabnik_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `bookstore`.`knjiga`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `bookstore`.`knjiga` (
  `knjiga_id` INT NOT NULL AUTO_INCREMENT,
  `knjiga_avtor` VARCHAR(255) NULL,
  `knjiga_naslov` VARCHAR(255) NULL,
  `knjiga_cena` FLOAT NULL,
  `knjiga_leto` VARCHAR(4) NULL,
  `knjiga_slika` VARCHAR(255) DEFAULT '',
  `knjiga_opis` VARCHAR(1020) DEFAULT '',
  `knjiga_aktiviran` INT DEFAULT 1,
  PRIMARY KEY (`knjiga_id`))
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `bookstore`.`knjiga_narocilo`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `bookstore`.`knjiga_narocilo` (
  `narocilo_id` INT NOT NULL,
  `knjiga_id` INT NOT NULL,
  `knjiga_narocilo_kolicina` INT NULL,
  PRIMARY KEY (`knjiga_id`, `narocilo_id`),
  INDEX `fk_knjiga_narocilo_narocilo1_idx` (`narocilo_id` ASC) VISIBLE,
  INDEX `fk_knjiga_narocilo_knjiga1_idx` (`knjiga_id` ASC) VISIBLE,
  CONSTRAINT `fk_knjiga_narocilo_narocilo1`
    FOREIGN KEY (`narocilo_id`)
    REFERENCES `bookstore`.`narocilo` (`narocilo_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_knjiga_narocilo_knjiga1`
    FOREIGN KEY (`knjiga_id`)
    REFERENCES `bookstore`.`knjiga` (`knjiga_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

INSERT INTO posta(posta_stevilka, posta_ime)
VALUES (1000, "Ljubljana");

INSERT INTO posta(posta_stevilka, posta_ime)
VALUES (1356, "Dobrova");

INSERT INTO uporabnik(uporabnik_tip, uporabnik_ime, uporabnik_priimek, uporabnik_email, uporabnik_geslo, uporabnik_telefon, uporabnik_aktiviran, uporabnik_naslov, posta_stevilka)
VALUES ("administrator", "Nikolay", "Vasilev", "administrator@gmail.com", "gesloAdmin", "040-854-425", 1, "Celovska 125", 1000);

INSERT INTO uporabnik(uporabnik_tip, uporabnik_ime, uporabnik_priimek, uporabnik_email, uporabnik_geslo, uporabnik_telefon, uporabnik_aktiviran, uporabnik_naslov, posta_stevilka)
VALUES ("prodajalec", "Eva", "Bizilj", "prodajalec@gmail.com", "gesloProdajalec" , "040-854-423", 1, "Hrusevo 52A", 1356);

INSERT INTO uporabnik(uporabnik_tip, uporabnik_ime, uporabnik_priimek, uporabnik_email, uporabnik_geslo, uporabnik_telefon, uporabnik_aktiviran, uporabnik_naslov, posta_stevilka)
VALUES ("stranka", "Borut", "Pahor", "borut.pahor@gmail.com", "borutPahor" , "040-444-423", 1, "Jamnikova ulica 10", 1000);

INSERT INTO uporabnik(uporabnik_tip, uporabnik_ime, uporabnik_priimek, uporabnik_email, uporabnik_geslo, uporabnik_telefon, uporabnik_aktiviran, uporabnik_naslov, posta_stevilka)
VALUES ("stranka", "Luka", "Novak", "luka.novak@gmail.com", "lukaNovak" , "040-444-548", 1, "Dolenska ulica 3", 1000);

INSERT INTO knjiga(knjiga_avtor, knjiga_naslov, knjiga_cena, knjiga_leto, knjiga_slika, knjiga_opis)
VALUES ("J. K. Rowling", "Harry Potter and the Philosopher's Stone", 12.99, "1997", "images/basicBookStoreBook.png", "Harry Potter and the Philosopher's Stone is a fantasy novel written by British author J. K. Rowling. The first novel in the Harry Potter series and Rowling's debut novel, it follows Harry Potter, a young wizard who discovers his magical heritage on his eleventh birthday, when he receives a letter of acceptance to Hogwarts School of Witchcraft and Wizardry...");

INSERT INTO knjiga(knjiga_avtor, knjiga_naslov, knjiga_cena, knjiga_leto, knjiga_slika, knjiga_opis)
VALUES ("Suzanne Collins", "The Hunger Games", 13.00, "2008", "images/basicBookStoreBook.png", "The Hunger Games trilogy takes place in an unspecified future time, in the dystopian, post-apocalyptic nation of Panem, located in North America. The country consists of a wealthy Capitol city, located in the Rocky Mountains, surrounded by twelve (originally thirteen) poorer districts ruled by the Capitol. The Capitol is lavishly rich and technologically advanced, but the districts are in varying states of poverty... ");

INSERT INTO knjiga(knjiga_avtor, knjiga_naslov, knjiga_cena, knjiga_leto,knjiga_slika, knjiga_opis)
VALUES ("William Shakespeare", "Romeo and Juliet", 8.38, "1597", "images/basicBookStoreBook.png", "Romeo and Juliet is a tragedy written by William Shakespeare early in his career about two young Italian star-crossed lovers whose deaths ultimately reconcile their feuding families. It was among Shakespeare's most popular plays during his lifetime and, along with Hamlet, is one of his most frequently performed plays. Today, the title characters are regarded as archetypal young lovers...")