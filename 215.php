<?php    
    $oBD = new baseDatos();
    $oBD -> estaConexion();
    $oBD -> consulta("SHOW TABLES;");
    $secuencia = new secuencia();
    echo($secuencia -> armaCadena($oBD -> bloque, $oBD -> baseDatos));

    class secuencia{
        function armaCadena($bloque, $baseDatos){
            $numeRegistros = mysqli_num_rows($bloque);
            $cadena = "";
            $array = array();
            for ($cont=1; $cont <= $numeRegistros; $cont++){
                $registro = mysqli_fetch_array($bloque);
                $array[$cont] = $registro['Tables_in_'.$baseDatos];
            }
            for ($cont = count($array); $cont > 0; $cont--){
                if ($cont - 1 == 0)
                    $cadena.= $array[$cont];
                else
                    $cadena.= $array[$cont].":";
            }
            return $cadena;
        }
    }

    class baseDatos{
        var $servidor;
        var $usuario;
        var $password;
        var $baseDatos;
        var $conexion;
        var $bloque;

        function estaConexion(){
            $this->servidor = trim(fgets(STDIN));
            $this->usuario = trim(fgets(STDIN));
            $this->password = trim(fgets(STDIN));
            $this->baseDatos = trim(fgets(STDIN));
            $this -> conexion = mysqli_connect($this -> servidor,$this -> usuario,$this -> password,$this -> baseDatos);
        }

        function consulta($query){
            $this->bloque = mysqli_query($this->conexion, $query);
        }
    }
?>