<?php

    class FuncaoExame {
        private $codFuncao;
        private $codExame;

        private $dbUsuario;
        private $dbSenha;

        //SETTERS
        public function setCodFuncao($codigo){
            $this->codFuncao = $codigo;
        }
        public function setCodExame($codigo){
            $this->codExame = $codigo;
        }
        public function setDBUsuario($usuario){
            $this->dbUsuario = $usuario;
        }
        public function setDBSenha($senha){
            $this->dbSenha = $senha;
        }

        //GETTERS
        public function getCodFuncao(){
            return $this->codFuncao;
        }
        public function getCodExame(){
            return $this->codExame;
        }

        //CRUD
        public function lista(){
            try {

                include_once('../../database.class.php');
                include_once('../../utils/ExameUtil.php');
                include_once('../../utils/FuncaoUtil.php');

                $db = new database();
                $db->setUsuario($this->dbUsuario);
                $db->setSenha($this->dbSenha);

                $conexao = $db->conecta_mysql();

                $sqlLista = "SELECT F.codFuncao, F.nome AS funcao, F.descricao AS descricao_funcao, F.setor, 
                                    E.codExame, E.nome AS exame, E.descricao AS descricao_exame, 
                                    E.preco, E.codigo AS codigo_exame
                             FROM funcao_exame FE 
                                INNER JOIN funcao F 
                                ON FE.codFuncao = F.codFuncao 
                                INNER JOIN exame E 
                                ON FE.codExame = E.codExame
                             ORDER BY FE.codFuncao ASC";
                $conexao->exec('SET NAMES utf8');
                $stmtLista = $conexao->prepare($sqlLista);
                $stmtLista->execute();

                $lista = $stmtLista->fetchALL(PDO::FETCH_ASSOC);
                /*$response = Array();

                $keys = array_keys($lista);
                $size = count($lista);

                for ($i = 0; $i < $size; $i++) {
                    $key = $keys[$i];

                    if($lista[$key]["codFuncao"] == null) continue;

                    $aux->codFuncao = $lista[$key]["codFuncao"];
                    $aux->funcao = $lista[$key]["funcao"];
                    $aux->descricao_funcao = $lista[$key]["descricao_funcao"];
                    $aux->setor = $lista[$key]["setor"];

                    $exame->codExame = $lista[$key]["codExame"];
                    $exame->exame = $lista[$key]["exame"];
                    $exame->descricao_exame = $lista[$key]["descricao_exame"];
                    $exame->preco = $lista[$key]["preco"];
                    $exame->codigo = $lista[$key]["codigo_exame"];

                    $aux->exames = array(clone $exame);

                    unset($lista[$key]);

                    foreach($lista as $key_aux => $row_aux) {
                        if(
                            $aux->codFuncao == $row_aux["codFuncao"] && 
                            !in_array($row_aux["codExame"], $aux->exames, true)
                            ) {
                                $aux_exame->codExame = $row_aux["codExame"];
                                $aux_exame->exame = $row_aux["exame"];
                                $aux_exame->descricao = $row_aux["descricao_exame"];
                                $aux_exame->codigo = $row_aux["codigo_exame"];
                                $aux_exame->rpeco = $row_aux["preco"];

                                array_push($aux->exames, clone $aux_exame);
                                unset($lista[$key_aux]);
                        }
                    }

                    array_push($response, clone $aux);
                }


                return $response;*/
                foreach($lista as $row) {
                    $funcao = isset($funcoes[$row['codFuncao']]) ? $funcoes[$row['codFuncao']] : NULL;
            
                    if(!$funcao) {
                        $funcao = new FuncaoUtil($row['codFuncao'],$row['funcao'],$row['descricao_funcao'],$row['setor']);
                        
                        $funcoes[$row['codFuncao']] = $funcao;
                    }
            
                    $novo_exame = new ExameUtil($row['codExame'],$row['exame'],$row['descricao_exame'],$row['preco'],$row['codigo']);
                    $funcao->addExame($novo_exame);
                }
                echo(json_encode($funcoes, JSON_FORCE_OBJECT));
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
                // Inicia uma transaction, possibilitando rollback
                $conexao->beginTransaction();

                // Limpando a relação para inserir apenas os selecionados no array da requisição
                $sqlDelete = "DELETE FROM funcao_exame WHERE codFuncao = ?";
                $conexao->exec('SET NAMES utf8');
                $stmtDelete = $conexao->prepare($sqlDelete);
                $stmtDelete->bindParam(1,$this->codFuncao);
                $stmtDelete->execute();

                $sqlCreate = "INSERT INTO funcao_exame(codFuncao,codExame) VALUES ";

                foreach($this->codExame as $codigo) {
                    $sqlCreate .= "($this->codFuncao, $codigo),";
                }

                $sqlCreate = rtrim($sqlCreate, ',');

                $conexao->exec('SET NAMES utf8');
                $stmtCreate = $conexao->prepare($sqlCreate);
                $result = $stmtCreate->execute();
                
                // Abaixo o rollback pode acontecer tanto em casos de Exceptions(catch) ou de resultado false da operação.
                if($result) {
                    $conexao->commit();
                    http_response_code(201);
                } else {
                    $conexao->rollback();
                    http_response_code(400);
                    echo(json_encode(array('error' => "Ocorreu um erro ao inserir o registro, verifique os valores."), JSON_FORCE_OBJECT));
                }

            } catch (PDOException $e) {
                $conexao->rollback();
                http_response_code(500);
                $erro = $e->getMessage();
                echo(json_encode(array('error' => "$erro"), JSON_FORCE_OBJECT));
            }
        }
        public function read(){

            try {

                include('../../database.class.php');

                $db = new database();
                $db->setUsuario($this->dbUsuario);
                $db->setSenha($this->dbSenha);

                $conexao = $db->conecta_mysql();

                $sqlRead = "SELECT E.codExame, E.nome, E.descricao, E.preco, E.codigo
                            FROM funcao_exame FE
                                INNER JOIN exame E
                                ON FE.codExame = E.codExame
                            WHERE FE.codFuncao = ?";
                $conexao->exec('SET NAMES utf8');
                $stmtRead = $conexao->prepare($sqlRead);
                $stmtRead->bindParam(1,$this->codFuncao);
                $stmtRead->execute();

                $exames = $stmtRead->fetchALL(PDO::FETCH_ASSOC);
                echo json_encode($exames);

            } catch (PDOException $e) {
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

                $sqlDelete = "DELETE FROM funcao_exame WHERE codFuncao = ? AND codExame = ?";
                $conexao->exec('SET NAMES utf8');
                $stmtDelete = $conexao->prepare($sqlDelete);
                $stmtDelete->bindParam(1,$this->codFuncao);
                $stmtDelete->bindParam(2,$this->codExame);
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
    }
?>