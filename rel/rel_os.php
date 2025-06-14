<?php
$id = $_GET['id'];
$id_orc = $_GET['id_orc'];
include('../conexao.php');

$query = "SELECT o.*, c.nome AS cli_nome, c.email, c.telefone, c.endereco, f.nome AS func_nome 
FROM orcamentos AS o 
INNER JOIN clientes AS c ON o.cliente = c.cpf 
INNER JOIN funcionarios AS f ON o.tecnico = f.id 
WHERE o.id = '$id_orc'";

$result = mysqli_query($conexao, $query);

while ($res_1 = mysqli_fetch_array($result)) {
  $data2 = implode('/', array_reverse(explode('-', $res_1['data_geracao'])));

  $query_os = "select * from os where id = '$id'";
  $result_os = mysqli_query($conexao, $query_os);

  while ($res_2 = mysqli_fetch_array($result_os)) {
    $data3 = implode('/', array_reverse(explode('-', $res_2['data_fechamento'])));

?>

    <!DOCTYPE html>
    <html lang="pt-br">

    <head>
      <meta charset="UTF-8">
      <title>Relatório de Ordem de Serviço</title>
      <style>
        body {
          font-family: Arial, sans-serif;
          font-size: 13px;
          color: #333;
          margin: 30px;
        }

        .cabecalho {
          background-color: #ebebeb;
          padding: 15px;
          margin-bottom: 20px;
        }

        .titulo {
          margin: 0;
        }

        .linha {
          display: flex;
          flex-wrap: wrap;
          margin-bottom: 10px;
        }

        .coluna {
          flex: 1;
          min-width: 200px;
          padding: 5px;
        }

        table {
          width: 100%;
          border-collapse: collapse;
          margin-top: 10px;
        }

        th,
        td {
          border: 1px solid #ccc;
          padding: 6px;
          text-align: left;
        }

        .areaTotais,
        .areaTotal {
          border: 1px solid #bcbcbc;
          padding: 10px;
          border-radius: 5px;
          background-color: #f9f9f9;
          margin-top: 10px;
        }

        .footer {
          position: absolute;
          bottom: 0;
          width: 100%;
          background-color: #ebebeb;
          text-align: center;
          font-size: 12px;
          padding: 10px;
        }
      </style>
    </head>

    <body>

      <div class="cabecalho">
        <div class="linha">
          <div class="coluna">
            <h2 class="titulo"> EletroService – Assistência Técnica para Eletrodomésticos</h2>
            <p>Rua dos Trabalhos Nº 1000, Centro - Irai de Minas - MG - CEP 38510000</p>
          </div>
        </div>
      </div>

      <div>
        <div class="linha">
          <div class="coluna"><strong>Ordem de Serviço Nº:</strong> <?php echo $id ?></div>
          <div class="coluna"><strong>Data:</strong> <?php echo $data3; ?></div>
        </div>

        <hr>

        <h4>Dados do Cliente</h4>
        <div class="linha">
          <div class="coluna">Nome: <?php echo $res_1['cli_nome']; ?></div>
          <div class="coluna">Email: <?php echo $res_1['email']; ?></div>
          <div class="coluna">Endereço: <?php echo $res_1['endereco']; ?></div>
        </div>
        <div class="linha">
          <div class="coluna">Telefone: <?php echo $res_1['telefone']; ?></div>
          <div class="coluna">CPF: <?php echo $res_1['cliente']; ?></div>
        </div>

        <hr>

        <h4>Dados do Aparelho</h4>
        <div class="linha">
          <div class="coluna">Produto: <?php echo $res_1['produto']; ?></div>
          <div class="coluna">Nº Série: <?php echo $res_1['serie']; ?></div>
          <div class="coluna">Modelo: XHPER</div>
        </div>
        <p>Defeito: <?php echo $res_1['problema']; ?></p>

        <hr>

        <h4>Laudo Técnico</h4>
        <p><?php echo $res_1['laudo']; ?></p>

        <table>
          <thead>
            <tr>
              <th>Peça</th>
              <th>Valor da Peça</th>
              <th>Quantidade</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><?php echo $res_1['pecas']; ?></td>
              <td><?php echo $res_1['valor_pecas']; ?></td>
              <td>1</td>
            </tr>
          </tbody>
        </table>

        <div class="linha">
          <div class="coluna areaTotais">
            <p><strong>Total de Peças:</strong> R$ <?php echo $res_1['valor_pecas']; ?></p>

            <p><strong>Total Mão de Obra:</strong> R$ <?php echo $res_1['valor_servico']; ?></p>

            <p><strong>Total Desconto:</strong> R$ <?php echo $res_1['desconto']; ?></p>
          </div>

          <div class="coluna areaTotal">
            <p><strong>Total a Pagar:</strong> R$ <?php echo $res_1['valor_total']; ?></p>
          </div>
        </div>

        <p>Garantia de <?php echo $res_2['garantia']; ?> a partir de <?php echo $data3; ?></p>
      </div>

      <div class="footer">
        EletroService - Paulo J. Rodrigues
      </div>

    </body>

    </html>

<?php }
} ?>