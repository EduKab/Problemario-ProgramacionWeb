<?php
    $bandera = true;
    $barcode = new barcode();

    do{
        $barcode -> m_obteNumero();
        if ($barcode->numero == "0"){
            $bandera = false;
        }
        else{
            //echo("NUMERO DE DIGITOS: ".strlen($barcode->numero)."\n");
            $barcode -> veriParidad();
        }
    }while($bandera == true);
    
    class barcode{
        var $numero;

        function m_obteNumero(){
            $this -> numero = trim(fgets(STDIN));
        }

        function veriParidad(){
            $primDigito = (int)substr($this->numero,0,1);
            $ultiDigito = (int)substr($this->numero,strlen($this->numero)-1,1);
            //echo("PRIMER DIGITO: ".substr($this->numero,0,1)."\n");
            //echo("ULTIMO DIGITO: ".substr($this->numero,strlen($this->numero)-1,1)."\n");
            if ((strlen($this->numero))%2 == 0){
                $par = new par();
                $par->verificar($this->numero, $primDigito, $ultiDigito);
            }else{
                $impar = new impar();
                $impar->verificar($this->numero, $primDigito, $ultiDigito);
            }
        }
    }

    class par{
        function verificar($p_numero, $p_primDigito, $p_ultiDigito){
            if (strlen($p_numero) < 4){
                echo("INCORRECTO PAR\n");
            }else{
                if($p_primDigito == $p_ultiDigito){
                    echo("INCORRECTO PAR\n");
                }else{
                    $sumaPartIzquierda = 0;
                    $sumaPartDerecha = 0;
                    $numeMitad = strlen($p_numero) / 2;
                    for($cont = 0; $cont < $numeMitad; $cont++){
                        $sumaPartIzquierda += (int)substr($p_numero,$cont,1);
                    }
                    for($cont = $numeMitad; $cont < $numeMitad * 2; $cont++){
                        $sumaPartDerecha += (int)substr($p_numero,$cont,1);
                    }
                    //echo("SUMA IZQUIERDA: ".$sumaPartIzquierda."\n");
                    //echo("SUMA DERECHA: ".$sumaPartDerecha."\n");
                    //echo("BINARIO IZQUIERDA: ".($sumaPartIzquierda)."\n");
                    //echo("BINARIO DERECHA: ".($sumaPartDerecha)."\n");
                    //echo("AND BINARIO: ".($sumaPartIzquierda & $sumaPartDerecha));
                    $resultado = $sumaPartIzquierda & $sumaPartDerecha;
                    //echo("RESULTADO: ".$resultado."\n");
                    if ($resultado % 2 == 0){
                        echo("INCORRECTO PAR\n");
                    }else{
                        echo("CORRECTO PAR\n");
                    }
                }
            }
        }
    }

    class impar{
        function verificar($p_numero, $p_primDigito, $p_ultiDigito){
            if (strlen($p_numero) < 4){
                echo("INCORRECTO IMPAR\n");
            }else{
                if ($p_primDigito == $p_ultiDigito){
                    echo("INCORRECTO IMPAR\n");
                }else{
                    $residuo = ($p_primDigito * $p_ultiDigito) % (abs($p_primDigito - $p_ultiDigito));
                    //echo("RESIDUO: ".$residuo."\n");
                    $numeEnmedio = ((int)(strlen($p_numero) / 2));
                    //echo ("NUMERO DIGITO CENTRO: ".$numeEnmedio."\n");
                    $centDigito = substr($p_numero,$numeEnmedio,1);
                    //echo("CENTRO DIGITO: ".$centDigito."\n");
                    if ($residuo == $centDigito){
                        echo("CORRECTO IMPAR\n");
                    }else{
                        echo("INCORRECTO IMPAR\n");
                    }
                }
            }
        }
    }
?>