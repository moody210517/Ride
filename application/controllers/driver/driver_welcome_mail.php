<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Driver_welcome_mail extends MY_Controller {

    function __construct() {
        parent::__construct();
    }

    public function index() {
        $driver_id = $this->input->post('driver_id');

        if (isset($driver_id) && $driver_id != '') {
            $this->mail_model->send_driver_register_confirmation_mail($driver_id);
        } else {
            echo 'Some Parameter Missing';
        }
    }

}

/* End of file generate_invoice.php */
/* Location: ./application/controllers/generate_invoice.php */