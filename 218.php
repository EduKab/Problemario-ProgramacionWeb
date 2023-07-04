<?php
    $oBD = new baseDatos();
    $oBD -> conecta();

    $acciones = new acciones();
    echo("Invita\n");
    $acciones -> mostrar($acciones -> deteMas($oBD, 'id_usuario'));

    echo("Invitado\n");
    $acciones -> mostrar($acciones -> deteMas($oBD, 'id_invitado'));

    class baseDatos{
        var $conexion;

        function conecta(){
            fscanf(STDIN, "%s", $server);
            fscanf(STDIN, "%s", $user);
            fscanf(STDIN, "%s", $password);
            fscanf(STDIN, "%s", $database);
            $this -> conexion = mysqli_connect($server, $user, $password, $database);
        }

        function consulta($p_query){
            $bloque = mysqli_query($this -> conexion, $p_query);
            return $bloque;
        }
    }

    class acciones{

        function deteMas($p_oBD, $tipoUsuario){
            $bloque = $p_oBD -> consulta("SELECT COUNT(".$tipoUsuario.") as numero_veces FROM BD_Domino_Juegos GROUP BY ".$tipoUsuario." ORDER BY numero_veces DESC LIMIT 1;");
            $registro = mysqli_fetch_array($bloque);
            $bloqueAuxiliar = $p_oBD -> consulta("SELECT CONCAT(s.Nombre, ' ', s.Apellidos) AS nombre FROM (SELECT ".$tipoUsuario.", CASE 
                                                                                                                                WHEN COUNT(".$tipoUsuario.") = ".$registro['numero_veces']." THEN 1
                                                                                                                                ELSE 0
                                                                                                                               END AS mostrar
                                                                                                            FROM BD_Domino_Juegos GROUP BY ".$tipoUsuario.")
                                                                                                        j JOIN Usuarios s ON j.".$tipoUsuario." = s.Usuario WHERE mostrar = 1 ORDER BY s.Apellidos;");
            return $bloqueAuxiliar;
        }

        function mostrar($p_bloque){
            for($cont = 0; $cont < mysqli_num_rows($p_bloque); $cont++){
                $registroAuxiliar = mysqli_fetch_array($p_bloque);
                echo($registroAuxiliar['nombre']."\n");
            }
        }
    }
?>