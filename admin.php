<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
    header("Location: connexion.php");
    exit();
}

// Vérifier si l'utilisateur est admin
$isAdmin = $_SESSION['login'] === 'admin';

$conn = new mysqli("localhost", "root", "1020", "livreor");
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Ajouter un nouvel utilisateur
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_user'])) {
    $login = $conn->real_escape_string($_POST['login']);
    $prenom = $conn->real_escape_string($_POST['prenom']);
    $nom = $conn->real_escape_string($_POST['nom']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO utilisateurs (login, prenom, nom, password) VALUES ('$login', '$prenom', '$nom', '$password')";
    if ($conn->query($sql) === TRUE) {
        echo "Nouvel utilisateur ajouté avec succès";
    } else {
        echo "Erreur : " . $sql . "<br>" . $conn->error;
    }
}

// Mettre à jour un utilisateur
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_user'])) {
    $id = $_POST['id'];
    $login = $conn->real_escape_string($_POST['login']);
    $prenom = $conn->real_escape_string($_POST['prenom']);
    $nom = $conn->real_escape_string($_POST['nom']);
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

    $sql = "UPDATE utilisateurs SET login='$login', prenom='$prenom', nom='$nom'";
    if ($password) {
        $sql .= ", password='$password'";
    }
    $sql .= " WHERE id='$id'";

    if ($conn->query($sql) === TRUE) {
        echo "Utilisateur mis à jour avec succès";
    } else {
        echo "Erreur : " . $sql . "<br>" . $conn->error;
    }
}

// Supprimer un utilisateur
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_user'])) {
    $id = $_POST['id'];

    $sql = "DELETE FROM utilisateurs WHERE id='$id'";
    if ($conn->query($sql) === TRUE) {
        echo "Utilisateur supprimé avec succès";
    } else {
        echo "Erreur : " . $sql . "<br>" . $conn->error;
    }
}

// Récupérer les informations de tous les utilisateurs
$sql = "SELECT * FROM utilisateurs";
$result = $conn->query($sql);
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Module Connexion</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <header class="bg-primary text-white text-center py-4">
        <h1>Administration</h1>
    </header>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Module Connexion</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Déconnexion</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="livreor.php">Livre d'or</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="profil.php">Profil</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <main class="container my-5">
        <?php if ($isAdmin): ?>
            <!-- Ajouter un nouvel utilisateur -->
            <h2>Ajouter un utilisateur</h2>
            <form action="admin.php" method="POST" class="bg-light p-4 rounded shadow-sm mb-5">
                <input type="hidden" name="add_user" value="1">
                <div class="mb-3">
                    <label for="login" class="form-label">Login :</label>
                    <input type="text" id="login" name="login" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="prenom" class="form-label">Prénom :</label>
                    <input type="text" id="prenom" name="prenom" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="nom" class="form-label">Nom :</label>
                    <input type="text" id="nom" name="nom" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Mot de passe :</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Ajouter</button>
            </form>

            <!-- Liste des utilisateurs -->
            <h2>Liste des utilisateurs</h2>
            <table class="table table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Login</th>
                        <th>Prénom</th>
                        <th>Nom</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo isset($row['id']) ? htmlspecialchars($row['id']) : ""; ?></td>
                                <td><?php echo isset($row['login']) ? htmlspecialchars($row['login']) : ""; ?></td>
                                <td><?php echo isset($row['prenom']) ? htmlspecialchars($row['prenom']) : ""; ?></td>
                                <td><?php echo isset($row['nom']) ? htmlspecialchars($row['nom']) : ""; ?></td>
                                <td>
                                    <!-- Formulaire de mise à jour -->
                                    <form action="admin.php" method="POST" class="d-inline-block">
                                        <input type="hidden" name="update_user" value="1">
                                        <input type="hidden" name="id" value="<?php echo isset($row['id']) ? htmlspecialchars($row['id']) : ""; ?>">
                                        <input type="text" name="login" value="<?php echo isset($row['login']) ? htmlspecialchars($row['login']) : ""; ?>" required class="form-control mb-1">
                                        <input type="text" name="prenom" value="<?php echo isset($row['prenom']) ? htmlspecialchars($row['prenom']) : ""; ?>" required class="form-control mb-1">
                                        <input type="text" name="nom" value="<?php echo isset($row['nom']) ? htmlspecialchars($row['nom']) : ""; ?>" required class="form-control mb-1">
                                        <input type="password" name="password" placeholder="Nouveau mot de passe" class="form-control mb-1">
                                        <button type="submit" class="btn btn-success">Mettre à jour</button>
                                    </form>
                                    <!-- Formulaire de suppression -->
                                    <form action="admin.php" method="POST" class="d-inline-block">
                                        <input type="hidden" name="delete_user" value="1">
                                        <input type="hidden" name="id" value="<?php echo isset($row['id']) ? htmlspecialchars($row['id']) : ""; ?>">
                                        <button type="submit" class="btn btn-danger">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">Aucun utilisateur trouvé</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-danger">Vous n'avez pas l'autorisation de voir cette page.</div>
        <?php endif; ?>
    </main>
    <footer class="bg-dark text-white text-center py-3">
        <p>&copy; 2024 Module Connexion</p>
    </footer>
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
