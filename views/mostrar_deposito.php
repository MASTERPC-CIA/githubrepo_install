<?php

echo open('form', array('method' => 'post'));
echo tagcontent('h2', 'DETALLE DEPOSITO');
echo input(array('type'=>'hidden', 'name'=>'dep_id', 'value'=>$id_dep));
echo '<label>MONTO DEPOSITO:</label>';
echo input(array('type'=>'text', 'name'=>'dep_monto', 'value'=>$monto));
echo tagcontent('br');
echo '<label>FECHA DEPOSITO:</label>';
echo input(array('type' => 'date','value' => $fecha, 'name' => 'fecha_inicio', 'class' => 'form-control datepicker','style'=>'width:50%'));

echo tagcontent('br');
echo tagcontent('h3', 'TARJETAS DEPOSITADAS');
echo Open('table', array('class' => 'table table-bordered'));
$thead = array('NUM DEP', 'VALOR', 'NUM TARJETA');
echo tablethead($thead);
foreach ($detalle_dep as $val) {
    echo Open('tr');
        echo tagcontent('td', $val->deposito_id);
        echo tagcontent('td', $val->valor); 
        echo tagcontent('td', $val->doc_id);
    echo Close('tr');
}
echo Close('table');
echo Close('form');




