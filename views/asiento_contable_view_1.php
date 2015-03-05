<?php

//     echo Open('div', array('class' => 'row')); 
        echo tagcontent('h3', 'Comprobante N° '.$asiento_id.' ', array('class' => 'titulos col-md-12'));
//        echo lineBreak2(1, array('class' => 'clr'));

        echo tagcontent('h4', $user, array('class' => 'titulos col-md-10'));
        echo Open('div', array('class' => 'col-md-2')); 
        //BTN imprimir
//        echo tagcontent('button', 'IMPRIMIR', array('name' => 'btnCancel', 
//            'class' => 'btn btn-warning col-md-2', 'id' => 'printbtn', 
//            'type' => 'button','data-target' => 'detalle'));
        echo tagcontent('button','Imprimir',array( 'id'=>'printbtn',
            'type' => 'button', 'data-target'=>'new_total_out',
            'class'=>'btn btn-primary'));
//        echo Close('div'); 
    echo Close('div');

    echo Open('div', array('class' => 'col-md-12'));

//    echo Open('div', array('class' => 'table-responsive','style'=>'height:1000px;overflow-y:scroll'));
    echo Open('div', array('class' => 'table-responsive'));

            echo tagcontent('div','',array('id'=>'new_deposito_out'));

//                echo tagcontent('button','Vista Previa',array( 'id'=>'ajaxformbtn','data-target'=>'new_deposito_out','class'=>'btn btn-primary'));

                echo Open('table', array('class' => 'table table-hover'));
                    echo Open('tr');
                    echo tagcontent('td', 'Cuenta');
                    echo tagcontent('td', 'Cod. Contable');
                    echo tagcontent('td', 'Detalle');
                    echo tagcontent('td', 'Debito');
                    echo tagcontent('td', 'Credito');                    
                    

                    echo Close('tr');

                    //    echo Open('table', array('class' => 'table table-hover'));

                    $sum_debito =0;   
                    $sum_credito =0;   
                    foreach ($asientos_data as $asiento) {
                        echo Open('tr');      
                            if ($asiento->banco){
                                echo tagcontent('td', $asiento->banco);                                

                            }else{
                                echo tagcontent('td', $asiento->cuenta);                                
                            }
                            echo tagcontent('td', $asiento->cuenta_cont_id);                                
                            echo tagcontent('td', $asiento->detalle);                                
                            echo tagcontent('td', $asiento->debito);  
                            $sum_debito+= $asiento->debito;
                            echo tagcontent('td', $asiento->credito);                                
                            $sum_credito+= $asiento->credito;
                            echo Close('div');                               
                        echo Close('tr');
                    }
                    echo Open('tr');
                        echo tagcontent('td', '<b>Total</b>');                                
                        echo tagcontent('td', '');                                
                        echo tagcontent('td', '');                                
                        echo tagcontent('td', '<b>'.$sum_debito);                                
                        echo tagcontent('td', '<b>'.$sum_credito);                                

                    echo Close('tr');      


                echo Close('table');
                if($show_btn == 1){
                    /*TRANSFERIR A OTRO PERIODO*/
                    echo Open('div');
                    echo Open('form', array('action'=>base_url('bancos/conciliaciones/transfer_dep'),
                                        'method'=>'post'));
                        echo tagcontent('h4', 'Transferir a otro periodo ', array('class' => 'titulos'));
                
                            //Periodo
                            $combo_periodo = combobox(
                                        $periodo_list, 
                                        array('label' => 'anio', 'value' => 'anio'), 
                                        array('name' => 'periodo_cta', 'class' => 'form-control'),
                                        true);
                            echo get_combo_group('Periodo', $combo_periodo, 'col-md-4 form-group');

                            //Mes
                            $combo_mes = combobox(
                                        $mes_list, 
                                        array('label' => 'mes',  'value' => 'id'), 
                                        array('name' => 'mes', 'class' => 'form-control'),
                                        true);
                            echo get_combo_group('Mes', $combo_mes, 'col-md-4 form-group');
                                    
                                        echo tagcontent('button', 'Transferir', array('id' => 'ajaxformbtn', 
                                        'data-target' => 'new_res_out', 'class' => 'btn btn-primary'));

                            echo  input(array('type' => 'hidden', 'name' => 'btn', 
                                 'class' => 'form-control', 'value'=>'5'));
                            echo  input(array('type' => 'hidden', 'name' => 'concil', 
                                 'class' => 'form-control', 'value'=>'1'));
                            echo  input(array('type' => 'hidden', 'name' => 'dep_id', 
                                 'class' => 'form-control', 'value'=>$dep_id));

                        echo Close('form');//
                    echo Close('div');

                }
            echo Close('div');

        echo Close('div');
    echo Close('div');
echo Close('div');


//    print_r($meses_data);
