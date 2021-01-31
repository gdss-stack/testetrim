<?php
if ($pedidoAjax) {
    require_once "../model/DbModel.php";
}

class MainController extends DbModel
{
    public function cadastraPessoas($post)
    {
        //limpar as tabelas
        DbModel::consultaSimples("DELETE FROM filhos");
        DbModel::consultaSimples("DELETE FROM pessoas");

        //reseta o auto increment
        DbModel::consultaSimples("ALTER TABLE filhos AUTO_INCREMENT = 1");
        DbModel::consultaSimples("ALTER TABLE pessoas AUTO_INCREMENT = 1");

        unset($post['metodo']);

        //inserir os pais e seus determinados filhos
        for ($i = 0; $i < count($post['pessoas']); $i++):
            $arrayFathers = [
                'nome' => $post['pessoas'][$i]['nome']
            ];
            DbModel::insert('pessoas', $arrayFathers);
            if (DbModel::connection()->errorCode() == 0 && $post['pessoas'][$i]['filhos'] != ""):
                $pessoa_id = DbModel::connection()->lastInsertId();
                $sons = $post['pessoas'][$i]['filhos'];
                foreach ($sons as $son):
                    if (!empty($filho)) :
                        $arraySons = [
                            'pessoa_id' => $pessoa_id,
                            'nome' => $son
                        ];
                        DbModel::insert('filhos', $arraySons);
                    endif;
                endforeach;
            endif;
        endfor;

        return "1";
    }

    public function lerDados()
    {
        $arrayPessoas = DbModel::consultaSimples("SELECT p.id, p.nome, f.nome AS 'filho' FROM pessoas p LEFT OUTER JOIN filhos f ON f.pessoa_id = p.id")->fetchAll(PDO::FETCH_ASSOC);

        if (count($arrayPessoas) > 0) {
            return json_encode(array($arrayPessoas));
        }
    }
}