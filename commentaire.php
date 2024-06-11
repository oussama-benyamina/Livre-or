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

// Vérifie si le formulaire de commentaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['commentaire'])) {
    // Récupère le commentaire soumis par l'utilisateur
    $commentaire = $_POST['commentaire'];

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

    // Prépare la requête SQL pour insérer le commentaire dans la base de données
    $stmt = $conn->prepare("INSERT INTO commentaires (utilisateur, commentaire) VALUES (?, ?)");
    $stmt->bind_param("ss", $_SESSION['login'], $commentaire);

    // Exécute la requête SQL
    if ($stmt->execute()) {
        // Redirige l'utilisateur vers la même page pour actualiser l'affichage et afficher le nouveau commentaire
        header("Location: commentaire.php");
        exit();
    } else {
        $error = "Erreur lors de l'enregistrement du commentaire : " . $stmt->error;
    }

    // Ferme la connexion à la base de données
    $stmt->close();
    $conn->close();
}

// Connexion à la base de données pour récupérer les commentaires existants
// Notez que cette partie du code est exécutée même si un commentaire a été soumis
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

// Récupère les anciens commentaires depuis la base de données
$sql = "SELECT * FROM commentaires";
$result = $conn->query($sql);

// Ferme la connexion à la base de données
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page de Commentaire</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Page de Commentaire</h1>
    </header>
    <nav>
        <ul>
            <li><a href="profil.php"> profil</a></li>
            <li><a href="index.php">Déconnexion</a></li>
            <li>
                <a  href="livreor.php"> Livre d'or</a>
            </li>
        </ul>
    </nav>
    <main>
        <form action="commentaire.php" method="post">
            <label for="commentaire">Votre commentaire :</label><br>
            <textarea id="commentaire" name="commentaire" rows="4" cols="50"></textarea><br>
            <input type="submit" value="Envoyer">
        </form>

        <h2>Commentaires</h2>
        <table>
            <thead>
                <tr>
                    <th>Photo de Profil</th>
                    <th>Nom</th>
                    <th>Commentaire</th>
                    <th>Date et Heure</th>
                    <?php if ($isAdmin): ?>
                        <th>Actions</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php
                // Affiche les anciens commentaires dans le tableau
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        // Vous devez remplacer les données par les données de votre base de données
                        echo "<td>Photo</td>";
                        echo "<td>" . $row['utilisateur'] . "</td>";
                        echo "<td>" . $row['commentaire'] . "</td>";
                        echo "<td>" . $row['date_commentaire'] . "</td>";
                        if ($isAdmin) {
                            echo "<td><a href='modifier_commentaire.php?id=" . $row['id'] . "'>Modifier</a> | <a href='supprimer_commentaire.php?id=" . $row['id'] . "'>Supprimer</a></td>";
                        }
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>Aucun commentaire trouvé.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </main>
</body>
</html>
