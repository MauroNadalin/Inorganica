<?php
//include('config.php');
include('conexao.php');
include('abre_ou_redireciona.php');
// Verificar se os parâmetros foram passados corretamente e são válidos
if (isset($_GET['nota']) && isset($_GET['coleta_id'])) {
    // Garantir que os parâmetros são numéricos
    $nota =  $_GET['nota'];
    $coleta_id = $_GET['coleta_id'];

    if (!is_numeric($nota) || !is_numeric($coleta_id)) {
        echo "Parâmetros inválidos.";
        exit;
    }

    $nota = (int)$nota;
    $coleta_id = (int)$coleta_id;

    try {
        // Recuperar o provedor_id da coleta
        $sql = "SELECT provedor_id FROM coletas WHERE id = :coleta_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':coleta_id' => $coleta_id]);
        $coleta = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($coleta) {
            $provedor_id = $coleta['provedor_id'];

            // Atualizar os pontos do provedor
            $update_sql = "
                UPDATE users
                SET pontos = pontos + :nota
                WHERE id = :provedor_id";
            $update_stmt = $pdo->prepare($update_sql);
            $update_stmt->execute([
                ':nota' => $nota,
                ':provedor_id' => $provedor_id
            ]);

            // Verificar se a atualização foi bem-sucedida
            if ($update_stmt->rowCount() > 0) {
                echo "Pontos atualizados com sucesso!";
            } else {
                echo "Erro ao atualizar pontos ou provedor não encontrado.";
            }

            // Redirecionar o usuário de volta
            header('Location: coletacoletor.php');
            exit;
        } else {
            echo "Coleta não encontrada.";
        }

    } catch (PDOException $e) {
        echo "Erro ao processar a nota: " . $e->getMessage();
    }

//} else {
//    echo "Parâmetros dadadadad  inválidos.";
}
?>
