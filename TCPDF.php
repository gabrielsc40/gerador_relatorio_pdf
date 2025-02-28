<?php

$dados = [
  'cobranca' => [
      [
          'titulo' => 'Cobrança Mensal',
          'quantidade' => 5,
          'contratos' => [
              [
                  'id_contrato' => 101,
                  'nome' => 'João Silva',
                  'endereco' => 'Rua A, 123 - Centro',
                  'recibos' => 5,
                  'dias' => 10,
                  'valor' => 1200.50
              ],
              [
                  'id_contrato' => 102,
                  'nome' => 'Maria Souza',
                  'endereco' => 'Av. B, 456 - Bairro X',
                  'recibos' => 3,
                  'dias' => 7,
                  'valor' => 900.75
              ],
              [
                  'id_contrato' => 103,
                  'nome' => 'Carlos Lima',
                  'endereco' => 'Travessa C, 789 - Centro',
                  'recibos' => 4,
                  'dias' => 8,
                  'valor' => 1100.30
              ],
              [
                  'id_contrato' => 106,
                  'nome' => 'Fernanda Oliveira',
                  'endereco' => 'Rua F, 999 - Vila Nova',
                  'recibos' => 6,
                  'dias' => 12,
                  'valor' => 1500.00
              ],
              [
                  'id_contrato' => 107,
                  'nome' => 'Roberto Dias',
                  'endereco' => 'Av. G, 777 - Jardim América',
                  'recibos' => 2,
                  'dias' => 5,
                  'valor' => 800.50
              ],
          ],
      ],
      [
          'titulo' => 'Cobrança Extra',
          'quantidade' => 3,
          'contratos' => [
              [
                  'id_contrato' => 104,
                  'nome' => 'Ana Pereira',
                  'endereco' => 'Rua D, 321 - Bairro Y',
                  'recibos' => 2,
                  'dias' => 5,
                  'valor' => 850.00
              ],
              [
                  'id_contrato' => 105,
                  'nome' => 'Ricardo Mendes',
                  'endereco' => 'Av. E, 654 - Centro',
                  'recibos' => 6,
                  'dias' => 12,
                  'valor' => 1350.90
              ],
              [
                  'id_contrato' => 108,
                  'nome' => 'Sofia Martins',
                  'endereco' => 'Rua H, 555 - Bela Vista',
                  'recibos' => 3,
                  'dias' => 7,
                  'valor' => 950.25
              ],
          ],
      ],
      [
          'titulo' => 'Cobrança Especial',
          'quantidade' => 4,
          'contratos' => [
              [
                  'id_contrato' => 109,
                  'nome' => 'Marcos Vinícius',
                  'endereco' => 'Rua I, 222 - Centro',
                  'recibos' => 4,
                  'dias' => 9,
                  'valor' => 1250.80
              ],
              [
                  'id_contrato' => 110,
                  'nome' => 'Camila Andrade',
                  'endereco' => 'Av. J, 333 - Vila Rica',
                  'recibos' => 5,
                  'dias' => 11,
                  'valor' => 1450.60
              ],
              [
                  'id_contrato' => 111,
                  'nome' => 'Felipe Souza',
                  'endereco' => 'Travessa K, 777 - Bairro Industrial',
                  'recibos' => 2,
                  'dias' => 6,
                  'valor' => 890.40
              ],
              [
                  'id_contrato' => 112,
                  'nome' => 'Aline Castro',
                  'endereco' => 'Rua L, 999 - Bairro Verde',
                  'recibos' => 3,
                  'dias' => 7,
                  'valor' => 970.70
              ],
          ],
      ],
  ],
];

$mr = new ModeloRelatorioAgrupado();
    $mr->setTitulo('Cobrança de contratos ativos')
      ->setPapel('L')
      ->addSubtitulo('Valor não cobrado', $dados['valor'])
      ->addSubtitulo('Valor total', $dados['valor_total'])
      ->addSubtitulo('Clientes não cobrados', $dados['quantidade'])
      ->addSubtitulo('Cliente cobrados', $dados['quantidade_total'] - $dados['quantidade'])
      ->addSubtitulo('Cliente em atraso', $dados['quantidade_total'])
      ->addSubtitulo('Percentual cobrado', $dados['percentual'] . '%');

    foreach ($dados['cobranca'] as $cobranca) {
      if (!isset($cobranca['contratos'])) continue;
      $mrg = new Grupo();
      $mrg->addCabecalho($cobranca['titulo'], 10, 'E')
        ->addCabecalho('Quantidade: ' . $cobranca['quantidade'], 10, 'D')
        ->addCabecalhoTabela('Contrato', 8, 'C')
        ->addCabecalhoTabela('Locatário', 30, 'E')
        ->addCabecalhoTabela('Imóvel', 40, 'E')
        ->addCabecalhoTabela('Recibos', 7, 'C')
        ->addCabecalhoTabela('Dias', 7, 'C')
        ->addCabecalhoTabela('Valor', 8, 'D');
      $soma = 0;
      foreach ($cobranca['contratos'] as $contrato) {
        $soma += $contrato['valor'];
        $mrg->addDados(
          $contrato['id_contrato'],
          Texto::iniciaisMaiusculas($contrato['nome']),
          Texto::iniciaisMaiusculas($contrato['endereco']),
          $contrato['recibos'],
          $contrato['dias'],
          Formatar::moeda($contrato['valor'])
        );
      }
      $mrg->addRodapeTabela('Valor total: ' . Formatar::moeda($soma), 10, 'D');

      $mr->addGrupo($mrg);
    }