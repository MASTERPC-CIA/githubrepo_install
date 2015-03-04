<?php

echo open('form', array('method' => 'post'));
echo tagcontent('h2', 'COMPROBANTE DE INGRESO Nº ' . $data_deposito->nro_comprobante);

echo '<label>BANCO:</label>';
echo input(array('type' => 'text', 'name' => 'dep_monto', 'value' => $data_banco->nombre));
echo tagcontent('br');
echo '<label>DETALLE COMPROBANTE</label>';
echo tagcontent('br');
echo Open('table', array('class' => 'table table-bordered'));
$thead = array('CUENTA', 'CODIGO CONTABLE', 'DETALLE', 'TIPO', 'DEBITO', 'CREDITO');
echo tablethead($thead);
for ($i = 0; $i < sizeof($cuentas); $i++) {
    echo Open('tr');
    if ($i <= 1) {
        echo tagcontent('td', $name_cuentas[$i]);
        echo tagcontent('td', $cuentas[$i]);
        echo tagcontent('td', '');
        echo tagcontent('td', '');
        echo tagcontent('td', $val_cuentas[$i]);
    } else {
        if ($i == sizeof($cuentas) - 1) {
            echo tagcontent('td', $data_banco->nombre);
            echo tagcontent('td', $data_banco->contacuentasplan_cod);
            echo tagcontent('td', '');
            echo tagcontent('td', '');
            echo tagcontent('td', $data_deposito->monto);
        }
    }
    
    echo tagcontent('td', '');
    echo Close('tr');
}
echo Open('tr');
$str_tarj='Tarjetas de Credito en Custodio';
$cod_conta='11020302';
echo tagcontent('td', $str_tarj);
echo tagcontent('td', $cod_conta);
echo tagcontent('td', '');
echo tagcontent('td', '');
echo tagcontent('td', '');
echo tagcontent('td', $total_tarjs);
echo Close('tr');
echo Close('table');
echo Close('form');

?>

   

