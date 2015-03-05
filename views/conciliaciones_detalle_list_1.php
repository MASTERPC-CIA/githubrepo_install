<?php

//echo tagcontent('div', 'Fecha inicial: ' . $fecha_inicio, array('class' => 'success-message'));
//echo tagcontent('div', 'Fecha final: ' . $fecha_fin, array('class' => 'success-message'));


echo lineBreak2(1, array('class' => 'clr'));
echo Open('div', array('class' => 'col-md-12'));
echo tagcontent('h3', 'Listado Bancos detallado: ', array('class' => 'titulos'));

//    echo Open('div', array('class' => 'table-responsive','style'=>'height:1000px;overflow-y:scroll'));
    echo Open('div', array('class' => 'table-responsive'));

        echo Open('form', array('action'=>base_url('bancos/conciliaciones/actualizar'),
            'method'=>'post', 'name'=>'form_ch_reg'));
            echo tagcontent('div','',array('id'=>'new_deposito_out'));
          
                echo Open('table', array('class' => 'table table-hover'));
                    echo Open('tr');
//                    echo tagcontent('td', 'id');
                    echo tagcontent('td', 'Usuario');
                    echo tagcontent('td', 'Banco');
                    echo tagcontent('td', 'Detalle');
                    echo tagcontent('td', 'Vence');
                    echo tagcontent('td', 'Registrado');
//                    echo tagcontent('td', 'Transaccion');
                    echo tagcontent('td', 'Debito');
                    echo tagcontent('td', 'Credito');
//                    echo tagcontent('td', 'Saldo');
//                    echo tagcontent('td', 'Nota');
                    echo tagcontent('td', 'Conciliado');
                   

                    echo Close('tr');
                 
                //Variable para identificar cada checkbox
                $id_cta=0;
                $sum_debito =0;    
                $sum_credito =0;
                foreach ($cuenta_data as $cuenta) {
                    echo Open('tr');
//                    echo tagcontent('td', $cuenta->id); //NUMERO DE CHEQUE
                    echo tagcontent('td', $cuenta->nombre_usuario); //Nombre usuario
                    echo tagcontent('td', $cuenta->banco); //Banco
                    $link = tagcontent('a', $cuenta->nota, array('id' => 'ajaxpanelbtn',
                            'data-url' => base_url('bancos/depositos_all/ver_asiento_contable_dep/' .
                                    $cuenta->doc_id.'/'.$cuenta->banco_id.'/'.'1/5/'.$cuenta->tipo_transaccion),
                            'title' => 'Ver Comprobante',
                            'data-target' => 'new_total_out', 'href' => '#'));
                    
                    if($cuenta->tipo_id!=4){//Para saldo inicial no se muestra link
                        echo tagcontent('td', $link); //Detalle/nota
                    }else{
                        echo tagcontent('td', $cuenta->nota); //Banco
                    }
//                    echo tagcontent('td', $cuenta->banco_id); //Banco
                    echo tagcontent('td', $cuenta->fecha_vence);
                    echo tagcontent('td', $cuenta->fecha_registro);
//                    echo tagcontent('td', $cuenta->tipo_transaccion);
                    echo tagcontent('td', $cuenta->debito);
                        $sum_debito+= $cuenta->debito;
                    echo tagcontent('td', $cuenta->credito);
                        $sum_credito+= $cuenta->credito;
//                    echo tagcontent('td', $cuenta->saldo);
//                    echo tagcontent('td', $cuenta->nota);
                    echo Open('td');
                        echo Open('div',array('class' => 'checkbox'));
                            echo Open('label');
                                if($cuenta->conciliado == 1){//Marcar los conciliados
                                    echo input(array('type' => 'checkbox', 'name' => 'chk_select[]', 
                                        'id' => 'cta'.$id_cta,'class' => 'checks', 
                                        'value'=>$cuenta->id, 'checked'=>' '));
//                                        echo  input(array('type' => 'hidden', 'name' => 'chk_cta[]', 
//                                            'class' => 'form-control', 'value'=>$cuenta->id));
                                        echo  input(array('type' => 'hidden', 'name' => 'chks_total[]', 
                                            'class' => 'form-control', 'value'=>$cuenta->id));
                                }else{//DesMarcar los NO conciliados
                                    echo input(array('type' => 'checkbox', 'name' => 'chk_select[]', 
                                        'id' => 'cta'.$id_cta,'class' => 'checks', 
                                        'value'=>$cuenta->id, 'unchecked'=>' '));
//                                        echo  input(array('type' => 'hidden', 'name' => 'chk_cta[]', 
//                                            'class' => 'form-control', 'value'=>$cuenta->id));
                                        echo  input(array('type' => 'hidden', 'name' => 'chks_total[]', 
                                            'class' => 'form-control', 'value'=>$cuenta->id));
                                }
                            echo Close('div');
                        echo Close('div');
                    echo Close('td');
                    echo Close('td');
                    echo Close('td');

                    echo Close('tr');
                    $id_cta++;
                }
                /*SUMATORIAS*/
                echo Open('tr');
                        echo tagcontent('td', '<b>Total</b>');                                
                        echo tagcontent('td', '');                                
                        echo tagcontent('td', '');                                
                        echo tagcontent('td', '');                                
                        echo tagcontent('td', '<b>'.$sum_debito);                                
                        echo tagcontent('td', '<b>'.$sum_credito);                                

                    echo Close('tr');     
                echo Close('table');
               
                echo Open('div', array('class' => 'row'));
                echo Open('div', array('class' => 'col-md-12'));

                
                echo Open('div', array('class' => 'col-md-3'));

                    echo tagcontent('td', tagcontent('button', 'Conciliar', array('id' => 'ajaxformbtn', 
                        'data-target' => 'new_res_out', 'class' => 'btn btn-success')));
                    echo Close('form');
                echo Close('div');
                
                    //Form para desconciliar todo
                    echo Open('div', array('class' => 'col-md-3'));
                        echo Open('form', array('action'=>base_url('bancos/conciliaciones/actualizar'),
                            'method'=>'post', 'name'=>'form_ch_reg'));
                            echo  input(array('type' => 'hidden', 'name' => 'desc', 
                                'class' => 'form-control', 'value'=>'1'));

                                echo tagcontent('td', tagcontent('button', 'Desconciliar Todo', array('id' => 'ajaxformbtn', 
                                    'data-target' => 'new_res_out', 'class' => 'btn btn-danger')));

                        echo Close('form');
                    echo Close('div');
                      

                echo Close('div');

       
   
echo Close('div');

