<?php 

 if (isset($_SESSION['user_id'])){
   $avatar=$_SESSION['avatar'];
   $openia=$_SESSION['openia'];

?>
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
          <!-- Sidebar user panel -->
          <div class="user-panel">
            <div class="pull-left image">
              <img src="<?php echo $avatar; ?>" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
              <p><?php echo $_SESSION['full_name']; ?></p>
              <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
          </div>
          <!-- sidebar menu: : style can be found in sidebar.less -->
          <ul class="sidebar-menu">
            <li class="header">MENÚ</li>
            <li class="<?php if (isset($home) and $home==1){echo "active";}?>">
              <a href="index.php">
                <i class="fa fa-home"></i> <span>Inicio</span> 
              </a>
              
            </li>
			
		           
           <?php 
				permisos_menu('Datos',$cadena_permisos);
				$permisos_datos=$permisos_ver_menu;
	           
              				
				if ($permisos_datos==1 or $permisos_rubros==1){
			?>
            
            <li class="<?php if (isset($datos) and $datos==1){echo "active";}?> treeview">
              <a href="#">
                <i class="glyphicon glyphicon-th-large"></i>
                <span>Datos</span>
                <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
			<?php 
		    if ($permisos_datos==1){
			?>	
               <li class="<?php if (isset($datos) and $datos==1){echo "active";}?>">
              <a href="datos.php">
                <i class="glyphicon glyphicon-list-alt"></i> <span>Agregar Dato</span>
              </a>
            </li>
			<?php } ?>
			            
           
            	
              </ul>
            </li>
			<?php } ?>
            
            
            <!-- -->
			<?php 
			   
				permisos_menu('Ministerios',$cadena_permisos);
				$permisos_ministerios=$permisos_ver_menu;
				permisos_menu('Funcionarios',$cadena_permisos);
				$permisos_funcionarios=$permisos_ver_menu;
				if ($permisos_funcionarios==1){
			?>
            <li class="<?php if (isset($gobierno) and $gobierno==1){echo "active";}?> treeview">
              <a href="#">
                <i class="fa fa-university"></i>
                <span>Gobierno</span>
                <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
			<?php 
				if ($permisos_ministerios==1){
			?>	
                <li class="<?php if (isset($organigrama) and $organigrama==1){echo "active";}?>"><a href="organigrama.php"><i class="glyphicon glyphicon-move"></i> Organigrama</a></li>
              
              <?php } ?>
			  
             <?php  permisos_menu('Planes_Programas_Proyectos',$cadena_permisos); 
               if ($permisos_ver_menu==1){?>    
             
               <li class="<?php if (isset($planes_programas_proyectos) and $planes_programas_proyectos==1){echo "active";}?> treeview">
              <a href="#">
                <i class="fa fa-folder-open"></i>
                <span>Planes, Programas y Proyectos </span>
                <i class="fa fa-angle-left pull-right"></i>              </a>
              <ul class="treeview-menu">
              
                      
            <li class="<?php if (isset($proyectos_abm) and $proyectos_abm==1){echo "active";}else {echo "";}?>">
              <a href="proyectos_abm.php">
                <i class="glyphicon glyphicon-tag"></i> <span>Proyectos ABM</span>              </a>            </li>
            <li class="<?php if (isset($programas_abm) and $programas_abm==1){echo "active";}else {echo "";}?>">
              <a href="programas_abm.php">
                <i class="glyphicon glyphicon-tag"></i> <span>Programas ABM</span>              </a>            </li>
            
            <li class="<?php if (isset($planes_abm) and $planes_abm==1){echo "active";}else {echo "";}?>">
              <a href="planes_abm.php">
                <i class="glyphicon glyphicon-tag"></i> <span>Planes ABM</span>              </a>            </li>
            </ul>  
              
			 <?php } ?>
            
            <?php  permisos_menu('Necesidades_Problemas',$cadena_permisos); 
               if ($permisos_ver_menu==1){?>               
            <li class="<?php if (isset($necesidades_problemas) and $necesidades_problemas==1){echo "active";}?>"><a href="necesidades_problemas.php"><i class="glyphicon glyphicon-pencil"></i> Necesidades y Problemas</a></li>
		           
            <?php } ?>
             
              <?php  permisos_menu('Objetivos_Metas_Indicadores',$cadena_permisos); 
               if ($permisos_ver_menu==1){?>  
                <li class="<?php if (isset($objetivos) and $objetivos==1){echo "active";}?>"><a href="objetivos.php"><i class="glyphicon glyphicon-eye-open"></i> Objetivos</a></li>
                
                 <li class="<?php if (isset($acciones) and $acciones==1){echo "active";}?>"><a href="acciones.php"><i class="glyphicon glyphicon-play-circle"></i> Acciones</a></li>
                 <li class="<?php if (isset($objetivos_metas_indicadores) and $objetivos_metas_indicadores==1){echo "active";}?> treeview">
              <a href="#">
                <i class="fa fa-folder-open"></i>
                <span>Obj., Metas e Indicadores </span>
                <i class="fa fa-angle-left pull-right"></i>              </a>
               <ul class="treeview-menu">
              
              <li class="<?php if (isset($objetivos_abm) and $objetivos_abm==1){echo "active";}else {echo "";}?>">
              <a href="objetivos_abm.php">
                <i class="glyphicon glyphicon-tag"></i> <span>Objetivos ABM</span>              </a>            </li>
              
               <li class="<?php if (isset($metas_abm) and $metas_abm==1){echo "active";}else {echo "";}?>">
              <a href="metas_abm.php">
                <i class="glyphicon glyphicon-tag"></i> <span>Metas ABM</span>              </a>            </li>
            
             <li class="<?php if (isset($indicadores_gestion_abm) and $indicadores_gestion_abm==1){echo "active";}else {echo "";}?>">
              <a href="indicadores_gestion_abm.php">
                <i class="glyphicon glyphicon-tag"></i> <span>indicadores ABM</span>              </a>            </li>
               </ul>
                
                
			<?php } ?>
         
              
              <?php 
				if ($permisos_funcionarios==1){
			?>	
                <li class="<?php if (isset($funcionarios) and $funcionarios==1){echo "active";}?>"><a href="funcionarios.php"><i class="glyphicon glyphicon-user"></i> Funcionarios</a></li>
			<?php } ?>
              </ul>
            </li>
			
            <?php } ?>
            
            
            
			<?php } ?>	
            
             <?php 
			permisos_menu('Obras',$cadena_permisos);
			if ($permisos_ver_menu==1){
			?>
            
             <li class="<?php if (isset($obras) and $obras==1){echo "active";}?> treeview">
              <a href="#">
                <i class="fa fa-cubes"></i>
                <span>Obras</span>
                <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
			
                <li class="<?php if (isset($listado_obras) and $listado_obras==1){echo "active";}?>"><a href="obras.php"><i class="glyphicon fa fa-cube"></i> Listado Obras</a></li>
                
                <li class="<?php if (isset($porcentaje_obras) and $porcentaje_obras==1){echo "active";}?>"><a href="porcentajes_obras.php"><i class="glyphicon glyphicon-stats"></i> Porcentaje Obras</a></li>
			     
                 <li class="<?php if (isset($costos_obras) and $costos_obras==1){echo "active";}?>"><a href="ver_dato.php?id=79"><i class="glyphicon fa fa-usd"></i> Costos X U.M.</a></li>
                 
                 <li class="<?php if (isset($cronograma_obras) and $cronograma_obras==1){echo "active";}?>"><a href="#"><i class="glyphicon glyphicon-time"></i> Cronograma Obras </a></li>  
                  
                        
              </ul>
            </li>
			<?php } //manage_producciones.php?>
            
            
			<?php 
				permisos_menu('Estadisticas',$cadena_permisos);
				if ($permisos_ver_menu==1){
			?>
            <li class="<?php if (isset($estadisticas) and $estadisticas==1){echo "active";}?> treeview">
              <a href="#">
                <i class="glyphicon glyphicon-signal"></i> <span>Estadísticas</span>
                <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                                
                 <li class="<?php if (isset($indicadores) and $indicadores==1){echo "active";}?>"><a href="indicadores.php"><i class="glyphicon glyphicon-globe"></i> Indicadores</a></li>               
                    <li class="<?php if (isset($graficos) and $graficos==1){echo "active";}?>"><a href="graficos.php"><i class="glyphicon glyphicon-stats"></i> Gr&aacute;ficos</a></li>                   
                                                     
              </ul>
            </li>
			<?php } ?>
                    
            
			<?php 
			    permisos_menu('Rubros',$cadena_permisos);
				$permisos_rubros=$permisos_ver_menu;
				permisos_menu('Configuracion',$cadena_permisos);
				if ($permisos_ver_menu==1){
			?>
			<li class="<?php if (isset($business_profile) and $business_profile==1){echo "active";}else {echo "";}?> treeview">
              <a href="#">
                <i class="fa fa-wrench"></i> <span>Configuración</span>
                <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                <li class="<?php if (isset($business_profile) and $business_profile==1){echo "active";}else {echo "";}?>"><a href="business_profile.php"><i class="glyphicon glyphicon-briefcase"></i> Perfil de la Organización</a></li>
                 
                  <?php 
			if ($permisos_rubros==1){
			?>	
               <li class="<?php if (isset($rubros) and $rubros==1){echo "active";}else {echo "";}?>">
              <a href="rubros.php">
                <i class="glyphicon glyphicon-tag"></i> <span>Rubros</span>
              </a>
            </li>
             <?php } ?>
                 
              </ul>
            </li>
			<?php }
			
			if ($permisos_rubros==1){
			?>	
               <li class="<?php if (isset($exportar) and $exportar==1){echo "active";}else {echo "";}?>">
              <a href="exportar_excel.php">
                <i class="glyphicon glyphicon-tag"></i> <span>Concejales</span>
              </a>
            </li>
             <?php } 
			
			 
				permisos_menu('Permisos',$cadena_permisos);
				$permisos_grupos=$permisos_ver_menu;
				permisos_menu('Usuarios',$cadena_permisos);
				$permisos_usuarios=$permisos_ver_menu;
				if ($permisos_grupos==1 or $permisos_usuarios==1){
			?>
			<li class="<?php if (isset($access) and $access==1){echo "active";}else {echo "";}?> treeview">
              <a href="#">
                <i class="fa fa-lock"></i> <span>Administrar accesos</span>
                <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
			<?php 
				if ($permisos_grupos==1){
			?>  
                <li class="<?php if (isset($groups) and $groups==1){echo "active";}else {echo "";}?>"><a href="group_list.php"><i class="glyphicon glyphicon-briefcase"></i> Grupos de usuarios</a></li>
			<?php } ?>	
			<?php 
				if ($permisos_usuarios==1){
			?>
				<li class="<?php if (isset($users) and $users==1){echo "active";}else {echo "";}?>"><a href="user_list.php"><i class="fa fa-users"></i> Usuarios</a></li>
			<?php } ?>	
              </ul>
            </li>
            <?php } ?>
           
          <?php  permisos_menu('Backups',$cadena_permisos); 
		  if ($permisos_ver_menu==1){
		  ?>
          
            <li class="<?php if (isset($Backups) and $Backups==1){echo "active";}else {echo "";}?>"><a href="backups.php"><i class="fa fa-database"></i><span>Backup</span>  </a></li>
              <?php } ?>
          
           <?php  permisos_menu('Localizaciones',$cadena_permisos); 
		  if ($permisos_ver_menu==1){
		  ?>
          
            <li class="<?php if (isset($Google_maps) and $Google_maps==1){echo "active";}else {echo "";}?>"><a href="google_maps.php"><i class="fa fa-map-marker"></i><span>Localizaciones</span>  </a></li>
             <li class="<?php if (isset($Google_maps) and $Google_maps==1){echo "active";}else {echo "";}?>"><a href="google_maps_confirmaciones.php"><i class="fa fa-map-marker"></i><span>Confirmaciones</span>  </a></li>
             
             <li class="<?php if (isset($archivos) and $archivos==1){echo "active";}else {echo "";}?>"><a href="archivos.php"><i class="fa fa-folder-open-o"></i><span>Archivos</span>  </a></li>
             
             <li class="<?php if (isset($links) and $links==1){echo "active";}else {echo "";}?>"><a href="links.php"><i class="fa fa-link"></i><span>Links</span>  </a></li>
             
            
              <?php } ?>  
          
          <?php  permisos_menu('Localidades',$cadena_permisos); 
		  if ($permisos_ver_menu==1){
		  ?>
          
            <li class="<?php if (isset($localidades) and $localidades==1){echo "active";}else {echo "";}?>"><a href="localidades.php"><i class="fa fa-building-o"></i> <span>Localidades</span>  </a></li>
              <?php } ?>  
           
            <?php permisos_menu('Compromisos',$cadena_permisos); 
		  if ($permisos_ver_menu==1){
		  ?>
            <li class="<?php if (isset($compromisos) and $compromisos==1){echo "active";}else {echo "";}?>"><a href="movimientos.php"><i class="fa fa-edit"></i> <span>Compromisos </span>  </a></li>
            <?php } ?>  
           
            <?php  permisos_menu('Entidades',$cadena_permisos); 
		  if ($permisos_ver_menu==1){
		  ?>
          
            <li class="<?php if (isset($entidades) and $entidades==1){echo "active";}else {echo "";}?>"><a href="entidades.php"><i class="fa fa-flag-o"></i> <span>Entidades</span>  </a></li>
                      
              <?php } ?>                  
           
          <?php  permisos_menu('Agenda',$cadena_permisos); 
		  if ($permisos_ver_menu==1){
		  ?>
          
            <li class="<?php if (isset($agenda) and $agenda==1){echo "active";}else {echo "";}?>"><a href="agenda.php"><i class="fa fa-calendar"></i><span>Agenda</span>  </a></li>
              <?php } ?>
              
           
           
           <!-- -->
             <?php 
				permisos_menu('Rubros',$cadena_permisos);
				if ($permisos_ver_menu==1){
			?>
            <li class="<?php if (isset($informacion_territorial) and $informacion_territorial==1){echo "active";}?> treeview">
              <a href="#">
                <i class="fa fa-info-circle"></i> <span>informacion_territorial</span>
                <i class="fa fa-angle-left pull-right"></i>
              </a>
              <ul class="treeview-menu">
                 
                 <li class="<?php if (isset($sistema_informacion) and $sistema_informacion==1){echo "active";}else {echo "";}?>"><a href="sistema_informacion.php"><i class="fa fa-gear"></i><span> Sistema Informacion</span>  </a></li>
                 <li class="<?php if (isset($volumenes_rubros_microregiones) and $volumenes_rubros_microregiones==1){echo "active";}else {echo "";}?>"><a href="volumenes_rubros_microregiones.php"><i class="fa fa-database"></i><span> Volumenes por Microregion</span>  </a></li>
                     
                <li class="<?php if (isset($grupo_informacion) and $grupo_informacion==1){echo "active";}else {echo "";}?>"><a href="grupo_informacion.php"><i class="fa fa-info-circle"></i><span>Grupo Informacion</span>  </a></li>                                            
              </ul>
            </li>
			<?php } ?>
            
           <!-- -->
          
            <li class="active"><a href="login.php?logout"><i class="fa fa-sign-out"></i><span>Salir</span>  </a></li>
                
          </ul>
      
             
        </section>
        <!-- /.sidebar -->
		