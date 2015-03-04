<?php

//echo tagcontent('div', 'Fecha inicial: ' . $fecha_inicio, array('class' => 'success-message'));
//echo tagcontent('div', 'Fecha final: ' . $fecha_fin, array('class' => 'success-message'));


echo lineBreak2(1, array('class' => 'clr'));
echo Open('div', array('class' => 'col-md-10'));

//    echo Open('div', array('class' => 'table-responsive','style'=>'height:1000px;overflow-y:scroll'));
    echo Open('div', array('class' => 'table-responsive'));

            echo tagcontent('div','',array('id'=>'new_deposito_out'));
            echo tagcontent('h3', 'Listado de Cheques: ', array('class' => 'titulos'));

                echo Open('div', array('class' => 'row'));
                //Formulario para grabar el deposito con o sin efectivo                   
                                           


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

                    echo Close('tr');

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
                            
                            echo Close('tr');
                            $id_dep++;
                        }
                echo Close('table');
            echo Close('div');

        echo Close('form');
    echo Close('div');
echo Close('div');

