<?php

echo lineBreak2(1, array('class' => 'clr'));
echo Open('div', array('class' => 'col-md-12'));

//    echo Open('div', array('class' => 'table-responsive','style'=>'height:1000px;overflow-y:scroll'));
    echo Open('div', array('class' => 'table-responsive'));

            echo tagcontent('div','',array('id'=>'new_deposito_out'));

//                echo tagcontent('button','Vista Previa',array( 'id'=>'ajaxformbtn','data-target'=>'new_deposito_out','class'=>'btn btn-primary'));

                echo Open('table', array('class' => 'table table-hover'));
                    echo Open('tr');
                    echo tagcontent('td', 'Deposito');
                    echo tagcontent('td', 'Banco');                    
                    echo tagcontent('td', 'Total');
                    echo tagcontent('td', 'Fecha');
                    echo tagcontent('td', 'Hora');

                    echo Close('tr');

                    //    echo Open('table', array('class' => 'table table-hover'));

                        //Variable para identificar cada checkbox
                    $id_dep=1;

                    foreach ($depositos_data as $dep) {
                        echo Open('tr');
                           
                                echo tagcontent('td', $dep->id);                                
                                echo tagcontent('td', $dep->banco);                                
                                echo tagcontent('td', $dep->monto);
                                echo tagcontent('td', $dep->fecha);
                                echo tagcontent('td', $dep->hora);
                            echo Close('div');                               
                        echo Close('tr');
                        $id_dep++;
                    }

                echo Close('table');
            echo Close('div');

    echo Close('div');
echo Close('div');