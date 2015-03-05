<?php

        echo tagcontent('h3', 'Deposito N'.$id_dep.' '.$estado, array('class' => 'titulos'));


        echo Open('div', array('class' => 'row'));
           
            if(!empty($nro_comprobante)){
                echo '<b>Comprobante: N° </b>' . $nro_comprobante.'<br>';
            }
            echo '<b>TOTAL: $ </b>' . $monto_total.'<br>';


        echo Close('div');


//    print_r($meses_data);
