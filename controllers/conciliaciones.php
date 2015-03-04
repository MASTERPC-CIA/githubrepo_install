<?php

class Conciliaciones extends MX_Controller {

    private $id_trans_send = null;
    private $conciliacion_data = array();

    public function __construct() {
        parent::__construct();
//        $this->load->library('check_session');

        $this->load->database();
        $this->load->helper('url');
        $this->load->library('form_validation');
        $this->load->library('grocery_CRUD');
    }

    function index() {


        $search_data['bancos_list'] = $this->generic_model->get('billing_banco', array('id >' => '0'), array('id', 'nombre'));
        $search_data['tipo_list'] = $this->generic_model->get('bill_cuentabancaria_tipo', array('id >' => '0'));
        $search_data['periodo_list'] = $this->generic_model->get('bill_cuentabancaria', array('id >' => '0'), array('anio'), null, null, null, null, array('anio'));
        $search_data['mes_list'] = $this->generic_model->get('bill_mes', array('id >' => '0'));


        $res['view'] = $this->load->view('conciliaciones_filter', $search_data, TRUE);
        $res['slidebar'] = $this->load->view('slidebar', '', TRUE);
        $this->load->view('templates/dashboard', $res);
    }

    /* Extraer datos segun los filtros */

    function extraer() {
        $banco = $this->input->post('banco');
        $tipo_cta = $this->input->post('tipo_cta');
        $periodo_cta = $this->input->post('periodo_cta');
        $mes = $this->input->post('mes');
        $f_vence_desde = $this->input->post('f_vence_desde');
        $f_vence_hasta = $this->input->post('f_vence_hasta');
        $f_reg_desde = $this->input->post('f_reg_desde');
        $f_reg_hasta = $this->input->post('f_reg_hasta');
        $nro_cheque = $this->input->post('nro_cheque');
        $nro_comprobante = $this->input->post('num_comprobante');

        //Si todo esta vacio extraer todo
        if ($banco == -1 && $tipo_cta == -1 && $periodo_cta == -1 && $mes == -1 && empty($f_reg_desde) && empty($f_reg_hasta) && empty($f_vence_desde) && empty($f_vence_hasta) && empty($nro_comprobante)) {
            $join_cluase = array(
                '0' => array('table' => 'billing_banco bl', 'condition' => 'cn.banco_id = bl.id '),
            );
            $res['cuenta_data'] = $this->generic_model->get_join('bill_cuentabancaria cn', null, $join_cluase, 'cn.*, bl.nombre banco', 0);

            $res['tipo'] = $tipo_cta;

            $this->load->view('conciliaciones_detalle_list', $res);
        } else if ($nro_comprobante) {//Si se ingreso un numero de referencia/cheque para buscar
            $this->get_cheque_ref($nro_comprobante);
        } else {
            $this->conciliacion_data = array('banco' => $banco,
                'tipo_cta' => $tipo_cta,
                'periodo_cta' => $periodo_cta,
                'mes' => $mes,
                'f_vence_desde' => $f_vence_desde,
                'f_vence_hasta' => $f_vence_hasta,
                'f_reg_desde' => $f_reg_desde,
                'f_reg_hasta' => $f_reg_hasta,
                'nro_cheque' => $nro_cheque,
            );
            $this->listar();
        }
    }

    public function listar() {
        $where_concil = array();
        $group_concil = null;
        $fields_concil = array('cn.*, bl.nombre banco');


        if ($this->conciliacion_data['banco'] != -1) {//Banco seleccionado
            $where_concil['cn.banco_id ='] = $this->conciliacion_data['banco'];
//            $fields_concil = array('cn.*, bl.banco');
        }
        //F. Vence seleccionado
        if ($this->conciliacion_data['f_vence_desde'] && $this->conciliacion_data['f_vence_hasta']) {//Vence vacio
            $where_concil['cn.fecha_vence >='] = $this->conciliacion_data['f_vence_desde'];
            $where_concil['cn.fecha_vence <='] = $this->conciliacion_data['f_vence_hasta'];
//            $fields_concil = array('cn.*, bl.banco');
        }
        //F. Registro seleccionado
        if ($this->conciliacion_data['f_reg_desde'] && $this->conciliacion_data['f_reg_hasta']) {//Vence vacio
            $where_concil['cn.fecha_registro >='] = $this->conciliacion_data['f_reg_desde'];
            $where_concil['cn.fecha_registro <='] = $this->conciliacion_data['f_reg_hasta'];
//            $fields_concil = array('cn.*, bl.nombre banco');
        }
        if ($this->conciliacion_data['periodo_cta'] != -1) {//Periodo seleccionado
            $where_concil['cn.anio ='] = $this->conciliacion_data['periodo_cta'];
//            $fields_concil = array('cn.*, bl.nombre banco');
        }
        if ($this->conciliacion_data['mes'] != -1) {//Mes seleccionado
            $where_concil['cn.mes ='] = $this->conciliacion_data['mes'];
//            $fields_concil = array('cn.*, bl.nombre banco');
        }
        if ($this->conciliacion_data['tipo_cta'] != -1) {//Tipo seleccionado
            $where_concil['cn.tipo_id ='] = $this->conciliacion_data['tipo_cta'];
//            $fields_concil = array('cn.*, bl.nombre banco');
        } else {//Listar por deposito si no hay ningun filtro en 'tipo'
            $solo_deposito = true;
//            $res['cuenta_data'] = $this->generic_model->get('bill_cuentabancaria', $where_concil, array('nombre_usuario, banco, fecha_vence, fecha_registro, SUM(debito), SUM(credito), nota'), null, null, null, null, array('doc_id'));
//            $where_concil['cn.tipo_id ='] = null;
            $fields_concil = array('cn.id', 'cn.tipo_id', 'cn.tipo_transaccion', 'cn.nota', 'nombre_usuario', 'nombre banco', 'fecha_vence', 'fecha_registro', 'SUM(debito) debito', 'SUM(credito) credito', 'conciliado', 'doc_id', 'bl.id banco_id');
//            $where_concil['periodo_list'] = $this->generic_model->get('bill_cuentabancaria', array('id >' => '0'), array('anio'), null, null, null, null, array('anio'));
//            $this->load->view('conciliaciones_deposito_list', $res);
            $group_concil = 'doc_id';
        }

        $join_clause = array(
            '0' => array('table' => 'billing_banco bl', 'condition' => 'cn.banco_id = bl.id '),
        );
        $res['cuenta_data'] = $this->generic_model->get_join('bill_cuentabancaria cn', $where_concil, $join_clause, $fields_concil, 0, null, $group_concil);
        if (!empty($solo_deposito)) {
            $this->load->view('conciliaciones_deposito_list', $res);
        } else {

            $this->load->view('conciliaciones_detalle_list', $res);
        }
//        print_r($where_concil);
    }

    public function actualizar() {
        $chk_select = $this->input->post('chk_select'); //recorremos los check marcados
//        print_r($chk_cuenta);
//        echo '<br>';
//        print_r($estado_cuenta);
        //Si entró desde el boton desconciliar todo
        if ($this->input->post('desc') == 1) {
            $this->generic_model->update('bill_cuentabancaria', array('conciliado' => '0'), array('id >' => '0'));
            echo 'TODO DESCONCILIADO';
        } else {
            //1. Desconciliamos todo
            $this->generic_model->update('bill_cuentabancaria', array('conciliado' => '0'), array('id >' => '0'));

            //2. Recorremos los marcados y los enviamos a conciliar
            if (!empty($chk_select)) {
                foreach ($chk_select as $id) {
//                echo '<br>____________Tot: '.$id_tot;
                    $this->generic_model->update('bill_cuentabancaria', array('conciliado' => '1'), array('id' => $id));
                    $det_debito = $this->generic_model->get_val_where('bill_cuentabancaria', array('id' => $id), 'debito');
                    $det_credito = $this->generic_model->get_val_where('bill_cuentabancaria', array('id' => $id), 'credito');
                    $det_nota = $this->generic_model->get_val_where('bill_cuentabancaria', array('id' => $id), 'nota');

                    echo '<br>Conciliado: ' . $det_credito . ' ' . $det_nota;
                }
            }
        }
    }

    public function is_checked($id_tot) {
        $chk_select = $this->input->post('chk_select'); //recorremos los check marcados

        foreach ($chk_select as $id_sel) {
//            echo '  Sel: '.$id_sel;
            if ($id_sel == $id_tot) {
//                echo '<br>Encontrado: tot '.$id_tot.' y sel: '.$id_sel;

                return true;
            }
        }
        return false;
    }

    /* Extraer el numero y el detalle del cheque */

    public function get_cheque($nro_cheque) {
        //Buscar el id en cheques_custodio
        $ch_id = $this->generic_model->get_val_where('bill_chequescustodio', array('nrocheque' => $nro_cheque), 'id');
        //Buscar el deposito al q pertenece en deposito_detalle
        $dep_id = $this->generic_model->get_val_where('bill_deposito_detalle', array('doc_id' => $ch_id), 'deposito_id');
        //Buscar el id de la cta a la q pertenece tipo 1(cheque)
        $cta_id = $this->generic_model->get_val_where('bill_cuentabancaria', array('doc_id' => $dep_id, 'tipo_id' => '1'), 'id');


        $res['cuenta_data'] = $this->generic_model->get('bill_cuentabancaria', array('id = ' => $cta_id));
//        $res['tipo'] = $this->conciliacion_data['tipo_cta'];

        $this->load->view('conciliaciones_detalle_list', $res);
    }

    public function get_cheque_ref($nro_ref) {
        //Buscar el deposito al q pertenece en deposito_detalle
        $dep_id = $this->generic_model->get_val_where('bill_deposito_detalle', array('doc_id' => $nro_ref), 'deposito_id');
        //Buscar el id de la cta a la q pertenece tipo 1(cheque)
        $cta_id = $this->generic_model->get_val_where('bill_cuentabancaria', array('doc_id' => $dep_id, 'tipo_id' => '1'), 'id');


        //Listado de bancos
        $join_cluase = array(
            '0' => array('table' => 'billing_banco bl', 'condition' => 'cn.banco_id = bl.id '),
        );
        $res['cuenta_data'] = $this->generic_model->get_join('bill_cuentabancaria cn', array('cn.id = ' => $cta_id), $join_cluase, 'cn.*, bl.nombre banco', 0);

//        $res['cuenta_data'] = $this->generic_model->get('bill_cuentabancaria', array('id = ' => $cta_id));
//        $res['tipo'] = $this->conciliacion_data['tipo_cta'];

        $this->load->view('conciliaciones_detalle_list', $res);
    }

    public function transfer_dep() {
        //Quedamos en grabar la transferencia de periodo ayer vef
        $anio = $this->input->post('periodo_cta');
        $mes = $this->input->post('mes');
        $dep_id = $this->input->post('dep_id');
        if ($this->generic_model->update('bill_cuentabancaria', array('anio' => $anio, 'mes' => $mes), array('doc_id =' => $dep_id, 'tipo_transaccion =' => '16'))) {
            echo '<b>Transferencia Exitosa. Deposito N° ' . $dep_id;
        }
    }

//    public function get_link_comprobante() {
//       
//        $dep_id = $this->input->post('dep_id');
//        $concil = $this->input->post('concil');
//        $nombre_banco = $this->input->post('banco');
//
//        
//        $asiento_id = $this->generic_model->get_val_where('bill_asiento_contable', array('doc_id' => $dep_id), 'id');
//
//
//        echo $link = tagcontent('a', 'Enlace', array('id' => 'ajaxpanelbtn',
//            'data-url' => base_url('bancos/depositos_all/ver_asiento_contable/' . $asiento_id.'/'.$nombre_banco.'/'.$concil),
//            'title' => 'Ver Comprobante',
//            'data-target' => 'new_total_out', 'href' => '#'));
//        
//
//    }
}
