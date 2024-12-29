<?php
require_once('tcpdf/tcpdf.php');

// Função para gerar o relatório em PDF
function gerarRelatorioPDF()
{
    // Criação de um novo objeto TCPDF
    $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);

    // Define o título do documento
    $pdf->SetTitle('Relatório de Empréstimos');

    // Adiciona uma página em branco
    $pdf->AddPage();

    // Define o cabeçalho do relatório
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Relatório de Empréstimos', 0, 1, 'C');

    // Define a largura das colunas
    $colWidthAluno = 80; // Largura da coluna "Aluno"
    $colWidthLivro = 80; // Largura da coluna "Livro"
    $colWidthData = 40; // Largura das colunas "Data de Empréstimo" e "Data de Devolução"

    // Calcula o deslocamento horizontal para centralizar a tabela
    $tableX = ($pdf->getPageWidth() - ($colWidthAluno + $colWidthLivro + $colWidthData * 2)) / 2;

    // Define a cor de fundo da linha
    $pdf->SetFillColor(144, 238, 144);

    // Define a cor do texto
    $pdf->SetFont('helvetica', 'B', 16);

    // Define a fonte do texto
    $pdf->SetFont('helvetica', '', 10);

    // Cabeçalhos da tabela
    $pdf->SetX($tableX);
    $pdf->SetFont('helvetica', 'B', 10); // Definindo a fonte em negrito novamente
    $pdf->Cell($colWidthAluno, 10, 'Aluno', 1, 0, 'C', true);
    $pdf->SetFont('helvetica', 'B', 10); // Definindo a fonte em negrito novamente
    $pdf->Cell($colWidthLivro, 10, 'Livro', 1, 0, 'C', true);
    $pdf->SetFont('helvetica', 'B', 10); // Definindo a fonte em negrito novamente
    $pdf->Cell($colWidthData, 10, 'Data de Empréstimo', 1, 0, 'C', true);
    $pdf->SetFont('helvetica', 'B', 10); // Definindo a fonte em negrito novamente
    $pdf->Cell($colWidthData, 10, 'Data de Devolução', 1, 1, 'C', true);

    // Restaura as configurações padrão para a cor do texto e fonte
    $pdf->SetTextColor(0); // Preto
    $pdf->SetFont('helvetica', '', 10);
    // Recupera os dados dos empréstimos do banco de dados

    $servername = 'localhost';
    $username = 'root';
    $password = 'c1macedo';
    $dbname = 'biblioteca';

    // Conexão com o banco de dados
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die('Erro na conexão com o banco de dados: ' . $conn->connect_error);
    }

    // Consulta para recuperar os dados dos empréstimos
    $sql = 'SELECT alunos.nome, livros.titulo, emprestimos.data_emprestimo, emprestimos.data_devolucao
            FROM emprestimos
            INNER JOIN alunos ON emprestimos.id_aluno = alunos.id
            INNER JOIN livros ON emprestimos.id_livro = livros.id';

    $result = $conn->query($sql);

    // Verifica se há registros retornados pela consulta
    if ($result->num_rows > 0) {
      // Loop através dos registros e adiciona as linhas à tabela do PDF
        while ($row = $result->fetch_assoc()) {
        // Obtém os valores das colunas do registro atual
        $aluno = $row['nome'];
        $livro = $row['titulo'];
        $dataEmprestimo = date('d/m/Y', strtotime($row['data_emprestimo']));
        $dataDevolucao = date('d/m/Y', strtotime($row['data_devolucao']));

        // Cria objetos DateTime das datas de empréstimo e devolução
        $dataEmprestimoObj = new DateTime($row['data_emprestimo']);
        $dataDevolucaoObj = new DateTime($row['data_devolucao']);

        // Calcula a diferença em dias entre a data de empréstimo e a data de devolução
        $diferenca = $dataEmprestimoObj->diff($dataDevolucaoObj);
        $diasAtraso = $diferenca->days;
        $isAtraso = ($diasAtraso > 7);

        // Define a cor de fundo da linha com base no atraso
        if ($isAtraso) {
            $pdf->SetFillColor(255, 0, 0); // Vermelho
        } else {
            $pdf->SetFillColor(255, 255, 255); // Branco
        }

    // Adiciona a linha à tabela do PDF
    $pdf->SetX($tableX);
    $pdf->Cell($colWidthAluno, 10, $aluno, 1, 0, 'C', true);
    $pdf->Cell($colWidthLivro, 10, $livro, 1, 0, 'C', true);
    $pdf->Cell($colWidthData, 10, $dataEmprestimo, 1, 0, 'C', true);
    $pdf->Cell($colWidthData, 10, $dataDevolucao, 1, 1, 'C', true);
}



    } else {
        // Caso não haja registros, exibe uma mensagem no PDF
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(0, 10, 'Nenhum empréstimo encontrado.', 1, 1, 'C');
    }

    // Fecha a conexão com o banco de dados
    $conn->close();

    // Saída do PDF
    $pdf->Output('relatorio_emprestimos.pdf', 'D');
}

// Chamada da função para gerar o relatório
gerarRelatorioPDF();
