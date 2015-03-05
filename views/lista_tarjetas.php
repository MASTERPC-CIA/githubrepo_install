<?php

echo Open('form', array('action' => base_url('bancos/tarjetascredito/cambiar_estado_tarj'), 'method' => 'post'));
echo Open('table', array('class' => 'table table-bordered'));
$thead = array('COD', 'TARJETA', 'CLIENTE', 'FECHA', 'VALOR', 'SELECCIONAR');
echo tablethead($thead);
foreach ($tarjetas as $val) {
    echo Open('tr');
    echo tagcontent('td', $val->id);
    echo tagcontent('td', $val->tarjeta);
    echo tagcontent('td', $val->nombres);
    echo tagcontent('td', $val->fecha);
    echo tagcontent('td', $val->valor);
    echo Open('td');
    echo input(array('type' => 'checkbox', 'name' => 'selected_tarj[]', 'value' => $val->id));
    echo input(array('type' => 'hidden', 'name' => 'selec_tarj_val[]', 'value' => $val->valor));
    echo Close('td');
    echo Close('tr');
}
echo Close('table');
echo tagcontent('button', 'DEPOSITAR', array('type' => 'submit', 'id' => 'ajaxformbtn', 'data-target' => 'tarjetas_out', 'class' => 'btn btn-primary'));
echo Close('form');



