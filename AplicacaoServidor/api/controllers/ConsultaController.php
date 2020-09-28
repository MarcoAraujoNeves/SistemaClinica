<?php
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: X-Requested-With,content-type");
    header("Access-Control-Allow-Methods:GET,POST");          
    header("Access-Control-Request-Method:POST");
    header("Content-type: application/json");
    
    include_once("../../models/Consulta.php");

    function executarAcao($acao, $requestHeaders, $requestBody){
        switch($acao) {
            case "INDEX":
                $consulta = new Consulta();
                $consulta->setDBUsuario($requestHeaders["db_user"]);
                $consulta->setDBSenha($requestHeaders["db_password"]);
                return $consulta->listaJSON();
                break;
            case "CREATE":
                $consulta = new Consulta();
                $consulta->setDBUsuario($requestHeaders["db_user"]);
                $consulta->setDBSenha($requestHeaders["db_password"]);
                $consulta->setCodPaciente($requestBody["paciente"]);
                $consulta->setCodEmpresa($requestBody["empresa"]);
                $consulta->setCodTipoConsulta($requestBody["tipo_consulta"]);
                $consulta->setStatus($requestBody["status"]);
                $consulta->setValidade($requestBody["validade"]);
                $mysql_datetime = date('Y-m-d H:i:s', strtotime($requestBody["dataHora"]));
                $consulta->setDataHora($mysql_datetime);
                $consulta->create();
                return;
            case "READ":
                $consulta = new Consulta();
                $consulta->setDBUsuario($requestHeaders["db_user"]);
                $consulta->setDBSenha($requestHeaders["db_password"]);
                $consulta->setCodConsulta($requestHeaders["_id"]);
                return $consulta->read();
                break;
            case "UPDATE":
                $consulta = new Consulta();
                $consulta->setDBUsuario($requestHeaders["db_user"]);
                $consulta->setDBSenha($requestHeaders["db_password"]);
                $consulta->setCodConsulta($requestBody["_id"]);
                $consulta->setCodTipoConsulta($requestBody["tipo_consulta"]);
                $consulta->setStatus($requestBody["status"]);
                $consulta->setValidade($requestBody["validade"]);

                $mysql_datetime = date('Y-m-d H:i:s', strtotime($requestBody["dataHora"]));
                $mysql_datetime_inicio = date('Y-m-d H:i:s', strtotime($requestBody["inicio"]));
                $mysql_datetime_termino = date('Y-m-d H:i:s', strtotime($requestBody["termino"]));
                
                $consulta->setDataHora($mysql_datetime);
                $consulta->setInicio($mysql_datetime_inicio);
                $consulta->setTermino($mysql_datetime_termino);
                $consulta->update();
                break;
            case "DELETE":
                $consulta = new Consulta();
                $consulta->setDBUsuario($requestHeaders["db_user"]);
                $consulta->setDBSenha($requestHeaders["db_password"]);
                $consulta->setCodConsulta($requestBody["_id"]);
                return $consulta->delete();
            case "CHANGE_STATUS":
                $consulta = new Consulta();
                $consulta->setDBUsuario($requestHeaders["db_user"]);
                $consulta->setDBSenha($requestHeaders["db_password"]);
                $consulta->setCodConsulta($requestBody["_id"]);
                $consulta->setStatus($requestBody["status"]);
                return $consulta->updateStatus();
            default:
                http_response_code(400);
                echo 'Erro: Opção de Ação inválida!';
                return;
        }
    }
?>