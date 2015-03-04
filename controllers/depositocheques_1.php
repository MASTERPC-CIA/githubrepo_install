<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Depositocheques extends MX_Controller {

    private $id_trans_send = null;

    public function __construct() {
        parent::__construct();
//        $this->load->library('check_session');
        $this->load->database();
        $this->load->helper('url');
        $this->load->library('form_validation');
        $this->load->library('grocery_CRUD');
        $this->load->helper('form');
    }

    function index() {
        $lista_estados = array('id >' => '-3');

        $estados_data['lista_estados'] = $this->generic_model->get('bill_cheque_estado', $lista_estados);

        $res['view'] = $this->load->view('cheques_filter', $estados_data, TRUE);
        $res['slidebar'] = $this->load->view('slidebar', '', TRUE);
        $this->load->view('templates/dashboard', $res);
//        $this->load->view('cheques_filter', $estados_data);
    }

    public function get_crud() {
        $this->config->load('grocery_crud');
        $this->config->set_item('grocery_crud_dialog_forms', true);
        $crud = new grocery_CRUD();
        $crud->set_table('bill_chequescustodio');
        $crud->columns('id', 'nrocheque', 'cliente_cedulaRuc', 'nombre_beneficiario', 'fecha', 'fechacobro', 'nrocuentacheque', 'banco', 'valorcheque');
        $crud->set_subject('Cheques');
        $output = $crud->render();
        $this->load->view('crud_view_datatable', $output);
    }

    /* Toma el rango de las fechas elegidas para seleccionar los cheques */

    function extraer() {
        $fecha_inicio = $this->input->post('fecha_inicio');
        $fecha_fin = $this->input->post('fecha_fin');
        $estado = $this->input->post('estado');

        $this->listar_cheques($fecha_inicio, $fecha_fin, $estado);
    }

    //function listar_cheques($fecha_inicio, $fecha_fin, $estado) {
    function filtrar_cheques($fecha_entr_desde, $fecha_entr_hasta, $chequestado_id) {

        $this->config->load('grocery_crud');
        $this->config->set_item('grocery_crud_dialog_forms', true);
        $crud = new grocery_CRUD();
        $crud->set_model('grocery_join_model');


        if (empty($fecha_entr_desde) && empty($fecha_entr_hasta) && $chequestado_id == -1) {
            $where_cheques = null;
            $crud->where('bill_chequescustodio.id >', '0');

            //sin crud:
            $res['cheques_data'] = $this->generic_model->get('bill_chequescustodio', $where_cheques);
        }

        if (!empty($fecha_entr_desde) AND ! empty($fecha_entr_hasta)) {
            $crud->where('bill_chequescustodio.fechacobro >=', $fecha_entr_desde);
            $crud->where('bill_chequescustodio.fechacobro <=', $fecha_entr_hasta);
            //sin crud:
            $where_cheques = array('ch.id >' => '0',
                'ch.fechacobro >=' => $fecha_entr_desde,
                'ch.fechacobro <=' => $fecha_entr_hasta,
            );
        }

        if ($chequestado_id != -1) {
            $crud->where('bill_chequescustodio.estado', $chequestado_id);
            //sin crud:
            $where_cheques['ch.estado ='] = $chequestado_id;
        }

        $crud->basic_model->set_select_join(
                ", bill_chequescustodio.id, nrocheque numero, cliente_cedulaRuc, "
                . "cl.nombres cliente, nombre_beneficiario titular, bill_chequescustodio.fecha emision, "
                . "fechacobro cobro, nrocuentacheque cuenta, bl.banco, valorcheque monto"); //Query text here

        $joins_array = array(
            '0' => array('table' => 'bill_bancoslist bl', 'condition' => 'bill_chequescustodio.bancolist_id = bl.id', 'option' => 'LEFT'),
            '1' => array('table' => 'billing_cliente cl', 'condition' => 'bill_chequescustodio.cliente_cedulaRuc = cl.PersonaComercio_cedulaRuc', 'option' => 'LEFT')
        );

        $crud->basic_model->set_join_clause($joins_array);

        $columns = array(
            'numero', 'cliente', 'titular', 'fecha', 'cobro', 'cuenta', 'banco', 'monto');


        $crud->columns($columns);
        $crud->set_table('bill_chequescustodio');
        $crud->unset_edit()->unset_delete()->unset_read()->unset_add();
        $crud->set_subject('Cheques');
        
        //Mostrar enlace en los nombres al deposito/comprobante
        if ($chequestado_id == 0) {//Protestado
            $crud->callback_column('cliente', array($this, 'get_link_comprobante'));
        }
        if ($chequestado_id == 3) {//Confirmado
            $crud->callback_column('cliente', array($this, 'get_link_edit'));
        }
        $output = $crud->render();

//        $this->load->view('crud_view_datatable', $output);
        // SIN CRUD

        $join_cluase = array(
            '0' => array('table' => 'bill_bancoslist bl', 'condition' => 'bancolist_id = bl.id ', 'type' => 'LEFT'),
            '1' => array('table' => 'billing_cliente cl', 'condition' => 'cliente_cedulaRuc = cl.PersonaComercio_cedulaRuc ', 'type' => 'LEFT'),
        );
        $res['cheques_data'] = $this->generic_model->get_join('bill_chequescustodio ch', $where_cheques, $join_cluase, 'ch.*, bl.banco, cl.nombres, cl.apellidos', 0);

        $res['fecha_inicio'] = $fecha_entr_desde;
        $res['fecha_fin'] = $fecha_entr_hasta;
        $res['estado'] = $chequestado_id;

        //Extraer el listado de bancos para seleccionar en cual se va a depositar
        $lista_bancos = array('id >' => '0');
        $res['lista_bancos'] = $this->generic_model->get('billing_banco', $lista_bancos);
        $res['monto_efectivo'] = 0;

        if ($chequestado_id == 1) {

            return $res;
        } else {
            return $output;
        }
    }

    public function get_link_edit($value, $row) {
        try{
        $link = tagcontent('a', $value, array('id' => 'ajaxpanelbtn',
            'data-url' => base_url('bancos/depositos_all/ver_deposito_cheque/' . $row->numero),
            'title' => 'Ver Deposito',
            'data-target' => 'new_total_out', 'href' => '#'));
        return $link;
        }  catch (Exception $e){
            echo 'No hay un numero de cheque registrado en nuestra Base de Datos<br>';
        }
    }
    
    public function get_link_comprobante($value, $row) {
        try{
        $link = tagcontent('a', $value, array('id' => 'ajaxpanelbtn',
            'data-url' => base_url('bancos/depositos_all/ver_asiento_contable_cheque_protestado/' . $row->numero),
            'title' => 'Ver Comprobante',
            'data-target' => 'new_total_out', 'href' => '#'));
        return $link;
        }  catch (Exception $e){
            echo 'No hay un numero de cheque registrado en nuestra Base de Datos<br>';
        }
    }

    function listar_cheques($fecha_inicio, $fecha_fin, $estado) {

        $res['tipo'] = 1;
        if ($estado == -2) {//Anulados
//            $this->load->view('ch_depositar_list', $res);
            $output = $this->filtrar_cheques($fecha_inicio, $fecha_fin, $estado);
            $this->load->view('crud_view_datatable', $output);
//            $this->get_crud();
        }
        if ($estado == -1) {//Todos
//            $this->get_crud();
            $output = $this->filtrar_cheques($fecha_inicio, $fecha_fin, $estado);
            $this->load->view('crud_view_datatable', $output);
        }
        if ($estado == 0) {//Protestados
            $output = $this->filtrar_cheques($fecha_inicio, $fecha_fin, $estado);
            $this->load->view('crud_view_datatable', $output);
//            $this->load->view('ch_depositar_list', $res);
        }
        if ($estado == 1) {//Registrados
            $res = $this->filtrar_cheques($fecha_inicio, $fecha_fin, $estado);

            $this->load->view('ch_registrados_list', $res);
        }
        if ($estado == 2) {//Por confirmar
            $output = $this->filtrar_cheques($fecha_inicio, $fecha_fin, $estado);
            $this->load->view('crud_view_datatable', $output);
//            $this->load->view('ch_depositar_list', $res);
        }
        if ($estado == 3) {//Depositados
            $output = $this->filtrar_cheques($fecha_inicio, $fecha_fin, $estado);
            $this->load->view('crud_view_datatable', $output);
//            $this->load->view('ch_depositar_list', $res);
        }
    }

    /* Grabar en la BD el deposito
     * bill_chequescustodio => modificar estado
     * bill_depositos => agregar nuevo registro
     * bill_depositos_detalle => agregar registros con ids de las 2 anteriores */

    function depositar() {

        $monto_efectivo = $this->input->post('monto_efectivo');
        $monto_cheques = 0;
        $banco = $this->input->post('nombre');
        $nombre_banco = $this->generic_model->get_val_where('billing_banco', array('id' => $banco), 'nombre');

        $fecha = date("Y-n-j"); //fecha de hoy
        $hora_actual = strtotime(date('H:i:s', time()));
//        $hora_actual = strtotime('-6 hour', strtotime(date('H:i:s', time())));
        $hora = date('H:i:s', $hora_actual);
        $existe = false;

        $chk_cheque = $this->input->post('chk_cheque'); //recorremos los check marcados
        if (!(empty($chk_cheque))) {
            foreach ($chk_cheque as $id_cheq) {
                $val_cheque = $this->extraer_valor_cheques($id_cheq);
                if ($this->cheque_ya_existe($id_cheq)) {
                    $existe = true;
                    break;
                }
                $monto_cheques += $val_cheque;
            }
        }

        if ($existe) {
            echo '<br>Reinicie la busqueda por favor';
        } else {
            $monto_total = $monto_cheques + $monto_efectivo;

            $deposito_data = array('banco_id' => $banco,
                'nombre_banco' => $nombre_banco,
                'monto_cheques' => $monto_cheques,
                'monto_efectivo' => $monto_efectivo,
                'monto_total' => $monto_total,
                'fecha' => $fecha,
                'user_id' => $this->user->id,
                'hora' => $hora);
            $deposito_guardar = array('banco_id' => $banco,
                'monto' => $monto_total,
                'fecha' => $fecha,
                'hora' => $hora);

//      print_r($deposito_data);

            /* DEPOSITO - Nuevo registro en la tabla bill_deposito  */
            //Verificar si el cheque ya esta en otro deposito
            $this->generic_model->save($deposito_guardar, 'bill_deposito');


            /* ACTUALIZACIONES */
            /* Solo Cheques */

            if ($monto_efectivo == 0) {
                /* bill_chequescustodio (depositado, fechacambioestado, horacambioestado)
                 * Extraer id de cheques depositados
                 */
                //Tipo 1-> cheque
                $deposito_id = mysql_insert_id();
                $deposito_data['deposito_id'] = $deposito_id;

                $this->registrar_cheques($deposito_data);
            }

            /* Solo Efectivo */
            if ($monto_cheques == 0) {
                //Tipo 2-> efectivo
                $deposito_id = mysql_insert_id();
                $deposito_data['deposito_id'] = $deposito_id;
                $this->registrar_efectivo($deposito_data);
            }

            /* Cheques y efectivo */
            if ($monto_cheques != 0 && $monto_efectivo != 0) {
                //cheques
                $deposito_id = mysql_insert_id();
                $deposito_data['deposito_id'] = $deposito_id;

                $this->registrar_cheques($deposito_data);

                //efectivo

                $this->registrar_efectivo($deposito_data);
            }

            $this->load->view('resumen_recibo', $deposito_data);

            //Refrescar el listado de cheques


            $fecha_inicio = $this->input->post('fecha_inicio');
            $fecha_fin = $this->input->post('fecha_fin');
            $estado = $this->input->post('estado');

//        $this->listar_cheques($fecha_inicio, $fecha_fin, $estado);
//            $this->extraer();
        }
//        print_r($deposito_data);
    }

    function cheque_ya_existe($id_dep) {
        $enviado = $this->generic_model->get_val_where('bill_deposito_detalle', array('doc_id' => $id_dep), 'estado_doc');
        if ($enviado == 2 || $enviado == 3) {
            echo '<br>CHEQUE YA REGISTRADO EN OTRO DEPOSITO';
            return true;
        } else {
            return false;
        }
    }

    //Metodo para registrar solo el efectivo
    function registrar_efectivo($deposito_data) {
        $tipo = 2;
        $doc_id = 0;
        $deposito_data['doc_id'] = $doc_id;
        $deposito_data['tipo'] = $tipo;
        $deposito_data['valor'] = $deposito_data['monto_efectivo'];

        $this->registrar_detalle($deposito_data);
    }

    //Metodo para recorrer los cheques marcados en el checkbox y registrarlos en bd
    function registrar_cheques($deposito_data) {
        $chk_cheque = $this->input->post('chk_cheque'); //recorremos los check marcados
        if (!(empty($chk_cheque))) {
            foreach ($chk_cheque as $id) {
                $val_cheque = $this->extraer_valor_cheques($id);
                $doc_id = $id;
                $fecha_cambio_estado = $deposito_data['fecha'];
                $hora_cambio_estado = $deposito_data['hora'];
                $deposito_data['doc_id'] = $doc_id;
                $deposito_data['tipo'] = 1;
                $deposito_data['valor'] = $val_cheque;


                $this->registrar_detalle($deposito_data);
                /* DEPOSITO -  */
                //Actualizar tabla cheques, estado, fecha y hora
                $this->generic_model->update('bill_chequescustodio', array('estado' => '2', 'fechacambioestado' => $fecha_cambio_estado,
                    'horacambioestado' => $hora_cambio_estado), array('id' => $doc_id));
            }
        }
    }

    //Metodo para ingresar un nuevo registro en la tabla detalle 
    function registrar_detalle($deposito_data) {

        $deposito_id = $deposito_data['deposito_id'];
        $valor = $deposito_data['valor'];
        $doc_id = $deposito_data['doc_id'];
        $tipo = $deposito_data['tipo'];
        $deposito_detalle = array('deposito_id' => $deposito_id,
            'valor' => $valor,
            'doc_id' => $doc_id,
            'tipo_doc_deposito' => $tipo,
            'estado_doc' => '2'); //estado por confirmar (igual a los demas estados)

        /* DEPOSITO -  */
        //Actualizar tabla bill_deposito_detalle
        $this->generic_model->save($deposito_detalle, 'bill_deposito_detalle');
    }

    //Metodo para consultar el monto del cheque segun su id
    function extraer_valor_cheques($id_cheque) {
        $res = $this->generic_model->get_val_where('bill_chequescustodio', array('id' => $id_cheque), 'valorcheque');
        return $res;
    }

}
