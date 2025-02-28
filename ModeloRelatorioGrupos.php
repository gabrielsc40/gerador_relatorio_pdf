<?php

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
require_once('tcpdf/tcpdf.php');

class ModeloRelatorioAgrupado
{
    private $grupos = [];
    private $papel = 'P';
    private $alinhamento = '';
    private $titulo = null;
    private $subTitulos = [];
    private $subTitulosPlus = [];
    private $sumario = [];

    public function setPapel($papel)
    {
        $papel = strtolower($papel);
        if ($papel == 'p') $papel = 'portrait';
        if ($papel == 'l') $papel = 'landscape';
        $this->papel = $papel;
        return $this;
    }

    public function setAlinhamento($alinhamento)
    {
        $alinhamento = strtolower($alinhamento);
        if ($alinhamento == 'd') $alinhamento = 'R';
        if ($alinhamento == 'r') $alinhamento = 'R';
        if ($alinhamento == 'e') $alinhamento = 'L';
        if ($alinhamento == 'l') $alinhamento = 'L';
        if ($alinhamento == 'c') $alinhamento = 'C';
        if ($alinhamento == 'j') $alinhamento = 'J';
        $this->alinhamento = $alinhamento;
        return $this;
    }

    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;
        return $this;
    }

    public function addSubtitulo($titulo, $texto, int $tamanho = 10, $alinhamento = 'C')
    {
        $this->setAlinhamento($alinhamento);
        $this->subTitulos[] = [
            'titulo' => $titulo,
            'texto' => $texto,
            'tamanho' => $tamanho,
            'alinhamento' => $this->alinhamento
        ];
        return $this;
    }

    public function addSubtituloPlus($titulo, $texto, int $tamanho = 10, $alinhamento = 'C')
    {
        $this->setAlinhamento($alinhamento);
        $this->subTitulosPlus[] = [
            'titulo' => $titulo,
            'texto' => $texto,
            'tamanho' => $tamanho,
            'alinhamento' => $this->alinhamento
        ];
        return $this;
    }

    public function addGrupo($grupo)
    {
        $this->grupos[] = $grupo->getArray();
        return $this;
    }

    public function addSumario($texto, int $tamanho = 10, $alinhamento = 'C')
    {
        $this->setAlinhamento($alinhamento);
        $this->sumario[] = [
            'texto' => $texto,
            'tamanho' => $tamanho,
            'alinhamento' => $this->alinhamento
        ];
        return $this;
    }

    public function getArray()
    {
        return [
            'papel' => $this->papel,
            'titulo' => $this->titulo,
            'subtitulos' => $this->subTitulos,
            'subtitulosPlus' => $this->subTitulosPlus,
            'grupos' => $this->grupos,
            'sumario' => $this->sumario,
        ];
    }

    public function gerar()
    {
        $dados = $this->getArray();
        if (!isset($dados['papel'])) $dados['papel'] = 'landscape';
        //configurações basicas do documento
        $pdf = new MYPDF($dados);

        //Gerar dados
        $pdf->gerar();
    }
}

class Grupo
{
    private $cabecalho = [];
    private $rodape = [];
    private $alinhamento = 'C';
    private $cabecalhoTabela = [];
    private $rodapeTabela = [];
    private $dados = [];

    public function setAlinhamento($alinhamento)
    {
        $alinhamento = strtolower($alinhamento);
        if ($alinhamento == 'd') $alinhamento = 'R';
        if ($alinhamento == 'r') $alinhamento = 'R';
        if ($alinhamento == 'e') $alinhamento = 'L';
        if ($alinhamento == 'l') $alinhamento = 'L';
        if ($alinhamento == 'c') $alinhamento = 'C';
        if ($alinhamento == 'j') $alinhamento = 'J';
        $this->alinhamento = $alinhamento;
        return $this;
    }
    public function addCabecalho($texto,  int $tamanho = 10, $alinhamento = 'L')
    {
        $this->setAlinhamento($alinhamento);
        $this->cabecalho[] = [
            'texto' => $texto,
            'tamanho' => $tamanho,
            'alinhamento' => $this->alinhamento
        ];
        return $this;
    }
    public function addCabecalhoTabela($texto, int $tamanho = 10, $alinhamento = 'L')
    {
        $this->setAlinhamento($alinhamento);
        $this->cabecalhoTabela[] = [
            'texto' => $texto,
            'tamanho' => $tamanho,
            'alinhamento' => $this->alinhamento
        ];
        return $this;
    }
    public function addRodapeTabela($texto, int $tamanho = 10, $alinhamento = 'L')
    {
        $this->setAlinhamento($alinhamento);
        $this->rodapeTabela[] = [
            'texto' => $texto,
            'tamanho' => $tamanho,
            'alinhamento' => $this->alinhamento
        ];
        return $this;
    }
    public function addDados(...$dados)
    {
        $this->dados[] = $dados;
    }
    public function getArray()
    {
        return  array(
            'cabecalho' => $this->cabecalho,
            'cabecalhoTabela' => $this->cabecalhoTabela,
            'dados' => $this->dados,
            'rodapeTabela' => $this->rodapeTabela,
            'rodape' => $this->rodape,
        );
    }
}

class MYPDF extends TCPDF
{
    protected $dados = null;
    protected $papel = null;
    protected $a = null;
    protected $l = null;
    protected $y = null;

    function __construct($dados)
    {
        //Construtor
        parent::__construct($dados['papel'], 'mm', 'A4', true, 'UTF-8', false);
        $this->dados = $dados;
        $this->papel = $dados['papel'];
        $this->SetCreator(PDF_CREATOR);
        $this->SetTitle("{$dados['titulo']}");
        $this->SetSubject('Documento para impressão de relatório');
        $this->SetAutoPageBreak(TRUE, 5);
        $this->SetMargins(10, 0, 10);
        if (strtolower($dados['papel']) == 'landscape') {
            $this->l = 277;
            $this->a = 190;
        } else {
            $this->l = 190;
            $this->a = 277;
        }
    }
    //Cabeçalho
    public function Header()
    {
        if ($this->page == 1) {
            $this->SetFont('Helvetica', 'N', 25);

            if ($this->papel == 'landscape') {
                $this->Cell(0, 30, "{$this->dados['titulo']}", 0, 1, 'L');
                $this->Image(FOLDER_PUBLIC . 'img/logomarca.png', 227, 10, '60%', 0, 'PNG', '', 'R', false, 300, '', false, false, 0, false, false, false);
                $this->SetDrawColor(110, 111, 114);
                $this->Line('10', '25', '287', '25');
            } else if ($this->papel == 'portrait') {
                $this->Cell(0, 30, "{$this->dados['titulo']}", 0, 1, 'L');
                $this->Image(FOLDER_PUBLIC . 'img/logomarca.png', 140, 10, '60%', 0, 'PNG', '', 'R', false, 300, '', false, false, 0, false, false, false);
                $this->SetDrawColor(110, 111, 114);
                $this->Line('10', '25', '200', '25');
            }
            if (count($this->dados['subtitulos']) > 0) {
                $tamanho = 0;
                // pegar o tamanho
                foreach ($this->dados['subtitulos'] as $st) {
                    $tamanho += $st['tamanho'];
                }
                $d = count($this->dados['subtitulos']) * 5 - 5;
                // agora definir o tamanho real de cada item baseado na largura de tela
                for ($i = 0; $i < count($this->dados['subtitulos']); $i++) {
                    $this->dados['subtitulos'][$i]['tamanho'] = ($this->dados['subtitulos'][$i]['tamanho'] / $tamanho) * ($this->l - $d);
                }
                $this->SetDrawColor(110, 111, 114);
                $x = 10;
                $h = 9;
                foreach ($this->dados['subtitulos'] as $subtitulo) {
                    $this->SetFont("helvetica", "B", 9);
                    $this->MultiCell($subtitulo['tamanho'], $h, $subtitulo['titulo'], 1, $subtitulo['alinhamento'], 0, 0, $x, '29');

                    $this->SetFont("helvetica", "", 9);
                    $this->MultiCell($subtitulo['tamanho'], $h, $subtitulo['texto'], 0, $subtitulo['alinhamento'], 0, 0, $x, '34');
                    $x += $subtitulo['tamanho'] + 5;
                }
                $this->Ln();
            }
            if (count($this->dados['subtitulosPlus']) > 0) {
                $tamanho = 0;
                // pegar o tamanho
                foreach ($this->dados['subtitulosPlus'] as $st) {
                    $tamanho += $st['tamanho'];
                }
                $d = count($this->dados['subtitulosPlus']) * 5 - 5;
                // agora definir o tamanho real de cada item baseado na largura de tela
                for ($i = 0; $i < count($this->dados['subtitulosPlus']); $i++) {
                    $this->dados['subtitulosPlus'][$i]['tamanho'] = ($this->dados['subtitulosPlus'][$i]['tamanho'] / $tamanho) * ($this->l - $d);
                }
                $this->SetDrawColor(110, 111, 114);
                $x = 10;
                $h = 9;
                foreach ($this->dados['subtitulosPlus'] as $subtitulo) {
                    $this->SetFont("helvetica", "B", 9);
                    $this->MultiCell($subtitulo['tamanho'], $h, $subtitulo['titulo'], 1, $subtitulo['alinhamento'], 0, 0, $x, '42');

                    $this->SetFont("helvetica", "", 9);
                    $this->MultiCell($subtitulo['tamanho'], $h, $subtitulo['texto'], 0, $subtitulo['alinhamento'], 0, 0, $x, '47');
                    $x += $subtitulo['tamanho'] + 5;
                }
                $this->Ln();
            }
        } else {
            $this->Ln();
        }
    }

    private function cabecalhoGrupo($cabecalho)
    {
        if (count($this->dados['grupos']) < 1) return;
        if (($this->papel == 'landscape' && $this->y >= 183) || $this->y >= 268) {
            $this->AddPage();
            $this->y = 10;
            $this->setY(10);
        }
        $this->mudarPagina();
        // preenchimento
        $this->SetFillColor(226, 227, 229);
        $this->SetDrawColor(110, 111, 114);
        // verificar o tamanho total
        $tamanho = 0;
        foreach ($cabecalho as $c) {
            $tamanho += $c['tamanho'];
        }
        $this->SetFont("helvetica", "N", 10);
        $x = 10;
        $h = 6;
        $y = null;
        $this->Ln();
        for ($i = 0; $i < count($cabecalho); $i++) {
            $cabecalho[$i]['tamanho'] = $cabecalho[$i]['tamanho'] / $tamanho * $this->l;
            $this->MultiCell($cabecalho[$i]['tamanho'], $h, $cabecalho[$i]['texto'], 1, $cabecalho[$i]['alinhamento'], 1, 0, $x, $this->$y, true, 0, true);
            $x += $cabecalho[$i]['tamanho'];
        }
        $this->y += 6;
    }

    private function sumario($sumario)
    {
        // preenchimento
        $this->SetFillColor(226, 227, 229);
        $this->SetDrawColor(110, 111, 114);
        // verificar o tamanho total
        $tamanho = 0;
        foreach ($sumario as $c) {
            $tamanho += $c['tamanho'];
        }
        $this->SetFont("helvetica", "N", 10);
        $x = 10;
        $h = 6;
        $y = null;
        for ($i = 0; $i < count($sumario); $i++) {
            $sumario[$i]['tamanho'] = $sumario[$i]['tamanho'] / $tamanho * $this->l;
            $this->MultiCell($sumario[$i]['tamanho'], $h, $sumario[$i]['texto'], 1, $sumario[$i]['alinhamento'], 1, 0, $x, $this->$y, true, 0, true);
            $x += $sumario[$i]['tamanho'];
        }
        $this->y += 6;
    }

    private function cabecalhoTabela($tabela)
    {
        if (count($tabela) < 1) return;
        $this->SetFillColor(226, 227, 229);
        $this->SetDrawColor(110, 111, 114);
        $this->mudarPagina();
        $tamanho = 0;
        foreach ($tabela as $c) {
            $tamanho += $c['tamanho'];
        }
        $this->SetFont("helvetica", "B", 10);
        $x = 10;
        $h = 5;
        $y = null;
        for ($i = 0; $i < count($tabela); $i++) {
            $tabela[$i]['tamanho'] = $tabela[$i]['tamanho'] / $tamanho * $this->l;
            $this->MultiCell($tabela[$i]['tamanho'], $h, $tabela[$i]['texto'], 1, $tabela[$i]['alinhamento'], 0, 0, $x, $this->$y);
            $x += $tabela[$i]['tamanho'];
        }
        $this->y += 5;
    }

    private function rodapeTabela($tabela, $ultimo)
    {
        if (count($tabela) < 1) return;
        if (!$ultimo) $this->mudarPagina();
        $tamanho = 0;
        foreach ($tabela as $c) {
            $tamanho += $c['tamanho'];
        }
        $this->SetFont("helvetica", "B", 10);
        $x = 10;
        $h = 5;
        $y = null;
        for ($i = 0; $i < count($tabela); $i++) {
            $tabela[$i]['tamanho'] = $tabela[$i]['tamanho'] / $tamanho * $this->l;
            $this->MultiCell($tabela[$i]['tamanho'], $h, $tabela[$i]['texto'], 1, $tabela[$i]['alinhamento'], 0, 0, $x, $this->$y);
            $x += $tabela[$i]['tamanho'];
        }
        $this->y += 5;
    }

    private function dadoTabela($tabela, $dado)
    {
        if (count($tabela) < 1) return;
        if ($this->y >= 190) {
            $this->mudarPagina($tabela);
        }
        $tamanho = 0;
        foreach ($tabela as $c) {
            $tamanho += $c['tamanho'];
        }
        $this->SetFont("helvetica", "", 10);
        $x = 10;
        $h = 5;
        $y = null;
        for ($i = 0; $i < count($tabela); $i++) {
            $tabela[$i]['tamanho'] = $tabela[$i]['tamanho'] / $tamanho * $this->l;
            $this->MultiCell($tabela[$i]['tamanho'], $h, $dado[$i], 1, $tabela[$i]['alinhamento'], 0, 0, $x, $this->$y);
            $x += $tabela[$i]['tamanho'];
        }
        $this->y += 5;
    }
    private function mudarPagina($tabela = null)
    {
        if ($this->y > $this->a) {
            $this->AddPage();
            $this->y = 10;
            $this->setY(10);
            if ($tabela != null) {
                $this->cabecalhoTabela($tabela);
            }
        }
    }
    public function gerar()
    {
        $this->AddPage();

        $this->y = 42;

        if (count($this->dados['subtitulosPlus']) > 0) {
            $this->y = 55;
        }
        //Limpar buffer anterior ao documento PDF
        foreach ($this->dados['grupos'] as $grupo) {
            // se tiver mais de um grupo vou imprimir o cabeçalho do grupo
            $this->cabecalhoGrupo($grupo['cabecalho']);
            $this->cabecalhoTabela($grupo['cabecalhoTabela']);
            $quantidade = count($grupo['dados']);
            for ($i = 0; $i < $quantidade; $i++) {
                $this->dadoTabela($grupo['cabecalhoTabela'], $grupo['dados'][$i]);
                $ultimo = $i + 1 == $quantidade;
            }
            //foreach($grupo['dados'] as $dado) $this->dadoTabela($grupo['cabecalhoTabela'], $dado);
            $this->rodapeTabela($grupo['rodapeTabela'], $ultimo);
        }
        $this->sumario($this->dados['sumario']);

        ob_clean();
        //Saída do documento
        $this->Output($this->dados['titulo'] . '.pdf', 'I');
        //unset($this);

    }

    // Rodapé de todas as páginas
    public function Footer()
    {
        $nome = 'Emitido por: ' . $_SESSION['dados_usuario']['nome'];
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $date = date('d/m/Y H:i:s');
        // Número da página
        $pagina = 'Página ' . $this->getAliasNumPage() . ' de ' . $this->getAliasNbPages();
        $data = $date;
        $this->Cell(0.1, 15, $pagina, 0, false, 'L', 0, '', 0, false, '', '');
        $this->Cell(0, 15, $nome, 0, false, 'C', 0, '', 0, false, '', '');
        $this->Cell(0, 15, $data, 0, false, 'R', 0, '', 0, false, '', '');
    }
}
