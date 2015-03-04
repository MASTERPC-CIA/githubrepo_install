<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Index extends MX_Controller {

 function __construct()
 {
   parent::__construct();
   
        $this->load->database();
        $this->load->helper('url');
        $this->load->library('form_validation');
        $this->load->library('grocery_CRUD');
//        $this->load->library('cuentasxcobrar');
 }

 function index()
 {
          $lista_estados = array('id >' => '-2');
        //Extraer el listado de bancos para seleccionar en cual se va a depositar
        $estados_data['lista_estados'] = $this->generic_model->get('bill_deposito_estado', $lista_estados);
        
        $res['view'] = $this->load->view('cheques_filter',$estados_data, TRUE);
        $res['slidebar'] = $this->load->view('slidebar','',TRUE);                           
        $this->load->view('templates/dashboard',$res); 
 }
}