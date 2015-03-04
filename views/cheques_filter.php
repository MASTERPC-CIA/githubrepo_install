<?php
	echo Open('form', array('action'=>base_url('bancos/depositocheques/extraer'),'method'=>'post'));
//		echo tagcontent('h1','DEPOSITOS - Cheques',array('class'=>'titulos'));

                echo Open('div', array('class' => 'col-md-12')); 
                    //INPUTS rango fechas
                    $text_inputs = array(
                        '0' => array('type' => 'date', 'name' => 'fecha_inicio', 'class' => 'form-control datepicker','style'=>'width:50%'),
                        '1' => array('type' => 'date', 'name' => 'fecha_fin', 'class' => 'form-control datepicker','style'=>'width:50%'),
                    );
                    echo get_field_group('Fechas', $text_inputs, $class = 'col-md-3 form-group');
                
                    //IMPUT estado cheque
                    $combo_tipos = combobox(
                        $lista_estados, 
                        array('label' => 'estado', 'value' => 'id', 'nombre'=>'estado'), 
                        array('name' => 'estado', 'class' => 'form-control'),
                        true);
                echo get_combo_group('Estado', $combo_tipos, $class = 'col-md-3 form-group');                    

                echo Open('div', array('class' => 'col-md-3')); 
                    echo tagcontent('button','Buscar',array( 'id'=>'ajaxformbtn','data-target'=>'new_deposito_out','class'=>'btn btn-primary'));
                echo Close('div'); 

            echo Close('div'); 
	echo Close('form');

        echo tagcontent('div','',array('id'=>'new_total_out', 'class'=>"col-md-10"));
	echo tagcontent('div','',array('id'=>'new_deposito_out', 'class'=>"col-md-10"));
        echo tagcontent('div','',array('id'=>'new_recibo_out'));
//	echo tagcontent('div','',array('id'=>'new_deposito_out'));


$jsarray = array(
    base_url('application/modules/bancos/js/deposito_cheques.js'),
);
echo jsload($jsarray);
