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

                $db = new database();
                $db->setUsuario($this->dbUsuario);
                $db->setSenha($this->dbSenha);

                $conexao = $db->conecta_mysql();

                $sqlLista = "SELECT E.codEstado, E.inicio, E.termino,
                                    T.codTipo, T.nome, T.descricao,
                                    C.codConsulta, C.dataHora, C.termino AS encerramento_consulta,
                                    TC.codTipoConsulta, TC.nome AS tipo_consulta
                             FROM estado E
                                INNER JOIN tipo_estado T 
                                ON E.codTipo = T.codTipo 
                                INNER JOIN consulta C 
                                ON E.codConsulta = C.codConsulta
                                INNER JOIN tipo_consulta TC 
                                ON C.codTipoConsulta = TC.codTipoConsulta
                             ORDER BY C.codConsulta ASC";
                $conexao->exec('SET NAMES utf8');
                $stmtLista = $conexao->prepare($sqlLista);
                $stmtLista->execute();

                $lista = $stmtLista->fetchALL(PDO::FETCH_ASSOC);
                return $lista;
            } catch (PDOException $e) {
                http_response_code(500);
                $erro = $e->getMessage();
                echo(json_encode(array('error' => "$erro"), JSON_FORCE_OBJECT));
            }
        }

        public function listaJSON(){
            echo json_encode($this->lista());
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
?>