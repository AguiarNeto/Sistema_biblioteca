<?php
// Informações de conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "c1macedo";
$dbname = "biblioteca";

// Cria a conexão com o banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica se ocorreu algum erro na conexão
if ($conn->connect_error) {
    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
}

// Verifica se o botão "Dar baixa" foi acionado
if (isset($_POST['emprestimo_id'])) {
    $emprestimo_id = $_POST['emprestimo_id'];

    // Consulta para obter as informações do empréstimo
    $sql = "SELECT * FROM emprestimos WHERE id = $emprestimo_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $livro_id = $row['id_livro'];

        // Inicia uma transação para garantir a integridade dos dados
        $conn->begin_transaction();

        try {
            // Remoção do registro da tabela "emprestimos"
            $sql_delete = "DELETE FROM emprestimos WHERE id = $emprestimo_id";
            $conn->query($sql_delete);

            // Adição de +1 exemplar na tabela "livros"
            $sql_update = "UPDATE livros SET exemplares_disponiveis = exemplares_disponiveis + 1 WHERE id = $livro_id";
            $conn->query($sql_update);

            // Confirma a transação
            $conn->commit();

            // Exibe um alerta de sucesso e redireciona para a página "baixalivro.php"
            echo '<script>alert("Livro dado baixa com sucesso!"); window.location.href = "baixalivro.php";</script>';
            exit();
        } catch (Exception $e) {
            // Ocorreu um erro, desfaz a transação
            $conn->rollback();
            echo "Erro ao dar baixa no livro: " . $e->getMessage();
        }
    } else {
        echo "Empréstimo não encontrado.";
    }
}

// Fecha a conexão com o banco de dados
$conn->close();
?>
