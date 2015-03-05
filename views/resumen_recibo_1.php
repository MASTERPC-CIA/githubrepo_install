<?php

        echo tagcontent('h3', 'Deposito: ', array('class' => 'titulos'));


//        echo Open('div', array('class' => 'row'));

            echo '<b> Banco: </b>' . $nombre_banco.'<br>';
            echo '<b> Fecha: </b>' . $fecha.'<br>';
            echo '<b> Cheques: $ </b>' . $monto_cheques.'<br>';
            echo '<b> Efectivo: $ </b>' . $monto_efectivo.'<br>';


            echo '<b>TOTAL: $ </b>' . $monto_total.'<br>';
                     


//        echo Close('div');


//    print_r($meses_data);
