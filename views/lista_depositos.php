<?php

echo tagcontent('h3', 'LISTA DE DEPÓSITOS');
echo Open('table', array('class' => 'table table-bordered'));
$thead = array('COD', 'MONTO', 'FECHA', 'OPCIONES');
echo tablethead($thead);

foreach ($depositos as $val) {
    echo Open('tr');
    echo tagcontent('td', $val->id);
    echo tagcontent('td', $val->monto);
    echo tagcontent('td', $val->fecha);
    echo Open('td');
        if($opcion_tabla==1){
            echo open('form', array('action'=>  base_url('bancos/depositotarjetas/confirmar_deposito'), 'method'=>'post'));
                echo input(array('type' => 'submit', 'name' => 'btnConfirmar', 'value' => 'CONFIRMAR', 'id' => 'ajaxformbtn', 'data-target' => 'depositos_out', 'class' => 'btn btn-success'));
                echo input(array('type'=>'hidden', 'name'=>'id_dep_conf', 'value'=>$val->id));
            echo close('form');
        }
        
        echo open('form', array('action'=>  base_url('bancos/depositotarjetas/obtener_datos_dep'), 'method'=>'post'));
            echo input(array('type' => 'submit', 'name' => 'btnModificar', 'value' => 'VISUALIZAR', 'id' => 'ajaxformbtn', 'data-target' => 'depositos_out', 'class' => 'btn btn-info'));
            echo input(array('type'=>'hidden', 'name'=>'id_dep_mod', 'value'=>$val->id));
            echo input(array('type'=>'hidden', 'name'=>'monto_dep_mod', 'value'=>$val->monto));
            echo input(array('type'=>'hidden', 'name'=>'fecha_dep_mod', 'value'=>$val->fecha));
        echo close('form');
        
        echo open('form', array('action'=>  base_url('bancos/depositotarjetas/anular_deposito'), 'method'=>'post'));
            echo input(array('type' => 'submit', 'name' => 'btnEliminar', 'value' => 'ANULAR', 'id' => 'ajaxformbtn', 'data-target' => 'depositos_out', 'class' => 'btn btn-warning'));
            echo input(array('type'=>'hidden', 'name'=>'id_dep_elim', 'value'=>$val->id));
        echo close('form');
    echo Close('td');
    echo Close('tr');
}
echo Close('table');



