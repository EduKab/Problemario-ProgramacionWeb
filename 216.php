<?php
    $oBD = new baseDatos();
    $oBD -> conecta();
    //Tabla BD_Domino_Juegos
    $registros = $oBD -> consulta("SELECT * FROM BD_Domino_Juegos");

    $valida = new valida();
    $valida -> valiRegistros($registros, $oBD);
    
    class baseDatos{
        var $conexion;

        function conecta(){
            fscanf(STDIN, "%s", $server);
            fscanf(STDIN, "%s", $user);
            fscanf(STDIN, "%s", $password);
            fscanf(STDIN, "%s", $dataBase);
            $this -> conexion = mysqli_connect($server,$user,$password,$dataBase);
        }

        function consulta($query){
            $bloque = mysqli_query($this->conexion, $query);
            return $bloque;
        }
    }

    class valida{
        function valiRegistros($p_bloque, $p_oBD){
            $numeRegistros = mysqli_num_rows($p_bloque);
            //echo("NUMERO REGISTROS: ".$numeRegistros."\n");
            for($cont = 0; $cont < $numeRegistros; $cont++){
                $bandera = false;
                $registro = mysqli_fetch_array($p_bloque);
                //echo($registro['secuencia']."\n");
                if ($registro['secuencia'] != ""){
                    $datos = explode(" ",$registro['secuencia']);
                }
                else
                    $bandera = true;
                
                //echo(count($datos)."\n");
                //echo($datos[1]."\n");
                for ($cont2 = 0; $cont2 < count($datos) && $bandera == false; $cont2++){
                    for ($cont3 = 0; $cont3 < count($datos) && $bandera == false; $cont3++){
                        //echo("Comparacion ".$cont2." -> ");
                        //echo($datos[$cont2])." ";
                        //echo($datos[$cont3]."\n");
                        if($datos[$cont2] == $datos[$cont3] && $cont2 != $cont3){
                            $bandera = true;
                            //Tabla Usuarios
                            $bloque =  $p_oBD -> consulta("SELECT CONCAT(Nombre, ' ', Apellidos) AS nombre FROM Usuarios WHERE Usuario = '".$registro['id_usuario']."'");
                            $nombUsuario = mysqli_fetch_array($bloque);
                            $bloque = $p_oBD -> consulta("SELECT CONCAT(Nombre, ' ', Apellidos) AS nombre FROM Usuarios WHERE Usuario = '".$registro['id_invitado']."'");
                            $nombInvitado = mysqli_fetch_array($bloque);
                            echo($registro['id'].":".$nombUsuario['nombre'].":".$nombInvitado['nombre'].":Ficha Duplicada\n");
                        }
                        else if($datos[$cont2] == strrev($datos[$cont3]) && $cont2 != $cont3){
                            //echo("FICHAS: ".$datos[$cont2]." = ".$datos[$cont3]."\n");
                            $bandera = true;
                            //Tabla Usuarios
                            $bloque =  $p_oBD -> consulta("SELECT CONCAT(Nombre, ' ', Apellidos) AS nombre FROM Usuarios WHERE Usuario = '".$registro['id_usuario']."'");
                            $nombUsuario = mysqli_fetch_array($bloque);
                            $bloque = $p_oBD -> consulta("SELECT CONCAT(Nombre, ' ', Apellidos) AS nombre FROM Usuarios WHERE Usuario = '".$registro['id_invitado']."'");
                            $nombInvitado = mysqli_fetch_array($bloque);
                            echo($registro['id'].":".$nombUsuario['nombre'].":".$nombInvitado['nombre'].":Ficha Duplicada\n");    
                        }
                    }
                }
                if ($bandera == false){
                    for($cont2 = 0; $cont2 < count($datos); $cont2++){
                        if($cont2 + 1 < count($datos)){
                            $valoFicha1 = explode(":", $datos[$cont2]);
                            $valoFicha2 = explode(":", $datos[$cont2 + 1]);
                            if ($valoFicha1[1] != $valoFicha2[0]){
                                $bandera = true;
                                //Tabla Usuarios
                                $bloque =  $p_oBD -> consulta("SELECT CONCAT(Nombre, ' ', Apellidos) AS nombre FROM Usuarios WHERE Usuario = '".$registro['id_usuario']."'");
                                $nombUsuario = mysqli_fetch_array($bloque);
                                $bloque = $p_oBD -> consulta("SELECT CONCAT(Nombre, ' ', Apellidos) AS nombre FROM Usuarios WHERE Usuario = '".$registro['id_invitado']."'");
                                $nombInvitado = mysqli_fetch_array($bloque);
                                echo($registro['id'].":".$nombUsuario['nombre'].":".$nombInvitado['nombre'].":Secuencia Mal\n");
                            }
                        }
                    }
                }
            }
        }
    }
?>