<?php
class github extends MX_Controller {

        public $fecha_ini;
    public $fecha_fin;

    public function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('generic_model');
        $this->load->library('grocery_CRUD');
    }
  public function get_github() {
        //$this->load->view('ventana_principal');
        $res['view'] = $this->load->view('vista_github_descargar', '', TRUE);
        $res['slidebar'] = $this->load->view('slidebar', '', TRUE);
        $this->load->view('templates/dashboard', $res);
    }
}