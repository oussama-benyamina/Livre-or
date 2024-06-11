<?php
session_start();

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
    // Redirige l'utilisateur vers la page de connexion s'il n'est pas connecté
    header("Location: connexion.php");
    exit(); // Assure que le script s'arrête après la redirection
}

// Vérifie si l'ID du commentaire à modifier est passé en paramètre
if (isset($_GET['id'])) {
    $comment_id = $_GET['id'];

    // Connexion à la base de données
    $conn = new mysqli("localhost", "root", "1020", "livreor");
    if ($conn->connect_error) {
        die("Échec de la connexion à la base de données : " . $conn->connect_error);
    }

    // Récupère le commentaire à modifier
    $sql = "SELECT * FROM commentaires WHERE id = $comment_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $commentaire = $row['commentaire'];
    } else {
        echo "Aucun commentaire trouvé avec cet identifiant.";
    }

    $conn->close();
} else {
    echo "Identifiant du commentaire non spécifié.";
}

// Vérifie si le formulaire de modification de commentaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['commentaire'])) {
    $new_commentaire = $_POST['commentaire'];

    // Connexion à la base de données
    $conn = new mysqli("localhost", "root", "1020", "livreor");
    if ($conn->connect_error) {
        die("Échec de la connexion à la base de données : " . $conn->connect_error);
    }

    // Prépare la requête SQL pour mettre à jour le commentaire dans la base de données
    $stmt = $conn->prepare("UPDATE commentaires SET commentaire = ? WHERE id = ?");
    $stmt->bind_param("si", $new_commentaire, $comment_id);

    // Exécute la requête SQL
    if ($stmt->execute()) {
        echo "Le commentaire a été mis à jour avec succès.";
    } else {
        echo "Erreur lors de la mise à jour du commentaire : " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Commentaire</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Modifier Commentaire</h1>
    </header>
    <nav>
        <ul>
            <li><a href="profil.php">Retour au profil</a></li>
            <li><a href="index.php">Déconnexion</a></li>
            <li><a href="commentaire.php">La liste des commenter</a></li>
        </ul>
    </nav>
    <main>
        <form action="modifier_commentaire.php?id=<?php echo $comment_id; ?>" method="post">
            <label for="commentaire">Votre commentaire :</label><br>
            <textarea id="commentaire" name="commentaire" rows="4" cols="50"><?php echo $commentaire; ?></textarea><br>
            <input type="submit" value="Enregistrer">
        </form>
    </main>
</body>
</html>
