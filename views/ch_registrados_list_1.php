<?php

//echo tagcontent('div', 'Fecha inicial: ' . $fecha_inicio, array('class' => 'success-message'));
//echo tagcontent('div', 'Fecha final: ' . $fecha_fin, array('class' => 'success-message'));


echo lineBreak2(1, array('class' => 'clr'));
echo Open('div', array('class' => 'col-md-12'));

//    echo Open('div', array('class' => 'table-responsive','style'=>'height:1000px;overflow-y:scroll'));
    echo Open('div', array('class' => 'table-responsive'));

        echo Open('form', array('action'=>base_url('bancos/depositocheques/depositar'),
            'method'=>'post', 'name'=>'form_ch_reg'));
            echo tagcontent('div','',array('id'=>'new_deposito_out'));
            echo tagcontent('h3', 'Resumen Deposito: ', array('class' => 'titulos'));

                echo Open('div', array('class' => 'row'));
                //Formulario para grabar el deposito con o sin efectivo
                    
                        //Total de cheques
                        echo Open('div', array('class' => 'col-md-3'));                          
                            echo Open('div', array('id' => 'resultado'));                               
                            //enviado desde javascript deposito_cheques.js
                            echo Close('div');
                        echo Close('div');
                        
                        //Banco a depositar
                        echo Open('div', array('class' => 'col-md-3'));
                            echo tagcontent('td', combobox($lista_bancos, array('label' => 'nombre', 
                                'value' => 'id', 'nombre'=>'nombre'), array('name' => 'nombre', 'class' => 'form-control')));
                        echo Close('div');


                        //Agregar efectivo
                        echo Open('div', array('class' => 'col-md-3')); 
                                echo tagcontent('td', input(array('type' => 'text', 'name' => 'monto_efectivo', 
                                    'class' => 'form-control', 'placeholder'=>'Agregar efectivo $')));

                        echo Close('div'); 

                        echo Open('div', array('class' => 'col-md-3'));
                                echo tagcontent('td', tagcontent('button', 'Depositar', array('id' => 'ajaxformbtn', 
                                    'data-target' => 'new_recibo_out', 'class' => 'btn btn-success')));
                        echo Close('div');                         


                echo Close('div');


                echo lineBreak2(1, array('class' => 'clr'));
//                echo tagcontent('button','Vista Previa',array( 'id'=>'ajaxformbtn','data-target'=>'new_deposito_out','class'=>'btn btn-primary'));

                echo Open('table', array('class' => 'table table-hover'));
                    echo Open('tr');
                    echo tagcontent('td', 'Nro Cheque');
                    echo tagcontent('td', 'Cliente');
                    echo tagcontent('td', 'Titular');
                    echo tagcontent('td', 'Emision');
                    echo tagcontent('td', 'Cobro');
                    echo tagcontent('td', 'Cuenta');
                    echo tagcontent('td', 'Banco');
                    echo tagcontent('td', 'Monto');
                    echo tagcontent('td', 'Select');

                    echo Close('tr');

                    
                    echo  input(array('type' => 'hidden', 'name' => 'fecha_inicio', 
                                 'class' => 'form-control', 'value'=>$fecha_inicio));
                    echo  input(array('type' => 'hidden', 'name' => 'fecha_fin', 
                                 'class' => 'form-control', 'value'=>$fecha_fin));
                    echo  input(array('type' => 'hidden', 'name' => 'estado', 
                                 'class' => 'form-control', 'value'=>$estado));
                    //    echo Open('table', array('class' => 'table table-hover'));

                        //Variable para identificar cada checkbox
                        $id_dep=1;

                        foreach ($cheques_data as $cheque) {
                            echo Open('tr');
                            echo tagcontent('td', $cheque->nrocheque); //NUMERO DE CHEQUE
                            echo tagcontent('td', $cheque->apellidos.' '.$cheque->nombres);
                            echo tagcontent('td', $cheque->nombre_beneficiario);
                            echo tagcontent('td', $cheque->fecha);
                            echo tagcontent('td', $cheque->fechacobro);
                            echo tagcontent('td', $cheque->nrocuentacheque);
                            echo tagcontent('td', $cheque->banco);
                            echo tagcontent('td', '$'.$cheque->valorcheque);
                            echo  input(array('type' => 'hidden', 'name' => 'cheque_id', 
                                 'class' => 'form-control', 'value'=>$cheque->id));
                            echo Open('td');
                                echo Open('div',array('class' => 'checkbox'));
                                    echo Open('label');
                                        echo input(array('type' => 'checkbox', 'name' => 'chk_cheque[]', 
                                            'id' => 'val'.$id_dep,'class' => 'checks', 'value'=>$cheque->id, 'monto'=>$cheque->valorcheque));
                                    echo Close('div');
                                echo Close('div');
                            echo Close('td');

                            echo Close('tr');
                            $id_dep++;
                        }
                echo Close('table');
            echo Close('div');

        echo Close('form');
    echo Close('div');
echo Close('div');

