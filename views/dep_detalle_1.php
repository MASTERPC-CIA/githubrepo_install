<?php

echo Open('div', array('class' => 'col-md-12'));

//    echo Open('div', array('class' => 'table-responsive','style'=>'height:1000px;overflow-y:scroll'));
    echo Open('div', array('class' => 'table-responsive'));

            echo tagcontent('h3',' DEPOSITO N° '.$id_dep.' - '.$nombre_banco,array('class'=>'titulos'));


//                echo tagcontent('button','Vista Previa',array( 'id'=>'ajaxformbtn','data-target'=>'new_deposito_out','class'=>'btn btn-primary'));
            echo Open('form', array('action'=>base_url('bancos/depositos_all/accion_deposito'),
                                                    'method'=>'post'));
                echo Open('table', array('class' => 'table table-hover'));
                    echo Open('tr');
                        echo tagcontent('td', 'Detalle');                    
                        echo tagcontent('td', 'Valor');
                        echo tagcontent('td', 'Quitar');
                        echo tagcontent('td', 'Anulado');
                        if($btn_select==4){//Mostrar el check protestar
                            echo tagcontent('td', 'Protestado');
                        }
                        echo tagcontent('td', 'Sin Accion');

                    echo Close('tr');

                    echo  input(array('type' => 'hidden', 'name' => 'monto_efectivo', 
                             'class' => 'form-control', 'value'=>$monto_efectivo));
                    echo  input(array('type' => 'hidden', 'name' => 'monto_total', 
                             'class' => 'form-control', 'value'=>$monto_total));
                    echo  input(array('type' => 'hidden', 'name' => 'dep_id', 
                                 'class' => 'form-control', 'value'=>$id_dep));
                          

                    $numero_cheques =0;
                    foreach ($cheques_data as $cheque) {
                            echo Open('tr');
                            echo tagcontent('td', $cheque->nrocheque); //NUMERO DE CHEQUE
//                            echo tagcontent('td', 'CH'.$cheque->doc_id);
                            echo tagcontent('td', $cheque->valor);

                            echo  input(array('type' => 'hidden', 'name' => 'monto_cheque'.$cheque->doc_id, 
                             'class' => 'form-control', 'value'=>$cheque->valor));
                            
                            echo  input(array('type' => 'hidden', 'name' => 'cheques_id[]', 
                                'class' => 'form-control', 'value'=>$cheque->doc_id,
                                'ch_monto'=>$cheque->valor));
                            
                            /*RADIO BUTTONS*/
                            //CHECK QUITAR
                            echo Open('td');
                                echo Open('div',array('class' => 'radio'));
                                        echo Open('label');
                                            echo input(array('type' => 'radio', 'name' => 'option_ch'.$cheque->doc_id, 
                                                'id' => 'option_ch'.$cheque->doc_id, 'value'=>'1'));
                                        echo Close('label');
                                echo Close('div');
                            echo Close('td');
                            //CHECK ANULAR
                            echo Open('td');
                                echo Open('div',array('class' => 'radio'));
                                        echo Open('label');
                                            echo input(array('type' => 'radio', 'name' => 'option_ch'.$cheque->doc_id, 
                                                'id' => 'option_ch'.$cheque->doc_id, 'value'=>'-1'));
                                        echo Close('label');
                                echo Close('div');
                            echo Close('td');
                            if($btn_select==4){//Mostrar el check protestar solo si esta confirmado
                            //CHECK PROTESTAR
                            echo Open('td');
                                 echo Open('div',array('class' => 'radio'));
                                        echo Open('label');
                                            echo input(array('type' => 'radio', 'name' => 'option_ch'.$cheque->doc_id, 
                                                'id' => 'option_ch'.$cheque->doc_id, 'value'=>'0'));
                                        echo Close('label');
                                echo Close('div');
                            echo Close('td');
                            }
                            //CHECK SIN ACCION
                            echo Open('td');
                                 echo Open('div',array('class' => 'radio'));
                                        echo Open('label');
                                            echo input(array('type' => 'radio', 'name' => 'option_ch'.$cheque->doc_id, 
                                                'id' => 'option_ch'.$cheque->doc_id, 'value'=>'2','checked'=>' '));
                                        echo Close('label');
                                echo Close('div');
                            echo Close('td');


                        echo Close('tr');
                        $numero_cheques++;
                        }
                        
                    echo Open('tr');
                    if($btn_select==1){//Si es de depositos pendientes se puede modificar el efectivo
                        //Detalle de efectivo
                            echo tagcontent('td', 'Efectivo');                    
                                echo tagcontent('td', input(array('type' => 'text', 'name' => 'input_efectivo', 
                                    'class' => 'form-control', 'placeholder'=>$monto_efectivo)));
                        echo Close('tr');
                    }
                    echo Open('tr');
                    //INgresar Numero de comprobante (papeleta/recibo) del banco
                    echo tagcontent('td', 'Comprobante N°');                    
                            echo tagcontent('td', input(array('type' => 'text', 'name' => 'input_comprobante', 
                                'class' => 'form-control','placeholder'=>$nro_comprobante)));
                    echo Close('tr');
                    
                        
                    echo  input(array('type' => 'hidden', 'name' => 'numero_cheques', 
                             'class' => 'form-control', 'value'=>$numero_cheques));
                    
                /*Boton Actualizar Cambios*/
                echo Open('tr');
//                        echo Open('td');

                        echo  input(array('type' => 'hidden', 'name' => 'btn', 
                                'class' => 'form-control', 'value'=>'3')); 
                        echo tagcontent('td', tagcontent('button', 'Actualizar Cambios', array('id' => 'ajaxformbtn', 
                                'data-target' => 'new_total_out', 'class' => 'btn btn-primary')));
//                        echo Close('td');
                echo Close('form');
                if($btn_select==1){//Si viene de depositos pendientes se muestra el boton confirmar
                /*Boton confirmar*/
                        echo Open('td');
                        echo Open('form', array('action'=>base_url('bancos/depositos_all/accion_deposito'),
                            'method'=>'post'));
                            echo tagcontent('button', 'Confirmar Deposito', array('id' => 'ajaxformbtn', 
                                'data-target' => 'new_total_out', 'class' => 'btn btn-success'));
                            echo  input(array('type' => 'hidden', 'name' => 'dep_id', 
                                 'class' => 'form-control', 'value'=>$id_dep));
                            echo  input(array('type' => 'hidden', 'name' => 'banco', 
                                 'class' => 'form-control', 'value'=>$nombre_banco));
                            echo  input(array('type' => 'hidden', 'name' => 'total', 
                                 'class' => 'form-control', 'value'=>$monto_total));
                            echo  input(array('type' => 'hidden', 'name' => 'fecha', 
                                 'class' => 'form-control', 'value'=>$fecha));
                            echo  input(array('type' => 'hidden', 'name' => 'btn', 
                                 'class' => 'form-control', 'value'=>'2'));
                        echo Close('form');// 
                        echo Close('td');
                }
                /*Boton Eliminar*/
                    echo Open('td');
                        echo Open('form', array('action'=>base_url('bancos/depositos_all/accion_deposito'),
                                    'method'=>'post'));
                                    echo tagcontent('button', 'Anular Deposito', array('id' => 'ajaxformbtn', 
                                        'data-target' => 'new_total_out', 'class' => 'btn btn-danger'));
                                    echo  input(array('type' => 'hidden', 'name' => 'dep_id', 
                                         'class' => 'form-control', 'value'=>$id_dep));
                                    echo  input(array('type' => 'hidden', 'name' => 'btn', 
                                         'class' => 'form-control', 'value'=>'0'));                            
                        echo Close('form');// 

                        echo Close('td');
                    echo Close('tr');

                echo Close('table');
                
    echo Close('div');
echo Close('div');