<?php

class Cheques_import extends MX_Controller {

//    private $id_trans_send = null;
//    private $conciliacion_data = array();

    public function __construct() {
        parent::__construct();
//        $this->load->library('check_session');

        $this->load->database();
        $this->load->helper('url');
        $this->load->library('form_validation');
        $this->load->library('grocery_CRUD');
    }

    function index() {

        $res['view'] = $this->load->view('ch_import_view', '', TRUE);
        $res['slidebar'] = $this->load->view('slidebar', '', TRUE);
        $this->load->view('templates/dashboard', $res);
    }

    function importar() {
        $upload_path = './uploads/cheques';
//        $this->get_idbanco($nombre_banco);
        $this->loadfromfile($upload_path);
    }

    function loadfromfile($upload_path) {
//        echo 'Ruta: '.$ruta;
        set_time_limit(0);
        $this->load->library('excel');
//        $this->load->model('client_model');
        $config['max_height'] = '0';

        $config['upload_path'] = $upload_path;
        $config['allowed_types'] = 'xlsx';
        $config['max_size'] = '0';
        $config['max_width'] = '0';

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload()) {
            echo 'no file ';
            $error = $this->upload->display_errors();
            echo tagcontent('strong', $error, array('class' => 'text-danger font20'));
            die();
        } else {
            $upl_data = $this->upload->data();
            echo 'Archivo subido<br>';
        }

        $upl_data = $this->upload->data();
//        $upl_data['file_name'];
        $this->get_chequesdata_xls($upl_data);
    }

    function get_chequesdata_xls($xls_data) {
        if (file_exists('./uploads/cheques/' . $xls_data['file_name'])) {
            // Cargando la hoja de c�lculo
            $Reader = new PHPExcel_Reader_Excel2007();
            $PHPExcel = $Reader->load('./uploads/cheques/' . $xls_data['file_name']);
            // Asignar hoja de excel activa
            $PHPExcel->setActiveSheetIndex(0);
            $bancos_list['data'] = $this->generic_model->get('bill_bancoslist', array('id >' => '0'), 'id, banco');


            for ($x = 2; $x <= $PHPExcel->getActiveSheet()->getHighestRow(); $x++) {
                $nrocheque = get_value_xls($PHPExcel, 0, $x);

                $valor = get_value_xls($PHPExcel, 1, $x);
                $nrocuenta = get_value_xls($PHPExcel, 2, $x);
                $fechacobro = get_value_xls($PHPExcel, 3, $x);
                $ci_ruc = get_value_xls($PHPExcel, 4, $x);
                $banco_id = $this->get_idbanco(get_value_xls($PHPExcel, 5, $x), $bancos_list);
                $beneficiario = get_value_xls($PHPExcel, 6, $x);


                $valuesprod = array(
                    'nrocheque' => $nrocheque,
                    'valorcheque' => $valor,
                    'nrocuentacheque' => $nrocuenta,
                    'fechacobro' => $fechacobro,
                    'cliente_cedulaRuc' => $ci_ruc,
                    'bancolist_id' => $banco_id,
                    'contaasientocontable_id' => null,
                    'nombre_beneficiario' => $beneficiario,
                    'fecha' => date('Y-m-d', time()),
                    'hora' => date('H:i:s', time()),
                    'empleado_id' => $this->user->id,
                );


//                echo 'Banco: '.get_value_xls($PHPExcel, 5, $x).' ID: '.$valuesprod['bancolist_id'].'<br>';
                //Guardar values en la BD
                try {
                    $res1 = $this->generic_model->save($valuesprod, 'bill_chequescustodio');
                } catch (Exception $e) {
                    echo tagcontent('div', 'Ha ocurrido un problema al grabar', array('class' => 'text-danger font20'));
                }
                if (!$res1) {
                    echo tagcontent('div', 'Ha ocurrido un problema al grabar', array('class' => 'text-danger font20'));
                }
            }
            echo tagcontent('strong', 'Se ha terminado de cargar el listado de cheques', array('class' => 'text-success font20'));
        } else {
            echo 'No se ha podido cargar el archivo .xlsx';
        }
    }

    function get_idbanco($nombre_banco, $bancos_list) {
//        print_r($bancos_list['data']);

        foreach ($bancos_list['data'] as $value) {
//            echo '<br> ' . $value->id . ' ' . $value->banco;
            if (strcmp($nombre_banco, $value->banco) == 0) {
//                echo '<br>Encontrado, ID: '.$value->id;
                return $value->id;
            }
        }
    }

}
