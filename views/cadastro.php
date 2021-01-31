<div class="col-md">
    <div class="col">
        <button class="btn btn-secondary" type="button" onclick="gravarBanco()">Gravar</button>
        <button class="btn btn-secondary" type="button" onclick="lerDados()">Ler</button>
    </div>

    <br>

    <div class="col-6">
        <label for="nome">Nome:</label>
        <input type="text" class="form-control" name="name" id="name">
        <button class="btn btn-primary" type="button" onclick="insereLinha()">Incluir</button>
    </div>

    <br>

    <div class="row">
        <div class="col">
            <table class="table table-bordered">
                <thead>
                <tr style="text-align: center">
                    <th colspan="4">
                        Pessoas
                    </th>
                </tr>
                </thead>

                <tbody id="tableBody">
                </tbody>

            </table>
        </div>

        <div class="col">
            <textarea rows="20" class="form-control" id="content" readonly></textarea>
        </div>
    </div>
</div>

<script>
    let fieldName = document.getElementById("name");
    let textArea = $('#content');
    let lineCount = 0;

    let fathers = {
        pessoas: []
    }

    textArea.val(JSON.stringify(fathers, null, 4));

    function insereLinhaPeloBanco(fatherName) {
        let bodyTable = document.getElementById("tableBody");
        let tr = document.createElement('tr');

        lineCount++;
        tr.setAttribute('id', `line${lineCount}`);
        tr.appendChild(criarColuna(fatherName));
        tr.appendChild(criarColuna(criarBotaoRemover()));
        tr.appendChild(criarColuna(criarBotaoAdd(fatherName)));
        tr.appendChild(criarColuna());

        bodyTable.appendChild(tr);
    }

    function insereLinha(readFatherName = "", readSonName = "") {
        let bodyTable = document.getElementById("tableBody");
        let tr = document.createElement('tr');
        let name;

        if (readFatherName != "")
            name = readFatherName;
        else
            name = fieldName.value;

        if (readSonName != "")
            adicionaFilho(name, readSonName);


        const repeatedName = fathers.pessoas.find(item => item.nome === name);
        if(repeatedName){
            alert("Este nome encontra-se cadastrado");
            return;
        }

        if (name !== "") {
            lineCount++;
            tr.setAttribute('id', `line${lineCount}`);
            tr.appendChild(criarColuna(name));
            tr.appendChild(criarColuna(criarBotaoRemover()));
            tr.appendChild(criarColuna(criarBotaoAdd(name)));
            tr.appendChild(criarColuna());

            bodyTable.appendChild(tr);

            adicionaPai(name);
        }

    }

    function adicionaPai(nome) {
        fathers.pessoas.push({nome, filhos: []});
        textArea.val(JSON.stringify(fathers, null, 4));
    }

    function adicionaFilho(fatherName, son = null, id = "") {
        const pessoa = fathers.pessoas.find(item => item.nome === fatherName);

        let line = document.getElementById(`line${lineCount}`);

        if (!pessoa)
            return;


        if (son !== null) {
            if (id !== "")
                line = document.getElementById(`line${id}`)

            fathers.pessoas.map(jovem => {
                if (jovem.nome === fatherName) {
                    const tbody = line.children;
                    tbody[3].appendChild(criarBotaoRemoverFilho(fatherName, son));
                    const br = document.createElement('br');
                    br.setAttribute('id', `deleteSpace${son}`)
                    tbody[3].appendChild(br);
                }
            });
        }

        pessoa.filhos.push(son);

        textArea.val(JSON.stringify(fathers, null, 4));
    }

    function recebeFilho(fatherName, id = "") {
        let nameSon = window.prompt("Digite o nome do filho:");
        if (nameSon)
            adicionaFilho(fatherName, nameSon, id)
    }

    function criarColuna(dado) {
        let td = document.createElement('td');
        if (typeof dado != "object")
            td.textContent = dado;
        else
            td.appendChild(dado);

        return td;
    }

    function criarBotaoRemover(fatherName) {
        let a = document.createElement('button');

        a.classList.add('btn');
        a.classList.add('btn-danger');
        a.classList.add('text-light');
        a.setAttribute('onclick', `removerPessoa('line${lineCount}', '${fatherName}')`);
        a.textContent = 'Remover';

        return a;
    }

    function criarBotaoRemoverFilho(fatherName = "", son = "") {

        if (son !== null) {
            let a = document.createElement('button');

            a.classList.add('btn');
            a.classList.add('btn-danger');
            a.classList.add('text-light');
            a.setAttribute('id', `${son}${lineCount}`);
            a.setAttribute('onclick', `removerFilho('${lineCount}','${son}')`);
            a.textContent = `Remover Filho: ${son}`;

            return a;
        }
    }

    function removerFilho(line_id, son) {
        const buttonRemove = document.getElementById(`${son}${line_id}`);

        const content = buttonRemove.parentElement;
        const deleteSpace = document.getElementById(`deleteSpace${son}`);
        content.removeChild(deleteSpace)

        buttonRemove.remove();

        for (let cont = 0; cont < fathers.pessoas.length; cont++) {
            for (let y = 0; y < fathers.pessoas[cont].filhos.length; y++) {
                if (fathers.pessoas[cont].filhos[y] === son)
                    fathers.pessoas[cont].filhos.splice(y, 1);
            }
        }

        textArea.val(JSON.stringify(fathers, null, 4));
    }

    function criarBotaoAdd(fatherName) {
        let b = document.createElement('button');

        b.classList.add('btn');
        b.classList.add('btn-secondary');
        b.setAttribute('id', `${lineCount}`);
        b.setAttribute('onclick', `recebeFilho('${fatherName}', '${lineCount}')`);
        b.textContent = 'Adicionar filho';

        return b;
    }

    function removerPessoa(line_id) {
        const father = $(`#${line_id}`);
        const tdObj = father.closest('tr').find('td');
        const fatherName = tdObj[0].innerHTML;
        const aux = [];

        father.remove();

        fathers.pessoas.map(jovem => {
            if (jovem.nome !== fatherName)
                aux.push(jovem);
        });

        fathers.pessoas = aux;
        textArea.val(JSON.stringify(fathers, null, 4));
    }

    function gravarBanco() {
        const pessoas = fathers.pessoas;

        $.ajax({
            type: "POST",
            url: "<?= SERVERURL ?>ajax/jsonAjax.php",
            data: {
                metodo: 'adicionaFilho',
                pessoas,
            },
            success: function (data, text) {
                if (text == 'success' && data != 0)
                    alert('Sucesso');
            },
            error: function (response, status, error) {
                throw new Error(`Status: ${status} não possivel conectar com arquivo AJAX.\n Erro: ${error} `);
            }
        })
    }

    function loadPais(result) {
        let father__id = 0;

        result.map(item => {
            if (father__id !== item.id) {
                father__id = item.id;
                adicionaPai(item.nome);
                insereLinhaPeloBanco(item.nome);
            }

            if (item.filho !== null)
                adicionaFilho(item.nome, item.filho)
        });
    }

    function lerDados() {
        $.ajax({
            type: "POST",
            url: "<?= SERVERURL ?>ajax/jsonAjax.php",
            data: {
                metodo: 'lerDados',
            },
            success: function (data, text) {
                if (text == 'success' && data != 0) {
                    limparTabela();
                    const result = JSON.parse(data)[0];
                    loadPais(result);
                }
            },
            error: function (response, status, error) {
                throw new Error(`Status: ${status} não possivel conectar com arquivo AJAX.\n Erro: ${error} `);
            }
        })
    }

    function limparTabela() {
        let trs = document.querySelectorAll('tbody tr');
        trs.forEach((tr) => {
            tr.remove();
        });

        fathers = null;

        textArea.val(JSON.stringify(fathers, null, 4));

        fathers = {
            pessoas: []
        }
    }
</script>
