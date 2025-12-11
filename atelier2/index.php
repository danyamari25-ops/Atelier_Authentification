<?php
// Démarrer une session utilisateur qui sera en mesure de pouvoir gérer les Cookies
session_start();

// --- MODIFICATION POUR L'EXERCICE 2 : Vérification du jeton dynamique ---
// 1. Définir le jeton valide attendu par le serveur (récupéré de la session)
$valid_token = isset($_SESSION['active_token']) ? $_SESSION['active_token'] : null;

// 2. Vérifier si le cookie existe ET s'il correspond au jeton actif stocké.
if (isset($_COOKIE['authToken']) && $_COOKIE['authToken'] === $valid_token && $valid_token !== null) {
    header('Location: page_admin.php');
    exit();
}
// --------------------------------------------------------------------------

// Gérer la soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Vérification simple du username et de son password.
    if ($username === 'admin' && $password === 'secret') {
        
        // --- MODIFICATION POUR L'EXERCICE 2 : Génération du jeton dynamique ---
        // Génère un jeton unique (32 caractères hexadécimaux)
        try {
            $new_token = bin2hex(random_bytes(16));
        } catch (Exception $e) {
            $error = "Erreur de génération de jeton : " . $e->getMessage();
        }

        if (!isset($error)) {
            // 1. Initialiser le cookie sur le poste de l'utilisateur avec le nouveau jeton unique.
            //    Expiration de 60 secondes (Exercice 1)
            setcookie('authToken', $new_token, time() + 60, '/', '', false, true); 

            // 2. Stocker ce jeton dans la session pour la vérification future (côté serveur)
            $_SESSION['active_token'] = $new_token;

            header('Location: page_admin.php'); 
            exit();
        }
        // --------------------------------------------------------------------------
        
    } else {
        $error = "Nom d'utilisateur ou mot de passe incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
</head>
<body>
    <h1>Atelier authentification par Cookie</h1>
    <h3>La page <a href="page_admin.php">page_admin.php</a> est inaccéssible tant que vous ne vous serez pas connecté avec le login 'admin' et mot de passe 'secret'</h3>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <form method="POST" action="">
        <label for="username">Nom d'utilisateur :</label>
        <input type="text" id="username" name="username" required>
        <br><br>
        <label for="password">Mot de passe :</label>
        <input type="password" id="password" name="password" required>
        <br><br>
        <button type="submit">Se connecter</button>
    </form>
    <br>
    <a href="../index.html">Retour à l'accueil</a>  
</body>
</html>
