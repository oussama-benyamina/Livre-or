<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
    header("Location: connexion.php");
    exit();
}

// Vérifier si l'utilisateur est admin
$isAdmin = $_SESSION['login'] === 'admin';

// Établir la connexion à la base de données
$conn = new mysqli("localhost", "root", "1020", "livreor");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Récupérer les informations de l'utilisateur
$login = $conn->real_escape_string($_SESSION['login']);
$sql = "SELECT * FROM utilisateurs WHERE login='$login'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    $error = "Utilisateur non trouvé.";
}

// Processus de mise à jour du profil
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérifier si les champs requis sont remplis
    if (!empty($_POST['prenom']) && !empty($_POST['nom'])) {
        $new_prenom = $conn->real_escape_string($_POST['prenom']);
        $new_nom = $conn->real_escape_string($_POST['nom']);

        $sql = "UPDATE utilisateurs SET prenom='$new_prenom', nom='$new_nom'";

        // Vérifier si les mots de passe sont mis à jour
        if (!empty($_POST['new_password']) && !empty($_POST['confirm_password'])) {
            if ($_POST['new_password'] === $_POST['confirm_password']) {
                $hashed_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
                $sql .= ", password='$hashed_password'";
            } else {
                $error = "Les nouveaux mots de passe ne correspondent pas.";
            }
        }

        $sql .= " WHERE login='$login'";
        if ($conn->query($sql) === TRUE) {
            // Mettre à jour les informations de session si nécessaire
            $_SESSION['prenom'] = $new_prenom;
            $_SESSION['nom'] = $new_nom;
            header("Location: profil.php?success=1");
            exit();
        } else {
            $error = "Erreur: " . $conn->error;
        }
    } else {
        $error = "Veuillez remplir tous les champs obligatoires.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - Module Connexion</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header class="bg-primary text-white text-center py-4">
        <h1>Profil <?php echo isset($row['prenom']) ? htmlspecialchars($row['prenom']) : ''; ?></h1>
    </header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Module Connexion</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <?php if ($isAdmin): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="admin.php">La liste des utilisateurs</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="commentaire.php">La liste des commenter</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="livreor.php">Livre d'or</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Déconnexion</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <main class="container my-5">
        <h2>Modifier votre profil</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form action="profil.php" method="POST" class="bg-light p-4 rounded shadow-sm">
            <div class="mb-3
">
                <label for="prenom" class="form-label">Prénom:</label>
                <input type="text" id="prenom" name="prenom" class="form-control" value="<?php echo isset($row['prenom']) ? htmlspecialchars($row['prenom']) : ''; ?>" required>
            </div>
            <div class="mb-3">
                <label for="nom" class="form-label">Nom:</label>
                <input type="text" id="nom" name="nom" class="form-control" value="<?php echo isset($row['nom']) ? htmlspecialchars($row['nom']) : ''; ?>" required>
            </div>
            <div class="mb-3">
                <label for="new_password" class="form-label">Nouveau mot de passe:</label>
                <input type="password" id="new_password" name="new_password" class="form-control">
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirmer le nouveau mot de passe:</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Mettre à jour</button>
        </form>
    </main>
    <footer class="bg-dark text-white text-center py-3">
        <p>&copy; 2024 Module Connexion</p>
    </footer>
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
