SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

DROP SCHEMA IF EXISTS `pcan` ;
CREATE SCHEMA IF NOT EXISTS `pcan` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `pcan` ;

-- -----------------------------------------------------
-- Table `pcan`.`users`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pcan`.`users` ;

CREATE TABLE IF NOT EXISTS `pcan`.`users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `password` CHAR(60) NOT NULL,
  `mustChangePassword` CHAR(1) NULL DEFAULT NULL,
  `profilesId` INT UNSIGNED NOT NULL,
  `banned` CHAR(1) NOT NULL,
  `suspended` CHAR(1) NOT NULL,
  `active` CHAR(1) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `profilesId` (`profilesId` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `pcan`.`email_confirmations`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pcan`.`email_confirmations` ;

CREATE TABLE IF NOT EXISTS `pcan`.`email_confirmations` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `usersId` INT UNSIGNED NOT NULL,
  `code` CHAR(32) NOT NULL,
  `createdAt` DATETIME NOT NULL,
  `modifiedAt` DATETIME NOT NULL,
  `confirmed` CHAR(1) NULL DEFAULT 'N',
  PRIMARY KEY (`id`),
  INDEX `fk_email_confirmations_1_idx` (`usersId` ASC),
  CONSTRAINT `fk_email_confirmations_1`
    FOREIGN KEY (`usersId`)
    REFERENCES `pcan`.`users` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `pcan`.`failed_logins`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pcan`.`failed_logins` ;

CREATE TABLE IF NOT EXISTS `pcan`.`failed_logins` (
  `id` INT UNSIGNED NOT NULL,
  `usersId` INT UNSIGNED NULL DEFAULT NULL,
  `ipAddress` CHAR(15) NOT NULL,
  `attempted` SMALLINT(5) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `usersId` (`usersId` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `pcan`.`password_changes`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pcan`.`password_changes` ;

CREATE TABLE IF NOT EXISTS `pcan`.`password_changes` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `usersId` INT UNSIGNED NOT NULL,
  `ipAddress` CHAR(15) NOT NULL,
  `userAgent` VARCHAR(48) NOT NULL,
  `createdAt` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `usersId` (`usersId` ASC),
  CONSTRAINT `fk_password_changes_1`
    FOREIGN KEY (`usersId`)
    REFERENCES `pcan`.`users` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `pcan`.`profiles`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pcan`.`profiles` ;

CREATE TABLE IF NOT EXISTS `pcan`.`profiles` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(64) NOT NULL,
  `active` CHAR(1) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `IX_NAME` (`name` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 4
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `pcan`.`permissions`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pcan`.`permissions` ;

CREATE TABLE IF NOT EXISTS `pcan`.`permissions` (
  `profilesId` INT UNSIGNED NOT NULL,
  `resource` VARCHAR(16) NOT NULL,
  `action` VARCHAR(16) NOT NULL,
  PRIMARY KEY (`profilesId`, `resource`, `action`),
  CONSTRAINT `fk_permissions_1`
    FOREIGN KEY (`profilesId`)
    REFERENCES `pcan`.`profiles` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 23
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `pcan`.`remember_tokens`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pcan`.`remember_tokens` ;

CREATE TABLE IF NOT EXISTS `pcan`.`remember_tokens` (
  `id` INT UNSIGNED NOT NULL,
  `usersId` INT UNSIGNED NOT NULL,
  `token` CHAR(32) NOT NULL,
  `userAgent` VARCHAR(120) NOT NULL,
  `createdAt` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `token` (`token` ASC),
  INDEX `fk_remember_tokens_idx` (`usersId` ASC),
  CONSTRAINT `fk_remember_tokens`
    FOREIGN KEY (`usersId`)
    REFERENCES `pcan`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `pcan`.`reset_passwords`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pcan`.`reset_passwords` ;

CREATE TABLE IF NOT EXISTS `pcan`.`reset_passwords` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `usersId` INT UNSIGNED NOT NULL,
  `code` VARCHAR(48) NOT NULL,
  `createdAt` DATETIME NOT NULL,
  `modifiedAt` DATETIME NOT NULL,
  `reset` CHAR(1) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `usersId` (`usersId` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `pcan`.`success_logins`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pcan`.`success_logins` ;

CREATE TABLE IF NOT EXISTS `pcan`.`success_logins` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `usersId` INT UNSIGNED NOT NULL,
  `ipAddress` CHAR(15) NOT NULL,
  `userAgent` VARCHAR(120) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `usersId` (`usersId` ASC),
  CONSTRAINT `fk_success_logins_1`
    FOREIGN KEY (`usersId`)
    REFERENCES `pcan`.`users` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `pcan`.`blog_category`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pcan`.`blog_category` ;

CREATE TABLE IF NOT EXISTS `pcan`.`blog_category` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `name_clean` VARCHAR(45) NOT NULL,
  `enabled` TINYINT(1) NOT NULL DEFAULT 1,
  `date_created` DATETIME NOT NULL,
  UNIQUE INDEX `index2` (`name_clean` ASC),
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `pcan`.`blog`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pcan`.`blog` ;

CREATE TABLE IF NOT EXISTS `pcan`.`blog` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `date_published` DATETIME NOT NULL,
  `date_updated` DATETIME NOT NULL,
  `title` VARCHAR(144) NOT NULL,
  `title_clean` VARCHAR(144) NOT NULL,
  `article` TEXT NULL DEFAULT NULL,
  `author_id` INT UNSIGNED NULL,
  `enabled` TINYINT(1) NOT NULL DEFAULT 0,
  `comments` TINYINT(1) NOT NULL DEFAULT 1,
  `featured` TINYINT(1) NOT NULL DEFAULT 0,
  `bundle_type` VARCHAR(24) NULL,
  `bundle_id` INT UNSIGNED NULL,
  PRIMARY KEY (`id`),
  INDEX `index3` (`title_clean` ASC),
  INDEX `fk_blog_post_1_idx` (`author_id` ASC),
  INDEX `title1` (`title` ASC),
  UNIQUE INDEX `title_clean_UNIQUE` (`title_clean` ASC),
  CONSTRAINT `fk_blog_post_1`
    FOREIGN KEY (`author_id`)
    REFERENCES `pcan`.`users` (`id`)
    ON DELETE SET NULL
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `pcan`.`blog_to_category`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pcan`.`blog_to_category` ;

CREATE TABLE IF NOT EXISTS `pcan`.`blog_to_category` (
  `category_id` INT UNSIGNED NOT NULL,
  `blog_id` INT UNSIGNED NOT NULL,
  INDEX `fk_blog_to_category_2_idx` (`blog_id` ASC),
  CONSTRAINT `fk_blog_to_category_1`
    FOREIGN KEY (`category_id`)
    REFERENCES `pcan`.`blog_category` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_blog_to_category_2`
    FOREIGN KEY (`blog_id`)
    REFERENCES `pcan`.`blog` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `pcan`.`blog_comment`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pcan`.`blog_comment` ;

CREATE TABLE IF NOT EXISTS `pcan`.`blog_comment` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `blog_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `head_id` INT UNSIGNED NULL,
  `reply_to_id` INT UNSIGNED NULL,
  `title` VARCHAR(127) NULL,
  `comment` TEXT NOT NULL,
  `mark_read` TINYINT(1) NULL DEFAULT 0,
  `enabled` TINYINT(1) NOT NULL DEFAULT 1,
  `date_comment` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_comment_1` (`blog_id` ASC),
  INDEX `fk_comment_2_idx` (`user_id` ASC),
  CONSTRAINT `fk_comment_1`
    FOREIGN KEY (`blog_id`)
    REFERENCES `pcan`.`blog` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_comment_2`
    FOREIGN KEY (`user_id`)
    REFERENCES `pcan`.`users` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `pcan`.`blog_tag`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pcan`.`blog_tag` ;

CREATE TABLE IF NOT EXISTS `pcan`.`blog_tag` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `blog_id` INT UNSIGNED NOT NULL,
  `tag` VARCHAR(45) NOT NULL,
  `tag_clean` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_tags_1` (`blog_id` ASC),
  INDEX `index3` (`tag_clean` ASC),
  CONSTRAINT `fk_tags_1`
    FOREIGN KEY (`blog_id`)
    REFERENCES `pcan`.`blog` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `pcan`.`images`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pcan`.`images` ;

CREATE TABLE IF NOT EXISTS `pcan`.`images` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `filepath` VARCHAR(255) NULL,
  `title` VARCHAR(127) NULL,
  `width` INT NULL,
  `height` INT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `filepath_UNIQUE` (`filepath` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `pcan`.`blog_related`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pcan`.`blog_related` ;

CREATE TABLE IF NOT EXISTS `pcan`.`blog_related` (
  `blog_id` INT UNSIGNED NOT NULL,
  `blog_related_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`blog_id`, `blog_related_id`),
  INDEX `fk_blog_related_1_idx` (`blog_related_id` ASC),
  CONSTRAINT `fk_blog_related_1`
    FOREIGN KEY (`blog_related_id`)
    REFERENCES `pcan`.`blog` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `pcan`.`meta`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pcan`.`meta` ;

CREATE TABLE IF NOT EXISTS `pcan`.`meta` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `meta_name` VARCHAR(20) NOT NULL,
  `template` VARCHAR(80) NULL,
  `data_limit` INT NULL,
  `auto_filled` TINYINT(1) NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_meta_UNIQUE` (`id` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `pcan`.`blog_meta`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pcan`.`blog_meta` ;

CREATE TABLE IF NOT EXISTS `pcan`.`blog_meta` (
  `blog_id` INT UNSIGNED NOT NULL,
  `meta_id` INT UNSIGNED NOT NULL,
  `content` VARCHAR(200) NULL,
  PRIMARY KEY (`blog_id`, `meta_id`),
  INDEX `meta_id_idx` (`meta_id` ASC),
  CONSTRAINT `blog_id`
    FOREIGN KEY (`blog_id`)
    REFERENCES `pcan`.`blog` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `meta_id`
    FOREIGN KEY (`meta_id`)
    REFERENCES `pcan`.`meta` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
COMMENT = 'add meta tags to blog pages';


-- -----------------------------------------------------
-- Table `pcan`.`event`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pcan`.`event` ;

CREATE TABLE IF NOT EXISTS `pcan`.`event` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `fromTime` DATETIME NOT NULL,
  `toTime` DATETIME NULL,
  `blogId` INT UNSIGNED NOT NULL,
  `enabled` TINYINT(1) NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_event_blog_idx` (`blogId` ASC),
  CONSTRAINT `fk_event_blog`
    FOREIGN KEY (`blogId`)
    REFERENCES `pcan`.`blog` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `pcan`.`contact`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pcan`.`contact` ;

CREATE TABLE IF NOT EXISTS `pcan`.`contact` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(125) NULL,
  `telephone` VARCHAR(15) NULL,
  `email` VARCHAR(45) NULL,
  `sendDate` DATETIME NULL,
  `body` TEXT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 1;


-- -----------------------------------------------------
-- Table `pcan`.`file_upload`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pcan`.`file_upload` ;

CREATE TABLE IF NOT EXISTS `pcan`.`file_upload` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `path` VARCHAR(255) NOT NULL,
  `mime_type` VARCHAR(30) NULL,
  `date_upload` DATETIME NULL,
  `blog_id` INT UNSIGNED NULL,
  `file_size` INT UNSIGNED NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_file_upload_1_idx` (`blog_id` ASC),
  CONSTRAINT `fk_file_upload_1`
    FOREIGN KEY (`blog_id`)
    REFERENCES `pcan`.`blog` (`id`)
    ON DELETE SET NULL
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 1;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- -----------------------------------------------------
-- Data for table `pcan`.`profiles`
-- -----------------------------------------------------
START TRANSACTION;
USE `pcan`;
INSERT INTO `pcan`.`profiles` (`id`, `name`, `active`) VALUES (1, 'Administrator', 'Y');
INSERT INTO `pcan`.`profiles` (`id`, `name`, `active`) VALUES (2, 'Member', 'Y');
INSERT INTO `pcan`.`profiles` (`id`, `name`, `active`) VALUES (3, 'Public', 'Y');
INSERT INTO `pcan`.`profiles` (`id`, `name`, `active`) VALUES (4, 'Editor', 'Y');

COMMIT;


-- -----------------------------------------------------
-- Data for table `pcan`.`permissions`
-- -----------------------------------------------------
START TRANSACTION;
USE `pcan`;
INSERT INTO `pcan`.`permissions` (`profilesId`, `resource`, `action`) VALUES (3, 'users', 'index');
INSERT INTO `pcan`.`permissions` (`profilesId`, `resource`, `action`) VALUES (3, 'users', 'search');
INSERT INTO `pcan`.`permissions` (`profilesId`, `resource`, `action`) VALUES (3, 'profiles', 'index');
INSERT INTO `pcan`.`permissions` (`profilesId`, `resource`, `action`) VALUES (3, 'profiles', 'search');
INSERT INTO `pcan`.`permissions` (`profilesId`, `resource`, `action`) VALUES (1, 'users', 'index');
INSERT INTO `pcan`.`permissions` (`profilesId`, `resource`, `action`) VALUES (1, 'users', 'search');
INSERT INTO `pcan`.`permissions` (`profilesId`, `resource`, `action`) VALUES (1, 'users', 'edit');
INSERT INTO `pcan`.`permissions` (`profilesId`, `resource`, `action`) VALUES (1, 'users', 'create');
INSERT INTO `pcan`.`permissions` (`profilesId`, `resource`, `action`) VALUES (1, 'users', 'delete');
INSERT INTO `pcan`.`permissions` (`profilesId`, `resource`, `action`) VALUES (1, 'users', 'changePassword');
INSERT INTO `pcan`.`permissions` (`profilesId`, `resource`, `action`) VALUES (1, 'profiles', 'index');
INSERT INTO `pcan`.`permissions` (`profilesId`, `resource`, `action`) VALUES (1, 'profiles', 'search');
INSERT INTO `pcan`.`permissions` (`profilesId`, `resource`, `action`) VALUES (1, 'profiles', 'edit');
INSERT INTO `pcan`.`permissions` (`profilesId`, `resource`, `action`) VALUES (1, 'profiles', 'create');
INSERT INTO `pcan`.`permissions` (`profilesId`, `resource`, `action`) VALUES (1, 'profiles', 'delete');
INSERT INTO `pcan`.`permissions` (`profilesId`, `resource`, `action`) VALUES (1, 'permissions', 'index');
INSERT INTO `pcan`.`permissions` (`profilesId`, `resource`, `action`) VALUES (2, 'users', 'index');
INSERT INTO `pcan`.`permissions` (`profilesId`, `resource`, `action`) VALUES (2, 'users', 'search');
INSERT INTO `pcan`.`permissions` (`profilesId`, `resource`, `action`) VALUES (2, 'users', 'edit');
INSERT INTO `pcan`.`permissions` (`profilesId`, `resource`, `action`) VALUES (2, 'users', 'create');
INSERT INTO `pcan`.`permissions` (`profilesId`, `resource`, `action`) VALUES (2, 'profiles', 'index');
INSERT INTO `pcan`.`permissions` (`profilesId`, `resource`, `action`) VALUES (2, 'profiles', 'search');

COMMIT;

