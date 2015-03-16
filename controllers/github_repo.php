<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of github_repo
 *
 * @author viche
 */
include('ssh2.php');
class github_repo extends MX_Controller {

    public function __construct() {
        parent::__construct();
    }
    
    public function downloadFile() {
        $name = $this->input->post('get_link');
        /* Guarda un archivo en ruta determinada */
        $ar = fopen("C://Users//viche//tallergit//datos.txt", "a") or
                die("Problemas en la creacion");
        fputs($ar, $_REQUEST['get_link']);
        fputs($ar, "\n");
        fputs($ar, "--------------------------------------------------------");
        fputs($ar, "\n");
        fclose($ar);
        $ruta = "C://Users//viche//tallergit";

        
        //exec("git clone https://github.com/MASTERPC-CIA/bill_anexos.git", $ruta);
        $ssh = new Net_SSH2('installersgit.billingsof.com');
        if (!$ssh->login('installersgit', '1234ABC')) {
            exit('Login Failed');
        }

        echo $ssh->exec('pwd');
        echo $ssh->exec('ls -la');
        echo ssh2_exec('git clone https://github.com/MASTERPC-CIA/bill_servicios.git testclone');
        //$suma = $name + 23;
        echo shell_exec('git clone https://github.com/MASTERPC-CIA/bill_servicios.git testclone');

        echo "Los datos se cargaron correctamente.";
        //$conexion = ssh2_connect('installersgit.billingsof.com', 2211);
        //ssh2_auth_password($conexion, 'installersgit', '1234ABC');
        //$comando = ssh2_exec($conexion, 'C:\bat\scriptwindows\scriptwindows.bat');
        // require_once('Git.php');
        //   $repo = Git::open('https://github.com/MASTERPC-CIA/bill_anexos.git');  // -or- Git::create('/path/to/repo')
//        $repo->add('.');
//        $repo->commit('Some commit message');
//        $repo->push('origin', 'master');
        // $repo->clone_from("https://github.com/MASTERPC-CIA/bill_anexos.git", $ruta);
//echo "<pre>$salida</pre>";


        /*  echo $suma;
          /  $LOCAL_ROOT = "/path/to/repo/parent/directory";
          $LOCAL_REPO_NAME = "REPO_NAME";
          $LOCAL_REPO = "{$LOCAL_ROOT}/{$LOCAL_REPO_NAME}";
          $REMOTE_REPO = "git@github.com:angelvcuenca/reponame.git";
          $BRANCH = "master";

          if ($_POST['payload']) {
          // Only respond to POST requests from Github

          if (file_exists($LOCAL_REPO)) {

          // If there is already a repo, just run a git pull to grab the latest changes
          shell_exec("cd {$LOCAL_REPO} && git pull");

          die("done " . mktime());
          } else {

          // If the repo does not exist, then clone it into the parent directory
          shell_exec("cd {$LOCAL_ROOT} && git clone {$REMOTE_REPO}");

          die("done " . mktime());
          }
          }

          /*
          // Establecemos el directorio donde se guardan los ficheros
          $sDirGuardar = $_SERVER["DOCUMENT_ROOT"]."/directorio/guardar/";
          $iContFicSubidos = 0;

          // Recorremos los Ficheros recibidos
          foreach ($_FILES as $vFichero)
          {

          // Se establece el fichero con el nombre original
          $sFichero = $sDirGuardar.$vFichero["name"];

          // Si el archivo ya existe, no lo guardamos
          if (file_exists($sFichero))
          {
          echo "<br/>El archivo ".$vFichero["name"]." ya existe<br/>";
          continue;
          }

          // Copiamos de la dirección temporal al directorio final

          if (filesize($vFichero["tmp_name"]))
          if (!(move_uploaded_file($vFichero["tmp_name"], $sFichero)))
          {
          echo "<br/>Error al escribir el archivo ".$vFichero["name"]."<br/>";
          }
          else
          {
          chmod($sFichero, 0666);
          $iContFicSubidos++;
          }

          }

          echo "<br/>Fin de ejecución de 'GuardaArchivosFormulario', $iContFicSubidos archivos subidos.<br/>";
         */
    }

    function my_ssh_disconnect($reason, $message, $language) {
        printf("Servidor desconectado con el siguiente código [%d] y mensaje: %s\n", $reason, $message);
    }

//put your code here
}
