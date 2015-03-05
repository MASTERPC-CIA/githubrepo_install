<?php
$this->load->helper('form');
date_default_timezone_set('America/Guayaquil');

$fecha_actual = date('Y-m-d');
echo Open('form', array('action' => base_url('bancos/tarjetascredito/listar_tarjetas_opc'), 'method' => 'post'));
  echo tagcontent('h2', 'TARJETAS DE CREDITO CUSTODIO', array('class' => 'class1'));
    echo '<div class="row">';
        echo '<div class="col-md-8">';
            echo tagcontent('h5', 'Seleccione el rango de fechas para buscar:');
            echo '<label>FECHA DESDE:</label>';
            echo input(array('value' => $fecha_actual, 'class' => 'input-md datepicker', 'name' => 'fecha_desde', 'contentclass' => 'col-md-4', 'required' => '', 'maxlength' => '10'));
            echo '<label>FECHA HASTA:</label>';
            echo input(array('value' => $fecha_actual, 'class' => 'input-md datepicker', 'name' => 'fecha_hasta', 'contentclass' => 'col-md-4', 'required' => '', 'maxlength' => '10'));
        echo '</div>';
        echo '<div class="col-md-4">';
            $opciones = array(
                  '1' => 'Tarjetas en Custodio',
                  '2' => 'Tarjetas en Comprobante' 
               );
            echo form_dropdown('opc_buscar', $opciones, '1');
        echo '</div>';
    echo '</div>';

    echo '<div class="row">';
        echo '<div class="col-md-8">';
                echo input(array('type' => 'text', 'name' => 'busqueda', 'placeholder' => 'Ingrese el criterio de busqueda', 'class'=>'form-control'));
        echo '</div>';
        echo '<div class="col-md-4">';
            echo tagcontent('button', 'BUSCAR', array('type' => 'submit', 'id' => 'ajaxformbtn', 'data-target' => 'tarjetas_out', 'class' => 'btn btn-primary'));
        echo '</div>';
    echo '</div>';
echo Close('form');
echo tagcontent('div', '', array('id' => 'tarjetas_out'));


