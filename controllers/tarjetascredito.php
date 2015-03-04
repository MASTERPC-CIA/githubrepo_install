<?php
/**
 * Description of Tarjetas
 *
 * @author Mariuxi
 */
class Tarjetascredito extends MX_Controller {

    public function index() {
        $res['view'] = $this->load->view('header_tarjetas', '', TRUE);
        $res['slidebar'] = $this->load->view('slidebar', '', TRUE);
        $this->load->view('templates/dashboard', $res);
    }

//  Listar tarjetas de crédito según la opción seleccionada en la vista principal
    public function listar_tarjetas_opc() {
        $opcion = $this->input->post('opc_buscar');
        $fecha_desde = $this->input->post('fecha_desde');
        $fecha_hasta = $this->input->post('fecha_hasta');
        //      Hace un join de una tarjeta en custodio
        $join_cluase = array(
            '0' => array('table' => 'billing_cliente', 'condition' => 'billing_cliente.PersonaComercio_cedulaRuc=bill_tarjetascustodio.cliente_cedulaRuc'),
            '1' => array('table' => 'billing_tarjetacredito', 'condition' => 'billing_tarjetacredito.cod=bill_tarjetascustodio.tarjeta_id'));
        //Determina que campos se deben mostrar
        $fields = array('bill_tarjetascustodio.id', "billing_tarjetacredito.nombre tarjeta", 'billing_cliente.nombres', 'bill_tarjetascustodio.fecha', 'bill_tarjetascustodio.valor');
        if ($opcion == 2) {
            $tarjetas['tarjetas'] = $this->generic_model->get_join('bill_tarjetascustodio', array('estado' => '2', 'bill_tarjetascustodio.fecha >=' => $fecha_desde, 'bill_tarjetascustodio.fecha <=' => $fecha_hasta), $join_cluase, $fields, 0);
            $this->load->view('lista_tarjetas', $tarjetas);
        } else {
            $tarjetas['tarjetas'] = $this->generic_model->get_join('bill_tarjetascustodio', array('estado' => '1', 'bill_tarjetascustodio.fecha >=' => $fecha_desde, 'bill_tarjetascustodio.fecha <=' => $fecha_hasta), $join_cluase, $fields, 0);
            $tarjetas['plan_cuentas'] = $this->generic_model->get('billing_contacuentasplan', array('contacuentas_cod' => '1'), array('billing_contacuentasplan.cod', 'billing_contacuentasplan.nombre'), null, 0);
            $tarjetas['lista_bancos'] = $this->generic_model->get('billing_banco', null, array('billing_banco.id', 'billing_banco.nombre'), null, 0);
            $this->load->view('comprobante_tarjeta', $tarjetas);
        }
    }

//Cambia de estado las tarjetas de custodio a depositadas
    public function cambiar_estado_tarj() {
        $ids_tarj = $this->input->post('selected_tarj');
        $date_time = $this->registrar_fecha_hora(); //Obtiene la fecha y hora
        //Modificar datos de las tarjetas seleccionadas
        foreach ($ids_tarj as $id_tarj) {
            $this->generic_model->update('bill_tarjetascustodio', array('estado' => '2', 'fechacambioestado' => $date_time['fecha'], 'horacambioestado' => $date_time['hora']), array('id' => $id_tarj));
        }
      }

    //Suma los valores de las tarjetas seleccionadas
    public function sumar_val_tarjetas() {
        $selected_tarj = $this->input->post('selected_tarj');
        $val_tot_tarj = 0;
        if ($selected_tarj) {
            foreach ($selected_tarj as $selec) {
                $val_tot_tarj += $this->generic_model->get_val_where('bill_tarjetascustodio', array('id' => $selec), 'valor', null, -1);
            }
        } else {
            die();
        }
        return $val_tot_tarj;
    }

    //Suma los valores de cada cuenta seleccionada
    public function sumar_val_cuentas() {
        $val_tot = 0;
        for ($i = 0; $i < 2; $i++) {
            $val_tot+=$this->guardar_val_cuentas()[$i];
        }
        return $val_tot;
    }

    //Crea un array con los ids de cada cuenta seleccionada incluida la del banco
    public function guardar_cuentas() {
        $data_cuentas = array(
            $this->input->post('cta_1'),
            $this->input->post('cta_2'),
            $this->input->post('banco_selecc'));
        return($data_cuentas);
    }
    public function get_name_cuentas(){
        $name_cuentas = array(
             $this->generic_model->get_val_where('billing_contacuentasplan', array('cod' => $this->input->post('cta_1')), 'nombre', null, -1),
             $this->generic_model->get_val_where('billing_contacuentasplan', array('cod' => $this->input->post('cta_2')), 'nombre', null, -1)
        );
        return $name_cuentas;
    }

    //Crea un array con los valores ingresados
    public function guardar_val_cuentas() {
        $data_valores = array(
            $this->input->post('valor_plan_1'),
            $this->input->post('valor_plan_2'));
//            $this->input->post('valor_banco'));
        return $data_valores;
    }

//Valida que los datos ingresados sean correctos para generar el comprobante de ingreso
    public function generar_comprobante() {
        $ids_tarj = $this->input->post('selected_tarj');
        $id_banco_selec = $this->input->post('banco_selecc');
        $str_estado='';
            if ($id_banco_selec > 0) {
                $date_time = $this->registrar_fecha_hora(); //Obtiene la fecha y hora
                //Guardar comprobante de ingreso
                $id_compIng = $this->generic_model->save(array(
                    'anio' => $date_time['anio'],
                    'fecha' => $date_time['fecha'],
                    'cantidad_number' => $this->sumar_val_tarjetas(),
                    'client_id' => '0101114031',
                    'recibo_tipo_id'=>1,
                    'nota' => 'ACREDITACION DE VOUCHERS',
                    'tipotransaccion_cod' => '16'),'bill_recibo');
                    
//                actualización del estado de la tarjeta de credito
                $this->cambiar_estado_tarj($ids_tarj);
//                Guardar en la tabla deposito
                $monto_dep=$this->sumar_val_tarjetas()-$this->sumar_val_cuentas();
                $this->guardar_deposito($id_banco_selec, $ids_tarj, $id_compIng, $date_time, $monto_dep);
            } else {
                $str_estado = 'Seleccione el banco en el que va a depositar';
            }
        
        return $str_estado;
    }

    public function guardar_deposito($id_banco, $id_tarj, $id_compIng, $date_time, $monto) {
        //Guardar en la tabla deposito
        $id_dep = $this->generic_model->save(array(
            'banco_id' => $id_banco,
            'nro_comprobante' => $id_compIng,
            'monto' => $monto,
            'estado' => '1',
            'fecha' => $date_time['fecha'],
            'hora' => $date_time['hora']), 'bill_deposito');
        //Guardar los detalles del depósito
        $this->guardarDetalleDeposito($id_dep, $id_tarj);
        $this->mostrarComprobante($id_dep, $id_banco);
    }

    //Muestra el detalle del deposito
    public function mostrarComprobante($idDep, $id_banco) {
        $data_compIng['data_deposito'] = $this->generic_model->get('bill_deposito', array('id' => $idDep), '', null, 1);
        $data_compIng['data_banco']=$this->generic_model->get('billing_banco', array('id'=>$id_banco), '', null, 1);
        $data_compIng['total_tarjs'] = $this->sumar_val_tarjetas();
        $data_compIng['cuentas'] = $this->guardar_cuentas();
        $data_compIng['val_cuentas'] = $this->guardar_val_cuentas();
        $data_compIng['name_cuentas']=$this->get_name_cuentas();
        $this->load->view('comprobante_ingreso', $data_compIng);
    }

    //Genera la fecha y hora actual
    public function registrar_fecha_hora() {
        date_default_timezone_set('America/Guayaquil');
        $datos_fecha['fecha'] = date('Y-m-d');
        $datos_fecha['hora'] = date('H:i:s');
        $datos_fecha['anio'] = date('Y');
        return $datos_fecha;
    }

    // Permite guardar los detalles del deposito
     // Permite guardar los detalles del deposito
    public function guardarDetalleDeposito($id_deposito, $ids_tarjetas) {
        foreach ($ids_tarjetas as $id_tarj) {
            $valor = $this->generic_model->get_val_where('bill_tarjetascustodio', array('id' => $id_tarj), 'valor', null, -1);
            $this->generic_model->save(array('deposito_id' => $id_deposito, 'valor' => $valor, 'doc_id' => $id_tarj, 'tipo_doc_deposito' => '3'), 'bill_deposito_detalle');
        }
    }

}
