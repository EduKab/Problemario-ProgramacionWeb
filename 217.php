<?php
    $oBD = new baseDatos();
    $oBD -> conecta();

    $ganador = new ganador();
    $ganador -> acumPuntos($oBD -> consulta("SELECT ganador, puntos FROM BD_Domino_Juegos WHERE id_estatus = 1"));
    $ganador -> deciGanador($oBD);

    class baseDatos{
        var $conexion;

        function conecta(){
            fscanf(STDIN, "%s", $server);
            fscanf(STDIN, "%s", $user);
            fscanf(STDIN, "%s", $password);
            fscanf(STDIN, "%s", $database);
            $this -> conexion = mysqli_connect($server, $user, $password, $database);
        }

        function consulta($query){
            $bloque = mysqli_query($this -> conexion, $query);
            return $bloque;
        }
    }

    class ganador{
        var $arrayJugadores = array();

        function acumPuntos($p_bloque){
            $numeRegistros = mysqli_num_rows($p_bloque);
            //echo("Numero de registros: ".$numeRegistros."\n");
            for ($cont = 0; $cont < $numeRegistros; $cont++){
                $bandera = true;
                $registro = mysqli_fetch_array($p_bloque);
                //echo("Registro: ".$registro['ganador']." ".$registro['puntos']."\n");
                //echo("Numero de jugadores: ".count($this -> arrayJugadores)."\n");
                if (count($this -> arrayJugadores) == 0){
                    array_push($this -> arrayJugadores, array($registro['ganador'], $registro['puntos']));
                }
                else{
                    for($cont2 = 0; $cont2 < count($this -> arrayJugadores) && $bandera == true; $cont2++){
                        //echo($registro['ganador']." ".$this -> arrayJugadores[$cont2][0]."\n");
                        if ($registro['ganador'] == $this -> arrayJugadores[$cont2][0] ){
                            $bandera = false;
                            $this -> arrayJugadores[$cont2][1] += $registro['puntos'];
                        }
                    }
                    if ($bandera == true){
                        array_push($this -> arrayJugadores, array($registro['ganador'], $registro['puntos']));
                    }
                }
            }
            //echo("Numero de jugadores: ".count($this -> arrayJugadores)."\n");
        }

        function deciGanador($p_oBD){
            $numeJugadores = count($this -> arrayJugadores);
            $nombres = "";
            $puntos = 0;

            if ($numeJugadores > 1){
                for($cont = 0; $cont < $numeJugadores; $cont++){
                    //echo ("Jugador: ".$this -> arrayJugadores[$cont][1]." >= ".$this -> arrayJugadores[$cont + 1][1]."\n");
                    if ($this -> arrayJugadores[$cont][1] > $puntos){
                        $puntos = $this -> arrayJugadores[$cont][1];
                    }
                }
                for ($cont = 0; $cont < count($this -> arrayJugadores); $cont++){
                    if ($this -> arrayJugadores[$cont][1] == $puntos){
                        $bloque = $p_oBD -> consulta("SELECT CONCAT(Nombre, ' ', Apellidos) AS Nombre FROM Usuarios WHERE Usuario = '".$this -> arrayJugadores[$cont][0]."'");
                        if ($bloque != false){
                            $registro = mysqli_fetch_array($bloque);
                            $nombres.=$registro['Nombre']. " ";
                        }
                    }
                }
                echo ($nombres.$puntos);
            }
            else{
                $bloque = $p_oBD -> consulta("SELECT CONCAT(Nombre, ' ', Apellidos) AS Nombre FROM Usuarios WHERE Usuario = '".$this -> arrayJugadores[0][0]."'");
                $registro = mysqli_fetch_array($bloque);
                echo($registro['Nombre']." ".$this -> arrayJugadores[0][1]."\n");
            }
        }
    }
?>