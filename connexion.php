<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérifier si les champs requis sont remplis
    if (!empty($_POST['login']) && !empty($_POST['password'])) {
        // Vérifier les informations de connexion
        $conn = new mysqli("localhost", "root", "1020", "livreor");
        if ($conn->connect_error) {
            die("Échec de la connexion : " . $conn->connect_error);
        }

        $login = $conn->real_escape_string($_POST['login']);
        $password = $_POST['password'];
        $sql = "SELECT * FROM utilisateurs WHERE login=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                // Connexion réussie, initialisation de la session
                $_SESSION['login'] = $login;
                $_SESSION['prenom'] = $row['prenom'];
                $_SESSION['nom'] = $row['nom'];
                if ($login === 'admin') {
                    header("Location: admin.php");
                } else {
                    header("Location: profil.php");
                }
                exit();
            } else {
                $error = "Mot de passe incorrect.";
            }
        } else {
            $error = "Utilisateur non trouvé.";
        }
        
        $stmt->close();
        $conn->close();
    } else {
        $error = "Veuillez remplir tous les champs.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Module Connexion</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header class="bg-primary text-white text-center py-4">
        <h1>Connexion</h1>
    </header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Livre d’or</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="inscription.php">Inscription</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <main class="container my-5">
        <!-- Formulaire de connexion -->
        <form action="connexion.php" method="POST" class="bg-light p-4 rounded shadow-sm">
            <div class="mb-3">
                <label for="login" class="form-label">Login :</label>
                <input type="text" id="login" name="login" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe :</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Se connecter</button>
        </form>
        <?php
        if (isset($error)) {
            echo "<p class='text-danger mt-3'>$error</p>";
        }
        ?>
    </main>
    <footer class="bg-dark text-white text-center py-3">
        <p>&copy; 2024 Module Connexion</p>
    </footer>
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>