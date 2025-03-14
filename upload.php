<?php
// Define upload directory
$uploadDir = 'skins/';

// Create directory if it doesn't exist
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Response array
$response = [
    'success' => false,
    'message' => '',
    'url' => '',
    'id' => '',
    'name' => ''
];

// Handle file upload
if (isset($_FILES['skin']) && $_FILES['skin']['error'] === UPLOAD_ERR_OK) {
    // Get file info
    $tmpName = $_FILES['skin']['tmp_name'];
    $fileName = $_FILES['skin']['name'];
    $fileSize = $_FILES['skin']['size'];
    $fileType = $_FILES['skin']['type'];
    
    // Check if it's a PNG file
    $imageInfo = getimagesize($tmpName);
    if ($imageInfo === false || $imageInfo[2] !== IMAGETYPE_PNG) {
        $response['message'] = 'File must be a valid PNG image.';
        echo json_encode($response);
        exit;
    }
    
    // Check dimensions - must be 64x64 or 64x32
    $width = $imageInfo[0];
    $height = $imageInfo[1];
    if (!(($width === 64 && $height === 64) || ($width === 64 && $height === 32))) {
        $response['message'] = 'Skin dimensions must be 64x64 or 64x32 pixels.';
        echo json_encode($response);
        exit;
    }
    
    // Generate a unique ID for the skin
    $uniqueId = uniqid('skin_');
    
    // Get skin name from POST or use default
    $skinName = isset($_POST['name']) && !empty($_POST['name']) 
                ? htmlspecialchars($_POST['name']) 
                : 'Unnamed Skin';
    
    // Get author name from POST or use default
    $authorName = isset($_POST['author']) && !empty($_POST['author'])
                ? htmlspecialchars($_POST['author'])
                : 'Unknown';
    
    // Get model type (slim or default)
    $skinType = isset($_POST['skinType']) && $_POST['skinType'] === 'slim'
                ? 'slim'
                : 'default';
    
    // Get category (custom, community, or official)
    $category = isset($_POST['category']) && in_array($_POST['category'], ['custom', 'community', 'official'])
                ? $_POST['category']
                : 'custom';
    
    // Create a clean filename
    $cleanName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $skinName);
    $finalFileName = $uniqueId . '_' . $cleanName . '.png';
    $targetFile = $uploadDir . $finalFileName;
    
    // Save the file
    if (move_uploaded_file($tmpName, $targetFile)) {
        // File was saved successfully
        
        // Create metadata for the skin
        $metadata = [
            'id' => $uniqueId,
            'name' => $skinName,
            'author' => $authorName,
            'date' => date('Y-m-d H:i:s'),
            'url' => $targetFile,
            'skinType' => $skinType,
            'category' => $category,
            'width' => $width,
            'height' => $height,
            'description' => isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''
        ];
        
        // Save metadata
        $metadataFile = $uploadDir . 'metadata.json';
        
        // Get existing metadata if file exists
        $existingMetadata = file_exists($metadataFile)
                          ? json_decode(file_get_contents($metadataFile), true)
                          : [];
        
        // Add new metadata
        $existingMetadata[] = $metadata;
        
        // Save updated metadata
        if (file_put_contents($metadataFile, json_encode($existingMetadata))) {
            $response['success'] = true;
            $response['message'] = 'Skin uploaded successfully.';
            $response['url'] = $targetFile;
            $response['id'] = $uniqueId;
            $response['name'] = $skinName;
        } else {
            $response['message'] = 'Skin was saved but metadata could not be updated.';
        }
    } else {
        $response['message'] = 'Failed to save the uploaded file.';
    }
} elseif (isset($_POST['url']) && !empty($_POST['url'])) {
    // Handle URL-based skin
    $url = $_POST['url'];
    
    // Validate URL (simple check)
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        $response['message'] = 'Invalid URL format.';
        echo json_encode($response);
        exit;
    }
    
    // Get skin name from POST or use default
    $skinName = isset($_POST['name']) && !empty($_POST['name']) 
                ? htmlspecialchars($_POST['name']) 
                : 'Unnamed Skin';
                
    // Get author name from POST or use default
    $authorName = isset($_POST['author']) && !empty($_POST['author'])
                ? htmlspecialchars($_POST['author'])
                : 'Unknown';
                
    // Get model type (slim or default)
    $skinType = isset($_POST['skinType']) && $_POST['skinType'] === 'slim'
                ? 'slim'
                : 'default';
                
    // Get category (custom, community, or official)
    $category = isset($_POST['category']) && in_array($_POST['category'], ['custom', 'community', 'official'])
                ? $_POST['category']
                : 'custom';
    
    // Generate a unique ID for the skin
    $uniqueId = uniqid('skin_');
    
    // Create a clean filename
    $cleanName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $skinName);
    $finalFileName = $uniqueId . '_' . $cleanName . '.png';
    $targetFile = $uploadDir . $finalFileName;
    
    // Attempt to download and save the file
    $imageContent = @file_get_contents($url);
    
    if ($imageContent === false) {
        $response['message'] = 'Failed to download image from URL.';
        echo json_encode($response);
        exit;
    }
    
    // Save the downloaded image
    if (file_put_contents($targetFile, $imageContent)) {
        // Check if it's a valid PNG
        $imageInfo = getimagesize($targetFile);
        
        if ($imageInfo === false || $imageInfo[2] !== IMAGETYPE_PNG) {
            // Not a valid PNG, delete it
            unlink($targetFile);
            $response['message'] = 'The URL does not point to a valid PNG image.';
            echo json_encode($response);
            exit;
        }
        
        // Check dimensions
        $width = $imageInfo[0];
        $height = $imageInfo[1];
        
        if (!(($width === 64 && $height === 64) || ($width === 64 && $height === 32))) {
            // Wrong dimensions, delete it
            unlink($targetFile);
            $response['message'] = 'The image dimensions must be 64x64 or 64x32 pixels.';
            echo json_encode($response);
            exit;
        }
        
        // Create metadata
        $metadata = [
            'id' => $uniqueId,
            'name' => $skinName,
            'author' => $authorName,
            'date' => date('Y-m-d H:i:s'),
            'url' => $targetFile,
            'skinType' => $skinType,
            'category' => $category,
            'width' => $width,
            'height' => $height,
            'description' => isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '',
            'source_url' => $url
        ];
        
        // Save metadata
        $metadataFile = $uploadDir . 'metadata.json';
        
        // Get existing metadata if file exists
        $existingMetadata = file_exists($metadataFile)
                          ? json_decode(file_get_contents($metadataFile), true)
                          : [];
        
        // Add new metadata
        $existingMetadata[] = $metadata;
        
        // Save updated metadata
        if (file_put_contents($metadataFile, json_encode($existingMetadata))) {
            $response['success'] = true;
            $response['message'] = 'Skin downloaded and saved successfully.';
            $response['url'] = $targetFile;
            $response['id'] = $uniqueId;
            $response['name'] = $skinName;
        } else {
            $response['message'] = 'Skin was saved but metadata could not be updated.';
        }
    } else {
        $response['message'] = 'Failed to save the downloaded file.';
    }
} else {
    $response['message'] = 'No file was uploaded or URL provided.';
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
