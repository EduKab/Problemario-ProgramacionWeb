<?php
    $oBD = new baseDatos();
    $oBD -> conecta();

    $procesos = new procesos();
    $procesos -> mostTotaAdeudos($oBD -> consulta("SELECT SUM(Monto) AS monto FROM BD_PagoServ_Facturas WHERE id_FormaPago = 4 AND fecha_Vencimiento <= '2019-01-20';"));
    $procesos -> mostAdeudos($oBD);

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

    class procesos{

        function mostTotaAdeudos($p_bloque){
            $totaAdeudo = mysqli_fetch_array($p_bloque);
            echo("Total de Adeudos: $".number_format(round($totaAdeudo['monto'], 2), 2)."\n");
        }

        function mostAdeudos($p_oBD){
            $bloque = $p_oBD -> consulta("SELECT id_Cliente FROM BD_PagoServ_Facturas f JOIN Usuarios u ON f.id_Cliente = u.Usuario WHERE id_FormaPago = 4 AND fecha_Vencimiento <= '2019-01-20' GROUP BY id_Cliente ORDER BY u.Apellidos;");
            for ($cont = 0; $cont < mysqli_num_rows($bloque); $cont++){
                $registro = mysqli_fetch_array($bloque);
                
                $bloqueAux = $p_oBD -> consulta("SELECT SUM(f.Monto) AS monto_total, CONCAT(u.Apellidos, ' ', u.Nombre) AS nombre FROM BD_PagoServ_Facturas f JOIN Usuarios u ON f.id_Cliente = u.Usuario JOIN BD_PagoServ_Servicios s ON f.id_Servicio = s.id WHERE id_FormaPago = 4 AND id_Cliente ='".$registro['id_Cliente']."' AND fecha_Vencimiento <= '2019-01-20';");
                $RegistroAux = mysqli_fetch_array($bloqueAux);
                echo("Cliente: ".$RegistroAux['nombre']." Total de Adeudo: $".number_format(round($RegistroAux['monto_total'],2),2)."\n");
                
                $bloqueAux = $p_oBD -> consulta("SELECT f.id_Cliente, f.monto, f.fecha_Vencimiento, s.Nombre FROM BD_PagoServ_Facturas f JOIN Usuarios u ON f.id_Cliente = u.Usuario JOIN BD_PagoServ_Servicios s ON f.id_Servicio = s.id WHERE id_FormaPago = 4 AND id_Cliente = '".$registro['id_Cliente']."' AND fecha_Vencimiento <= '2019-01-20' ORDER BY fecha_Vencimiento ASC;");
                for($cont2 = 0; $cont2 < mysqli_num_rows($bloqueAux); $cont2++){
                    $registroAux = mysqli_fetch_array($bloqueAux);;
                    echo("Servicio: ".$registroAux['Nombre']." Total: $".number_format(round($registroAux['monto'],2),2)." Fecha Venc.: ".$registroAux['fecha_Vencimiento']."\n");
                }
            }
        }
    }
?>