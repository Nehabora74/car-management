<?php
/**
 * Helper Functions
 */

require_once __DIR__ . '/database.php';

/**
 * Get all site settings
 */
function getSettings() {
    $db = getDB();
    $stmt = $db->query("SELECT setting_key, setting_value FROM site_settings");
    $settings = [];
    while ($row = $stmt->fetch()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    return $settings;
}

/**
 * Get single setting
 */
function getSetting($key) {
    $db = getDB();
    $stmt = $db->prepare("SELECT setting_value FROM site_settings WHERE setting_key = ?");
    $stmt->execute([$key]);
    $result = $stmt->fetch();
    return $result ? $result['setting_value'] : '';
}

/**
 * Update setting
 */
function updateSetting($key, $value) {
    $db = getDB();
    $stmt = $db->prepare("UPDATE site_settings SET setting_value = ? WHERE setting_key = ?");
    return $stmt->execute([$value, $key]);
}

/**
 * Get menu items
 */
function getMenuItems() {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM menu_items WHERE is_active = 1 ORDER BY sort_order ASC");
    return $stmt->fetchAll();
}

/**
 * Get active banners
 */
function getBanners() {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM banners WHERE is_active = 1 ORDER BY sort_order ASC");
    return $stmt->fetchAll();
}

/**
 * Get cars by type
 */
function getCarsByType($type) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM cars WHERE car_type = ? AND is_active = 1 ORDER BY sort_order ASC");
    $stmt->execute([$type]);
    return $stmt->fetchAll();
}

/**
 * Get all cars
 */
function getAllCars() {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM cars ORDER BY car_type, sort_order ASC");
    return $stmt->fetchAll();
}

/**
 * Upload image
 */
function uploadImage($file, $folder = 'cars') {
    $targetDir = UPLOAD_PATH . $folder . '/';
    
    // Create directory if not exists
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    
    $fileName = time() . '_' . basename($file['name']);
    $targetFile = $targetDir . $fileName;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    
    // Check if image
    $check = getimagesize($file['tmp_name']);
    if ($check === false) {
        return ['success' => false, 'message' => 'File is not an image.'];
    }
    
    // Check file size (5MB max)
    if ($file['size'] > 5000000) {
        return ['success' => false, 'message' => 'File is too large. Max 5MB.'];
    }
    
    // Allow certain formats
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($imageFileType, $allowedTypes)) {
        return ['success' => false, 'message' => 'Only JPG, JPEG, PNG, GIF & WEBP allowed.'];
    }
    
    // Upload
    if (move_uploaded_file($file['tmp_name'], $targetFile)) {
        return ['success' => true, 'path' => 'uploads/' . $folder . '/' . $fileName];
    } else {
        return ['success' => false, 'message' => 'Error uploading file.'];
    }
}

/**
 * Delete image
 */
function deleteImage($path) {
    $fullPath = __DIR__ . '/../' . $path;
    if (file_exists($fullPath) && is_file($fullPath)) {
        return unlink($fullPath);
    }
    return false;
}

/**
 * Sanitize input
 */
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Redirect
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * Flash message
 */
function setFlash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
?>
