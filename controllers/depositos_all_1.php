<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Depositos_all extends MX_Controller {

    private $id_trans_send = null;
    private $tipotransaccion_cod = '16';

    public function __construct() {
        parent::__construct();
//        $this->load->library('check_session');

        $this->load->database();
        $this->load->helper('url');
        $this->load->library('form_validation');
        $this->load->library('grocery_CRUD');
        $this->load->library('common/cuentasxcobrar');
    }

    function index() {
        $lista_estados = array('id >' => '-3');
        //Extraer el listado de bancos para seleccionar en cual se va a depositar
        $estados_data['lista_estados'] = $this->generic_model->get('bill_deposito_estado', $lista_estados);

        $res['view'] = $this->load->view('depositos_filter', $estados_data, TRUE);
        $res['slidebar'] = $this->load->view('slidebar', '', TRUE);
        $this->load->view('templates/dashboard', $res);
    }

    /* Toma el rango de las fechas elegidas para seleccionar los cheques */

    function extraer() {
        $fecha_inicio = $this->input->post('fecha_inicio');
        $fecha_fin = $this->input->post('fecha_fin');
        $estado = $this->input->post('estado');

        $this->listar_depositos($fecha_inicio, $fecha_fin, $estado);
    }

    function listar_depositos($fecha_inicio, $fecha_fin, $estado) {

        $where_depositos = array();
        $where_depositos['dp.id >'] = '0';

        if (empty($fecha_inicio) && empty($fecha_fin) && $estado == -1) {
            $where_depositos = null;
//            $res['cheques_data'] = $this->generic_model->get('bill_chequescustodio', $where_cheques);
        }
        if ($estado != -1) {
            $where_depositos['dp.estado ='] = $estado;
        }
        if (!empty($fecha_inicio) && !empty($fecha_fin)) {           

            $where_depositos['dp.fecha >='] = $fecha_inicio;
            $where_depositos['dp.fecha <='] = $fecha_fin;
        }

        $join_clause = array(
            '0' => array('table' => 'billing_banco bl', 'condition' => 'dp.banco_id = bl.id ')
        );

        $res['depositos_data'] = $this->generic_model->get_join('bill_deposito dp', $where_depositos, $join_clause, 'dp.*, bl.nombre banco, bl.id banco_id', 0);

        if ($estado == -1) {
            $this->load->view('dep_all_list', $res);
        }
        if ($estado == -2) {
            $this->load->view('dep_all_list', $res);
        }
        if ($estado == 1) {
            $this->load->view('dep_pendientes_list', $res);
        }
        if ($estado == 2) {
            $this->load->view('dep_confirmados_list', $res);
        }
//        $this->sumar_cheques($res);
    }

    function accion_deposito() {
        /* ACCION DEL BOTON
         * 0=> eliminar
         * 1=> modificar
         * 2=> confirmar
         */
        $existe = false; //Var para verificar si ya esta depositado
        $dep_id = $this->input->post('dep_id');

        $banco = $this->generic_model->get_val_where('bill_deposito', array('id' => $dep_id), 'banco_id');
        $nombre_banco = $this->input->post('banco');
        $monto_cheques = $this->generic_model->get_val_where('bill_deposito_detalle', //que no extraiga cheques protestados ni anulados 
                array('deposito_id' => $dep_id, 'tipo_doc_deposito' => '1', 'estado_doc >' => '0'), 'SUM(valor)');
        $monto_efectivo = $this->generic_model->get_val_where('bill_deposito_detalle', array('deposito_id' => $dep_id, 'tipo_doc_deposito' => '2'), 'valor');
        $nro_comprobante = $this->generic_model->get_val_where('bill_deposito', array('id' => $dep_id), 'nro_comprobante');
        $fecha = $this->input->post('fecha');
        $boton_select = $this->input->post('btn');



        if ($monto_efectivo == -1) {//Si la bd nos devuelve un -1 lo ponemos en 0
            $monto_efectivo = 0;
        }
        if ($nro_comprobante == -1) {
            $nro_comprobante = 'Ingrese numero';
        }

        $monto_total = $monto_cheques + $monto_efectivo;

        //Lista de estados de cheques
//        $lista_estados = $this->generic_model->get('bill_cheque_estado', array('id <=' => '0'));

        $deposito_data = array('id_dep' => $dep_id,
            'banco_id' => $banco,
            'nombre_banco' => $nombre_banco,
            'monto_cheques' => $monto_cheques,
            'monto_efectivo' => $monto_efectivo,
            'monto_total' => $monto_total,
            'nro_comprobante' => $nro_comprobante,
            'btn_select' => $boton_select, //Variable para controlar desde donde proviene la consulta: desde depositados o confirmados
            'fecha' => $fecha);
//            'lista_estados' => $lista_estados);




        /* ELIMINAR DEPOSITO */
        if ($boton_select == 0) {
            $this->eliminar_deposito($deposito_data, $dep_id);
        }

        /* VER / MODIFICAR DEPOSITO */
        if ($boton_select == 1) {
            //Extraer el detalle de los cheques de ese deposito

            $where_cheques = array('dt.deposito_id =' => $dep_id, 'dt.tipo_doc_deposito =' => '1',
                'dt.estado_doc >' => '1');

            $join_cluase = array(
                '0' => array('table' => 'bill_chequescustodio ch', 'condition' => 'ch.id = dt.doc_id ')
            );

            $deposito_data['cheques_data'] = $this->generic_model->get_join('bill_deposito_detalle dt', $where_cheques, $join_cluase, 'ch.*, dt.*', 0);

            $this->load->view('dep_detalle', $deposito_data);
        }


        /* CONFIRMAR DEPOSITO */
        if ($boton_select == 2) {
            $existe = $this->ya_registrado($dep_id); //verificacion ya depositado
            if (!$existe) {
                $this->confirmar_deposito($deposito_data, $dep_id);
            }
        }

        /* ACTUALIZAR CAMBIOS */
        if ($boton_select == 3) {//Boton Actualizar detalle de vista dep_detalle
            $this->modificar_deposito($deposito_data);
        }
        /* VER / MODIFICAR DEPOSITO desde depositos confirmados (quitamos btn confirmar) */
        if ($boton_select == 4) {
            //Extraer el detalle de los cheques de ese deposito

            $where_cheques = array('dt.deposito_id =' => $dep_id, 'dt.tipo_doc_deposito =' => '1',
                'dt.estado_doc >' => '1');

            $join_cluase = array(
                '0' => array('table' => 'bill_chequescustodio ch', 'condition' => 'ch.id = dt.doc_id ')
            );

            $deposito_data['cheques_data'] = $this->generic_model->get_join('bill_deposito_detalle dt', $where_cheques, $join_cluase, 'ch.*, dt.*', 0);
            $this->load->view('dep_detalle', $deposito_data);
        }

        //Boton Actualizar detalle de vista dep_detalle
        if ($boton_select == 5) {
            $show_transfer_btn = $this->input->post('concil');

            $asiento_id = $this->generic_model->get_val_where('bill_asiento_contable', array('doc_id' => $dep_id), 'id');
            $this->ver_asiento_contable($asiento_id, $deposito_data['nombre_banco'], $show_transfer_btn);
        }
    }

    /* Metodo llamado desde la lista de depositos para visualizar y editar el detalle en ventana emergente */

    function ver_deposito($dep_id, $banco, $boton_select, $fecha) {

        $nombre_banco = urldecode($banco);
        $banco_id = $this->generic_model->get_val_where('bill_deposito', array('id' => $dep_id), 'banco_id');

        $monto_cheques = $this->generic_model->get_val_where('bill_deposito_detalle', //que no extraiga cheques protestados ni anulados 
                array('deposito_id' => $dep_id, 'tipo_doc_deposito' => '1', 'estado_doc >' => '0'), 'SUM(valor)');
        $monto_efectivo = $this->generic_model->get_val_where('bill_deposito_detalle', array('deposito_id' => $dep_id, 'tipo_doc_deposito' => '2'), 'valor');
        $nro_comprobante = $this->generic_model->get_val_where('bill_deposito', array('id' => $dep_id), 'nro_comprobante');

//        $fecha = $this->input->post('fecha');



        if ($monto_efectivo == -1) {//Si la bd nos devuelve un -1 lo ponemos en 0
            $monto_efectivo = 0;
        }
        if ($nro_comprobante == -1) {
            $nro_comprobante = 'Ingrese numero';
        }

        $monto_total = $monto_cheques + $monto_efectivo;

        //Lista de estados de cheques
//        $lista_estados = $this->generic_model->get('bill_cheque_estado', array('id <=' => '0'));

        $deposito_data = array('id_dep' => $dep_id,
            'banco_id' => $banco_id,
            'nombre_banco' => $nombre_banco,
            'monto_cheques' => $monto_cheques,
            'monto_efectivo' => $monto_efectivo,
            'monto_total' => $monto_total,
            'nro_comprobante' => $nro_comprobante,
            'btn_select' => $boton_select,
            'fecha' => $fecha
        );

        //Extraer el detalle de los cheques de ese deposito

        $where_cheques = array('dt.deposito_id =' => $dep_id, 'dt.tipo_doc_deposito =' => '1',
            'dt.estado_doc >' => '1');

        $join_cluase = array(
            '0' => array('table' => 'bill_chequescustodio ch', 'condition' => 'ch.id = dt.doc_id ')
        );

        $deposito_data['cheques_data'] = $this->generic_model->get_join('bill_deposito_detalle dt', $where_cheques, $join_cluase, 'ch.*, dt.*', 0);
        $this->load->view('dep_detalle', $deposito_data);
    }

    /* Metodo llamado desde los cheques depositados para poder protestarlo desde ahi */

    function ver_deposito_cheque($nro_cheque) {
        $existe = false; //Var para verificar si ya esta depositado
        //Consultamos el deposito al que pertenece ese cheque
        if (empty($nro_cheque)) {
            echo 'No se puede buscar el detalle del deposito';
            return;
        }
        $cheque_id = $this->generic_model->get_val_where('bill_chequescustodio', array('nrocheque' => $nro_cheque), 'id');

        $dep_id = $this->generic_model->get_val_where('bill_deposito_detalle', array('doc_id' => $cheque_id), 'deposito_id');

        $banco_id = $this->generic_model->get_val_where('bill_deposito', array('id' => $dep_id), 'banco_id');
        $nombre_banco = $this->generic_model->get_val_where('billing_banco', array('id' => $banco_id), 'nombre');
        $monto_cheques = $this->generic_model->get_val_where('bill_deposito_detalle', //que no extraiga cheques protestados ni anulados 
                array('deposito_id' => $dep_id, 'tipo_doc_deposito' => '1', 'estado_doc >' => '0'), 'SUM(valor)');
        $monto_efectivo = $this->generic_model->get_val_where('bill_deposito_detalle', array('deposito_id' => $dep_id, 'tipo_doc_deposito' => '2'), 'valor');
        $nro_comprobante = $this->generic_model->get_val_where('bill_deposito', array('id' => $dep_id), 'nro_comprobante');

//        $fecha = $this->input->post('fecha');
        $boton_select = 4;



        if ($monto_efectivo == -1) {//Si la bd nos devuelve un -1 lo ponemos en 0
            $monto_efectivo = 0;
        }
        if ($nro_comprobante == -1) {
            $nro_comprobante = 'Ingrese numero';
        }

        $monto_total = $monto_cheques + $monto_efectivo;

        //Lista de estados de cheques
//        $lista_estados = $this->generic_model->get('bill_cheque_estado', array('id <=' => '0'));

        $deposito_data = array('id_dep' => $dep_id,
            'banco_id' => $banco_id,
            'nombre_banco' => $nombre_banco,
            'monto_cheques' => $monto_cheques,
            'monto_efectivo' => $monto_efectivo,
            'monto_total' => $monto_total,
            'nro_comprobante' => $nro_comprobante,
            'btn_select' => $boton_select,
//            'fecha' => $fecha
        );

        //Extraer el detalle de los cheques de ese deposito

        $where_cheques = array('dt.deposito_id =' => $dep_id, 'dt.tipo_doc_deposito =' => '1',
            'dt.estado_doc >' => '1');

        $join_cluase = array(
            '0' => array('table' => 'bill_chequescustodio ch', 'condition' => 'ch.id = dt.doc_id ')
        );

        $deposito_data['cheques_data'] = $this->generic_model->get_join('bill_deposito_detalle dt', $where_cheques, $join_cluase, 'ch.*, dt.*', 0);
        $this->load->view('dep_detalle', $deposito_data);
    }

    function eliminar_deposito($deposito_data, $id_dep) {
//        echo 'ELIMINAR<br>';
//        echo 'ID: ' . $id_dep;
        $deposito_data['estado'] = 'Anulado';
        $estado = 1; //Cheque pasa a estar como 'registrado' de nuevo
        $this->load->view('resumen_deposito', $deposito_data);

        //Actualizar estado del deposito a -2: Anulado
        $this->generic_model->update('bill_deposito', array('estado' => '-2'), array('id' => $id_dep));

        //Actualizar estado de cheques a 1: Registrado, para anadir a otro deposito nuevamente
        $this->cambiar_estado_cheques($id_dep, $estado, '', NULL);
        $this->generic_model->update('bill_deposito_detalle', array('estado_doc' => '1'), array('deposito_id' => $id_dep, 'tipo_doc_deposito' => '1'));
    }

    /* Invocado desde el boton grabar de la vista dep_detalle */

    function modificar_deposito($deposito_data) {
        $cheques_id = $this->input->post('cheques_id');
        $dep_id = $this->input->post('dep_id');
//        $comprobante = $this->input->post('input_comprobante');
//        $monto_total = 0;
        $monto_total = $this->input->post('monto_total');
//        $deposito_data ['cheques_id'] = $cheques_id;
        $deposito_data ['id_dep'] = $dep_id;
        $deposito_data ['monto_total'] = $monto_total;
//        $deposito_data ['nro_comprobante'] = $comprobante;
        $deposito_data ['estado'] = 'Modificado';

//        echo 'TOTAL'.$monto_total = $this->input->post('monto_total');

        if ($cheques_id) {//si hay cheques en el deposito:
            $this->recorrer_cheques($deposito_data, $cheques_id, $dep_id);
        } else {
            if (!empty($this->input->post('input_efectivo'))) {//Si se ingreso un valor al input_efectivo
                echo 'SE VA A ACTUALIZAR EL EFECTIVO<br>';
                $monto_efectivo = $this->input->post('monto_efectivo');
                $deposito_data['monto_efectivo'] = $monto_efectivo;

                $this->actualizar_efectivo($deposito_data, $dep_id);
            }
        }
        if (!empty($this->input->post('input_comprobante'))) {//Si se ingreso un valor al input comprobante
            echo 'SE VA A ACTUALIZAR EL COMPROBANTE<br>';
//                $nro_comprobante = $this->input->post('input_comprobante');
            $comprobante = $this->input->post('input_comprobante');

            $deposito_data['nro_comprobante'] = $comprobante;

            $this->actualizar_efectivo($deposito_data, $dep_id);
        }
    }

    function recorrer_cheques($cheque_data, $cheques_id, $dep_id) {
        //Recorriendo cheques de ese deposito
        foreach ($cheques_id as $cheque_id) {
            $option_selected = $this->input->post('option_ch' . $cheque_id);
            $num_cheque = $this->generic_model->get_val_where('bill_chequescustodio', array('id' => $cheque_id), 'nrocheque');
            $cliente_cheque = $this->generic_model->get_val_where('bill_chequescustodio', array('id' => $cheque_id), 'cliente_cedulaRuc');
            $cheque_data['num_cheque'] = $num_cheque;
            $cheque_data['cliente_cheque'] = $cliente_cheque;

            if ($option_selected == -1) {//Anular cheque
                $estado = -2;
                $cheque_data['estado'] = 'Anulado';
//                $cheque_data['monto_total'] += $this->input->post('monto_cheque' . $cheque_id);
//                $cheque_data['monto_total'] -= $this->input->post('monto_cheque' . $cheque_id);

                echo '<br><b>Cheque: ' . $num_cheque . ' Anulado </b><br>';
                $cheque_restar = $this->input->post('monto_cheque' . $cheque_id);
                $this->actualizar_cheque($cheque_data, $cheque_id, $estado, $cheque_restar);
            }
            if ($option_selected == 0) {//Protestar cheque
                $estado = 0;
                $cheque_data['estado'] = 'Protestado';
//                $cheque_data['monto_total'] += $this->input->post('monto_cheque' . $cheque_id);
//                $cheque_data['monto_total'] -= $this->input->post('monto_cheque' . $cheque_id);


                $cheque_restar = $this->input->post('monto_cheque' . $cheque_id);
                $this->actualizar_cheque($cheque_data, $cheque_id, $estado, $cheque_restar);
                $this->protestar_cheque($cheque_data, $cheque_id);

                echo '<br><b>Cheque: ' . $num_cheque . ' Protestado </b><br>';
            }
            if ($option_selected == 1) {//Quitar cheque (Vuelve a los cheques registrados)
                $estado = 1;
                $cheque_data['monto_total'] = $this->input->post('monto_total');
                echo '<br><b>Cheque: ' . $num_cheque . ' Reintegrado </b><br>';

                //Borrar el cheque en detalle de ese deposito
                $this->generic_model->delete('bill_deposito_detalle', array('doc_id' => $cheque_id));

                //Actualizar valor del deposito

                $cheque_restar = $this->input->post('monto_cheque' . $cheque_id);
                $this->actualizar_cheque($cheque_data, $cheque_id, $estado, $cheque_restar);
            }
        }
        //Actualizar efectivo
        //Se ingreso valor al input
        if (!empty($this->input->post('input_efectivo'))) {//Si se ingreso un valor al input
//            echo 'SE VA A ACTUALIZAR EL EFECTIVO<br>';
            $monto_efectivo = $this->input->post('monto_efectivo');

            $cheque_data['monto_efectivo'] = $monto_efectivo;
            $this->actualizar_efectivo($cheque_data, $dep_id);
        }
    }

    function confirmar_deposito($deposito_data, $dep_id) {
//        echo 'DEPOSITAR<br>';
//        echo 'ID: ' . $id_dep;
        $estado = 3; //Cheque pasa a estar como 'depositado'
        $monto_efectivo = $this->generic_model->get_val_where('bill_deposito_detalle', array('deposito_id' => $dep_id, 'tipo_doc_deposito' => '2'), 'valor');


//        $this->load->view('resumen_recibo', $deposito_data);

        /* REGISTRAR ASIENTO CONTABLE */
        $this->registrar_asiento_cont($dep_id, $this->tipotransaccion_cod);
        $asiento_id = mysql_insert_id();
//        echo 'Asiento ID> '.$asiento_id = $this->generic_model->get_val_where('bill_asiento_contable', array('doc_id' => $dep_id), 'id');
        //Debito
        $monto_total = $this->generic_model->get_val_where('bill_deposito', array('id' => $dep_id), 'monto');
        $this->registrar_debito_asiento($dep_id, null, $deposito_data['nombre_banco'], $monto_total, $asiento_id, $deposito_data['banco_id']);

        //Credito si hay efectivo
        if ($monto_efectivo != -1) {
            $this->registrar_credito_asiento($dep_id, $cod_cta = '031', $monto_efectivo, '1', null, $asiento_id, $deposito_data['banco_id']);
        }

        //Credito registrado para cada cheque en el metodo cambiar_estado_cheques
        //Actualizar estado de cheques a 3: Depositado
        $this->cambiar_estado_cheques($dep_id, $estado, $asiento_id, $deposito_data['banco_id']);
        $this->generic_model->update('bill_deposito_detalle', array('estado_doc' => '3'), array('deposito_id' => $dep_id));

        //Actualizar estado del deposito a 2: Confirmado
        $this->generic_model->update('bill_deposito', array('estado' => '2'), array('id' => $dep_id));

        //Mostrar enlace a asiento
        echo 'Depositado ' . $link = tagcontent('a', $asiento_id, array('id' => 'ajaxpanelbtn',
    'data-url' => base_url('bancos/depositos_all/ver_asiento_contable/' . $asiento_id . '/' . $deposito_data['nombre_banco'] . '/null'),
    'title' => 'Ver Comprobante',
    'data-target' => 'new_total_out', 'href' => '#'));

//        $this->ver_asiento_contable($asiento_id, $deposito_data['nombre_banco'], null);
    }

    function actualizar_cheque($cheque_data, $cheque_id, $estado, $cheque_restar) {


        $cheque_data['monto_total'] -=$cheque_restar;

        //Tabla cheques, cheque estado
        $this->generic_model->update('bill_chequescustodio', array('estado' => $estado), array('id' => $cheque_id));

        //Tabla depositos,cambia valor del deposito
//        $this->generic_model->update('bill_deposito', array('monto' => $cheque_data['monto_total']), array('id' => $cheque_data['id_dep']));
//                        
        //Tabla depositos_detalle, detalle estado_doc=-1 Anulado 
        $this->generic_model->update('bill_deposito_detalle', array('estado_doc' => $estado), array('doc_id' => $cheque_id));
    }

    function protestar_cheque($cheque_data, $cheque_id) {
        /* SI EL CHEQUE ES PROTESTADO ACTUALIZAR CUENTAS 
         * Crear asiento contable (comprobante de egreso)
         */
        $this->tipotransaccion_cod = '24';

        //Registrar asiento contable
        $monto_total = $this->generic_model->get_val_where('bill_chequescustodio', array('id' => $cheque_id), 'valorcheque');
        $this->registrar_cxc($cheque_data, $cheque_id, $monto_total);
        $cxc_id = $this->generic_model->get_val_where('bill_cxc', array('doc_id' => $cheque_id), 'id');

        $this->registrar_asiento_cont($cxc_id, $this->tipotransaccion_cod);
        $asiento_id = mysql_insert_id();
        $this->registrar_debito_asiento($cxc_id, $cod_cta = '030', null, $monto_total, $asiento_id, $cheque_data['banco_id']);
        $this->registrar_credito_asiento($cxc_id, NULL, $monto_total, '1', $cheque_id, $asiento_id, $cheque_data['banco_id']);
    }

    function registrar_cxc($cheque_data, $cheque_id, $monto_total) {
        //Registrar CxC
        $vence_cheque = $this->generic_model->get_val_where('bill_chequescustodio', array('id' => $cheque_id), 'fechacobro');

        $saldo_anterior = $this->cuentasxcobrar->get_client_saldo($cheque_data['cliente_cheque']);
        $client_id = $cheque_data['cliente_cheque'];

        if ($saldo_anterior == '-1') {
            $saldo_anterior = '0';
        }
        $new_saldo_client = $saldo_anterior + $monto_total;

        /* Registramos la cuenta x cobrar */
        $cxc = array(
            'doc_id' => $cheque_id,
            'tipotransaccion_cod' => '24',
            'tipopago_id' => null,
            'total_neto' => $monto_total,
            'vencecadadias' => 1,
            'nrocuotas' => 1,
            'idcuota' => 1,
            'cuota_neto' => $monto_total,
            'vence_cuota' => $vence_cheque,
            'balance' => $monto_total,
            'observaciones' => 'Protesto Cheque N° ' . $cheque_data['num_cheque'],
            'valor_cobrado' => 0,
            'fecha_cobro' => null,
            'valor_cobrado_bruto' => 0,
            'cambio' => 0,
            'saldototal' => $monto_total,
            'fecha' => date('Y-m-d', time()),
            'hora' => date('H:i:s', time()),
            'client_id' => $client_id,
            'saldo_client' => $new_saldo_client,
        );

        $this->generic_model->save($cxc, 'bill_cxc');

        $this->cuentasxcobrar->update_cxc_saldos($client_id, $new_saldo_client);
    }

    function actualizar_efectivo($cheque_data, $dep_id) {

//        $cheque_data['banco'] = $this->input->post('nombre_banco');
        if (!empty($cheque_data['nro_comprobante'])) {
//            $cheque_data['nro_comprobante'] = $this->input->post('input_comprobante');
            $this->generic_model->update('bill_deposito', array('nro_comprobante' => $cheque_data['nro_comprobante']), array('id' => $dep_id));
        } else {

            $efectivo_nuevo = $this->input->post('input_efectivo');
            $cheque_data['monto_total'] = $this->generic_model->get_val_where('bill_deposito', array('id' => $dep_id), 'monto');

            //Depuracion
//        echo '<br>Deposito Id: ' . $dep_id;
//        echo '<br>Monto actual efectivo: ' . $monto_efectivo;
//        echo '<br>Monto anterior efectivo: ' . $cheque_data['monto_efectivo'];
//        echo '<br>Monto total: ' . $cheque_data['monto_total'];

            $cheque_data['monto_total'] = ($cheque_data['monto_total'] - $cheque_data['monto_efectivo']) + $efectivo_nuevo;
            //Actualizar o guardar el detalle de efectivo        
            if (!empty($efectivo_nuevo)) {
                $detalle_guardar = array('deposito_id' => $dep_id,
                    'valor' => $efectivo_nuevo,
                    'doc_id' => '0',
                    'tipo_doc_deposito' => '2',
                    'estado_doc' => '2',
                );
                $this->generic_model->update('bill_deposito_detalle', array('valor' => $efectivo_nuevo), array('tipo_doc_deposito' => '2', 'deposito_id' => $dep_id));

                $this->generic_model->save($detalle_guardar, 'bill_deposito_detalle');
            }//Actualizar el numero de comprobante
        }
        //Actualizar monto total deposito
        $this->generic_model->update('bill_deposito', array('monto' => $cheque_data['monto_total']), array('id' => $dep_id));
        $this->load->view('resumen_deposito', $cheque_data);
    }

    /* Funcion para pasar los cheques de ese deposito a depositado, registrado */

    function cambiar_estado_cheques($id_dep, $estado, $asiento_id, $banco_id) {
        $where_cheques = array('deposito_id =' => $id_dep, 'tipo_doc_deposito =' => '1', 'estado_doc = ' => '2');
        $lista_cheques = $this->generic_model->get('bill_deposito_detalle', $where_cheques);
//        echo 'Asiento ID M> '.$asiento_id;

        foreach ($lista_cheques as $cheque) {
            $id_cheque = $cheque->doc_id;
            $nro_cheque = $this->generic_model->get_val_where('bill_chequescustodio', array('id' => $cheque->doc_id), 'nrocheque');
            $monto_cheque = $cheque->valor;

//            echo '<br>IdCheque: ' . $id_cheque . '<br>';
            $this->generic_model->update('bill_chequescustodio', array('estado' => $estado), array('id' => $id_cheque));
            if ($estado == 3) {//Si se confirma el deposito registrar credito de ese cheque
                $this->registrar_credito_asiento($id_dep, $cod_cta = '013', $monto_cheque, '2', $nro_cheque, $asiento_id, $banco_id);
            }
        }
//        print_r($lista_cheques);
    }

    function ya_registrado($id_dep) {
        $depositado = $this->generic_model->get_val_where('bill_deposito', array('id' => $id_dep), 'estado');
        if ($depositado == 2) {
            echo '<br>YA ESTA REGISTRADO COMO DEPOSITADO';
            return true;
        }
        if ($depositado == -2) {
            echo '<br>YA ESTA REGISTRADO COMO ANULADO';
            return true;
        } else {
            return false;
        }
    }

    function registrar_asiento_cont($doc_id, $tipo_trans) {
        $anio = date("Y"); //anio actual
        $mes = date("n"); //mes actual
        $fecha = date("Y-n-j"); //fecha de hoy
//        $hora_actual = strtotime('-6 hour', strtotime(date('H:i:s', time())));
        $hora_actual = strtotime(date('H:i:s', time()));

        $hora = date('H:i:s', $hora_actual);

        $asiento_cont = array('anio' => $anio,
            'mes_id' => $mes,
            'fecha' => $fecha,
            'hora' => $hora,
            'estado' => '1',
            'user_id' => $this->user->id,
            'tipotransaccion_cod' => $tipo_trans,
            'doc_id' => $doc_id);

        $this->generic_model->save($asiento_cont, 'bill_asiento_contable');
    }

    function registrar_debito_asiento($doc_id, $cod_cta, $nombre_banco, $monto, $asiento_id, $banco_id) {
        //Si es debito otra cuenta, se extrae de 'contaconfigcuentas'

        $detalle_trans = $this->generic_model->get_val_where('billing_tipotransaccion', array('cod' => $this->tipotransaccion_cod), 'nombre');
        $detalle = $this->generic_model->get_val_where('billing_contaconfigcuentas', array('cod' => $cod_cta), 'nombre');
        if (!empty($cod_cta)) {
            $codigo_cont = $this->generic_model->get_val_where('billing_contaconfigcuentas', array('cod' => $cod_cta), 'contacuentasplan_cod');
        }
        //Si es debito banco jalamos la cta de la tabla bancos
        if (!empty($nombre_banco)) {
            $codigo_cont = $this->generic_model->get_val_where('billing_banco', array('id' => $banco_id), 'contacuentasplan_cod');
            $detalle = ' ';
        } else {
            //Asignamos a nombre_banco el nombre del usuario de la cxc para ahorrar variables
            $cheque_id = $this->generic_model->get_val_where('bill_cxc', array('id' => $doc_id), 'doc_id'); //extraigo el id del cheque de cxc
            $cedula_cliente = $this->generic_model->get_val_where('bill_chequescustodio', array('id' => $cheque_id), 'cliente_cedulaRuc');
            $nombre_banco = $this->generic_model->get_val_where('billing_cliente', array('PersonaComercio_cedulaRuc' => $cedula_cliente), 'nombres');
        }
        $detalle_asiento = array('asiento_contable_id' => $asiento_id,
            'cuenta_cont_id' => $codigo_cont,
            'debito' => $monto,
            'credito' => '0',
            'tipotransaccion_cod' => $this->tipotransaccion_cod,
            'doc_id' => $doc_id,
            'detalle' => $detalle_trans . ' ' . $detalle . ' ' . $nombre_banco
        );
        $this->generic_model->save($detalle_asiento, 'bill_asiento_contable_det');
    }

    function registrar_credito_asiento($doc_id, $cod_cta, $monto, $cta_tipo, $nro_cheque, $asiento_id, $banco_id) {
        //Teniendo en cuenta q el registro de un credito de un deposito modifica las ctas bancarias
//      echo 'Asiento ID M> '.$asiento_id;
//      echo 'Cod Cta> '.$cod_cta;
        //Extraer nombre del cliente para grabar en el detalle del credito del a.c
        $detalle_trans = $this->generic_model->get_val_where('billing_tipotransaccion', array('cod' => $this->tipotransaccion_cod), 'nombre');
        $cedula_cliente = $this->generic_model->get_val_where('bill_chequescustodio', array('nrocheque' => $nro_cheque), 'cliente_cedulaRuc');
        $nombre_cliente = $this->generic_model->get_val_where('billing_cliente', array('PersonaComercio_cedulaRuc' => $cedula_cliente), 'nombres');


        //Array con los datos del asiento
        $detalle_asiento = array('asiento_contable_id' => $asiento_id,
            'debito' => '0',
            'credito' => $monto,
            'tipotransaccion_cod' => $this->tipotransaccion_cod,
            'doc_id' => $doc_id,
        );

//Si hay un cod_cta, lo extrae de contaconfigcuentas        
        if (!empty($cod_cta)) {
            $codigo_cont = $this->generic_model->get_val_where('billing_contaconfigcuentas', array('cod' => $cod_cta), 'contacuentasplan_cod');

            $detalle_asiento['cuenta_cont_id'] = $codigo_cont;

            if ($cta_tipo == '1') {//detalle credito efectivo
                $detalle_asiento['detalle'] = $detalle_trans;
            }
            if ($cta_tipo == '2') {//detalle credito cheques
                $detalle_asiento['detalle'] = 'Cheque N°' . $nro_cheque . ' Cliente ' . $nombre_cliente;
            }
            $this->registrar_cuenta_bancaria($doc_id, $nro_cheque, '0', $monto, $cta_tipo, $asiento_id, $banco_id);
        } else {//si cod_cta es null, el codigo se extrae de la tabla bancos
            $codigo_cont = $this->generic_model->get_val_where('billing_banco', array('id' => $banco_id), 'contacuentasplan_cod');

            $detalle_asiento['cuenta_cont_id'] = $codigo_cont;
            $detalle_asiento['detalle'] = $detalle_trans;

            //Registro de saldos de ctas bancarias
            $this->registrar_cuenta_bancaria($doc_id, $nro_cheque, $monto, '0', $cta_tipo, $asiento_id, $banco_id);
        }
        $this->generic_model->save($detalle_asiento, 'bill_asiento_contable_det');

        //Tipo de tabla cuentabancaria_tipo
    }

    function registrar_cuenta_bancaria($dep_id, $nro_cheque, $debito, $credito, $cta_tipo, $asiento_id, $banco_id) {
        $fecha = date("Y-n-j"); //fecha de hoy
        $anio = date("Y"); //anio actual
        $mes = date("n"); //mes actual
//        $hora_actual = strtotime('-6 hour', strtotime(date('H:i:s', time())));
        $hora_actual = strtotime(date('H:i:s', time()));

        $hora = date('H:i:s', $hora_actual);
        $saldo_anterior = $this->generic_model->get_val_where('bill_cuentabancaria_saldos', array('banco_id' => $banco_id), 'saldo');

        $detalle_cuenta = array('banco_id' => $banco_id,
            'nombre_usuario' => 'MASTER PC CIA. LTDA',
            'doc_id' => $dep_id,
            'tipo_id' => $cta_tipo,
            'tipo_transaccion' => $this->tipotransaccion_cod,
            'asiento_id' => $asiento_id,
            'anio' => $anio,
            'mes' => $mes,
            'conciliado' => 0,
            'fecha_registro' => $fecha,
            'empleado_id' => '1',
            'hora' => $hora,
            'estado' => '1');

        if (!empty($credito)) {
//            echo 'cta_tipo: '.$cta_tipo;
            $saldo_nuevo = $saldo_anterior + $credito;
            $detalle_cuenta['credito'] = $credito;
            $detalle_cuenta['saldo'] = $saldo_nuevo;


            if ($cta_tipo == '2') {//deposito cuenta bancaria con cheques
                $detalle_cuenta['fecha_vence'] = $this->generic_model->get_val_where('bill_chequescustodio', array('nrocheque' => $nro_cheque), 'fechacobro');

                $detalle_cuenta['nota'] = 'Por registro deposito de cheque';
            }
            if ($cta_tipo == '1') {//deposito en efectivo
                $detalle_cuenta['fecha_vence'] = $fecha;
                $detalle_cuenta['fecha_cobro_real'] = $fecha;
                $detalle_cuenta['fecha_registro_cobro_real'] = $fecha;

                $detalle_cuenta['nota'] = 'Por registro deposito en efectivo';
            }
//            echo '<br> Detalle Nota: '.$detalle_cuenta['nota'];
        }
        if (!empty($debito)) {
            $saldo_nuevo = $saldo_anterior - $debito;
            $detalle_cuenta['debito'] = $debito;
            $detalle_cuenta['saldo'] = $saldo_nuevo;
            $nota = $this->generic_model->get_val_where('billing_tipotransaccion', array('cod' => $this->tipotransaccion_cod), 'nombre')
                    . ' ' . $this->generic_model->get_val_where('bill_cuentabancaria_tipo', array('id' => $cta_tipo), 'tipo');
            $detalle_cuenta['nota'] = $nota;
        }

        $this->registrar_saldos_banco($banco_id, $saldo_nuevo);
        $this->generic_model->save($detalle_cuenta, 'bill_cuentabancaria');
    }

    function registrar_saldos_banco($id_banco, $saldo) {
        $fecha = date("Y-n-j"); //fecha de hoy  
        $mes = date("n"); //mes actual
//        $hora_actual = strtotime('-6 hour', strtotime(date('H:i:s', time())));
        $hora_actual = strtotime(date('H:i:s', time()));

        $hora = date('H:i:s', $hora_actual);

        $detalle_saldo = array(
            'saldo' => $saldo,
            'mes_id' => $mes,
            'fecha' => $fecha,
            'user_id' => $this->user->id,
            'hora' => $hora);

        $this->generic_model->update('bill_cuentabancaria_saldos', $detalle_saldo, array('banco_id' => $id_banco));
    }

    function ver_asiento_contable($asiento_id, $banco_name, $show_transfer_btn) {

        $banco = urldecode($banco_name);
        $codigo_cuenta_cont = $this->generic_model->get_val_where('bill_asiento_contable_det', array('asiento_contable_id =' => $asiento_id), 'cuenta_cont_id');


        $where_asiento = array('ac.id =' => $asiento_id);
        $group_by = null;

        if ($codigo_cuenta_cont == '101010102') {//Mismo codigo para cxc en 2 cuentas, se quema el codigo para evitar mala presentacion del comprobante
            $join_clause = array(
                '0' => array('table' => 'bill_asiento_contable_det ad', 'condition' => 'ac.id = ad.asiento_contable_id '),
                '1' => array('table' => 'billing_contaconfigcuentas cta', 'condition' => 'ad.cuenta_cont_id = cta.contacuentasplan_cod AND cta.cod = 030', 'type' => 'LEFT'),
//                '1' => array('table' => 'billing_contaconfigcuentas cta', 'condition' => 'ad.cuenta_cont_id = cta.contacuentasplan_cod', 'type' => 'LEFT'),
                '2' => array('table' => 'billing_banco bc', 'condition' => 'ad.cuenta_cont_id = bc.contacuentasplan_cod', 'type' => 'LEFT')
            );
            $group_by = 'cta.contacuentasplan_cod';
        } else {
            $join_clause = array(
                '0' => array('table' => 'bill_asiento_contable_det ad', 'condition' => 'ac.id = ad.asiento_contable_id '),
//                        '1' => array('table' => 'billing_contaconfigcuentas cta', 'condition' => 'ad.cuenta_cont_id = cta.contacuentasplan_cod AND cta.cod = 030', 'type' => 'LEFT'),
                '1' => array('table' => 'billing_contaconfigcuentas cta', 'condition' => 'ad.cuenta_cont_id = cta.contacuentasplan_cod', 'type' => 'LEFT'),
                '2' => array('table' => 'billing_banco bc', 'condition' => 'ad.cuenta_cont_id = bc.contacuentasplan_cod', 'type' => 'LEFT')
            );
        }

        $res['asientos_data'] = $this->generic_model->get_join('bill_asiento_contable ac', $where_asiento, $join_clause, 'ad.*, ac.fecha, ac.hora, ac.estado, cta.nombre cuenta, bc.nombre banco', 0, null, $group_by);
        $res['asiento_id'] = $asiento_id;
        $res['show_btn'] = $show_transfer_btn;
        $res['user'] = $banco;
        //para transferir a otro periodo
        $res['periodo_list'] = $this->generic_model->get('bill_cuentabancaria', array('id >' => '0'), array('anio'), null, null, null, null, array('anio'));
        $res['mes_list'] = $this->generic_model->get('bill_mes', array('id >' => '0'));
        $res['dep_id'] = $this->generic_model->get_val_where('bill_asiento_contable', array('id =' => $asiento_id), 'doc_id');

//        print_r($res);

        $this->load->view('asiento_contable_view', $res);
    }

    /* Extraer el asiento contable(comprobante) desde el doc_id(deposito o cxc) */

    function ver_asiento_contable_dep($doc_id, $banco_id, $concil, $btn, $tipo_trans) {
//        echo 'Ver asiento contable dep id: '.$dep_id;
//        echo '<br>concil: '.$concil;
        // tipo trans:
        //16 deposito o 24 cxc
        $boton_select = $btn;
        $show_transfer_btn = $concil;
        $nombre_banco = $this->generic_model->get_val_where('billing_banco', array('id' => $banco_id), 'nombre');

//        $nombre_banco = urldecode($banco);


        /* Obtener el idAsiento desde el doc_id */
        if ($boton_select == 5) {
//            $dep_id = $this->input->get('dep_id');
//            $asiento_id = $this->generic_model->get('bill_asiento_contable', 
//                    array('doc_id' => $doc_id,
//                        '(tipotransaccion_cod ='=>'16'
//                        ), 'id', null, null, null, null, null, 
//                    array('tipotransaccion_cod ='=>'24)'));
            if ($tipo_trans == '16') {//si es deposito
                $asiento_id = $this->generic_model->get_val_where('bill_asiento_contable', array('doc_id' => $doc_id, 'tipotransaccion_cod =' => $tipo_trans), 'id');
            }
            if ($tipo_trans == '24') {//si es cxc
                $asiento_id = $this->generic_model->get_val_where('bill_asiento_contable', array('doc_id' => $doc_id, 'tipotransaccion_cod =' => $tipo_trans), 'id');
            }
            $this->ver_asiento_contable($asiento_id, $nombre_banco, $show_transfer_btn);
        }
    }

//    function ver_asiento_contable_dep() {
//        $boton_select = $this->input->post('btn');
//        $show_transfer_btn = $this->input->post('concil');
//        $nombre_banco = $this->input->post('banco');
//
//
//        /* Obtener el idAsiento desde el deposito */
//        if ($boton_select == 5) {
//            $dep_id = $this->input->post('dep_id');
//            $asiento_id = $this->generic_model->get_val_where('bill_asiento_contable', array('doc_id' => $dep_id), 'id');
//            $this->ver_asiento_contable($asiento_id, $nombre_banco, $show_transfer_btn);
//        }
//    }
    //Ver comprobante de un cheque depositado
    function ver_asiento_contable_cheque_depositado($nro_cheque) {

        $cheque_id = $this->generic_model->get_val_where('bill_chequescustodio', array('nrocheque' => $nro_cheque), 'id');

        //Extraemos el doc_id desde depositos
        $doc_id = $this->generic_model->get_val_where('bill_deposito_detalle', array('doc_id' => $cheque_id), 'deposito_id');


        $banco_id = $this->generic_model->get_val_where('bill_deposito', array('id' => $doc_id), 'banco_id');
        $nombre_banco = $this->generic_model->get_val_where('billing_banco', array('id' => $banco_id), 'nombre');

        $show_transfer_btn = 0;

//        $asiento_id = $this->generic_model->get_val_where('bill_asiento_contable', array('doc_id' => $cheque_id), 'id');
        $asiento_id = $this->generic_model->get_val_where('bill_asiento_contable', array('doc_id' => $doc_id), 'id');
        $this->ver_asiento_contable($asiento_id, $nombre_banco, $show_transfer_btn);
    }

    /* Ver comprobante de un cheque depositado */

    function ver_asiento_contable_cheque_protestado($nro_cheque) {

        $cheque_id = $this->generic_model->get_val_where('bill_chequescustodio', array('nrocheque' => $nro_cheque), 'id');


        //Extraemos el doc_id desde cxc del cheque
        $doc_id = $this->generic_model->get_val_where('bill_cxc', array('doc_id' => $cheque_id), 'id');


        $banco_id = $this->generic_model->get_val_where('bill_cuentabancaria', array('doc_id' => $doc_id), 'banco_id');
        $nombre_banco = $this->generic_model->get_val_where('billing_banco', array('id' => $banco_id), 'nombre');

        $show_transfer_btn = 0;

//        $asiento_id = $this->generic_model->get_val_where('bill_asiento_contable', array('doc_id' => $cheque_id), 'id');
        $asiento_id = $this->generic_model->get_val_where('bill_asiento_contable', array('doc_id' => $doc_id, 'tipotransaccion_cod' => 24), 'id');
        $this->ver_asiento_contable($asiento_id, $nombre_banco, $show_transfer_btn);
    }

}
