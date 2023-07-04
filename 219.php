<?php
    $oBD = new baseDatos();
    $oBD -> conecta();

    $procesos = new procesos();
    $procesos -> mostLlavPrimaria($oBD -> consulta("DESCRIBE BD_PagoServ_Facturas;"));
    //echo ("Parte de foraneas");
    $procesos -> mostLlavForanea($oBD -> consulta("DESCRIBE BD_PagoServ_Facturas;"), $oBD -> consulta("SHOW CREATE TABLE BD_PagoServ_Facturas;"));

    class baseDatos{
        var $conexion;
        var $database;

        function conecta(){
            fscanf(STDIN, "%s", $server);
            fscanf(STDIN, "%s", $user);
            fscanf(STDIN, "%s", $password);
            fscanf(STDIN, "%s", $this -> database);
            //echo($this -> database);
            $this -> conexion = mysqli_connect($server, $user, $password, $this -> database);
        }

        function consulta($p_query){
            $bloque = mysqli_query($this -> conexion, $p_query);
            return $bloque;
        }
    }

    class procesos{

        function mostLlavPrimaria($p_bloque){
            for($cont = 0; $cont < mysqli_num_rows($p_bloque); $cont++){
                $registro = mysqli_fetch_array($p_bloque);
                //echo($registro['Key']);
                if($registro['Key'] == 'PRI'){
                    echo("Nombre de llave primaria: ".$registro['Field']." [". $registro['Type']."]\n");
                }
            }   
        }

        function mostLlavForanea($p_bloque, $p_bloqueCreate){
            $arrayFK = array();

            echo("Foraneas:\n");
            for ($cont = 0; $cont < mysqli_num_rows($p_bloque); $cont++){
                $registro = mysqli_fetch_array($p_bloque);
                if($registro['Key'] == 'MUL'){
                    array_push($arrayFK, array($registro['Field'], $registro['Type']));
                }
            }
            sort($arrayFK);
            //for ($cont = 0; $cont < count($arrayFK); $cont++){
            //    echo($arrayFK[$cont][0]." ".$arrayFK[$cont][1]);
            //}
            $registro = mysqli_fetch_array($p_bloqueCreate);
            $posicion = strpos($registro['Create Table'], "CONSTRAINT");
            $cadena = substr($registro['Create Table'], $posicion);
            //echo("\n".$cadena."\n");
            $arrayCadenas = explode("CONSTRAINT", $cadena);
            //echo("\n".count($arrayCadenas)."\n");
            $arrayMostrar = array();
            for ($cont = 0; $cont < count($arrayCadenas) - 1; $cont++){
                $arrayContFK = explode("`", $arrayCadenas[$cont + 1]);
                array_push($arrayMostrar, "Nombre:".$arrayContFK[3]." <=> Tabla Referenciada:".$arrayContFK[5]." <=> CampoForaneo:".$arrayContFK[7]." <=> ");
            }

            sort($arrayMostrar);
            for($cont = 0; $cont < count($arrayMostrar); $cont++){
                echo($arrayMostrar[$cont]. "[".$arrayFK[$cont][1]."]\n");
            }
        }
    }
?>