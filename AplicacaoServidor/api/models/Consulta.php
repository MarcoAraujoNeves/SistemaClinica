<?php
    class Consulta {
        private $codConsulta;
        private $codPaciente;
        private $codEmpresa;
        private $codTipoConsulta;
        private $status;
        private $validade;
        private $dataHora;
        private $inicio;
        private $termino;

        private $dbUsuario;
        private $dbSenha;

        //GETS
        public function getCodConsulta(){
            return $this->codConsulta;
        }
        public function getCodPaciente(){
            return $this->codPaciente;
        }
        public function getCodEmpresa(){
            return $this->codEmpresa;
        }
        public function getCodTipoConsulta(){
            return $this->codTipoConsulta;
        }
        public function getStatus(){
            return $this->status;
        }
        public function getValidade(){
            return $this->validade;
        }
        public function getDataHora(){
            return $this->dataHora;
        }
        public function getInicio(){
            return $this->inicio;
        }
        public function getTermino(){
            return $this->termino;
        }
        //SETS
        public function setCodConsulta($codigo){
            $this->codConsulta = $codigo;
        }
        public function setCodPaciente($codigo){
            $this->codPaciente = $codigo;
        }
        public function setCodEmpresa($codigo){
            $this->codEmpresa = $codigo;
        }
        public function setCodTipoConsulta($codigo){
            $this->codTipoConsulta = $codigo;
        }
        public function setStatus($status){
            $this->status = $status;
        }
        public function setValidade($validade){
            $this->validade = $validade;
        }
        public function setDataHora($dataHora){
            $this->dataHora = $dataHora;
        }
        public function setInicio($inicio){
            $this->inicio = $inicio;
        }
        public function setTermino($termino){
            $this->termino = $termino;
        }
        public function setDBUsuario($usuario){
            $this->dbUsuario = $usuario ;
        }
        public function setDBSenha($senha){
            $this->dbSenha = $senha ;
        }

        //CRUD
        public function lista(){
            try {

                include_once('../../database.class.php');

                $db = new database();
                $db->setUsuario($this->dbUsuario);
                $db->setSenha($this->dbSenha);

                $conexao = $db->conecta_mysql();

                $sqlLista = "SELECT C.codConsulta, C.dataHora, C.inicio, C.termino, C.validade, C.status, 
                                    TC.codTipoConsulta, TC.nome AS tipo_consulta,
                                    P.codPaciente AS paciente, P.nome AS nome_paciente, P.cpf, P.rg, P.nascimento,
                                    E.codEmpresa AS empresa, E.nome AS nome_empresa, E.cnpj, E.tipoPgto
                             FROM consulta C
                                INNER JOIN tipo_consulta TC
                                ON C.codTipoConsulta = TC.codTipoConsulta
                                INNER JOIN paciente P
                                ON C.codPaciente = P.codPaciente
                                INNER JOIN empresa E
                                ON C.codEmpresa = E.codEmpresa
                            ORDER BY C.dataHora ASC";
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

                $sqlCreate = "INSERT INTO consulta(codPaciente,codEmpresa,codTipoConsulta,status,validade,dataHora,inicio,termino) VALUES(?,?,?,?,?,?,NULL,NULL);";
                $conexao->exec('SET NAMES utf8');
                $stmtCreate = $conexao->prepare($sqlCreate);
                $stmtCreate->bindParam(1,$this->codPaciente);
                $stmtCreate->bindParam(2,$this->codEmpresa);
                $stmtCreate->bindParam(3,$this->codTipoConsulta);
                $stmtCreate->bindParam(4,$this->status);
                $stmtCreate->bindParam(5,$this->validade);
                $stmtCreate->bindParam(6,$this->dataHora);
                $result = $stmtCreate->execute();
                
                if($result) {
                    http_response_code(201);
                    $id = $conexao->lastInsertId();
                    echo(json_encode(array('codConsulta' => "$id" ), JSON_FORCE_OBJECT));
                } else {
                    http_response_code(400);
                    echo(json_encode(array('error' => "Ocorreu um erro ao criar o registro, verifique os valores."), JSON_FORCE_OBJECT));
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

                $sqlRead = "SELECT C.codConsulta, C.dataHora, C.inicio, C.termino, C.validade, C.status, 
                                    TC.codTipoConsulta, TC.nome AS tipo_consulta,
                                    P.codPaciente AS paciente, P.nome AS nome_paciente, P.cpf, P.rg, P.nascimento,
                                    E.codEmpresa AS empresa, E.nome AS nome_empresa, E.cnpj, E.tipoPgto
                            FROM consulta C
                                INNER JOIN tipo_consulta TC
                                ON C.codTipoConsulta = TC.codTipoConsulta
                                INNER JOIN paciente P
                                ON C.codPaciente = P.codPaciente
                                INNER JOIN empresa E
                                ON C.codEmpresa = E.codEmpresa
                            WHERE C.codConsulta = ?";
                $conexao->exec('SET NAMES utf8');
                $stmtRead = $conexao->prepare($sqlRead);
                $stmtRead->bindParam(1,$this->codConsulta);
                $stmtRead->execute();
                
                $dados = $stmtRead->fetch(PDO::FETCH_ASSOC);
                echo json_encode($dados);

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

                $sqlUpdate = "UPDATE consulta 
                              SET codTipoConsulta = ?, status = ?, validade = ?, inicio = ?, termino = ?, dataHora=?
                              WHERE codConsulta= ?";
                $conexao->exec('SET NAMES utf8');
                $stmtUpdate = $conexao->prepare($sqlUpdate);
                $stmtUpdate->bindParam(1,$this->codTipoConsulta);
                $stmtUpdate->bindParam(2,$this->status);
                $stmtUpdate->bindParam(3,$this->validade);
                $stmtUpdate->bindParam(4,$this->inicio);
                $stmtUpdate->bindParam(5,$this->termino);
                $stmtUpdate->bindParam(6,$this->dataHora);
                $stmtUpdate->bindParam(7,$this->codConsulta);
                $result = $stmtUpdate->execute();

                if($result) {
                    http_response_code(200);
                    $this->read();
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
        public function delete(){

            try {

                include('../../database.class.php');

                $db = new database();
                $db->setUsuario($this->dbUsuario);
                $db->setSenha($this->dbSenha);

                $conexao = $db->conecta_mysql();

                $sqlDelete = "DELETE FROM consulta WHERE codConsulta = ?";
                $conexao->exec('SET NAMES utf8');
                $stmtDelete = $conexao->prepare($sqlDelete);
                $stmtDelete->bindParam(1,$this->codConsulta);
                $result = $stmtDelete->execute();

                if($result) {
                    http_response_code(204);
                } else {
                    http_response_code(400);
                    echo(json_encode(array('error' => "Ocorreu um erro ao remover o registro, verifique os valores."), JSON_FORCE_OBJECT));
                }

            } catch (PDOException $e) {
                http_response_code(500);
                $erro = $e->getMessage();
                echo(json_encode(array('error' => "$erro"), JSON_FORCE_OBJECT));
            }
        }
        public function updateStatus(){

            try {

                include('../../database.class.php');

                $db = new database();
                $db->setUsuario($this->dbUsuario);
                $db->setSenha($this->dbSenha);

                $conexao = $db->conecta_mysql();

                $sqlUpdate = "UPDATE consulta SET status = ? WHERE codConsulta= ?";
                $conexao->exec('SET NAMES utf8');
                $stmtUpdate = $conexao->prepare($sqlUpdate);
                $stmtUpdate->bindParam(1,$this->status);
                $stmtUpdate->bindParam(2,$this->codConsulta);
                $result = $stmtUpdate->execute();

                if($result) {
                    http_response_code(200);
                    $this->read();
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