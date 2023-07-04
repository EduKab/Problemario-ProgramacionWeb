<?php
    $oBD = new baseDatos();
    $oBD -> conecta();
    
    $procesos = new procesos();
    $procesos -> obteReportes($oBD -> consulta("SELECT * FROM (SELECT s.Nombre, CASE 
    WHEN f.id_FormaPago = 4 THEN 0 
    WHEN f.id_FormaPago IS NULL THEN 0 
    WHEN f.fecha_Pago >= f.fecha_Vencimiento THEN 0 
    ELSE f.Monto 
END AS monto_total 
FROM BD_PagoServ_Servicios s LEFT JOIN BD_PagoServ_Facturas f ON s.id = f.id_Servicio GROUP BY s.Nombre,f.id ORDER BY (SUBSTRING(s.Nombre, 1, 1)) ASC, monto_total DESC) BD_PagoServ_Servicios GROUP BY Nombre ORDER BY (SUBSTRING(Nombre, 1, 1)) ASC, monto_total DESC;"), $oBD);

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

        function obteReportes($p_bloque, $p_oBD){
            for ($cont = 0; $cont < mysqli_num_rows($p_bloque); $cont++){
                $servicio = mysqli_fetch_array($p_bloque);
                $bloqueAux = $p_oBD -> consulta("SELECT COUNT(f.id) as numero_registros, SUM(f.Monto) as monto_total FROM BD_PagoServ_Servicios s JOIN BD_PagoServ_Facturas f ON s.id = f.id_Servicio WHERE s.Nombre = '".$servicio['Nombre']."' AND f.id_FormaPago != 4 AND f.fecha_Pago < f.fecha_Vencimiento;");
                $registroAux = mysqli_fetch_array($bloqueAux);

                if($registroAux['monto_total'] == NULL){
                    $monto = "0.00";
                }
                else{
                    $monto = number_format(round($registroAux['monto_total'], 2), 2,".","");
                }
                echo($servicio['Nombre'].":".$registroAux['numero_registros'].":$".$monto."\n");
            }
        }
    }
?>