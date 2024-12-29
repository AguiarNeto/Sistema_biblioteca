<?php
// Definir as informações do banco de dados
$host = "Unnamed";
$user = "root";
$password = "eeepmcc";
$database = "biblioteca";

// Conectar ao banco de dados
$conn = mysqli_connect($host, $user, $password, $database);

// Verificar se a conexão foi bem sucedida
if (!$conn) {
    die("Falha na conexão: " . mysqli_connect_error());
}

// Verificar se o formulário foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Obter os valores dos campos do formulário
    $nome = mysqli_real_escape_string($conn, $_POST["nome"]);
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $senha = mysqli_real_escape_string($conn, $_POST["senha"]);
    $turma = mysqli_real_escape_string($conn, $_POST["turma"]);
    $data_nascimento = mysqli_real_escape_string($conn, $_POST["data-nascimento"]);
    $telefone = mysqli_real_escape_string($conn, $_POST["telefone"]);

    // Criar a query SQL para inserir os dados na tabela de alunos
    $sql = "INSERT INTO alunos (nome, email, senha, turma, data_nascimento, telefone)
            VALUES ('$nome', '$email', '$senha', '$turma', '$data_nascimento', '$telefone')";

    // Executar a query SQL
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Usuário cadastrado com sucesso!'); window.location.href = 'login.php';</script>";
    } else {
        echo "Erro ao cadastrar usuário: " . mysqli_error($conn);
    }
}    

// Fechar a conexão com o banco de dados
mysqli_close($conn);
?>
