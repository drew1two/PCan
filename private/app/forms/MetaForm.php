<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Pcan\Forms;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Select;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Email;
/**
 * Description of MetaForm
 *
 * @author http
 */

/**
 * DROP TABLE IF EXISTS `pcan`.`meta` ;

CREATE TABLE IF NOT EXISTS `pcan`.`meta` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `attr_value` VARCHAR(30) NOT NULL,
  `attr_name` VARCHAR(15) NOT NULL,
  `content_type` VARCHAR(45) NULL,
  `auto_filled` TINYINT(1) NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_meta_UNIQUE` (`id` ASC),
  UNIQUE INDEX `keyvalue_UNIQUE` (`attr_value` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 1;
 * 
 */
class MetaForm extends Form {
    //put your code here
    public function initialize($entity = null, $options = null)
    {
 
        // In edition the id is hidden
        /* $isMyAccount = isset($options['myAccount']) && $options['myAccount'];
        if ($isMyAccount) {
            //$id = new Text('id', array(
            //    'readonly'=>'readonly', 'placeholder' => 'Id'
        ));
            //$id = new Hidden('id');
        } else {
            $id = new Text('id');
        }*/
        $id = new Hidden('id');
        $this->add($id);

        $name = new Text('attr_name', array(
            'placeholder' => 'Name',
           'size' => 15,
        ));

        $name->addValidators(array(
            new PresenceOf(array(
                'message' => 'Attribute name is required'
            ))
        ));

        $this->add($name);

        $atvalue = new Text('attr_value', array(
            'placeholder' => 'Value',
            'size' => 30,

        ));

        $atvalue->addValidators(array(
            new PresenceOf(array(
                'message' => 'Attribute Value is required'
            )),
        ));

        $this->add($atvalue);

        $content = new Text('content_type', array(
            'placeholder' => 'Content Type',
            'size' => 45,

        ));
        $this->add($content);

        $this->add(new Select('auto_filled', array(
            'Y' => 'Yes',
            'N' => 'No'
                )
                ));       
    }
}
