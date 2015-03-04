<?php

/**
 * Description of Depositos
 *
 * @author Mariuxi
 */
class Depositotarjetas extends MX_Controller {

    public function index() {
        $res['view'] = $this->load->view('header_depositos', '', TRUE);
        $res['slidebar'] = $this->load->view('slidebar','',TRUE);                           
        $this->load->view('templates/dashboard',$res);
    }

//  Listar tarjetas de crédito según la opción seleccionada en la página principal
    public function listar_depositos_opc() {
        $opcion = $this->input->post('opc_buscar_dep');
        $fecha_desde = $this->input->post('fecha_desde_dep');
        $fecha_hasta = $this->input->post('fecha_hasta_dep');
        $fields = array('bill_deposito.id', 'bill_deposito.monto', "bill_deposito.fecha");
        $depositos['opcion_tabla'] = $opcion;
        if ($opcion == 1) {
            $depositos['depositos'] = $this->generic_model->get('bill_deposito', array('estado' => '1', 'bill_deposito.fecha >=' => $fecha_desde, 'bill_deposito.fecha <=' => $fecha_hasta), $fields, 0);
        } elseif ($opcion == 2) {
            $depositos['depositos'] = $this->generic_model->get('bill_deposito', array('estado' => '2', 'bill_deposito.fecha >=' => $fecha_desde, 'bill_deposito.fecha <=' => $fecha_hasta), $fields, 0);
        }
        $this->load->view('lista_depositos', $depositos);
    }
//Confirma el deposito y guarda en las tablas correspondientes
    public function confirmar_deposito() {
        $id_Dep = $this->input->post('id_dep_conf');
        //Cambia el estado de un deposito a confirmado
        $this->generic_model->update('bill_deposito', array('estado' => '2'), array('id' => $id_Dep));
        //Procedimiento para guardar el asiento contable
        $id_asiento=$this->guardar_asiento_cont($id_Dep);
        //Procedimiento para guardar el detalle del asiento contable correspondiente al banco
        $this->guardar_detalle_asiento_banco($id_Dep, $id_asiento);
        //Procedimiento para guardar el detalle del asiento contable correspondiente a las tarjetas
        $this->guardar_detalle_asiento_tarjs($this->cambiar_est_tarj($id_Dep), $id_asiento, $id_Dep);
        echo 'Deposito confirmado';
    }
//Guarda en la tabla bill_asiento_contable el deposito realizado y retorna el numero del registro generado
    public function guardar_asiento_cont($idDep) {
        //Para obtener la fecha y hora en la cual se hizo efectivo el deposito
        $fechas_hora = $this->registrar_fecha_hora();
        //Datos para guardar en la tabla bill_asiento_contable
        $form_data_asi = array(
            'anio' => $fechas_hora['anio'],
            'mes_id' => $fechas_hora['mes'],
            'fecha' => $fechas_hora['fecha'],
            'hora' => $fechas_hora['hora'],
            'tipotransaccion_cod' => '16',
            'estado' => '1',
            'user_id' => '1',
            'doc_id' => $idDep
        );
        $asiento = $this->generic_model->save($form_data_asi, 'bill_asiento_contable');
        return $asiento;
    }
 //Trabaja con la tabla bill_asiento_contable_det, en este caso se guardan los datos
// con respecto al banco en el cual se hizo el deposito
public function guardar_detalle_asiento_banco($id_Dep,  $id_asiento){
        $id_banco=$this->generic_model->get_val_where('bill_deposito', array('id'=>$id_Dep), 'banco_id',  null,  -1);
        $monto_dep=$this->generic_model->get_val_where('bill_deposito', array('id'=>$id_Dep), 'monto',  null,  -1);
        $cuenta_plan_banco=$this->generic_model->get_val_where('billing_banco', array('id'=>$id_banco), 'contacuentasplan_cod',  null,  -1);
        $nombre_banco=$this->generic_model->get_val_where('billing_banco', array('id'=>$id_banco), 'nombre',  null,  -1);
        $str_detalle='DEPOSITO, ';
        $form_data_det=array(
            'asiento_contable_id'=>$id_asiento,
            'cuenta_cont_id'=>$cuenta_plan_banco,
            'debito'=>$monto_dep,
            'tipotransaccion_cod'=>'16',
            'doc_id'=>$id_Dep,
            'detalle'=>$str_detalle.$nombre_banco
        );
        $this->generic_model->save($form_data_det, 'bill_asiento_contable_det');
        //Actualiza el saldo en el banco en el cual se realiza el deposito
        $this->actualizar_saldo_banco($id_banco, $monto_dep);
        //Guarda en la tabla bill_cuentabancaria
        $this->guardar_cuenta_bancaria($id_banco, $id_Dep, $monto_dep, $id_asiento);
    }
    
//Actualiza el saldo en el banco en el cual se hizo el deposito
    public function actualizar_saldo_banco($id_banco, $monto_dep){
        $fields=array('bill_cuentabancaria_saldos.id', 'bill_cuentabancaria_saldos.saldo' );
        $cuenta_banco=$this->generic_model->get('bill_cuentabancaria_saldos', array('banco_id'=>$id_banco), $fields, null, 0 );
        $saldo_banco=$cuenta_banco[0]->saldo+$monto_dep;
        $id_cuenta_bancaria=$cuenta_banco[0]->id;
        $data_tiempo=$this->registrar_fecha_hora();
        $data_cuenta_saldos=array(
            'saldo'=>$saldo_banco, 
            'mes_id'=>$data_tiempo['mes'],
            'fecha'=>$data_tiempo['fecha'],
            'hora'=>$data_tiempo['hora'],
            'user_id'=> '',
        );
        //actualiza datos como el saldo en el banco 
        $this->generic_model->update_by_id('bill_cuentabancaria_saldos',$data_cuenta_saldos , $id_cuenta_bancaria, 'id');
       
    }
 //Guarda en la tabla bill_cuentabancaria los datos correspondientes al deposito, asiento...
    public function guardar_cuenta_bancaria($id_banco, $id_dep, $monto_dep, $id_asiento){
        $data_tiempo=$this->registrar_fecha_hora();
        $saldo_banco=$this->generic_model->get_val_where('bill_cuentabancaria_saldos', array('banco_id'=>$id_banco), 'saldo', null, -1);
        $str_nota='DEPOSITO TARJETAS DE CREDITO';
        //Datos para guardar en la tabla bill_cuentabancaria
        $form_data=array(
            'banco_id'=>$id_banco,
            'nombre_usuario'=>get_settings('RAZON_SOCIAL'),
            'tipo_id'=>'3',
            'fecha_vence'=>$data_tiempo['fecha'],
            'fecha_cobro_real'=>$data_tiempo['fecha'],
            'fecha_registro'=>$data_tiempo['fecha'],
            'fecha_registro_cobro_real'=>$data_tiempo['fecha'],
            'doc_id'=>$id_dep,
            'tipo_transaccion'=>'16',
            'debito'=>$monto_dep,
            'credito'=>'',
            'saldo'=>$saldo_banco,
            'asiento_id'=>$id_asiento,
            'anio'=>$data_tiempo['anio'],
            'mes'=>$data_tiempo['mes'],
            'conciliado'=>'1',
            'empleado_id'=>'1',
            'hora'=>$data_tiempo['hora'],
            'nota'=>$str_nota,
            'estado'=>'1',
        );
        $this->generic_model->save($form_data,'bill_cuentabancaria');
    }

    //Guarda el detalle de las tarjetas depositadas en la tabla asiento_contable_det  
    public function guardar_detalle_asiento_tarjs($tarjetas_asiento, $id_asiento, $id_Dep){
        $cuenta_plan_tarj='10102060102';
        $str_detalle='DEPOSITO, TARJETA N ';
        foreach ($tarjetas_asiento as $val) {
            $valor_tar=$this->generic_model->get_val_where('bill_tarjetascustodio', array('id'=>$val->doc_id), 'valor',  null,  -1);
            $nro_tar=$this->generic_model->get_val_where('bill_tarjetascustodio', array('id'=>$val->doc_id), 'nrotarjeta',  null,  -1);
            $form_data_det=array(
            'asiento_contable_id'=>$id_asiento,
            'cuenta_cont_id'=>$cuenta_plan_tarj,
            'credito'=>$valor_tar,
            'tipotransaccion_cod'=>'16',
            'doc_id'=>$id_Dep,
            'detalle'=>$str_detalle.$nro_tar
        );
        $this->generic_model->save($form_data_det, 'bill_asiento_contable_det');
        }
    }
    
    //Cambia de estado las tarjetas de credito, después de confirmar el déposito
    public function cambiar_est_tarj($idDep) {
        $fields = array('bill_deposito_detalle.doc_id', 'bill_deposito_detalle.valor', );
        $tar_confir = $this->generic_model->get('bill_deposito_detalle', array('deposito_id' => $idDep), $fields, 0);
        //Actualización de las tarjetas con deposito confirmado
        foreach ($tar_confir as $value) {
            $this->generic_model->update_by_id('bill_tarjetascustodio', array('estado' => '2'), $value->doc_id, 'id');
        }
        return $tar_confir;
    }

    //Permite obtener un array con datos correspondientes a la fecha y hora
    public function registrar_fecha_hora() {
        date_default_timezone_set('America/Guayaquil');
        $datos_fecha['fecha'] = date('Y-m-d');
        $datos_fecha['hora'] = date('H:i:s');
        $datos_fecha['anio'] = date('Y');
        $datos_fecha['mes'] = date('m');
        return $datos_fecha;
    }

    //Obtiene datos de un depósito
    public function obtener_datos_dep() {
        $id_Dep = $this->input->post('id_dep_mod');
        $dep_modificar['id_dep'] = $id_Dep;
        $dep_modificar['monto'] = $this->input->post('monto_dep_mod');
        $dep_modificar['fecha'] = $this->input->post('fecha_dep_mod');
        $fields2 = array('bill_deposito_detalle.deposito_id', 'bill_deposito_detalle.valor', "bill_deposito_detalle.doc_id");
        $dep_modificar['detalle_dep'] = $this->generic_model->get('bill_deposito_detalle', array('deposito_id' => $id_Dep), $fields2, null, 0);
        $this->load->view('mostrar_deposito', $dep_modificar);
    }

    //Cambia el estado de un depósito a anulado
    public function anular_deposito() {
        $id_Dep = $this->input->post('id_dep_elim');
        $this->generic_model->update('bill_deposito', array('estado' => '3'), array('id' => $id_Dep));
        echo 'DEPOSITO ANULADO';
    }

}
