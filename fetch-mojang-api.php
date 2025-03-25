<?php
// Define constants
define('MOJANG_API_UUID', 'https://api.mojang.com/users/profiles/minecraft/');
define('MOJANG_API_PROFILE', 'https://sessionserver.mojang.com/session/minecraft/profile/');
define('UPLOAD_DIR', 'skins/');

// Create directory if it doesn't exist
if (!file_exists(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0777, true);
}

// Response array
$response = [
    'success' => false,
    'message' => '',
    'url' => '',
    'id' => '',
    'name' => '',
    'username' => ''
];

// Get username
$username = isset($_GET['username']) ? trim($_GET['username']) : '';

if (empty($username)) {
    $response['message'] = 'Please provide a Minecraft username.';
    echo json_encode($response);
    exit;
}

// Step 1: Get UUID from username
$uuidUrl = MOJANG_API_UUID . urlencode($username);
$uuidData = @file_get_contents($uuidUrl);

if ($uuidData === false) {
    $response['message'] = 'Failed to connect to Mojang API. Please try again later.';
    echo json_encode($response);
    exit;
}

$uuidJson = json_decode($uuidData, true);

if (empty($uuidJson) || !isset($uuidJson['id'])) {
    $response['message'] = 'Player not found. Check the username and try again.';
    echo json_encode($response);
    exit;
}

$uuid = $uuidJson['id'];
$realUsername = $uuidJson['name']; // Get the correct capitalization

// Step 2: Get profile data (including skin)
$profileUrl = MOJANG_API_PROFILE . $uuid;
$profileData = @file_get_contents($profileUrl);

if ($profileData === false) {
    $response['message'] = 'Failed to retrieve player profile from Mojang.';
    echo json_encode($response);
    exit;
}

$profileJson = json_decode($profileData, true);

if (empty($profileJson) || !isset($profileJson['properties'])) {
    $response['message'] = 'Could not retrieve player skin information.';
    echo json_encode($response);
    exit;
}

// Find the textures property
$texturesBase64 = '';
foreach ($profileJson['properties'] as $property) {
    if ($property['name'] === 'textures') {
        $texturesBase64 = $property['value'];
        break;
    }
}

if (empty($texturesBase64)) {
    $response['message'] = 'No texture information found for this player.';
    echo json_encode($response);
    exit;
}

// Decode the textures data
$texturesJson = json_decode(base64_decode($texturesBase64), true);

if (!isset($texturesJson['textures']['SKIN']['url'])) {
    $response['message'] = 'No skin URL found for this player.';
    echo json_encode($response);
    exit;
}

$skinUrl = $texturesJson['textures']['SKIN']['url'];

// Check if the skin is slim model (Alex)
$isSlim = false;
if (isset($texturesJson['textures']['SKIN']['metadata']['model'])) {
    $isSlim = ($texturesJson['textures']['SKIN']['metadata']['model'] === 'slim');
}

// Generate a unique ID for the skin
$uniqueId = uniqid('mojang_');

// Create a clean filename
$skinName = $realUsername . "'s Skin";
$cleanName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $realUsername);
$finalFileName = $uniqueId . '_' . $cleanName . '.png';
$targetFile = UPLOAD_DIR . $finalFileName;

// Download and save the skin
$skinContent = @file_get_contents($skinUrl);

if ($skinContent === false) {
    $response['message'] = 'Failed to download skin from Mojang servers.';
    echo json_encode($response);
    exit;
}

// Save the skin file
if (file_put_contents($targetFile, $skinContent)) {
    // Check image properties
    $imageInfo = getimagesize($targetFile);
    $width = $imageInfo[0];
    $height = $imageInfo[1];
    
    // Create metadata for the skin
    $metadata = [
        'id' => $uniqueId,
        'name' => $skinName,
        'author' => 'Mojang',
        'date' => date('Y-m-d H:i:s'),
        'url' => $targetFile,
        'skinType' => $isSlim ? 'slim' : 'default',
        'category' => 'custom',
        'width' => $width,
        'height' => $height,
        'description' => 'Official skin of Minecraft player ' . $realUsername,
        'source_url' => $skinUrl,
        'minecraft_username' => $realUsername,
        'mojang_uuid' => $uuid
    ];
    
    // Save metadata
    $metadataFile = UPLOAD_DIR . 'metadata.json';
    
    // Get existing metadata if file exists
    $existingMetadata = file_exists($metadataFile)
                      ? json_decode(file_get_contents($metadataFile), true)
                      : [];
    
    // Add new metadata
    $existingMetadata[] = $metadata;
    
    // Save updated metadata
    if (file_put_contents($metadataFile, json_encode($existingMetadata))) {
        $response['success'] = true;
        $response['message'] = 'Successfully fetched ' . $realUsername . "'s skin!";
        $response['url'] = $targetFile;
        $response['id'] = $uniqueId;
        $response['name'] = $skinName;
        $response['username'] = $realUsername;
        $response['isSlim'] = $isSlim;
    } else {
        $response['message'] = 'Skin was saved but metadata could not be updated.';
    }
} else {
    $response['message'] = 'Failed to save the downloaded skin.';
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>