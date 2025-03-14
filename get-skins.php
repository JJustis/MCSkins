<?php
// Define upload directory
$uploadDir = 'skins/';
$metadataFile = $uploadDir . 'metadata.json';

// Response array
$response = [
    'success' => false,
    'message' => '',
    'skins' => []
];

// Check if the action parameter is set
$action = isset($_GET['action']) ? $_GET['action'] : 'list';

// Handle different actions
switch ($action) {
    case 'list':
        // Get category filter if specified
        $category = isset($_GET['category']) ? $_GET['category'] : null;
        
        // Get skins list
        if (file_exists($metadataFile)) {
            $metadata = json_decode(file_get_contents($metadataFile), true);
            
            // Filter by category if specified
            if ($category !== null) {
                $metadata = array_filter($metadata, function($skin) use ($category) {
                    return $skin['category'] === $category;
                });
            }
            
            // Sort by date (newest first)
            usort($metadata, function($a, $b) {
                return strtotime($b['date']) - strtotime($a['date']);
            });
            
            $response['success'] = true;
            $response['skins'] = array_values($metadata); // Reset array indices
        } else {
            $response['message'] = 'No skins found.';
        }
        break;
        
    case 'delete':
        // Get skin ID
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        
        if ($id === null) {
            $response['message'] = 'No skin ID provided.';
            break;
        }
        
        // Check if metadata file exists
        if (!file_exists($metadataFile)) {
            $response['message'] = 'No skins found.';
            break;
        }
        
        // Get metadata
        $metadata = json_decode(file_get_contents($metadataFile), true);
        
        // Find the skin to delete
        $skinIndex = null;
        $skinToDelete = null;
        
        foreach ($metadata as $index => $skin) {
            if ($skin['id'] === $id) {
                $skinIndex = $index;
                $skinToDelete = $skin;
                break;
            }
        }
        
        if ($skinIndex === null) {
            $response['message'] = 'Skin not found.';
            break;
        }
        
        // Delete the skin file
        if (file_exists($skinToDelete['url']) && is_file($skinToDelete['url'])) {
            unlink($skinToDelete['url']);
        }
        
        // Remove from metadata
        array_splice($metadata, $skinIndex, 1);
        
        // Save updated metadata
        if (file_put_contents($metadataFile, json_encode($metadata))) {
            $response['success'] = true;
            $response['message'] = 'Skin deleted successfully.';
        } else {
            $response['message'] = 'Failed to update metadata.';
        }
        break;
        
    case 'update':
        // Get skin ID and data
        $id = isset($_POST['id']) ? $_POST['id'] : null;
        
        if ($id === null) {
            $response['message'] = 'No skin ID provided.';
            break;
        }
        
        // Check if metadata file exists
        if (!file_exists($metadataFile)) {
            $response['message'] = 'No skins found.';
            break;
        }
        
        // Get metadata
        $metadata = json_decode(file_get_contents($metadataFile), true);
        
        // Find the skin to update
        $skinIndex = null;
        
        foreach ($metadata as $index => $skin) {
            if ($skin['id'] === $id) {
                $skinIndex = $index;
                break;
            }
        }
        
        if ($skinIndex === null) {
            $response['message'] = 'Skin not found.';
            break;
        }
        
        // Update fields
        if (isset($_POST['name']) && !empty($_POST['name'])) {
            $metadata[$skinIndex]['name'] = htmlspecialchars($_POST['name']);
        }
        
        if (isset($_POST['author']) && !empty($_POST['author'])) {
            $metadata[$skinIndex]['author'] = htmlspecialchars($_POST['author']);
        }
        
        if (isset($_POST['description'])) {
            $metadata[$skinIndex]['description'] = htmlspecialchars($_POST['description']);
        }
        
        if (isset($_POST['category']) && in_array($_POST['category'], ['custom', 'community', 'official'])) {
            $metadata[$skinIndex]['category'] = $_POST['category'];
        }
        
        if (isset($_POST['skinType']) && in_array($_POST['skinType'], ['slim', 'default'])) {
            $metadata[$skinIndex]['skinType'] = $_POST['skinType'];
        }
        
        // Save updated metadata
        if (file_put_contents($metadataFile, json_encode($metadata))) {
            $response['success'] = true;
            $response['message'] = 'Skin updated successfully.';
            $response['skin'] = $metadata[$skinIndex];
        } else {
            $response['message'] = 'Failed to update metadata.';
        }
        break;
        
    case 'submit':
        // Handling submission to community library (changing category)
        $id = isset($_POST['id']) ? $_POST['id'] : null;
        
        if ($id === null) {
            $response['message'] = 'No skin ID provided.';
            break;
        }
        
        // Check if metadata file exists
        if (!file_exists($metadataFile)) {
            $response['message'] = 'No skins found.';
            break;
        }
        
        // Get metadata
        $metadata = json_decode(file_get_contents($metadataFile), true);
        
        // Find the skin to update
        $skinIndex = null;
        
        foreach ($metadata as $index => $skin) {
            if ($skin['id'] === $id) {
                $skinIndex = $index;
                break;
            }
        }
        
        if ($skinIndex === null) {
            $response['message'] = 'Skin not found.';
            break;
        }
        
        // Update category to community
        $metadata[$skinIndex]['category'] = 'community';
        
        // Update other fields if provided
        if (isset($_POST['name']) && !empty($_POST['name'])) {
            $metadata[$skinIndex]['name'] = htmlspecialchars($_POST['name']);
        }
        
        if (isset($_POST['author']) && !empty($_POST['author'])) {
            $metadata[$skinIndex]['author'] = htmlspecialchars($_POST['author']);
        }
        
        if (isset($_POST['description'])) {
            $metadata[$skinIndex]['description'] = htmlspecialchars($_POST['description']);
        }
        
        // Save updated metadata
        if (file_put_contents($metadataFile, json_encode($metadata))) {
            $response['success'] = true;
            $response['message'] = 'Skin submitted to community library successfully.';
            $response['skin'] = $metadata[$skinIndex];
        } else {
            $response['message'] = 'Failed to update metadata.';
        }
        break;
        
    default:
        $response['message'] = 'Invalid action.';
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
