<?php
 if (!isset($_SESSION)) {
  session_start();
}

 include('Connections/conexionUsuarios.php');

 
/* //Propcedimiento que se hace una vez cargado todo, para fijar la cantidad de mesas de cada localidad, de esta manera se evita sobrecargar de consultas a la base de datos

$descriptor= mysql_select_db($_SESSION['database_conexionUsuarios'], $_SESSION['conexionUsuarios']);
for ($i = 1; $i <= 94; $i++) {
 $Idlocalidad = $i;
 $registros="select * from mesas WHERE CodigoLocalidad='$Idlocalidad'" ;
 $consulta = mysql_query($registros) or die (mysql_error());
 $CantidadMesas = mysql_num_rows($consulta ) or (mysql_error());  
 
 $actualizar1 = "UPDATE localidades SET CantidadMesas=$CantidadMesas  WHERE Id=$Idlocalidad";
 $actualizar = mysql_query($actualizar1) or die (mysql_error());
  
}

*/




if (isset($_POST['Clave'])) { 
$_POST['Clave'] = sha1($_POST['Clave']);

}

?>
<?php
// *** Validate request to login to this site.

$loginFormAction = $_SERVER['PHP_SELF'];
if (isset($_GET['accesscheck'])) {
   $_SESSION['PrevUrl'] = $_GET['accesscheck'];
}

if (isset($_POST['Usuario'])) {


  $password = mysqli_real_escape_string($con,(strip_tags($_POST['Clave'], ENT_QUOTES)));
  $loginUsername = mysqli_real_escape_string($con,(strip_tags($_POST['Usuario'], ENT_QUOTES)));
  $MM_fldUserAuthorization = "nivel";
  $MM_redirectLoginSuccess = "verestadistica.php";
  $MM_redirectLoginFailed = "error.php";
  $MM_redirecttoReferrer = true;
  
 
 $LoginRSquery="SELECT * FROM users where user_name='$loginUsername' AND password='$password'";  
   
  $sql1=mysqli_query($con,$LoginRSquery);
  $LoginRS=mysqli_fetch_array($sql1);
  
  $loginFoundUser = mysqli_num_rows($sql1);
 
   
  if ($loginFoundUser>0) {
    
    $loginStrGroup  = $LoginRS['nivel'];
	$usuario_id  = $LoginRS['user_id'];
	$nivel_especial  = $LoginRS['nivel_especial'];
	 
    $_SESSION['nivel_especial'] = $nivel_especial;
	
	$_SESSION['usuario_id'] = $usuario_id;
   
    $_SESSION['MM_Username'] = $loginUsername;
    $_SESSION['MM_UserGroup'] = $loginStrGroup;
	$_SESSION['ver_mas']=$LoginRS['ver_mas'];
		      

    if (isset($_SESSION['PrevUrl']) && true) {
      $MM_redirectLoginSuccess = $_SESSION['PrevUrl'];	
    }
    header("Location: " . $MM_redirectLoginSuccess );
  }
  else {
     header("Location: ". $MM_redirectLoginFailed );
  }
}

?>

<!-- === ESTILOS Y LIBRERÍAS === -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>


<style>

  :root {
    --bg1: #eaf3ff;          /* azul muy claro */
    --bg2: #cfe3ff;          /* azul pastel */
    --card: #ffffff;
    --accent: #2563eb;       /* azul principal (similar a Tailwind blue-600) */
    --accent-hover: #1d4ed8; /* azul más intenso */
  }

  body {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    background: linear-gradient(135deg, var(--bg1), var(--bg2));
    margin: 0;
    padding: 0;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
  }

  .container {
    margin: 0;
    padding: 0;
    width: 100%;
    display: flex;
    justify-content: center;
  }

  /* Brillo suave en el fondo */
  body::before {
    content: "";
    position: fixed;
    inset: 0;
    background: radial-gradient(circle at center, rgba(37, 99, 235, 0.15), transparent 70%);
    z-index: -1;
  }

  .login-card {
    width: 100%;
    max-width: 420px;
    border: 0;
    border-radius: 1.25rem;
    box-shadow: 0 15px 45px rgba(37, 99, 235, 0.25);
    overflow: hidden;
    background: var(--card);
  }

  .login-header {
    background: linear-gradient(135deg, rgba(37, 99, 235, 0.08), rgba(37, 99, 235, 0.05));
    border-bottom: 1px solid rgba(37, 99, 235, 0.2);
    padding: 28px 28px 18px;
  }

  .login-title {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 0;
    font-weight: 700;
    color: #1e3a8a; /* azul oscuro para el título */
  }

  .login-sub {
    margin: 6px 0 0;
    color: #64748b;
    font-size: 0.95rem;
  }

  .lock-icon {
    width: 38px;
    height: 38px;
    flex: 0 0 auto;
    color: var(--accent);
  }

  .card-body {
    padding: 28px;
    background: var(--card);
  }

  .form-control {
    height: 48px;
    border-radius: 0.75rem;
    font-size: 1rem;
  }

  .input-group .form-control {
    border-right: 0;
  }

  .input-group-append .btn {
    border-left: 0;
    border-radius: 0 0.75rem 0.75rem 0;
    height: 48px;
  }

  .btn-accent {
    background: var(--accent);
    color: #fff;
    border: 0;
    border-radius: 0.75rem;
    height: 48px;
    transition: transform 0.03s ease, box-shadow 0.2s ease;
    box-shadow: 0 8px 20px rgba(37, 99, 235, 0.35);
  }

  .btn-accent:hover {
    background: var(--accent-hover);
    color: #fff;
  }

  .btn-accent:active {
    transform: translateY(1px);
  }

  .helper {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 8px;
    margin-top: 6px;
  }

  .brand-mini {
    display: flex;
    gap: 8px;
    align-items: center;
    justify-content: center;
    margin-top: 18px;
    color: #64748b;
    font-size: 0.85rem;
  }

  .brand-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: var(--accent);
    opacity: 0.9;
  }

  /* Validación simple */
  .is-invalid {
    border-color: #ef4444;
  }

  .invalid-feedback {
    display: block;
  }
</style>



<!-- === CONTENIDO === -->
<div class="container px-3">
  <div class="card login-card">
    <div class="login-header">
      <h2 class="login-title">
        <!-- Ícono de candado en SVG -->
        <svg class="lock-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <rect x="3" y="10" width="18" height="12" rx="2"></rect>
          <path d="M7 10V7a5 5 0 0 1 10 0v3"></path>
        </svg>
        Ingreso de usuarios
      </h2>
     
    </div>

    <div class="card-body">
      <form name="form2" method="POST" action="<?php echo htmlspecialchars($loginFormAction, ENT_QUOTES, 'UTF-8'); ?>" novalidate>
        <!-- Usuario -->
        <div class="form-group">
          <label for="Usuario" class="font-weight-semibold">Usuario</label>
          <input class="form-control" name="Usuario" id="Usuario" type="text" maxlength="20" autocomplete="username" required>
         
        </div>

        <!-- Clave con mostrar/ocultar -->
     <div class="form-group">
     <label for="Clave" class="font-weight-semibold">Clave</label>
      <div class="input-group">
        <input class="form-control" name="Clave" id="Clave" type="password" maxlength="20" autocomplete="current-password" required>
        <div class="input-group-append">
         <button class="btn btn-outline-secondary" type="button" id="togglePass" aria-label="Mostrar u ocultar clave">
          <i id="toggleIcon" class="fa fa-eye"></i>
         </button>
       </div>
      </div>
    </div>
        <!-- Botón -->
        <button type="submit" name="Submit" value="Aceptar" class="btn btn-accent btn-block mt-4">
          Ingresar
        </button>

        <div class="brand-mini">
          <span class="brand-dot"></span>
          Acceso seguro
          <span class="brand-dot"></span>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- === SCRIPTS === -->
<script>
  // Mostrar/ocultar clave
  $('#togglePass').on('click', function() {
    const input = $('#Clave');
    const icon = $('#toggleIcon');
    const isPass = input.attr('type') === 'password';
    input.attr('type', isPass ? 'text' : 'password');
    icon.toggleClass('fa-eye fa-eye-slash');
  });



  // Validación simple en cliente (sin romper tu validación del servidor)
  $('form[name="form2"]').on('submit', function(e){
    let ok = true;
    const u = $('#Usuario'), p = $('#Clave');
    if(!u.val().trim()){ u.addClass('is-invalid'); ok = false; } else { u.removeClass('is-invalid'); }
    if(!p.val().trim()){ p.addClass('is-invalid'); ok = false; } else { p.removeClass('is-invalid'); }
    if(!ok){ e.preventDefault(); }
  });

  // Quitar estado de error al tipear
  $('#Usuario, #Clave').on('input', function(){ $(this).removeClass('is-invalid'); });
</script>
