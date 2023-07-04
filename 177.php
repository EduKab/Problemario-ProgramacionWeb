<?php
    $numeDatos = (int)trim(fgets(STDIN));
    $datos = new datos();
    $evaluaciones = new evaluaciones();

    for($cont = 0; $cont < $numeDatos; $cont++){
        $correo = $datos->ingrCadena();
        if($evaluaciones->evalLocal($correo[0])){
            echo("USUARIO INCORRECTO\n");
        }
        else{
            if(count($correo) != 2){
                echo("DOMINIO INCORRECTO\n");
            }
            else{
                if($evaluaciones->evalDigiDominio($correo[1])){
                    echo("DOMINIO INCORRECTO\n");
                }else{
                    if($evaluaciones->evalDominio($correo)){
                        echo("DOMINIO INCORRECTO\n");
                    }else{
                        echo($correo[1]."\n");
                    }
                }
            }
        }
    }

    class datos{
        function ingrCadena(){
            $line = trim(fgets(STDIN));
            $correo = explode("@",$line);

            return $correo;
        }
    }

    class evaluaciones{
        function evalLocal($p_local){
            if ($p_local == ""){
                return true;
            }
            else{
                for($cont=0; $cont < strlen($p_local); $cont++){
                    if($cont + 1 < strlen($p_local)){
                        if (substr($p_local, $cont, 2) == ".."){
                            return true;
                        }
                    }
                }
            }
            return $this->evalEspacios($p_local);
        }

        function evalDigiDominio($p_local){
           $digiAceptados = array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z",
                                "0","1","2","3","4","5","6","7","8","9","-","_",".");
            for($cont = 0; $cont < strlen($p_local); $cont++){
                $bandera = true;
                for($cont2 = 0; $cont2 < count($digiAceptados) && $bandera == true; $cont2++){
                    //echo(substr($p_local, $cont, 1)." - ".$digiAceptados[$cont2]);
                    if(substr($p_local, $cont, 1) == $digiAceptados[$cont2]){
                        $bandera = false;
                    }
                }
                if ($bandera == true){
                    return true;
                }
            }
            return false;
        }

        function evalDominio($p_correo){
            if(count($p_correo) > 2){
                return true;
            }else{
                $dominio = $p_correo[1];
                if($dominio == ""){
                    return true;
                }else{
                    for($cont=0; $cont < strlen($dominio); $cont++){
                        if($cont + 1 < strlen($dominio)){
                            if (substr($dominio, $cont, 2) == ".."){
                                return true;
                                }
                        }
                    }
                }
            }
            return $this->evalEspacios($dominio);
        }

        function evalEspacios($p_cadena){
            for($cont = 0; $cont < strlen($p_cadena); $cont++){
                if(substr($p_cadena, $cont, 1) == " "){
                    return true;
                }
            }
            return false;
        }
    }
?>