<?php
//		echo tagcontent('h1','CONCILIACIONES',array('class'=>'titulos'));
//                echo lineBreak2(1, array('class' => 'clr'));

                echo Open('div', array('class' => 'row')); 
                echo Open('div', array('class' => 'col-md-12')); 
                echo Open('form', array('action'=>base_url('bancos/conciliaciones/extraer'),'method'=>'post','class'=>'col-md-9'));
              
                //Banco                     
                $combo_bancos = combobox(
                            $bancos_list, 
                            array('label' => 'nombre', 'value' => 'id', 'nombre'=>'banco'), 
                            array('name' => 'banco', 'class' => 'form-control'),
                            true);
                echo get_combo_group('Banco', $combo_bancos, $class = 'col-md-6 form-group');
                //Tipo                     
                $combo_tipos = combobox(
                            $tipo_list, 
                            array('label' => 'tipo', 'value' => 'id'), 
                            array('name' => 'tipo_cta', 'class' => 'form-control'),
                            true);
                echo get_combo_group('Tipo', $combo_tipos, $class = 'col-md-4 form-group');
             
//                //Rango fecha vence
//                $text_vence = array(
//                    '0' => array('type'=>'text','name'=>'f_vence_desde','class'=>'form-control datepicker','style'=>'width:50%'),
//                    '1' => array('type'=>'text','name'=>'f_vence_hasta','class'=>'form-control datepicker','style'=>'width:50%'),
//                );
//                echo get_field_group('F. Vence', $text_vence, $class = 'col-md-3 form-group');
//                
//                //Rango fecha registro
//                $text_registro = array(
//                    '0' => array('type'=>'text','name'=>'f_reg_desde','class'=>'form-control datepicker','style'=>'width:50%'),
//                    '1' => array('type'=>'text','name'=>'f_reg_hasta','class'=>'form-control datepicker','style'=>'width:50%'),
//                );
//                echo get_field_group('F. Registro', $text_registro, $class = 'col-md-3 form-group');
//

                //Periodo
                
                $combo_periodo = combobox(
                            $periodo_list, 
                            array('label' => 'anio', 'value' => 'anio'), 
                            array('name' => 'periodo_cta', 'class' => 'form-control'),
                            true);
                echo get_combo_group('Periodo', $combo_periodo, 'col-md-3 form-group');

                //Mes
                $combo_mes = combobox(
                            $mes_list, 
                            array('label' => 'mes',  'value' => 'id'), 
                            array('name' => 'mes', 'class' => 'form-control'),
                            true);
                echo get_combo_group('Mes', $combo_mes, 'col-md-3 form-group');
                
                //Numero de cheque
//                echo Open('div', array('class' => 'col-md-3')); 
//                    echo tagcontent('div', input(array('type' => 'text', 'name' => 'nro_cheque', 
//                            'class' => 'form-control', 'placeholder'=>'N° Cheque')));
//                    echo Close('div'); 
//                    
                //Numero de referencia
                echo Open('div', array('class' => 'col-md-3')); 
                    echo tagcontent('div', input(array('type' => 'text', 'name' => 'num_comprobante', 
                            'class' => 'form-control', 'placeholder'=>'N° Comprobante')));
                    echo Close('div'); 
                    
                //Buscar cheque
                echo Open('div', array('class' => 'col-md-3')); 
                    echo tagcontent('button','Buscar',array( 'id'=>'ajaxformbtn',
                        'data-target'=>'new_conciliacion_out','class'=>'btn btn-primary'));
                echo Close('div'); 

                
            echo Close('form');
        echo Close('div'); 

	echo tagcontent('div','',array('id'=>'new_total_out', 'class'=>"col-md-10"));
	echo tagcontent('div','',array('id'=>'new_conciliacion_out'));
        echo Close('div'); 
