<?php


$this->load->helper('form');
date_default_timezone_set('America/Guayaquil');
//Para obtener la fecha menor de todos lo depositos
$fechas=$this->generic_model->get('bill_deposito', null, array('bill_deposito.fecha'), array('fecha'=>'asc'),  0 );
$fecha_inicio=$fechas[0]->fecha;
$fecha_actual = date('Y-m-d');
echo Open('form', array('action' => base_url('bancos/depositotarjetas/listar_depositos_opc'), 'method' => 'post'));

    echo tagcontent('h2', 'DEPOSITOS  DE TARJETAS DE CREDITO', array('class' => 'class1'));
    echo '<div class="row">';
        echo '<div class="col-md-8">';
            echo tagcontent('h5', 'Seleccione el rango de fechas para buscar:');
            echo '<label>FECHA DESDE:</label>';
            echo input(array('value' => $fecha_inicio, 'class' => 'input-md datepicker', 'name' => 'fecha_desde_dep', 'contentclass' => 'col-md-4', 'required' => '', 'maxlength' => '10'));
            echo '<label>FECHA HASTA:</label>';
            echo input(array('value' => $fecha_actual, 'class' => 'input-md datepicker', 'name' => 'fecha_hasta_dep', 'contentclass' => 'col-md-4', 'required' => '', 'maxlength' => '10'));
        echo '</div>';
        echo '<div class="col-md-4">';
            $opciones_dep = array(
                  '1'  => 'Depositos por Confirmar',
                  '2'  => 'Depositos Confirmados',
            );
            echo form_dropdown('opc_buscar_dep', $opciones_dep, '1');
        echo '</div>';
    echo '</div>';

    echo '<div class="row">';
        echo '<div class="col-md-8">';
                echo input(array('type' => 'text', 'name' => 'busqueda_dep', 'placeholder' => 'Ingrese el criterio de busqueda', 'class'=>'form-control'));
        echo '</div>';
        echo '<div class="col-md-4">';
            echo tagcontent('button', 'BUSCAR', array('type' => 'submit', 'id' => 'ajaxformbtn', 'data-target' => 'depositos_out', 'class' => 'btn btn-primary'));
        echo '</div>';
    echo '</div>';
echo Close('form');
echo tagcontent('div', '', array('id' => 'depositos_out'));

