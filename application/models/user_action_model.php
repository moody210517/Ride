<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 *
 * This model contains all db functions related to user_action CONTROLLER
 * @author Casperon
 *
 */

class User_action_model extends My_Model {

    public function __construct() {
        parent::__construct();
    }

    public function remove_favorite_driver($condition = array(), $field = '') {
        $this->mongo_db->where($condition);
        $this->mongo_db->unset_field($field);
        $this->mongo_db->update_all(FAVOURITE);
    }

	

}
