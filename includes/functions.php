<?php
// Continuer après vos fonctions existantes...

/**
 * Vérifie si l'image existe dans le dossier assets/img/
 * 
 * @param string $imageName Nom du fichier image
 * @return bool True si l'image existe, false sinon
 */
function imageExists($imageName) {
    if (empty($imageName)) {
        return false;
    }
    
    // Chemin complet vers l'image
    $imagePath = __DIR__ . '/../assets/img/' . $imageName;
    
    // Vérifier si le fichier existe
    return file_exists($imagePath);
}

/**
 * Retourne le chemin de l'image si elle existe, sinon retourne l'image par défaut
 * 
 * @param string $imageName Nom du fichier image
 * @return string Chemin de l'image
 */
function getImagePath($imageName) {
    if (empty($imageName) || !imageExists($imageName)) {
        return 'assets/img/default-product.jpg'; // Image par défaut
    }
    
    return 'assets/img/' . $imageName;
}

/**
 * Valide et traite l'upload d'une image de produit
 * 
 * @param array $uploadedFile Tableau $_FILES['image']
 * @param string|null $oldImageName Nom de l'ancienne image (en cas de modification)
 * @return array Tableau avec les clés success, message et filename
 */
function validateProductImage($uploadedFile, $oldImageName = null) {
    $uploadDir = __DIR__ . '/../assets/img/';
    $result = ['success' => false, 'message' => '', 'filename' => ''];
    
    // Si aucun fichier n'est téléchargé et qu'il s'agit d'une modification
    if (empty($uploadedFile['name']) && $oldImageName) {
        // Conserver l'ancienne image
        return ['success' => true, 'message' => 'Image inchangée', 'filename' => $oldImageName];
    }
    
    // Si aucun fichier n'est téléchargé (nouvel ajout)
    if (empty($uploadedFile['name'])) {
        return ['success' => true, 'message' => 'Image par défaut utilisée', 'filename' => 'default-product.jpg'];
    }
    
    // Vérifier les erreurs de téléchargement
    if ($uploadedFile['error'] !== UPLOAD_ERR_OK) {
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE => 'L\'image dépasse la taille maximale définie dans php.ini',
            UPLOAD_ERR_FORM_SIZE => 'L\'image dépasse la taille maximale définie dans le formulaire',
            UPLOAD_ERR_PARTIAL => 'L\'image n\'a été que partiellement téléchargée',
            UPLOAD_ERR_NO_FILE => 'Aucune image n\'a été téléchargée',
            UPLOAD_ERR_NO_TMP_DIR => 'Dossier temporaire manquant',
            UPLOAD_ERR_CANT_WRITE => 'Échec d\'écriture de l\'image sur le disque',
            UPLOAD_ERR_EXTENSION => 'Une extension PHP a arrêté le téléchargement de l\'image'
        ];
        $errorMessage = isset($errorMessages[$uploadedFile['error']]) 
                        ? $errorMessages[$uploadedFile['error']] 
                        : 'Erreur inconnue lors du téléchargement';
        return ['success' => false, 'message' => $errorMessage, 'filename' => ''];
    }
    
    // Vérifier le type de fichier
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($uploadedFile['type'], $allowedTypes)) {
        return ['success' => false, 'message' => 'Type de fichier non autorisé. Utilisez JPG, PNG, GIF ou WEBP.', 'filename' => ''];
    }
    
    // Vérifier la taille du fichier (5 Mo max)
    if ($uploadedFile['size'] > 5 * 1024 * 1024) {
        return ['success' => false, 'message' => 'L\'image est trop volumineuse (5 Mo maximum)', 'filename' => ''];
    }
    
    // Générer un nom de fichier unique
    $extension = pathinfo($uploadedFile['name'], PATHINFO_EXTENSION);
    $newFileName = uniqid('product_') . '.' . $extension;
    $uploadPath = $uploadDir . $newFileName;
    
    // Vérifier si le dossier de destination existe, sinon le créer
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            return ['success' => false, 'message' => 'Impossible de créer le dossier de destination', 'filename' => ''];
        }
    }
    
    // Déplacer le fichier téléchargé
    if (move_uploaded_file($uploadedFile['tmp_name'], $uploadPath)) {
        // Supprimer l'ancienne image si elle existe et n'est pas l'image par défaut
        if ($oldImageName && $oldImageName !== 'default-product.jpg' && file_exists($uploadDir . $oldImageName)) {
            unlink($uploadDir . $oldImageName);
        }
        
        return ['success' => true, 'message' => 'Image téléchargée avec succès', 'filename' => $newFileName];
    } else {
        return ['success' => false, 'message' => 'Échec du téléchargement de l\'image', 'filename' => ''];
    }
}

/**
 * Récupère le nom de l'image d'un produit par son ID
 * 
 * @param int $productId ID du produit
 * @return string|null Nom de l'image ou null si le produit n'existe pas
 */
function getExistingImageName($productId) {
    global $conn;
    $stmt = $conn->prepare("SELECT image FROM items WHERE id = ?");
    $stmt->execute([$productId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['image'] : null;
}

/**
 * Redimensionne une image si nécessaire
 * Note: Cette fonction nécessite la bibliothèque GD de PHP
 * 
 * @param string $sourcePath Chemin complet vers l'image source
 * @param int $maxWidth Largeur maximale
 * @param int $maxHeight Hauteur maximale
 * @return bool True si le redimensionnement a réussi, false sinon
 */
function resizeImage($sourcePath, $maxWidth = 800, $maxHeight = 800) {
    // Vérifier si GD est installé
    if (!extension_loaded('gd') || !function_exists('imagecreatetruecolor')) {
        return false;
    }
    
    // Obtenir l'extension du fichier
    $extension = strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION));
    
    // Créer une ressource d'image à partir du fichier source
    switch ($extension) {
        case 'jpg':
        case 'jpeg':
            $sourceImage = imagecreatefromjpeg($sourcePath);
            break;
        case 'png':
            $sourceImage = imagecreatefrompng($sourcePath);
            break;
        case 'gif':
            $sourceImage = imagecreatefromgif($sourcePath);
            break;
        case 'webp':
            $sourceImage = imagecreatefromwebp($sourcePath);
            break;
        default:
            return false;
    }
    
    if (!$sourceImage) {
        return false;
    }
    
    // Obtenir les dimensions de l'image source
    $sourceWidth = imagesx($sourceImage);
    $sourceHeight = imagesy($sourceImage);
    
    // Ne redimensionner que si l'image est plus grande que les dimensions maximales
    if ($sourceWidth <= $maxWidth && $sourceHeight <= $maxHeight) {
        imagedestroy($sourceImage);
        return true;
    }
    
    // Calculer les nouvelles dimensions tout en conservant les proportions
    if ($sourceWidth > $sourceHeight) {
        $newWidth = $maxWidth;
        $newHeight = intval($sourceHeight * $maxWidth / $sourceWidth);
    } else {
        $newHeight = $maxHeight;
        $newWidth = intval($sourceWidth * $maxHeight / $sourceHeight);
    }
    
    // Créer une nouvelle image avec les dimensions calculées
    $newImage = imagecreatetruecolor($newWidth, $newHeight);
    
    // Préserver la transparence pour les PNG et GIF
    if ($extension === 'png' || $extension === 'gif') {
        imagecolortransparent($newImage, imagecolorallocatealpha($newImage, 0, 0, 0, 127));
        imagealphablending($newImage, false);
        imagesavealpha($newImage, true);
    }
    
    // Redimensionner l'image
    imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $sourceWidth, $sourceHeight);
    
    // Enregistrer l'image redimensionnée
    $result = false;
    switch ($extension) {
        case 'jpg':
        case 'jpeg':
            $result = imagejpeg($newImage, $sourcePath, 90); // Qualité 90%
            break;
        case 'png':
            $result = imagepng($newImage, $sourcePath, 9); // Compression maximale
            break;
        case 'gif':
            $result = imagegif($newImage, $sourcePath);
            break;
        case 'webp':
            $result = imagewebp($newImage, $sourcePath, 90); // Qualité 90%
            break;
    }
    
    // Libérer la mémoire
    imagedestroy($sourceImage);
    imagedestroy($newImage);
    
    return $result;
}