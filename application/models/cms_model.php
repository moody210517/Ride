<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 
 * This model contains all db functions related to cms management
 * @author Teamtweaks
 *
 */
class Cms_model extends My_Model {

    public function __construct() {
        parent::__construct();
    }

    public function check_page_exist($condition, $cms_id) {
        $this->mongo_db->select(array('_id'));
        $this->mongo_db->where($condition);
        $this->mongo_db->where_ne('_id', MongoID($cms_id));
        $res = $this->mongo_db->get(CMS);
        return $res;
    }

    

}
