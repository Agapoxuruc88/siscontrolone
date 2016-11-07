<?php
session_start();
$username       = "";
$password       = "";
$mensaje        = "";
$response       = null;
$error          = false;
$respuesta      = (object) array(
    'mensaje'      => "",
    'response' => ""
);

 $cambioPass = '
    <div class="form-group">
        <div class="input-group">
            <div class="input-group-addon"><span class="glyphicon glyphicon-pencil"></span></div>
            <input type="password" class="form-control" name="pass1" placeholder="Ingrese nueva contraseña" autofocus>
        </div>
    </div>
    <div class="form-group">
        <div class="input-group">
            <div class="input-group-addon"><span class="glyphicon glyphicon-pencil"></span></div>
            <input type="password" class="form-control" name="pass2" placeholder="Confirme nueva Contraseña">
        </div>
    </div>';

if( !isset($_SESSION['idPerfil']) ) {

    if( isset($_POST['username']) && isset($_POST['password']) ) { 
        require_once 'class/conexion.class.php';
        require_once 'class/validacion.class.php';
        $conexion   = new Conexion();
        $validacion = new Validacion( $conexion );

        $username  = $_POST['username'];
        $password  = $_POST['password'];
        $respuesta = $validacion->validarUsuario( $username, $password );

        if( $respuesta->respuesta == 0 ){           // ERROR NO EXISTE USUARIO o CONTRASEÑA
            $error = true;
            $response = $respuesta->respuesta;
            $mensaje  = "<div class='alert error'>{$respuesta->mensaje}</div>";
        }

        if( $respuesta->respuesta == 1 ){       // LOGUEO EXITOSO
            include 'session.php';
            crearSession( $username );
            header('Location: ./');
        }

        // AGREGAR INPUTS PARA CAMBIO DE CONTRASEÑA SI ES PRIMER INICIO
        if( $respuesta->respuesta == 2 || isset( $_POST['pass1'] ) ){

            // VALIDAR CONTRASEÑAS NUEVOS ESTEN DEFINIDOS
            if( isset($_POST['pass1']) && isset($_POST['pass2']) ){
                // OBTENER NUEVOS PASSWORDS
                $pass1 = $_POST['pass1'];
                $pass2 = $_POST['pass2'];

                // VALIDA QUE LA CONSTRASEÑA NUEVA NO SEA IGUAL A LA ANTERIOR
                if( $password == $pass1 AND $password == $pass2 ):
                    $response = 2;
                    $error    = true;
                    $mensaje  = "<div class='alert warning'>La constraseña es la misma, ingrese otra diferente.</div>";;

                elseif( $pass1 != $pass2 ):    // VALIDA QUE LAS CONSTRASEÑAS NUEVAS SEAN IGUALES
                    $response = 2;
                    $error = true;
                    $mensaje  = "<div class='alert warning'>Las contraseñas no coinciden</div>";;
                else:
                    $respuesta = $validacion->cambiarPassword( $username, $pass1 );

                    // SI CAMBIAR LA CONTRASEÑA
                    if( $respuesta->respuesta):
                        include 'session.php';
                        crearSession( $username );
                        header('Location: ./');
                    else:
                        $error = true;
                        $mensaje  = "<div class='alert error'>No se pudo cambiar la contraseña, intentelo nuevamente.</div>";
                    endif;
                    
                endif;

            }else{
                $error    = true;
                $response = 2;
                $mensaje  = "<div class='alert warning'>$respuesta->mensaje</div>";
            }

        }

        $conexion->close();
    }

?>

<!DOCTYPE html>
<html>
<head>
    <title>LOGIN</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <style>
        body {
            color: #FFF;
            background: rgba(73,155,234,1);
            background: -moz-linear-gradient(left, rgba(73,155,234,1) 0%, rgba(49,106,168,1) 48%, rgba(6,89,179,1) 100%);
            background: -webkit-gradient(left top, right top, color-stop(0%, rgba(73,155,234,1)), color-stop(48%, rgba(49,106,168,1)), color-stop(100%, rgba(6,89,179,1)));
            background: -webkit-linear-gradient(left, rgba(73,155,234,1) 0%, rgba(49,106,168,1) 48%, rgba(6,89,179,1) 100%);
            background: -o-linear-gradient(left, rgba(73,155,234,1) 0%, rgba(49,106,168,1) 48%, rgba(6,89,179,1) 100%);
            background: -ms-linear-gradient(left, rgba(73,155,234,1) 0%, rgba(49,106,168,1) 48%, rgba(6,89,179,1) 100%);
            background: linear-gradient(to right, rgba(73,155,234,1) 0%, rgba(49,106,168,1) 48%, rgba(6,89,179,1) 100%);
            filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#499bea', endColorstr='#0659b3', GradientType=1 );
            display:table-cell;
            border: 1px solid transparent;
            vertical-align:middle;
        }
        .alert {
            color: #FFF;
            padding: 6px;
            font-size: 14px;
            text-align: center;
        }
        .error{
            background-color: #C12E2A;
        }
        .warning{
            background-color: #ff5722;
        }
        .panel-opacity {
            padding: 0 20px;
            border-radius: 0;
            background-color: rgba(0,0,0,0.25);
        }
    </style>
</head>
<body>
    <div class="row">
        <div class="col-md-offset-6 col-md-7">
            <div class="panel panel-opacity">
                <div class="panel-heading text-center">
                    <img src="img/logo.jpg" class="img-responsive">
                    <h3>
                        SISTEMA DE ACCESO
                    </h3>
                </div>
                <div class="panel-body" >
                    <form method="POST" autocomplete="off" action=" <?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?> ">
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-addon"><span class="glyphicon glyphicon-user"></span></div>
                                <input type="text" class="form-control" name="username" placeholder="Usuario" value="<?= $username ?>" >
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></div>
                                <input type="password" class="form-control" name="password" placeholder="Contraseña" value="<?= $password ?>">                                        
                            </div>
                        </div>
                        <?php 
                            if( $response == 2 && $error )
                                echo $cambioPass;
                        ?>
                        <div class="form-group">
                            <button type="submit" name="submit" class="btn btn-success btn-block">
                                <span class="glyphicon glyphicon-globe"></span> Acceder
                            </button>
                        </div>
                        <?php 
                        if ( $error ){
                        ?>
                            <div class="form-group">
                                <?php
                                    echo $mensaje;
                                ?>
                            </div>
                        <?php
                        }
                        ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php
}
?>
