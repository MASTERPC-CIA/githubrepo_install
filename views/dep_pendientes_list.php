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
                    echo tagcontent('td', 'Accion');

                    echo Close('tr');

                    //    echo Open('table', array('class' => 'table table-hover'));

                        //Variable para identificar cada checkbox
                    $id_dep=1;

                    foreach ($depositos_data as $dep) {
                        echo Open('tr');
                           
                                //Ling a ver / editar deposito en ventana emergente
//                                echo tagcontent('td', $dep->id);                                
                                $link = tagcontent('a', $dep->id, array('id' => 'ajaxpanelbtn',
                                'data-url' => base_url('bancos/depositos_all/ver_deposito/' . $dep->id.'/'.$dep->banco.'/1/'.$dep->fecha),
                                'title' => 'Ver / Editar Deposito',
                                'data-target' => 'new_total_out', 'href' => '#'));
                                echo tagcontent('td', $link); //Detalle/nota
                    
                                echo tagcontent('td', $dep->banco);                                
                                echo tagcontent('td', '$ '.$dep->monto);
                                echo tagcontent('td', $dep->fecha);
                                echo tagcontent('td', $dep->hora);
                            echo Close('div');
                               
                                echo Open('td');
                                /*Boton confirmar*/
                                    echo Open('form', array('action'=>base_url('bancos/depositos_all/accion_deposito'),
                                        'method'=>'post'));
                                        echo tagcontent('button', 'Confirmar', array('id' => 'ajaxformbtn', 
                                            'data-target' => 'new_total_out', 'class' => 'btn btn-success'));
                                        echo  input(array('type' => 'hidden', 'name' => 'dep_id', 
                                             'class' => 'form-control', 'value'=>$dep->id));
                                        echo  input(array('type' => 'hidden', 'name' => 'banco', 
                                             'class' => 'form-control', 'value'=>$dep->banco));
                                        echo  input(array('type' => 'hidden', 'name' => 'total', 
                                             'class' => 'form-control', 'value'=>$dep->monto));
                                        echo  input(array('type' => 'hidden', 'name' => 'fecha', 
                                             'class' => 'form-control', 'value'=>$dep->fecha));
                                        echo  input(array('type' => 'hidden', 'name' => 'btn', 
                                             'class' => 'form-control', 'value'=>'2'));
                                    echo Close('form');// 

                                    /*Boton Editar*/
//                                    echo Open('form', array('action'=>base_url('bancos/depositos_all/accion_deposito'),
//                                        'method'=>'post'));
//                                        echo tagcontent('button', 'Ver / Editar', array('id' => 'ajaxformbtn', 
//                                        'data-target' => 'new_total_out', 'class' => 'btn btn-warning'));
//                                        echo  input(array('type' => 'hidden', 'name' => 'dep_id', 
//                                             'class' => 'form-control', 'value'=>$dep->id));
//                                        echo  input(array('type' => 'hidden', 'name' => 'btn', 
//                                             'class' => 'form-control', 'value'=>'1'));
//                                         echo  input(array('type' => 'hidden', 'name' => 'banco', 
//                                             'class' => 'form-control', 'value'=>$dep->banco));
//                                         
//                                    echo Close('form');// 

                                    /*Boton Eliminar*/
//                                    echo Open('form', array('action'=>base_url('bancos/depositos_all/accion_deposito'),
//                                        'method'=>'post'));
//                                        echo tagcontent('button', 'Anular', array('id' => 'ajaxformbtn', 
//                                            'data-target' => 'new_total_out', 'class' => 'btn btn-danger'));
//                                        echo  input(array('type' => 'hidden', 'name' => 'dep_id', 
//                                             'class' => 'form-control', 'value'=>$dep->id));
//                                        echo  input(array('type' => 'hidden', 'name' => 'btn', 
//                                             'class' => 'form-control', 'value'=>'0'));
                                    echo Close('form');// 
                                    
                                    
                                    

                                      
                                     echo Close('form');//                                    
                                echo Close('td');
                        echo Close('tr');
                        $id_dep++;
                    }

                echo Close('table');
            echo Close('div');

    echo Close('div');
echo Close('div');