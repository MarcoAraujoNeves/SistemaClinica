<?php

    class Estado {
        private $codEstado;
        private $codTipo;
        private $codConsulta;
        private $termino;

        private $dbUsuario;
        private $dbSenha;

        //SETTERS
        public function setCodEstado($codigo){
            $this->codEstado = $codigo;
        }
        public function setCodTipo($codigo){
            $this->codTipo = $codigo;
        }
        public function setCodConsulta($codigo){
            $this->codConsulta = $codigo;
        }
        public function setTermino($data){
            $this->termino = $data;
        }
        public function setDBUsuario($usuario){
            $this->dbUsuario = $usuario;
        }
        public function setDBSenha($senha){
            $this->dbSenha = $senha;
        }

        //GETTERS
        public function getCodEstado(){
            return $this->codEstado;
        }
        public function getCodTipo(){
            return $this->codTipo;
        }
        public function getCodConsulta(){
            return $this->codConsulta;
        }
        public function getTermino(){
            return $this->termino;
        }

        //CRUD
        public function lista(){
            try {

                include_once('../../database.class.php');
                include_once('../../utils/ConsultaUtil.php');
                include_once('../../utils/EstadoUtil.php');
                
                $db = new database();
                $db->setUsuario($this->dbUsuario);
                $db->setSenha($this->dbSenha);

                $conexao = $db->conecta_mysql();

                $sqlLista = "SELECT E.codEstado, E.inicio, E.termino, E.ativo AS ativo,
                                    T.codTipo, T.nome, T.descricao,
                                    C.codConsulta, C.dataHora, C.termino AS encerramento_consulta,
                                    P.nome AS paciente, Em.nome AS empresa, 
                                    TC.codTipoConsulta, TC.nome AS tipo_consulta
                             FROM estado E
                                INNER JOIN tipo_estado T 
                                ON E.codTipo = T.codTipo 
                                INNER JOIN consulta C 
                                ON E.codConsulta = C.codConsulta
                                INNER JOIN paciente P 
                                ON P.codPaciente = C.codPaciente
                                INNER JOIN empresa Em 
                                ON Em.codEmpresa = C.codEmpresa
                                INNER JOIN tipo_consulta TC 
                                ON C.codTipoConsulta = TC.codTipoConsulta
                                WHERE E.ativo = '1' AND DATE(C.dataHora) = CURDATE()
                             ORDER BY C.codConsulta ASC";
                $conexao->exec('SET NAMES utf8');
                $stmtLista = $conexao->prepare($sqlLista);
                $stmtLista->execute();

                $lista = $stmtLista->fetchALL(PDO::FETCH_ASSOC);
                /*$response = Array();
                $keys = array_keys($lista);
                $size = count($lista);
                for ($i = 0; $i < $size; $i++) {
                    $key = $keys[$i];
                    if($lista[$key]["codEstado"] == null) continue;
                    $aux->codConsulta = $lista[$key]["codConsulta"];
                    $aux->dataHora = $lista[$key]["dataHora"];
                    $aux->encerramento_consulta = $lista[$key]["encerramento_consulta"];
                    $aux->paciente = $lista[$key]["paciente"];
                    $aux->empresa = $lista[$key]["empresa"];
                    $aux->codTipoConsulta = $lista[$key]["codTipoConsulta"];
                    $aux->tipo_consulta = $lista[$key]["tipo_consulta"];
                    $estado->codEstado = $lista[$key]["codEstado"];
                    $estado->inicio = $lista[$key]["inicio"];
                    $estado->termino = $lista[$key]["termino"];
                    $estado->codTipo = $lista[$key]["codTipo"];
                    $estado->nome = $lista[$key]["nome"];
                    $estado->descricao = $lista[$key]["descricao"];
                    $estado->ativo = $lista[$key]["ativo"];
                    $aux->estados = array(clone $estado);
                    unset($lista[$key]);
                    foreach($lista as $key_aux => $row_aux) {
                        if(
                            $aux->codConsulta == $row_aux["codConsulta"] && 
                            !in_array($row_aux["codEstado"], $aux->estados, true)
                            ) {
                                $aux_estado->codEstado = $row_aux["codEstado"];
                                $aux_estado->inicio = $row_aux["inicio"];
                                $aux_estado->termino = $row_aux["termino"];
                                $aux_estado->codTipo = $row_aux["codTipo"];
                                $aux_estado->nome = $row_aux["nome"];
                                $aux_estado->descricao = $row_aux["descricao"];
                                $aux_estado->ativo = $row_aux["ativo"];
                                
                                array_push($aux->estados, clone $aux_estado);
                                unset($lista[$key_aux]);
                        }
                    }
                    array_push($response, clone $aux);
                }
                return $response;*/
                foreach($lista as $row) {
                    $consulta = isset($consultas[$row['codConsulta']]) ? $consultas[$row['codConsulta']] : NULL;
            
                    if(!$consulta) {
                        $consulta = new ConsultaUtil($row['codConsulta'], $row['dataHora'], $row['encerramento_consulta'], $row['paciente'], $row['empresa'], $row['codTipoConsulta'], $row['tipo_consulta'],$row['status'],$row['codEmpresa']);
                        
                        $consultas[$row['codConsulta']] = $consulta;
                    }
            
                    $novo_estado = new EstadoUtil($row['codEstado'], $row['inicio'], $row['termino'], $row['ativo'], $row['codTipo'], $row['nome'], $row['descricao']);
                    $consulta->addEstado($novo_estado);
                }
            
                echo(json_encode($consultas, JSON_FORCE_OBJECT));


            } catch (PDOException $e) {
                http_response_code(500);
                $erro = $e->getMessage();
                echo(json_encode(array('error' => "$erro"), JSON_FORCE_OBJECT));
            }
        }

        public function listaJSON(){
            echo json_encode($this->lista());
        }
        public function listaIniciados(){
            try {

                include_once('../../database.class.php');
                include_once('../../utils/ConsultaUtil.php');
                include_once('../../utils/EstadoUtil.php');
                
                $db = new database();
                $db->setUsuario($this->dbUsuario);
                $db->setSenha($this->dbSenha);

                $conexao = $db->conecta_mysql();

                $sqlLista = "SELECT E.codEstado, E.inicio, E.termino, E.ativo AS ativo,
                                    T.codTipo, T.nome, T.descricao,
                                    C.codConsulta, C.dataHora, C.termino AS encerramento_consulta,
                                    P.nome AS paciente, Em.nome AS empresa, 
                                    TC.codTipoConsulta, TC.nome AS tipo_consulta
                             FROM estado E
                                INNER JOIN tipo_estado T 
                                ON E.codTipo = T.codTipo 
                                INNER JOIN consulta C 
                                ON E.codConsulta = C.codConsulta
                                INNER JOIN paciente P 
                                ON P.codPaciente = C.codPaciente
                                INNER JOIN empresa Em 
                                ON Em.codEmpresa = C.codEmpresa
                                INNER JOIN tipo_consulta TC 
                                ON C.codTipoConsulta = TC.codTipoConsulta
                                WHERE T.codTipo != '1' AND E.ativo = '1'
                             ORDER BY C.codConsulta ASC";
                $conexao->exec('SET NAMES utf8');
                $stmtLista = $conexao->prepare($sqlLista);
                $stmtLista->execute();

                $lista = $stmtLista->fetchALL(PDO::FETCH_ASSOC);
                /*$response = Array();
                $keys = array_keys($lista);
                $size = count($lista);
                for ($i = 0; $i < $size; $i++) {
                    $key = $keys[$i];
                    if($lista[$key]["codEstado"] == null) continue;
                    $aux->codConsulta = $lista[$key]["codConsulta"];
                    $aux->dataHora = $lista[$key]["dataHora"];
                    $aux->encerramento_consulta = $lista[$key]["encerramento_consulta"];
                    $aux->paciente = $lista[$key]["paciente"];
                    $aux->empresa = $lista[$key]["empresa"];
                    $aux->codTipoConsulta = $lista[$key]["codTipoConsulta"];
                    $aux->tipo_consulta = $lista[$key]["tipo_consulta"];
                    $estado->codEstado = $lista[$key]["codEstado"];
                    $estado->inicio = $lista[$key]["inicio"];
                    $estado->termino = $lista[$key]["termino"];
                    $estado->codTipo = $lista[$key]["codTipo"];
                    $estado->nome = $lista[$key]["nome"];
                    $estado->descricao = $lista[$key]["descricao"];
                    $estado->ativo = $lista[$key]["ativo"];
                    $aux->estados = array(clone $estado);
                    unset($lista[$key]);
                    foreach($lista as $key_aux => $row_aux) {
                        if(
                            $aux->codConsulta == $row_aux["codConsulta"] && 
                            !in_array($row_aux["codEstado"], $aux->estados, true)
                            ) {
                                $aux_estado->codEstado = $row_aux["codEstado"];
                                $aux_estado->inicio = $row_aux["inicio"];
                                $aux_estado->termino = $row_aux["termino"];
                                $aux_estado->codTipo = $row_aux["codTipo"];
                                $aux_estado->nome = $row_aux["nome"];
                                $aux_estado->descricao = $row_aux["descricao"];
                                $aux_estado->ativo = $row_aux["ativo"];
                                
                                array_push($aux->estados, clone $aux_estado);
                                unset($lista[$key_aux]);
                        }
                    }
                    array_push($response, clone $aux);
                }
                return $response;*/
                foreach($lista as $row) {
                    $consulta = isset($consultas[$row['codConsulta']]) ? $consultas[$row['codConsulta']] : NULL;
            
                    if(!$consulta) {
                        $consulta = new ConsultaUtil($row['codConsulta'], $row['dataHora'], $row['encerramento_consulta'], $row['paciente'], $row['empresa'], $row['codTipoConsulta'], $row['tipo_consulta'],$row['status'],$row['codEmpresa']);
                        
                        $consultas[$row['codConsulta']] = $consulta;
                    }
            
                    $novo_estado = new EstadoUtil($row['codEstado'], $row['inicio'], $row['termino'], $row['ativo'], $row['codTipo'], $row['nome'], $row['descricao']);
                    $consulta->addEstado($novo_estado);
                }
            
                echo(json_encode($consultas, JSON_FORCE_OBJECT));


            } catch (PDOException $e) {
                http_response_code(500);
                $erro = $e->getMessage();
                echo(json_encode(array('error' => "$erro"), JSON_FORCE_OBJECT));
            }
        }
        public function listaJSONIniciados(){
            echo json_encode($this->listaIniciados());
        }
        public function create(){

            try {

                include('../../database.class.php');

                $db = new database();
                $db->setUsuario($this->dbUsuario);
                $db->setSenha($this->dbSenha);

                $conexao = $db->conecta_mysql();

                $sqlCreate = "INSERT INTO estado(codTipo,codConsulta,termino,ativo,inicio) VALUES(?,?,?,1,NOW())";
                $conexao->exec('SET NAMES utf8');
                $stmtCreate = $conexao->prepare($sqlCreate);
                $stmtCreate->bindParam(1,$this->codTipo);
                $stmtCreate->bindParam(2,$this->codConsulta);
                $stmtCreate->bindParam(3,$this->termino);
                $result = $stmtCreate->execute();
                
                if($result) {
                    http_response_code(201);
                    $id = $conexao->lastInsertId();
                    echo(json_encode(array('codEstado' => "$id" ), JSON_FORCE_OBJECT));
                } else {
                    http_response_code(400);
                    echo(json_encode(array('error' => "Ocorreu um erro ao cadastrar o registro, verifique os valores."), JSON_FORCE_OBJECT));
                }

            } catch (PDOException $e) {
                http_response_code(500);
                $erro = $e->getMessage();
                echo(json_encode(array('error' => "$erro"), JSON_FORCE_OBJECT));
            }
        }
        public function read(){

            try {

                include_once('../../database.class.php');

                $db = new database();
                $db->setUsuario($this->dbUsuario);
                $db->setSenha($this->dbSenha);

                $conexao = $db->conecta_mysql();

                $sqlRead = "SELECT  E.codEstado, E.inicio, E.termino, E.ativo, 
                                    T.codTipo, T.nome, T.descricao
                            FROM estado E 
                                INNER JOIN tipo_estado T 
                                ON E.codTipo = T.codTipo
                            WHERE E.codConsulta = ?";
                $conexao->exec('SET NAMES utf8');
                $stmtRead = $conexao->prepare($sqlRead);
                $stmtRead->bindParam(1,$this->codConsulta);
                $stmtRead->execute();

                $estado = $stmtRead->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($estado);

            } catch (PDOException $e) {
                http_response_code(500);
                $erro = $e->getMessage();
                echo(json_encode(array('error' => "$erro"), JSON_FORCE_OBJECT));
            }
        }
        public function update(){

            try {

                include('../../database.class.php');

                $db = new database();
                $db->setUsuario($this->dbUsuario);
                $db->setSenha($this->dbSenha);

                $conexao = $db->conecta_mysql();

                $sqlUpdate = "UPDATE estado SET termino = NOW(), ativo = 0 WHERE codEstado = ?";
                $conexao->exec('SET NAMES utf8');
                $stmtUpdate = $conexao->prepare($sqlUpdate);
                $stmtUpdate->bindParam(1,$this->codEstado);
                $result = $stmtUpdate->execute();

                if($result) {
                    http_response_code(200);
                } else {
                    http_response_code(400);
                    echo(json_encode(array('error' => "Ocorreu um erro ao atualizar o registro, verifique os valores."), JSON_FORCE_OBJECT));
                }


            } catch (PDOException $e){
                http_response_code(500);
                $erro = $e->getMessage();
                echo(json_encode(array('error' => "$erro"), JSON_FORCE_OBJECT));
            }
        }
    }