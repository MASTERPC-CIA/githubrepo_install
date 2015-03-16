
<?php

    echo' <h3>Download Repositorio</h3>';
       echo '<br><br>';
        echo Open('form', array('action' => base_url('githubrepo/github_repo/downloadFile'), 'method' => 'post'));
            
         echo Open('div',array('class'=>'col-md-10 form-group'));
            echo Open('div',array('class'=>'input-group'));
              echo tagcontent('span', 'Copy Repo. : ', array('class'=>'input-group-addon'));
               echo input(array('type' => 'text', 'name' => 'get_link', 'id' => 'get_link', 'placeholder' => 'pegar el link aqui', 'class' => 'form-control'));
             //  echo tagcontent('div', $input3, array('class' => 'col-md-8'));
              echo Close('div');
        echo Close('div'); 
        
                   
        echo tagcontent('button', 'Descargar', array('name' => 'btnreportes', 'class' => 'btn btn-warning  col-md-1', 'id' => 'ajaxformbtn', 'type' => 'submit', 'data-target' => 'opcion_elegida'));
        echo Close('form');

    echo '<br>';

    echo tagcontent('div', '', array('id' => 'opcion_elegida', 'class' => 'col-md-12'));
    echo '<br>';
    echo '<br>';

