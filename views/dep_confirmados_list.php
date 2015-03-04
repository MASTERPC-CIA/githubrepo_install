<?php

echo lineBreak2(1, array('class' => 'clr'));
echo Open('div', array('class' => 'col-md-10'));

//    echo Open('div', array('class' => 'table-responsive','style'=>'height:1000px;overflow-y:scroll'));
    echo Open('div', array('class' => 'table-responsive'));

            echo tagcontent('div','',array('id'=>'new_deposito_out'));

//                echo tagcontent('button','Vista Previa',array( 'id'=>'ajaxformbtn','data-target'=>'new_deposito_out','class'=>'btn btn-primary'));

                echo Open('table', array('class' => 'table table-hover'));
                    echo Open('tr');
                    echo tagcontent('td', 'Deposito');
                    echo tagcontent('td', 'Banco');                    
                    echo tagcontent('td', 'Monto');
                    echo tagcontent('td', 'Fecha');
                    echo tagcontent('td', 'Hora');
                    echo tagcontent('td');


                    echo Close('tr');

                    //    echo Open('table', array('class' => 'table table-hover'));

                        //Variable para identificar cada checkbox
                    $id_dep=1;

                    foreach ($depositos_data as $dep) {
                        echo Open('tr');
                           
//                                echo tagcontent('td', $dep->id);
                                $link = tagcontent('a', $dep->id, array('id' => 'ajaxpanelbtn',
                                'data-url' => base_url('bancos/depositos_all/ver_asiento_contable_dep/' . 
                                        $dep->id.'/'.$dep->banco_id.'/'.'0/5/16'),
                                'title' => 'Ver Comprobante',
                                'data-target' => 'new_total_out', 'href' => '#'));
                                echo tagcontent('td', $link); //Detalle/nota
                    
                                echo tagcontent('td', $dep->banco);                                
                                echo tagcontent('td', '$ '.$dep->monto);
                                echo tagcontent('td', $dep->fecha);
                                echo tagcontent('td', $dep->hora);
                                echo Open('td');
                                /*Enlace a ver/editar*/
                                $link1 = tagcontent('a', 'Ver / Editar', array('id' => 'ajaxpanelbtn',
                                'data-url' => base_url('bancos/depositos_all/ver_deposito/' . $dep->id.'/'.$dep->banco.'/4/'.$dep->fecha),
                                'title' => 'Ver / Editar Deposito',
                                'data-target' => 'new_total_out', 'href' => '#'));
                                echo tagcontent('td', $link1); //Detalle/nota
                                
                                 /*Boton Editar*/
//                                    echo Open('form', array('action'=>base_url('bancos/depositos_all/accion_deposito'),
//                                        'method'=>'post'));
//                                        echo tagcontent('button', 'Ver / Editar', array('id' => 'ajaxformbtn', 
//                                        'data-target' => 'new_total_out', 'class' => 'btn btn-warning'));
//                                        echo  input(array('type' => 'hidden', 'name' => 'dep_id', 
//                                             'class' => 'form-control', 'value'=>$dep->id));
//                                        echo  input(array('type' => 'hidden', 'name' => 'banco', 
//                                             'class' => 'form-control', 'value'=>$dep->banco));
//                                        echo  input(array('type' => 'hidden', 'name' => 'total', 
//                                             'class' => 'form-control', 'value'=>$dep->monto));
//                                        echo  input(array('type' => 'hidden', 'name' => 'fecha', 
//                                             'class' => 'form-control', 'value'=>$dep->fecha));
//                                        echo  input(array('type' => 'hidden', 'name' => 'btn', 
//                                             'class' => 'form-control', 'value'=>'4'));
//
//                                    echo Close('form');// 
                                 /*Boton Ver asiento contable*/
//                                    echo Open('form', array('action'=>base_url('bancos/depositos_all/ver_asiento_contable_dep'),
//                                        'method'=>'post'));
//                                        echo tagcontent('button', 'Ver Comprobante', array('id' => 'ajaxformbtn', 
//                                        'data-target' => 'new_total_out', 'class' => 'btn btn-primary'));
//                                        echo  input(array('type' => 'hidden', 'name' => 'dep_id', 
//                                             'class' => 'form-control', 'value'=>$dep->id));
//                                        echo  input(array('type' => 'hidden', 'name' => 'banco', 
//                                             'class' => 'form-control', 'value'=>$dep->banco));
//                                        echo  input(array('type' => 'hidden', 'name' => 'btn', 
//                                             'class' => 'form-control', 'value'=>'5'));
//
//                                    echo Close('form');// 
                                echo Close('td');

                            echo Close('div');                               
                        echo Close('tr');
                        $id_dep++;
                        

                    }

                echo Close('table');
            echo Close('div');

    echo Close('div');
echo Close('div');