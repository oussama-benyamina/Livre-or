<?php
session_start();

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
    // Redirige l'utilisateur vers la page de connexion s'il n'est pas connecté
    header("Location: connexion.php");
    exit(); // Assure que le script s'arrête après la redirection
}

// Vérifie si l'utilisateur est un administrateur
$isAdmin = $_SESSION['login'] === 'admin';

// Vérifie si l'identifiant du commentaire à supprimer est présent dans la requête
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    // Récupère l'identifiant du commentaire depuis la requête
    $comment_id = $_GET['id'];

    // Vérifie si l'utilisateur a le droit de supprimer des commentaires
    if ($isAdmin) {
        // Vous devrez remplacer ces paramètres avec vos informations de connexion à la base de données
        $servername = "localhost";
        $username = "root";
        $password = "1020";
        $dbname = "livreor";

        // Connexion à la base de données
        $conn = new mysqli($servername, $username, $password, $dbname);
        // Vérifie la connexion
        if ($conn->connect_error) {
            die("Échec de la connexion à la base de données : " . $conn->connect_error);
        }

        // Prépare la requête SQL pour supprimer le commentaire de la base de données
        $stmt = $conn->prepare("DELETE FROM commentaires WHERE id = ?");
        $stmt->bind_param("i", $comment_id);

        // Exécute la requête SQL
        if ($stmt->execute()) {
            // Redirige l'utilisateur vers la page des commentaires après la suppression
            header("Location: commentaire.php");
            exit();
        } else {
            echo "Erreur lors de la suppression du commentaire : " . $stmt->error;
        }

        // Ferme la connexion à la base de données
        $stmt->close();
        $conn->close();
    } else {
        echo "Vous n'avez pas les droits nécessaires pour supprimer des commentaires.";
    }
} else {
    echo "Identifiant de commentaire non spécifié.";
}
?>
