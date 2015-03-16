<div class="navbar-defaults sidebar" role="navigation">
                <div class="sidebar-nav navbar-collapse">
                    <ul class="nav" id="side-menu">
                        <li class="sidebar-search">
                            <div class="input-group custom-search-form">
                                <input type="text" class="form-control" placeholder="Search...">
                                <span class="input-group-btn">
                                <button class="btn btn-default" type="button">
                                    <i class="fa fa-search"></i>
                                </button>
                            </span>
                            </div>
                            <!-- /input-group -->
                        </li>
                        
                        
                        <li>
                            <a href="#"><i class="fa fa-wrench fa-fw"></i> Cheques<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="<?= base_url('bancos/cheques_import')?>"><i class="glyphicon glyphicon-upload"></i> Importar</a>
                                </li>
                                <li>
                                    <a href="<?= base_url('bancos/depositocheques')?>"><i class="glyphicon glyphicon-plus"></i> Custodio</a>
                                </li>
                               
                                
                                
                            </ul>
                            <!-- /.nav-second-level -->
                        </li>                        
                        
                        <li>
                            <a href="#"><i class="fa fa-wrench fa-fw"></i> Tarjetas de Credito<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="<?= base_url('bancos/tarjetascredito')?>"><i class="glyphicon glyphicon-credit-card"></i> Custodio</a>
                                </li>
                                <li>
                                    <a href="<?= base_url('bancos/depositotarjetas')?>"><i class="glyphicon glyphicon-transfer"></i> Depositadas</a>
                                </li>
                                            
                        
                            </ul>
                            
                        </li>    
                        <li>
                            <a href="#"><i class="fa fa-wrench fa-fw"></i> Depositos <span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                 <li>
                                    <a href="<?= base_url('bancos/depositos_all')?>"><i class="glyphicon glyphicon-pencil"></i> Depositos</a>
                                </li>
                                            
                        
                            </ul>
                            
                        </li>    
                        <li>
                            <a href="#"><i class="fa fa-wrench fa-fw"></i> Conciliaciones<span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">
                                <li>
                                    <a href="<?= base_url('bancos/conciliaciones')?>"><i class="glyphicon glyphicon-asterisk"></i> Conciliaciones</a>
                                </li>
                                            
                        
                            </ul>
                            
                        </li>    
                        
                        
                        <li>
                            <a href="<?= base_url('githubrepo/github/get_github')?>"><i class="fa fa-wrench fa-fw"></i> Descargar Repositorios<span class="fa arrow"></span></a>
                        </li>
                       
                        
                    </ul>
                </div>
                <!-- /.sidebar-collapse -->
            </div>
            <!-- /.navbar-static-side -->
