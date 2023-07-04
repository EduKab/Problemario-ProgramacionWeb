<?php
    $baseDatos = new baseDatos();
    $baseDatos->conecta();

    $procesos = new procesos();
    //G
    $procesos->persGano($baseDatos->consulta("SELECT CONCAT(u.Nombre, ' ', u.Apellidos) AS nombre, p.Nombre AS partido, COUNT(v.IdVoto) AS nume_votos FROM BD_Elecciones_Voto v JOIN BD_Elecciones_Candidato c ON v.IdPartido = c.IdPartido JOIN BD_Elecciones_Partido p ON v.IdPartido = p.IdPartido JOIN Usuarios u ON c.IdPersona = u.Usuario GROUP BY v.IdPartido ORDER BY nume_votos DESC LIMIT 2;"));
    //M
    $procesos->partNoVotos($baseDatos->consulta("SELECT Nombre FROM BD_Elecciones_Partido WHERE IdPartido NOT IN (SELECT v.IdPartido FROM BD_Elecciones_Voto v JOIN BD_Elecciones_Partido p ON v.IdPartido = p.IdPartido GROUP BY v.IdPartido);"));
    //M
    $procesos->partNoCandidatos($baseDatos->consulta("SELECT Nombre FROM BD_Elecciones_Partido WHERE IdPartido NOT IN (SELECT IdPartido FROM BD_Elecciones_Candidato GROUP BY IdPartido);"));
    //G
    $procesos->listDistVotos($baseDatos->consulta("SELECT d.Nombre, COUNT(v.IdDistrito) AS nume_votos FROM BD_Elecciones_Voto v JOIN BD_Elecciones_Distrito d ON v.IdDistrito = d.IdDistrito GROUP BY d.IdDistrito ORDER BY d.Nombre ASC; "));
    //G
    $procesos->votoSufragados($baseDatos);

    class baseDatos{
        var $conexion;

        function conecta(){
            fscanf(STDIN, "%s", $server);
            fscanf(STDIN, "%s", $user);
            fscanf(STDIN, "%s", $password);
            fscanf(STDIN, "%s", $database);
            $this->conexion = mysqli_connect($server, $user, $password, $database);
        }

        function consulta($p_query){
            $bloque = mysqli_query($this->conexion, $p_query);
            return $bloque;
        }
    }

    class procesos{
        function persGano($p_bloque){
            $registro = mysqli_fetch_array($p_bloque);
            $nombre = $registro['nombre'];
            $partido = $registro['partido'];
            $votos = $registro['nume_votos'];

            $registro = mysqli_fetch_array($p_bloque);
            $nombre2 = $registro['nombre'];
            $votos -= $registro['nume_votos'];

            $html='<b>'.$nombre.'</b>'.' de '.$partido.' gano con '.$votos.' votos a '.$nombre2.".";
            echo($html."\n");
        }

        function partNoVotos($p_bloque){
            $html = "";
            for($cont = 0; $cont < mysqli_num_rows($p_bloque); $cont++){
                $registro = mysqli_fetch_array($p_bloque);
                if($cont+1 < mysqli_num_rows($p_bloque)){
                    $html.= $registro['Nombre'].", ";
                }else{
                    $html.= $registro['Nombre'];
                }
            }
            echo($html." Sin Votos.\n");
        }

        function partNoCandidatos($p_bloque){
            $html = "<i>";
            for($cont = 0; $cont < mysqli_num_rows($p_bloque); $cont++){
                $registro = mysqli_fetch_array($p_bloque);
                if($cont+1 < mysqli_num_rows($p_bloque)){
                    $html.= $registro['Nombre'].", ";
                }else{
                    $html.= $registro['Nombre'];
                }
            }
            echo($html." No tenian candidatos.</i>\n");
        }

        function listDistVotos($p_bloque){
            $html = "";
            for($cont=0; $cont < mysqli_num_rows($p_bloque); $cont++){
                $registro = mysqli_fetch_array($p_bloque);
                if($cont+1 < mysqli_num_rows($p_bloque)){
                    $html.="<b>".$registro['Nombre']."</b> <u>".$registro['nume_votos']."</u> : ";
                }else{
                    $html.="<b>".$registro['Nombre']."</b> <u>".$registro['nume_votos']."</u>";
                }
            }
            echo($html."\n");
        }

        function votoSufragados($p_baseDatos){
            $html = "";
            $bloque = $p_baseDatos->consulta("SELECT Nombre, Rango_Papeleta FROM BD_Elecciones_Distrito;");

            for($cont=0; $cont < mysqli_num_rows($bloque); $cont++){
                $registro = mysqli_fetch_array($bloque);

                $rangos = explode("-", $registro['Rango_Papeleta']);
                //echo($rangos[0]."-".$rangos[1]."\n");
                $bloqueAux = $p_baseDatos->consulta("SELECT d.Nombre, COUNT(v.IdPapeleta) as nume_sufragados 
                                                        FROM BD_Elecciones_Voto v JOIN BD_Elecciones_Distrito d ON v.IdDistrito = d.IdDistrito 
                                                        WHERE d.Nombre = '".$registro['Nombre']."' AND (v.IdPapeleta < '".$rangos[0]."' OR v.IdPapeleta > '".$rangos[1]."');");
                $registro = mysqli_fetch_array($bloqueAux);
                if($cont+1 < mysqli_num_rows($bloque)){
                    if($cont % 2 == 0){
                        $html .= "<b>".$registro['Nombre']." ".$registro['nume_sufragados']."</b> : ";
                    }
                    else{
                        $html .= "<i>".$registro['Nombre']." ".$registro['nume_sufragados']."</i> : ";
                    }
                }else{
                    if($cont % 2 == 0){
                        $html .= "<b>".$registro['Nombre']." ".$registro['nume_sufragados']."</b>";
                    }
                    else{
                        $html .= "<i>".$registro['Nombre']." ".$registro['nume_sufragados']."</i>";
                    }
                }
            }
            echo ($html."\n");
        }
    }
?>