<?php
require_once '../config/db.php';
include '../includes/header.php';

// Initialiser les variables
$nameErr = $emailErr = $messageErr = "";
$name = $email = $message = "";
$successMessage = "";
$error = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validation
    if (empty($_POST["name"])) {
        $nameErr = "Le nom est requis.";
        $error = true;
    } else {
        $name = test_input($_POST["name"]);
        if (!preg_match("/^[a-zA-Z ]*$/", $name)) {
            $nameErr = "Seulement des lettres et des espaces sont autorisés.";
            $error = true;
        }
    }

    if (empty($_POST["email"])) {
        $emailErr = "L'email est requis.";
        $error = true;
    } else {
        $email = test_input($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = "Format d'email invalide.";
            $error = true;
        }
    }

    if (empty($_POST["message"])) {
        $messageErr = "Le message est requis.";
        $error = true;
    } else {
        $message = test_input($_POST["message"]);
    }

    // Si il n'y a pas d'erreurs, traiter le formulaire
    if (!$error) {
        try {
            // Préparer et exécuter la requête pour insérer les données
            $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $message]);

            // Afficher un message de succès
            $successMessage = "Votre message a été enregistré avec succès!";
            $name = $email = $message = ""; // Réinitialiser les champs

        } catch (PDOException $e) {
            $messageErr = "Erreur lors de l'enregistrement du message : " . $e->getMessage();
        }
    }
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>

<div class="container mt-5">
    <h1>Contactez-nous</h1>
    <p>N'hésitez pas à nous contacter pour toute question ou demande.</p>

    <?php if (!empty($successMessage)): ?>
        <div class="alert alert-success"><?php echo $successMessage; ?></div>
    <?php endif; ?>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" name="contactForm" onsubmit="return validateForm()">
        <div class="mb-3">
            <label for="name" class="form-label">Nom:</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo $name; ?>">
            <span class="text-danger" id="nameError"><?php echo $nameErr; ?></span>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email:</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo $email; ?>">
            <span class="text-danger" id="emailError"><?php echo $emailErr; ?></span>
        </div>

        <div class="mb-3">
            <label for="message" class="form-label">Message:</label>
            <textarea class="form-control" id="message" name="message" rows="5"><?php echo $message; ?></textarea>
            <span class="text-danger" id="messageError"><?php echo $messageErr; ?></span>
        </div>

        <button type="submit" class="btn btn-primary">Envoyer</button>
    </form>
</div>

<script>
function validateForm() {
    let name = document.forms["contactForm"]["name"].value;
    let email = document.forms["contactForm"]["email"].value;
    let message = document.forms["contactForm"]["message"].value;

    let nameError = document.getElementById("nameError");
    let emailError = document.getElementById("emailError");
    let messageError = document.getElementById("messageError");

    nameError.textContent = "";
    emailError.textContent = "";
    messageError.textContent = "";

    let isValid = true;

    if (name == "") {
        nameError.textContent = "Le nom est requis.";
        isValid = false;
    }

    if (email == "") {
        emailError.textContent = "L'email est requis.";
        isValid = false;
    } else {
        //  email validation
        if (!/^\S+@\S+\.\S+$/.test(email)) {
            emailError.textContent = "Format d'email invalide.";
            isValid = false;
        }
    }

    if (message == "") {
        messageError.textContent = "Le message est requis.";
        isValid = false;
    }

    return isValid;
}
</script>
<?php include '../includes/footer.php'; ?>

