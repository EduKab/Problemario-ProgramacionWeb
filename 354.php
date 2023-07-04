<?php
    $baseDatos = new baseDatos();
    $baseDatos -> conecta();
    
    $procesos = new procesos();
    $procesos -> clieDebeMas($baseDatos->consulta("SELECT CONCAT(u.Nombre, ' ', u.Apellidos) AS nombre, SUM(Monto - Pagos) AS deuda 
                                                    FROM Creditos c JOIN Usuarios u ON c.IdUsuario = u.Usuario GROUP BY c.IdUsuario 
                                                    ORDER BY deuda DESC LIMIT 1;"));
    $procesos->clieLiquCreditos($baseDatos->consulta("SELECT CONCAT(u.Nombre, ' ', u.Apellidos) AS nombre, SUM(Pagos) AS monto_pagos 
                                                    FROM Creditos c JOIN Usuarios u ON c.IdUsuario = u.Usuario WHERE c.Monto = c.Pagos 
                                                    GROUP BY c.IdUsuario ORDER BY nombre ASC;"));
    $procesos->bancMasCreditos($baseDatos->consulta("SELECT b.Banco, COUNT(IdBanco) AS numero_creditos 
                                                        FROM Creditos c JOIN Bancos b ON c.IdBanco = b.Id 
                                                        GROUP BY b.Id ORDER BY numero_creditos DESC LIMIT 1;"));
    $procesos->bancMasCreditos($baseDatos->consulta("SELECT b.Banco, SUM(Monto) AS dinero_prestado 
                                                        FROM Creditos c JOIN Bancos b ON c.IdBanco = b.Id 
                                                        GROUP BY b.Id ORDER BY dinero_prestado DESC LIMIT 1;"));
    $procesos->bancSinCreditos($baseDatos->consulta("SELECT Banco 
                                                        FROM Bancos 
                                                        WHERE Banco NOT IN (SELECT b.Banco 
                                                                                FROM Creditos c JOIN Bancos b ON c.IdBanco = b.Id) ORDER BY Banco;"));
    class baseDatos{
        var $conexion;

        function conecta(){
            fscanf(STDIN, "%s", $server);
            fscanf(STDIN, "%s", $user);
            fscanf(STDIN, "%s", $password);
            fscanf(STDIN, "%s", $database);
            $this->conexion = mysqli_connect($server,$user,$password,$database);
        }

        function consulta($p_query){
            $bloque = mysqli_query($this->conexion, $p_query);
            return $bloque;
        }
    }

    class procesos{
        function clieDebeMas($p_bloque){
            $registro = mysqli_fetch_array($p_bloque);
            echo($registro['nombre']." $".number_format($registro['deuda'],0,"",",")."\n");
        }

        function clieLiquCreditos($p_bloque){
            $cadena = "";
            //echo(mysqli_num_rows($p_bloque));
            for($cont = 0; $cont < mysqli_num_rows($p_bloque); $cont++){
                $registro = mysqli_fetch_array($p_bloque);
                if ($cont+1 < mysqli_num_rows($p_bloque)){
                    $cadena .= $registro['nombre']." $".number_format($registro['monto_pagos'],0,"",",").", ";
                }else{
                    $cadena .= $registro['nombre']." $".number_format($registro['monto_pagos'],0,"",",");
                }   
            }
            echo($cadena."\n");
        }

        function bancMasCreditos($p_bloque){
            $registro = mysqli_fetch_array($p_bloque);
            echo($registro['Banco']."\n");
        }

        function bancMasDinePrestado($p_bloque){
            $registro = mysqli_fetch_array($p_bloque);
            echo($registro['Banco']."\n");
        }

        function bancSinCreditos($p_bloque){
            $cadena = "";
            for($cont = 0; $cont < mysqli_num_rows($p_bloque); $cont++){
                $registro = mysqli_fetch_array($p_bloque);
                if ($cont+1 < mysqli_num_rows($p_bloque)){
                    $cadena .= $registro['Banco'].", ";
                }else{
                    $cadena .= $registro['Banco'];
                }   
            }
            echo($cadena."\n");
        }
    }
?>