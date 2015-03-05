<?php

echo Open('form', array('action' => base_url('bancos/tarjetascredito/generar_comprobante'), 'method' => 'post'));
echo tagcontent('h4', 'CUENTAS CONTABLES');
$combo_plan_ctas_1 = combobox($plan_cuentas, array('label' => 'nombre', 'value' => 'cod'), array('name' => 'cta_1', 'class' => 'form-control'), 'Seleccione una Cuenta');
echo tagcontent('div', $combo_plan_ctas_1, array('class' => 'col-md-4'));
echo input(array('type' => 'text', 'name' => 'valor_plan_1', 'placeholder' => 'Ingrese el valor'));
echo tagcontent('br');
$combo_plan_ctas_2 = combobox($plan_cuentas, array('label' => 'nombre', 'value' => 'cod'), array('name' => 'cta_2', 'class' => 'form-control'), 'Seleccione una Cuenta');
echo tagcontent('div', $combo_plan_ctas_2, array('class' => 'col-md-4'));
echo input(array('type' => 'text', 'name' => 'valor_plan_2', 'placeholder' => 'Ingrese el valor'));
echo tagcontent('br');
echo tagcontent('h4', 'BANCO');
echo '<div class="form-group">';
        $combo_bancos = combobox($lista_bancos, array('label' => 'nombre', 'value' => 'id'), array('name' => 'banco_selecc', 'class' => 'form-control'), 'Seleccione un Banco');
        echo tagcontent('div', $combo_bancos, array('class' => 'col-md-6'));
echo '</div>';
echo tagcontent('br');
echo tagcontent('h4', 'LISTADO DE TARJETAS DE CREDITO');
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
//    echo input(array('type' => 'checkbox', 'name' => 'selected_tarj', 'value' => $val->id));
    echo Close('td');
    echo Close('tr');
}
echo Close('table');
echo tagcontent('button', 'GENERAR COMPROBANTE', array('type' => 'submit', 'id' => 'ajaxformbtn', 'data-target' => 'tarjetas_out', 'class' => 'btn btn-SUCCESS'));
echo Close('form');