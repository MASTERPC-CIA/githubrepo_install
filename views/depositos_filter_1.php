
<?php

//        echo lineBreak2(1, array('class' => 'clr'));

//    echo tagcontent('h1','LISTADO DEPOSITOS',array('class'=>'titulos'));
        echo Open('div', array('class' => 'col-md-12'));          

        echo Open('form', array('action'=>base_url('bancos/depositos_all/extraer'),'method'=>'post'));
        $text_inputs = array(
            '0' => array('type' => 'date', 'name' => 'fecha_inicio', 'class' => 'form-control datepicker','style'=>'width:50%'),
            '1' => array('type' => 'date', 'name' => 'fecha_fin', 'class' => 'form-control datepicker','style'=>'width:50%'),
        );
        echo get_field_group('Fechas', $text_inputs, $class = 'col-md-3 form-group');
                    
//                    echo tagcontent('td', combobox($lista_estados, 
//                            array('label' => 'estado', 'value' => 'id', 'nombre'=>'estado'), 
//                            array('name' => 'estado', 'class' => 'form-control')));
                $combo_tipos = combobox(
                    $lista_estados, 
                    array('label' => 'estado', 'value' => 'id', 'nombre'=>'estado'), 
                    array('name' => 'estado', 'class' => 'form-control'),
                    true);
                echo get_combo_group('Estado', $combo_tipos, $class = 'col-md-3 form-group');
                    

                echo Open('div', array('class' => 'col-md-3')); 
                
//                    echo input(array('type' => 'date', 'name' => 'fecha_inicio', 'class' => 'form-control'));
//                    echo input(array('type' => 'date', 'name' => 'fecha_fin', 'class' => 'form-control'));
                    echo tagcontent('button','Buscar',array( 'id'=>'ajaxformbtn','data-target'=>'new_deposito_out','class'=>'btn btn-primary'));
                echo Close('div'); 

        echo Close('form');
    echo Close('div');  

    echo tagcontent('div','',array('id'=>'new_total_out', 'class'=>"col-md-10"));
    echo tagcontent('div','',array('id'=>'new_deposito_out'));
    echo Close('div');
//	echo tagcontent('div','',array('id'=>'new_deposito_out'));

