<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minecraft Skin Previewer</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #2c3e50, #4ca1af);
            min-height: 100vh;
            padding-top: 20px;
            padding-bottom: 20px;
            color: #333;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        h1 {
            color: white;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
            font-weight: 700;
        }
        
        #canvas-container {
            width: 100%;
            height: 500px;
            position: relative;
            margin-top: 20px;
            overflow: hidden;
            border-radius: 8px;
            background-color: #f0f0f0;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        
        canvas {
            display: block;
            width: 100%;
            height: 100%;
        }
        
        .loading {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 18px;
            color: #666;
        }
        
        .card {
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            border: none;
            border-radius: 10px;
            overflow: hidden;
            backdrop-filter: blur(5px);
        }
        
        .card-header {
            background: linear-gradient(45deg, #3498db, #6c5ce7);
            color: white;
            font-weight: 600;
            border-bottom: none;
        }
        
        .card-body {
            background-color: rgba(255, 255, 255, 0.9);
        }
        
        .btn-primary {
            background: linear-gradient(45deg, #3498db, #6c5ce7);
            border: none;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        
        .btn-primary:hover {
            background: linear-gradient(45deg, #2980b9, #5c4bdd);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.15);
        }
        
        .btn-secondary {
            background: linear-gradient(45deg, #95a5a6, #7f8c8d);
            border: none;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        
        .btn-secondary:hover {
            background: linear-gradient(45deg, #7f8c8d, #636e72);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.15);
        }
        
        .btn-success {
            background: linear-gradient(45deg, #2ecc71, #27ae60);
            border: none;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        
        .btn-success:hover {
            background: linear-gradient(45deg, #27ae60, #219653);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.15);
        }
        
        .btn-danger {
            background: linear-gradient(45deg, #e74c3c, #c0392b);
            border: none;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        
        .btn-danger:hover {
            background: linear-gradient(45deg, #c0392b, #a93226);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.15);
        }
        
        .nav-tabs .nav-link {
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 5px 5px 0 0;
            margin-right: 5px;
            transition: all 0.3s ease;
        }
        
        .nav-tabs .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }
        
        .nav-tabs .nav-link.active {
            color: #333;
            background-color: #fff;
            font-weight: 600;
        }
        
        .preset-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        
        .preset-item {
            border: 2px solid transparent;
            border-radius: 10px;
            padding: 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background-color: white;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        
        .preset-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.15);
            border-color: #3498db;
        }
        
        .preset-item.active {
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.3), 0 8px 15px rgba(0,0,0,0.15);
        }
        
        .preset-item img {
            width: 64px;
            height: 64px;
            display: block;
            margin: 0 auto 8px;
            image-rendering: pixelated;
            border-radius: 5px;
        }
        
        .preset-name {
            font-size: 12px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-weight: 600;
        }
        
        .bg-options {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-top: 10px;
        }
        
        .bg-option {
            width: 80px;
            height: 50px;
            border-radius: 8px;
            cursor: pointer;
            border: 2px solid transparent;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        
        .bg-option:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }
        
        .bg-option.active {
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.3), 0 4px 10px rgba(0,0,0,0.1);
        }
        
        .bg-option img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: all 0.3s ease;
        }
        
        .texture-list {
            max-height: 300px;
            overflow-y: auto;
            margin-top: 10px;
            border-radius: 8px;
            box-shadow: inset 0 0 5px rgba(0,0,0,0.1);
            background-color: rgba(255, 255, 255, 0.5);
        }
        
        .texture-item {
            display: flex;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid rgba(0,0,0,0.1);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .texture-item:hover {
            background-color: rgba(52, 152, 219, 0.1);
        }
        
        .texture-item img {
            width: 40px;
            height: 40px;
            margin-right: 15px;
            image-rendering: pixelated;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .texture-item.active {
            background-color: rgba(52, 152, 219, 0.2);
        }
        
        #texture-search {
            margin-bottom: 15px;
            border-radius: 20px;
            padding-left: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        /* Fancy scrollbar */
        .texture-list::-webkit-scrollbar {
            width: 8px;
            background-color: rgba(0,0,0,0.05);
            border-radius: 4px;
        }
        
        .texture-list::-webkit-scrollbar-thumb {
            background: linear-gradient(45deg, #3498db, #6c5ce7);
            border-radius: 4px;
        }
        
        .texture-list::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(45deg, #2980b9, #5c4bdd);
        }
        
        /* Style form elements */
        .form-control {
            border-radius: 8px;
            border: 1px solid rgba(0,0,0,0.1);
            padding: 10px 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .form-control:focus {
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.3);
            border-color: #3498db;
        }
        
        .form-select {
            border-radius: 8px;
            border: 1px solid rgba(0,0,0,0.1);
            padding: 10px 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .form-select:focus {
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.3);
            border-color: #3498db;
        }
        
        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .card {
            animation: fadeIn 0.5s ease-out;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center mb-4">Minecraft Skin Previewer</h1>
        
        <div class="row">
            <div class="col-lg-8">
                <!-- Skin Preview Area -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Skin Preview</h5>
                        <div id="canvas-container">
                            <canvas id="skin-canvas"></canvas>
                            <div class="loading" id="loading-message" style="display: none;">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2">Loading skin...</p>
                            </div>
                        </div>
                        <div id="error-message" class="text-danger mt-2"></div>
                        
                        <div class="d-flex flex-wrap gap-2 mt-3">
                            <button id="rotate-toggle" class="btn btn-primary">
                                <i class="fas fa-sync-alt me-1"></i> Pause Rotation
                            </button>
                            <button id="reset-view" class="btn btn-secondary">
                                <i class="fas fa-redo me-1"></i> Reset View
                            </button>
                            <button id="zoom-in" class="btn btn-secondary">
                                <i class="fas fa-search-plus"></i>
                            </button>
                            <button id="zoom-out" class="btn btn-secondary">
                                <i class="fas fa-search-minus"></i>
                            </button>
                            <button id="download-skin" class="btn btn-success">
                                <i class="fas fa-download me-1"></i> Download Skin
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Background Options -->
                <div class="card mt-3">
                    <div class="card-body">
                        <h5 class="card-title">Background Options</h5>
                        <div class="bg-options" id="bg-options">
                            <!-- Background options will be added here by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <!-- Tabs for Different Input Methods -->
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs" id="inputTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="upload-tab" data-bs-toggle="tab" data-bs-target="#upload" type="button" role="tab" aria-controls="upload" aria-selected="true">Upload</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="url-tab" data-bs-toggle="tab" data-bs-target="#url" type="button" role="tab" aria-controls="url" aria-selected="false">URL</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="library-tab" data-bs-toggle="tab" data-bs-target="#library" type="button" role="tab" aria-controls="library" aria-selected="false">Library</button>
                            </li>
							<li class="nav-item" role="presentation">
    <button class="nav-link" id="mojang-tab" data-bs-toggle="tab" data-bs-target="#mojang" type="button" role="tab" aria-controls="mojang" aria-selected="false">Mojang</button>
</li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="inputTabsContent">
                            <!-- File Upload Tab -->
                            <div class="tab-pane fade show active" id="upload" role="tabpanel" aria-labelledby="upload-tab">
                                <div class="mb-3">
                                    <label for="skin-file" class="form-label">Upload Minecraft Skin (PNG)</label>
                                    <input class="form-control" type="file" id="skin-file" accept="image/png">
                                    <div class="form-text">Select a Minecraft skin PNG file (64×64 or 64×32)</div>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="save-to-library" checked>
                                    <label class="form-check-label" for="save-to-library">
                                        Save to texture library
                                    </label>
                                </div>
                                <div class="mb-3">
                                    <label for="texture-name" class="form-label">Texture Name</label>
                                    <input type="text" class="form-control" id="texture-name" placeholder="Enter a name for this skin">
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="slim-model">
                                    <label class="form-check-label" for="slim-model">
                                        Use slim model (Alex)
                                    </label>
                                </div>
                            </div>
                            
                            <!-- URL Tab -->
                            <div class="tab-pane fade" id="url" role="tabpanel" aria-labelledby="url-tab">
                                <div class="mb-3">
                                    <label for="skin-url" class="form-label">Skin URL</label>
                                    <input type="text" class="form-control" id="skin-url" placeholder="https://example.com/skin.png">
                                    <div class="form-text">Enter a direct URL to a Minecraft skin PNG</div>
                                </div>
                                <button id="load-url" class="btn btn-primary">Load Skin</button>
                                <div class="form-check mb-3 mt-3">
                                    <input class="form-check-input" type="checkbox" id="save-url-to-library" checked>
                                    <label class="form-check-label" for="save-url-to-library">
                                        Save to texture library
                                    </label>
                                </div>
                                <div class="mb-3">
                                    <label for="url-texture-name" class="form-label">Texture Name</label>
                                    <input type="text" class="form-control" id="url-texture-name" placeholder="Enter a name for this skin">
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="url-slim-model">
                                    <label class="form-check-label" for="url-slim-model">
                                        Use slim model (Alex)
                                    </label>
                                </div>
                            </div>
							
							
    <div class="tab-pane fade" id="mojang" role="tabpanel" aria-labelledby="mojang-tab">
    <div class="mb-3">
        <label for="minecraft-username" class="form-label">Minecraft Username</label>
        <div class="input-group">
            <input type="text" class="form-control" id="minecraft-username" placeholder="Enter Minecraft username">
            <button class="btn btn-primary" id="fetch-mojang-skin">
                <i class="fas fa-search me-1"></i> Fetch Skin
            </button>
        </div>
        <div class="form-text">Enter a Minecraft username to fetch their official skin from Mojang servers</div>
    </div>
    
    <div id="mojang-result" class="mb-3 d-none">
        <div class="card border-success">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <span><i class="fas fa-check-circle me-1"></i> Skin found!</span>
                <span class="badge bg-light text-success" id="mojang-save-status">Saved to Library</span>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <img id="mojang-preview" src="" alt="Skin Preview" class="me-3" style="width: 64px; height: 64px; image-rendering: pixelated;">
                    <div>
                        <h5 id="mojang-username" class="card-title mb-0"></h5>
                        <p id="mojang-model-type" class="text-muted mb-0"></p>
                    </div>
                </div>
                <button id="load-mojang-skin" class="btn btn-primary">
                    <i class="fas fa-user me-1"></i> Load This Skin
                </button>
            </div>
        </div>
    </div>
    
    <div id="mojang-error" class="alert alert-danger d-none">
        <i class="fas fa-exclamation-circle me-1"></i>
        <span id="mojang-error-message"></span>
    </div>
    
    <div class="card mt-3">
        <div class="card-header">
            Recently Fetched Players
        </div>
        <div class="card-body">
            <div id="recent-players" class="d-flex flex-wrap gap-2 mt-2">
                <!-- Recent players will be added here by JavaScript -->
                <div class="text-muted" id="no-recent-players">No players fetched yet</div>
            </div>
        </div>
    </div>
    
    <div class="card mt-3">
        <div class="card-header">
            Popular Minecraft Players
        </div>
        <div class="card-body">
            <div class="popular-players d-flex flex-wrap gap-2">
                <button class="btn btn-sm btn-outline-primary popular-player" data-username="Notch">Notch</button>
                <button class="btn btn-sm btn-outline-primary popular-player" data-username="jeb_">jeb_</button>
                <button class="btn btn-sm btn-outline-primary popular-player" data-username="Dinnerbone">Dinnerbone</button>
                <button class="btn btn-sm btn-outline-primary popular-player" data-username="Dream">Dream</button>
                <button class="btn btn-sm btn-outline-primary popular-player" data-username="GeorgeNotFound">GeorgeNotFound</button>
                <button class="btn btn-sm btn-outline-primary popular-player" data-username="Technoblade">Technoblade</button>
                <button class="btn btn-sm btn-outline-primary popular-player" data-username="Ph1LzA">Ph1LzA</button>
                <button class="btn btn-sm btn-outline-primary popular-player" data-username="Sapnap">Sapnap</button>
                <button class="btn btn-sm btn-outline-primary popular-player" data-username="TommyInnit">TommyInnit</button>
                <button class="btn btn-sm btn-outline-primary popular-player" data-username="Tubbo">Tubbo</button>
            </div>
        </div>
    </div>
</div>
                            <!-- Library Tab -->
                            <div class="tab-pane fade" id="library" role="tabpanel" aria-labelledby="library-tab">
                                <div class="mb-3">
                                    <label for="texture-search" class="form-label">Search Textures</label>
                                    <input type="text" class="form-control" id="texture-search" placeholder="Search by name...">
                                </div>
                                <div class="texture-list" id="texture-list">
                                    <!-- Texture items will be added here by JavaScript -->
                                    <div class="alert alert-info" id="no-textures-message">
                                        No textures in your library yet. Upload some skins to get started!
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Texture Submit Card -->
                <div class="card mt-3">
                    <div class="card-header">
                        Submit to Texture Library
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Share your texture</h5>
                        <p class="card-text">Submit your custom skin to the public texture library for others to use.</p>
                        <div class="d-grid gap-2">
                            <button id="submit-texture" class="btn btn-primary">
                                <i class="fas fa-cloud-upload-alt me-1"></i> Submit Current Skin
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Presets Section -->
        <div class="card mt-4">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" id="presetTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="official-tab" data-bs-toggle="tab" data-bs-target="#official" type="button" role="tab" aria-controls="official" aria-selected="true">Official</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="community-tab" data-bs-toggle="tab" data-bs-target="#community" type="button" role="tab" aria-controls="community" aria-selected="false">Community</button>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="presetTabsContent">
                    <div class="tab-pane fade show active" id="official" role="tabpanel" aria-labelledby="official-tab">
                        <h5 class="card-title">Official Minecraft Skins</h5>
                        <div class="preset-grid" id="official-grid">
                            <!-- Official Minecraft skins will be added here by JavaScript -->
                        </div>
                    </div>
                    <div class="tab-pane fade" id="community" role="tabpanel" aria-labelledby="community-tab">
                        <h5 class="card-title">Community Skins</h5>
                        <div class="preset-grid" id="community-grid">
                            <!-- Community skins will be added here by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Save to Library Modal -->
    <div class="modal fade" id="saveModal" tabindex="-1" aria-labelledby="saveModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="saveModalLabel">Save to Texture Library</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="modal-texture-name" class="form-label">Texture Name</label>
                        <input type="text" class="form-control" id="modal-texture-name" placeholder="My Custom Skin">
                    </div>
                    <div class="mb-3">
                        <label for="modal-texture-category" class="form-label">Category</label>
                        <select class="form-select" id="modal-texture-category">
                            <option value="custom">Custom</option>
                            <option value="official">Official</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirm-save">Save</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Submit to Community Modal -->
    <div class="modal fade" id="submitModal" tabindex="-1" aria-labelledby="submitModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="submitModalLabel">Submit to Community Library</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="submit-texture-name" class="form-label">Texture Name</label>
                        <input type="text" class="form-control" id="submit-texture-name" placeholder="My Awesome Skin">
                    </div>
                    <div class="mb-3">
                        <label for="submit-texture-author" class="form-label">Author Name</label>
                        <input type="text" class="form-control" id="submit-texture-author" placeholder="Your Name">
                    </div>
                    <div class="mb-3">
                        <label for="submit-texture-description" class="form-label">Description</label>
                        <textarea class="form-control" id="submit-texture-description" rows="3" placeholder="Describe your skin..."></textarea>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="submit-texture-terms">
                        <label class="form-check-label" for="submit-texture-terms">
                            I agree to share this skin with the community
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirm-submit">Submit</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap, Popper and Three.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
	<script src="skin-editor.js"></script>
	<script src="skin-editor-patch.js"></script>
	<script src="fix-double-canvas.js"></script>
	<script src="comprehensive-editor-fix.js"></script>
	<script src="3d-model-repair.js"></script>
<script src="drawing-preview-fix.js"></script>
<script src="direct-drawing-fix.js"></script>
<script>
// Add this to the bottom of your index.php file before the closing </body> tag

// Load skin-editor.js script
// Add this code to the end of your index.php file, just before the closing </body> tag

// Make sure we have the complete model shown in the editor
document.addEventListener('DOMContentLoaded', function() {
    // Load skin-editor.js script if not already loaded
    if (!document.querySelector('script[src="skin-editor.js"]')) {
        const editorScript = document.createElement('script');
        editorScript.src = 'skin-editor.js';
        document.body.appendChild(editorScript);
    }
    
    // Initialize the editor when the button is clicked
    const editButton = document.getElementById('edit-skin-button');
    if (!editButton) {
        // Create the edit button if it doesn't exist yet
        const controlsContainer = document.querySelector('.d-flex.flex-wrap.gap-2.mt-3');
        if (controlsContainer) {
            const editButton = document.createElement('button');
            editButton.id = 'edit-skin-button';
            editButton.className = 'btn btn-warning';
            editButton.innerHTML = '<i class="fas fa-paint-brush me-1"></i> Edit Skin';
            editButton.onclick = initSkinEditor;
            
            // Insert before the download button
            const downloadButton = document.getElementById('download-skin');
            controlsContainer.insertBefore(editButton, downloadButton);
        }
    } else {
        // If the button already exists, just add the click handler
        editButton.onclick = initSkinEditor;
    }
});

// Helper function to initialize the skin editor
function initSkinEditor() {
    if (!currentSkinTexture) {
        alert('Please load a skin first before editing.');
        return;
    }
    
    // Make sure the SkinEditor object is available
    if (typeof SkinEditor === 'undefined') {
        // If not available, wait for the script to load and retry
        console.log('Waiting for SkinEditor to load...');
        setTimeout(function() {
            if (typeof SkinEditor !== 'undefined') {
                console.log('SkinEditor now available, initializing...');
                prepareSkinEditor();
            } else {
                alert('Failed to load the skin editor. Please refresh the page and try again.');
            }
        }, 1000);
    } else {
        prepareSkinEditor();
    }
}

// Helper function to prepare the skin editor with correct data
function prepareSkinEditor() {
    // Expose necessary variables to the global scope for the editor
    window.currentSkinTexture = currentSkinTexture;
    window.createCharacterModel = createCharacterModel;
    window.detectSkinFormat = detectSkinFormat;
    window.skinType = skinType;
    
    // Define the correct UV mappings if not already defined
    if (typeof uvMappings === 'undefined') {
        window.uvMappings = {
            head: {
                base: {
                    up:    [8, 0, 16, 8],
                    down:  [16, 0, 24, 8],
                    right: [0, 8, 8, 16],
                    left:  [16, 8, 24, 16],
                    front: [8, 8, 16, 16],
                    back:  [24, 8, 32, 16]
                },
                overlay: {
                    up:    [40, 0, 48, 8],
                    down:  [48, 0, 56, 8],
                    right: [32, 8, 40, 16],
                    left:  [48, 8, 56, 16],
                    front: [40, 8, 48, 16],
                    back:  [56, 8, 64, 16]
                }
            },
            body: {
                base: {
                    up:    [20, 16, 28, 20],
                    down:  [28, 16, 36, 20],
                    right: [16, 20, 20, 32],
                    left:  [28, 20, 32, 32],
                    front: [20, 20, 28, 32],
                    back:  [32, 20, 40, 32]
                },
                overlay: {
                    up:    [20, 32, 28, 36],
                    down:  [28, 32, 36, 36],
                    right: [16, 36, 20, 48],
                    left:  [28, 36, 32, 48],
                    front: [20, 36, 28, 48],
                    back:  [32, 36, 40, 48]
                }
            },
            rightArm: {
                base: {
                    up:    [44, 16, 48, 20],
                    down:  [48, 16, 52, 20],
                    right: [40, 20, 44, 32],
                    left:  [48, 20, 52, 32],
                    front: [44, 20, 48, 32],
                    back:  [52, 20, 56, 32]
                },
                overlay: {
                    up:    [44, 32, 48, 36],
                    down:  [48, 32, 52, 36],
                    right: [40, 36, 44, 48],
                    left:  [48, 36, 52, 48],
                    front: [44, 36, 48, 48],
                    back:  [52, 36, 56, 48]
                }
            },
            leftArm: {
                base: {
                    up:    [36, 48, 40, 52],
                    down:  [40, 48, 44, 52],
                    right: [32, 52, 36, 64],
                    left:  [40, 52, 44, 64],
                    front: [36, 52, 40, 64],
                    back:  [44, 52, 48, 64]
                },
                overlay: {
                    up:    [52, 48, 56, 52],
                    down:  [56, 48, 60, 52],
                    right: [48, 52, 52, 64],
                    left:  [56, 52, 60, 64],
                    front: [52, 52, 56, 64],
                    back:  [60, 52, 64, 64]
                }
            },
            rightLeg: {
                base: {
                    up:    [4, 16, 8, 20],
                    down:  [8, 16, 12, 20],
                    right: [0, 20, 4, 32],
                    left:  [8, 20, 12, 32],
                    front: [4, 20, 8, 32],
                    back:  [12, 20, 16, 32]
                },
                overlay: {
                    up:    [4, 32, 8, 36],
                    down:  [8, 32, 12, 36],
                    right: [0, 36, 4, 48],
                    left:  [8, 36, 12, 48],
                    front: [4, 36, 8, 48],
                    back:  [12, 36, 16, 48]
                }
            },
            leftLeg: {
                base: {
                    up:    [20, 48, 24, 52],
                    down:  [24, 48, 28, 52],
                    right: [16, 52, 20, 64],
                    left:  [24, 52, 28, 64],
                    front: [20, 52, 24, 64],
                    back:  [28, 52, 32, 64]
                },
                overlay: {
                    up:    [4, 48, 8, 52],
                    down:  [8, 48, 12, 52],
                    right: [0, 52, 4, 64],
                    left:  [8, 52, 12, 64],
                    front: [4, 52, 8, 64],
                    back:  [12, 52, 16, 64]
                }
            }
        };
    } else {
        window.uvMappings = uvMappings;
    }
    
    // Set up the skin texture for editing
    if (typeof SkinEditor.open === 'function') {
        console.log('Opening skin editor...');
        
        // Create editor UI if it doesn't exist yet
        if (!document.getElementById('skin-editor-container')) {
            createSkinEditorUI();
        }
        
        // Initialize the editor and make sure it's set up correctly
        if (!SkinEditor.isInitialized) {
            SkinEditor.initialize();
            SkinEditor.isInitialized = true;
        }
        
        // Open the editor with the current skin texture
        SkinEditor.open(currentSkinTexture);
    } else {
        console.error('SkinEditor.open is not a function');
        alert('The skin editor could not be initialized properly. Please refresh the page and try again.');
    }
}

// Patch the createEditableCuboid method to ensure all body parts are shown
if (typeof SkinEditor !== 'undefined') {
    const originalCreateEditableCuboid = SkinEditor.createEditableCuboid;
    
    SkinEditor.createEditableCuboid = function(width, height, depth, x, y, z, texture, uvMap, partName, transparent = false) {
        console.log(`Creating editable part: ${partName}`);
        console.log('UV Map:', uvMap);
        
        return originalCreateEditableCuboid.call(
            this, width, height, depth, x, y, z, texture, uvMap, partName, transparent
        );
    };
}

// Patch the createSkinModel method to ensure all body parts are created
if (typeof SkinEditor !== 'undefined') {
    SkinEditor.createSkinModel = function(texture) {
        // Create a group to hold all parts
        this.editorModel = new THREE.Group();
        
        // Get current skin type from global variable
        const currentSkinType = window.skinType || 'default';
        console.log('Creating skin model with type:', currentSkinType);
        
        // Determine the arm width
        const armWidth = currentSkinType === 'slim' ? 3 : 4;
        
        // Create the head
        this.createEditableCuboid(
            8, 8, 8,        // width, height, depth
            0, 28, 0,       // position x, y, z
            texture,        // texture
            window.uvMappings.head.base,  // UV mapping
            'head'          // part name
        );
        
        // HEAD OVERLAY
        this.createEditableCuboid(
            8.5, 8.5, 8.5,  // slightly larger
            0, 28, 0,       // same position
            texture,
            window.uvMappings.head.overlay,
            'head-overlay',
            true            // transparent
        );
        
        // BODY
        this.createEditableCuboid(
            8, 12, 4,
            0, 18, 0,
            texture,
            window.uvMappings.body.base,
            'body'
        );
        
        // BODY OVERLAY
        this.createEditableCuboid(
            8.5, 12.5, 4.5,
            0, 18, 0,
            texture,
            window.uvMappings.body.overlay,
            'body-overlay',
            true
        );
        
        // RIGHT ARM
        this.createEditableCuboid(
            armWidth, 12, 4,
            -(4 + armWidth/2), 18, 0,
            texture,
            window.uvMappings.rightArm.base,
            'right-arm'
        );
        
        // RIGHT ARM OVERLAY
        this.createEditableCuboid(
            armWidth + 0.5, 12.5, 4.5,
            -(4 + armWidth/2), 18, 0,
            texture,
            window.uvMappings.rightArm.overlay,
            'right-arm-overlay',
            true
        );
        
        // LEFT ARM
        this.createEditableCuboid(
            armWidth, 12, 4,
            4 + armWidth/2, 18, 0,
            texture,
            window.uvMappings.leftArm.base,
            'left-arm'
        );
        
        // LEFT ARM OVERLAY
        this.createEditableCuboid(
            armWidth + 0.5, 12.5, 4.5,
            4 + armWidth/2, 18, 0,
            texture,
            window.uvMappings.leftArm.overlay,
            'left-arm-overlay',
            true
        );
        
        // RIGHT LEG
        this.createEditableCuboid(
            4, 12, 4,
            -2, 6, 0,
            texture,
            window.uvMappings.rightLeg.base,
            'right-leg'
        );
        
        // RIGHT LEG OVERLAY
        this.createEditableCuboid(
            4.5, 12.5, 4.5,
            -2, 6, 0,
            texture,
            window.uvMappings.rightLeg.overlay,
            'right-leg-overlay',
            true
        );
        
        // LEFT LEG
        this.createEditableCuboid(
            4, 12, 4,
            2, 6, 0,
            texture,
            window.uvMappings.leftLeg.base,
            'left-leg'
        );
        
        // LEFT LEG OVERLAY
        this.createEditableCuboid(
            4.5, 12.5, 4.5,
            2, 6, 0,
            texture,
            window.uvMappings.leftLeg.overlay,
            'left-leg-overlay',
            true
        );
        
        // Add the model to the scene
        this.scene.add(this.editorModel);
        
        console.log('Editor model created with all body parts');
    };
}

// This ensures the editor works correctly with model patching
window.addEventListener('load', function() {
    // Check if SkinEditor is loaded
    if (typeof SkinEditor !== 'undefined') {
        console.log('SkinEditor found, patching methods...');
        
        // Record the skin editor is properly initialized
        SkinEditor.isInitialized = false;
    }
});
// Standalone Mojang skin fetcher implementation
(function() {
    // DOM elements
    const fetchButton = document.getElementById('fetch-mojang-skin');
    const usernameInput = document.getElementById('minecraft-username');
    const resultContainer = document.getElementById('mojang-result');
    const errorContainer = document.getElementById('mojang-error');
    const errorMessage = document.getElementById('mojang-error-message');
    const usernameDisplay = document.getElementById('mojang-username');
    const skinPreview = document.getElementById('mojang-preview');
    const modelType = document.getElementById('mojang-model-type');
    const loadSkinButton = document.getElementById('load-mojang-skin');
    const popularButtons = document.querySelectorAll('.popular-player');
    
    // Skin data storage
    let currentSkinData = null;
    
    // Function to show loading
    function showLoading(message) {
        const loadingElement = document.getElementById('loading-message');
        if (loadingElement) {
            loadingElement.innerHTML = `
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">${message}</p>
            `;
            loadingElement.style.display = 'block';
        }
    }
    
    // Function to hide loading
    function hideLoading() {
        const loadingElement = document.getElementById('loading-message');
        if (loadingElement) {
            loadingElement.style.display = 'none';
        }
    }
    
    // Function to show error
    function showError(message) {
        if (errorContainer && errorMessage) {
            errorMessage.textContent = message;
            errorContainer.classList.remove('d-none');
        } else {
            alert("Error: " + message);
        }
    }
    
    // Function to hide error
    function hideError() {
        if (errorContainer) {
            errorContainer.classList.add('d-none');
        }
    }
    
    // Function to fetch Mojang skin
    function fetchMojangSkin(username) {
        // Validate username
        if (!username) {
            showError('Please enter a Minecraft username.');
            return;
        }
        
        // Reset UI
        hideError();
        if (resultContainer) {
            resultContainer.classList.add('d-none');
        }
        
        // Show loading
        showLoading('Fetching skin from Mojang...');
        
        // Make API request
        fetch('fetch-mojang-api.php?username=' + encodeURIComponent(username))
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                hideLoading();
                
                if (data.success) {
                    // Store data
                    currentSkinData = data;
                    
                    // Update UI
                    if (usernameDisplay) usernameDisplay.textContent = data.username;
                    if (skinPreview) skinPreview.src = data.url;
                    if (modelType) modelType.textContent = data.isSlim ? 'Slim (Alex) model' : 'Default (Steve) model';
                    
                    // Show result
                    if (resultContainer) resultContainer.classList.remove('d-none');
                } else {
                    showError(data.message || 'Failed to fetch skin from Mojang.');
                }
            })
            .catch(function(error) {
                hideLoading();
                console.error('Error:', error);
                showError('An error occurred while connecting to the server.');
            });
    }
    
    // Function to load the fetched skin
    function loadMojangSkin() {
        if (!currentSkinData) {
            showError('No skin data available. Please fetch a skin first.');
            return;
        }
        
        if (typeof loadSkin === 'function') {
            loadSkin(currentSkinData.url, currentSkinData.isSlim ? 'slim' : 'default');
        } else {
            showError('Skin loading function not available.');
        }
    }
    
    // Set up event listeners
    if (fetchButton && usernameInput) {
        fetchButton.addEventListener('click', function() {
            fetchMojangSkin(usernameInput.value.trim());
        });
        
        usernameInput.addEventListener('keyup', function(event) {
            if (event.key === 'Enter') {
                fetchMojangSkin(this.value.trim());
            }
        });
    }
    
    if (loadSkinButton) {
        loadSkinButton.addEventListener('click', loadMojangSkin);
    }
    
    // Set up popular player buttons
    if (popularButtons.length > 0) {
        popularButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                const username = this.getAttribute('data-username');
                if (usernameInput) usernameInput.value = username;
                fetchMojangSkin(username);
            });
        });
    }
    
    console.log('Mojang skin fetcher initialized');
})();
</script>
    <script>
        // Debounce function to prevent too many renders
        function debounce(func, wait) {
            let timeout;
            return function() {
                const context = this;
                const args = arguments;
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    func.apply(context, args);
                }, wait);
            };
        }
        
        // DOM elements
        const skinCanvas = document.getElementById('skin-canvas');
        const loadingMessage = document.getElementById('loading-message');
        const errorMessage = document.getElementById('error-message');
        const skinFileInput = document.getElementById('skin-file');
        const skinUrlInput = document.getElementById('skin-url');
        const loadUrlBtn = document.getElementById('load-url');
        const rotateToggle = document.getElementById('rotate-toggle');
        const resetViewBtn = document.getElementById('reset-view');
        const zoomInBtn = document.getElementById('zoom-in');
        const zoomOutBtn = document.getElementById('zoom-out');
        const downloadSkinBtn = document.getElementById('download-skin');
        const submitTextureBtn = document.getElementById('submit-texture');
        const confirmSaveBtn = document.getElementById('confirm-save');
        const confirmSubmitBtn = document.getElementById('confirm-submit');
        const textureSearch = document.getElementById('texture-search');
        const textureList = document.getElementById('texture-list');
        const noTexturesMessage = document.getElementById('no-textures-message');
        const slimModelCheckbox = document.getElementById('slim-model');
        const urlSlimModelCheckbox = document.getElementById('url-slim-model');
                let currentMojangData = null;
let recentPlayers = [];
        // Three.js variables
        let scene, camera, renderer;
        let playerModel;
        let isRotating = true;
        let animationId = null;
        let currentBackground = 'default';
        let currentSkinTexture = null;
        let skinType = 'default'; // 'default' or 'slim'
        
        // Define available backgrounds
        const backgrounds = [
            { name: "Default", src: "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNs/Q8AAjcBztbuJZUAAAAASUVORK5CYII=", type: "color", value: "#f0f0f0" },
            { name: "Space", src: "https://i.imgur.com/vhKGrmB.jpg", type: "scene", value: "space" },
            { name: "Castle", src: "https://i.imgur.com/X4yfI8N.jpg", type: "scene", value: "castle" },
            { name: "Forest", src: "https://i.imgur.com/kPsKDrU.jpg", type: "scene", value: "forest" },
            { name: "Nether", src: "https://i.imgur.com/RIHqbOB.jpg", type: "scene", value: "nether" }
        ];
        
        // Define official Minecraft skins
        const officialSkins = [
            { name: "Steve", url: "https://textures.minecraft.net/texture/1a4af718455d4aab528e7a61f86fa25e6a369d1768dcb13f7df319a713eb810b", type: "default" },
            { name: "Alex", url: "https://textures.minecraft.net/texture/63b098967340daac529293c24e04910301b54fca92f3e5a9514fbcf4f5739e", type: "slim" }
        ];
        
        // Texture library (stored in localStorage)
        let textureLibrary = [];


// Function to load and save the fetched skin
function loadMojangSkin() {
    if (!currentSkinData) {
        showError('No skin data available. Please fetch a skin first.');
        return;
    }
    
    // First, load the skin for preview
    if (typeof loadSkin === 'function') {
        loadSkin(currentSkinData.url, currentSkinData.isSlim ? 'slim' : 'default');
        
        // Then make sure it's saved in the texture library
        if (typeof addTexture === 'function') {
            // Create a name for the skin
            const name = currentSkinData.username + "'s Skin";
            
            // Add the skin to the texture library
            addTexture(
                name,
                currentSkinData.url,
                'custom',
                currentSkinData.isSlim ? 'slim' : 'default'
            );
            
            // Show a success message
            if (typeof showMessage === 'function') {
                showMessage('success', `Saved ${currentSkinData.username}'s skin to your library!`);
            } else {
                alert(`Saved ${currentSkinData.username}'s skin to your library!`);
            }
        } else {
            console.warn("addTexture function not available. Skin loaded but not saved to library.");
        }
    } else {
        showError('Skin loading function not available.');
    }
}

// Show Mojang error message
function showMojangError(message) {
    const errorElement = document.getElementById('mojang-error');
    const errorMessageElement = document.getElementById('mojang-error-message');
    
    if (errorElement && errorMessageElement) {
        errorMessageElement.textContent = message;
        errorElement.classList.remove('d-none');
    }
}

// Add to recent players
function addToRecentPlayers(playerData) {
    // Check if player already exists
    const existingIndex = recentPlayers.findIndex(p => p.username.toLowerCase() === playerData.username.toLowerCase());
    
    if (existingIndex !== -1) {
        // Remove existing entry
        recentPlayers.splice(existingIndex, 1);
    }
    
    // Add to the beginning of the array
    recentPlayers.unshift(playerData);
    
    // Keep only the last 10 players
    if (recentPlayers.length > 10) {
        recentPlayers = recentPlayers.slice(0, 10);
    }
    
    // Save to localStorage
    localStorage.setItem('recent-minecraft-players', JSON.stringify(recentPlayers));
    
    // Update UI
    updateRecentPlayersUI();
}

// Load recent players from localStorage
function loadRecentPlayers() {
    const savedPlayers = localStorage.getItem('recent-minecraft-players');
    
    if (savedPlayers) {
        try {
            recentPlayers = JSON.parse(savedPlayers);
            updateRecentPlayersUI();
        } catch (e) {
            console.error('Error parsing recent players:', e);
            recentPlayers = [];
        }
    }
}

// Update recent players UI
function updateRecentPlayersUI() {
    const container = document.getElementById('recent-players');
    const noRecentMessage = document.getElementById('no-recent-players');
    
    if (!container) return;
    
    if (recentPlayers.length === 0) {
        if (noRecentMessage) noRecentMessage.style.display = 'block';
        return;
    }
    
    // Hide no players message
    if (noRecentMessage) noRecentMessage.style.display = 'none';
    
    // Clear existing players except the no players message
    Array.from(container.children).forEach(child => {
        if (child !== noRecentMessage) {
            child.remove();
        }
    });
    
    // Add players
    recentPlayers.forEach(player => {
        const playerEl = document.createElement('div');
        playerEl.className = 'recent-player';
        playerEl.style.cursor = 'pointer';
        playerEl.innerHTML = `
            <img src="${player.url}" alt="${player.username}" 
                title="${player.username} (${player.isSlim ? 'Slim' : 'Default'})" 
                style="width: 40px; height: 40px; image-rendering: pixelated; border-radius: 4px;">
        `;
        
        playerEl.addEventListener('click', () => {
            // Set the username in the input field
            document.getElementById('minecraft-username').value = player.username;
            
            // Trigger the fetch
            fetchMojangSkin(player.username);
        });
        
        container.appendChild(playerEl);
    });
}
function fetchMojangSkin(username) {
    console.log("Fetch Mojang skin function called for:", username);
    
    if (!username) {
        showMojangError('Please enter a Minecraft username.');
        return;
    }
    
    // Show loading indicator
    showLoading('Fetching skin from Mojang...');
    
    // Hide previous results
    document.getElementById('mojang-result').classList.add('d-none');
    document.getElementById('mojang-error').classList.add('d-none');
    
    // Fetch from our proxy API
    fetch('fetch-mojang-api.php?username=' + encodeURIComponent(username))
        .then(response => {
            console.log("API response received", response);
            return response.json();
        })
        .then(data => {
            console.log("API data:", data);
            hideLoading();
            
            if (data.success) {
                // Store the current Mojang data
                currentMojangData = data;
                
                // Update result UI
                document.getElementById('mojang-username').textContent = data.username;
                document.getElementById('mojang-preview').src = data.url;
                document.getElementById('mojang-model-type').textContent = 
                    data.isSlim ? 'Slim (Alex) model' : 'Default (Steve) model';
                
                // Show result UI
                document.getElementById('mojang-result').classList.remove('d-none');
                
                // Add to recent players
                addToRecentPlayers({
                    username: data.username,
                    url: data.url,
                    isSlim: data.isSlim
                });
            } else {
                showMojangError(data.message || 'Failed to fetch skin from Mojang.');
            }
        })
        .catch(error => {
            hideLoading();
            console.error('Error fetching Mojang skin:', error);
            showMojangError('An error occurred while connecting to the server.');
        });
}

// Fixed initMojangTab function
function initMojangTab() {
    console.log("Initializing Mojang tab");
    
    // Fetch button
    const fetchMojangBtn = document.getElementById('fetch-mojang-skin');
    const minecraftUsernameInput = document.getElementById('minecraft-username');
    
    if (fetchMojangBtn && minecraftUsernameInput) {
        console.log("Found fetch button and input field");
        
        fetchMojangBtn.addEventListener('click', function() {
            console.log("Fetch button clicked");
            const username = minecraftUsernameInput.value.trim();
            fetchMojangSkin(username);
        });
        
        // Also trigger on Enter key
        minecraftUsernameInput.addEventListener('keyup', function(event) {
            if (event.key === 'Enter') {
                console.log("Enter key pressed in username field");
                const username = this.value.trim();
                fetchMojangSkin(username);
            }
        });
    } else {
        console.warn("Could not find fetch button or input field");
    }
    
    // Load Mojang skin button
    const loadMojangSkinBtn = document.getElementById('load-mojang-skin');
    if (loadMojangSkinBtn) {
        loadMojangSkinBtn.addEventListener('click', loadMojangSkin);
    } else {
        console.warn("Could not find load Mojang skin button");
    }
    
    // Popular player buttons
    const popularPlayerBtns = document.querySelectorAll('.popular-player');
    popularPlayerBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const username = this.getAttribute('data-username');
            console.log("Popular player clicked:", username);
            minecraftUsernameInput.value = username;
            fetchMojangSkin(username);
        });
    });
    
    // Load recent players from localStorage
    loadRecentPlayers();
}

// Fixed Show Mojang Error function
function showMojangError(message) {
    console.log("Showing Mojang error:", message);
    const errorElement = document.getElementById('mojang-error');
    const errorMessageElement = document.getElementById('mojang-error-message');
    
    if (errorElement && errorMessageElement) {
        errorMessageElement.textContent = message;
        errorElement.classList.remove('d-none');
    } else {
        console.warn("Could not find error elements");
        alert("Error: " + message);
    }
}

// Add this to your init function or replace the existing call
function init() {
    // Initialize variables
    currentSkinId = null;
    currentMojangData = null;
    recentPlayers = [];
    textureLibrary = [];
    
    // Initialize UI components
    initScene();
    initBackgrounds();
    initPresets();
    initEventListeners();
    initCape();
    initMojangTab(); // Make sure this is called
    addSkinFormatControls();
    addVotingStyles();
    
    // Fetch skins from server
    fetchSkins();
    
    // Start with Steve skin
    loadSkin(officialSkins[0].url, officialSkins[0].type);
    
    // Initialize skin format detection
    updateFormatInfo();
    
    console.log("Initialization complete");
}




        // Load texture library from localStorage
        function loadTextureLibrary() {
            const savedTextures = localStorage.getItem('minecraft-textures');
            if (savedTextures) {
                textureLibrary = JSON.parse(savedTextures);
                updateTextureList();
            }
        }
        
        // Save texture library to localStorage
        function saveTextureLibrary() {
            localStorage.setItem('minecraft-textures', JSON.stringify(textureLibrary));
            updateTextureList();
        }
        
        // Update the texture list in the UI
        function updateTextureList() {
            const searchTerm = textureSearch ? textureSearch.value.toLowerCase() : '';
            const filteredTextures = textureLibrary.filter(texture => 
                texture.name.toLowerCase().includes(searchTerm)
            );
            
            textureList.innerHTML = '';
            
if (filteredTextures.length === 0) {
                noTexturesMessage.style.display = 'block';
            } else {
                noTexturesMessage.style.display = 'none';
                
                filteredTextures.forEach(texture => {
                    const textureItem = document.createElement('div');
                    textureItem.className = 'texture-item';
                    textureItem.innerHTML = `
                        <img src="${texture.url}" alt="${texture.name}">
                        <div class="flex-grow-1">
                            <div>${texture.name}</div>
                            <small class="text-muted">${texture.category || 'Custom'} ${texture.skinType === 'slim' ? '(Slim)' : ''}</small>
                        </div>
                        <button class="btn btn-sm btn-danger delete-texture" data-id="${texture.id}">
                            <i class="fas fa-trash"></i>
                        </button>
                    `;
                    
                    textureItem.addEventListener('click', function(e) {
                        // Don't trigger if clicking the delete button
                        if (!e.target.closest('.delete-texture')) {
                            loadSkin(texture.url, texture.skinType || 'default');
                            
                            // Mark as active
                            document.querySelectorAll('.texture-item').forEach(item => {
                                item.classList.remove('active');
                            });
                            textureItem.classList.add('active');
                        }
                    });
                    
                    // Add delete functionality
                    const deleteBtn = textureItem.querySelector('.delete-texture');
                    deleteBtn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        const textureId = this.getAttribute('data-id');
                        deleteTexture(textureId);
                    });
                    
                    textureList.appendChild(textureItem);
                });
            }
        }
        
        // Add a texture to the library
        function addTexture(name, url, category = 'custom', skinType = 'default') {
            const newTexture = {
                id: Date.now().toString(),
                name: name || `Texture ${textureLibrary.length + 1}`,
                url: url,
                category: category,
                skinType: skinType,
                dateAdded: new Date().toISOString()
            };
            
            textureLibrary.push(newTexture);
            saveTextureLibrary();
            updateCommunityPresets();
            
            return newTexture;
        }
        
        // Delete a texture from the library
        function deleteTexture(id) {
            if (confirm('Are you sure you want to delete this texture?')) {
                textureLibrary = textureLibrary.filter(texture => texture.id !== id);
                saveTextureLibrary();
                updateCommunityPresets();
            }
        }
        
        // Initialize the 3D scene
        function initScene() {
            // Create scene
            scene = new THREE.Scene();
            scene.background = new THREE.Color(0xf0f0f0);
            
            // Create camera
            camera = new THREE.PerspectiveCamera(
                50,
                skinCanvas.clientWidth / skinCanvas.clientHeight,
                0.1,
                1000
            );
            camera.position.z = 100;
            camera.position.y = 25;
            
            // Create renderer
            renderer = new THREE.WebGLRenderer({
                canvas: skinCanvas,
                antialias: true
            });
            renderer.setSize(skinCanvas.clientWidth, skinCanvas.clientHeight);
            
            // Add lights
            const ambientLight = new THREE.AmbientLight(0xffffff, 0.6);
            scene.add(ambientLight);
            
            const directionalLight = new THREE.DirectionalLight(0xffffff, 0.8);
            directionalLight.position.set(1, 1, 1);
            scene.add(directionalLight);
            
            // Handle window resize
            window.addEventListener('resize', () => {
                const width = skinCanvas.clientWidth;
                const height = skinCanvas.clientHeight;
                renderer.setSize(width, height);
                camera.aspect = width / height;
                camera.updateProjectionMatrix();
            });
            
            // Start animation loop
            animate();
        }
        
        // Animation loop
        function animate() {
            animationId = requestAnimationFrame(animate);
            
            if (playerModel && isRotating) {
                playerModel.rotation.y += 0.01;
            }
            
            renderer.render(scene, camera);
        }
        
        // Initialize background options
        function initBackgrounds() {
            const bgOptions = document.getElementById('bg-options');
            bgOptions.innerHTML = '';
            
            backgrounds.forEach(bg => {
                const div = document.createElement('div');
                div.className = 'bg-option' + (bg.value === currentBackground ? ' active' : '');
                div.innerHTML = `<img src="${bg.src}" alt="${bg.name}" title="${bg.name}">`;
                div.setAttribute('data-value', bg.value);
                div.setAttribute('data-type', bg.type);
                
                div.addEventListener('click', () => {
                    document.querySelectorAll('.bg-option').forEach(opt => {
                        opt.classList.remove('active');
                    });
                    div.classList.add('active');
                    setBackground(bg.type, bg.value);
                });
                
                bgOptions.appendChild(div);
            });
        }
        
        // Initialize preset skins
        function initPresets() {
            // Official Minecraft skins
            initPresetGrid('official-grid', officialSkins);
            
            // Community skins (dynamically loaded from library)
            updateCommunityPresets();
        }
        
        // Initialize a preset grid
        function initPresetGrid(gridId, skins) {
            const grid = document.getElementById(gridId);
            grid.innerHTML = '';
            
            skins.forEach(skin => {
                const div = document.createElement('div');
                div.className = 'preset-item';
                div.innerHTML = `
                    <img src="${skin.url}" alt="${skin.name}">
                    <div class="preset-name">${skin.name}</div>
                `;
                div.addEventListener('click', () => {
                    // Remove active class from all items in this grid
                    grid.querySelectorAll('.preset-item').forEach(item => {
                        item.classList.remove('active');
                    });
                    
                    // Add active class to clicked item
                    div.classList.add('active');
                    
                    // Load the skin
                    loadSkin(skin.url, skin.type || 'default');
                });
                grid.appendChild(div);
            });
        }
        
        // Update community presets from the library
        function updateCommunityPresets() {
            const communityGrid = document.getElementById('community-grid');
            communityGrid.innerHTML = '';
            
            const communitySkins = textureLibrary.filter(texture => 
                texture.category === 'custom' || texture.category === 'community'
            );
            
            if (communitySkins.length === 0) {
                communityGrid.innerHTML = `
                    <div class="alert alert-info w-100 text-center">
                        No community skins available yet. Upload your own to get started!
                    </div>
                `;
                return;
            }
            
            communitySkins.forEach(skin => {
                const div = document.createElement('div');
                div.className = 'preset-item';
                div.innerHTML = `
                    <img src="${skin.url}" alt="${skin.name}">
                    <div class="preset-name">${skin.name}</div>
                `;
                div.addEventListener('click', () => {
                    // Remove active class from all items in this grid
                    communityGrid.querySelectorAll('.preset-item').forEach(item => {
                        item.classList.remove('active');
                    });
                    
                    // Add active class to clicked item
                    div.classList.add('active');
                    
                    // Load the skin
                    loadSkin(skin.url, skin.skinType || 'default');
                });
                communityGrid.appendChild(div);
            });
        }
        
        // Initialize event listeners
        function initEventListeners() {
            // File upload
            skinFileInput.addEventListener('change', handleFileUpload);
            
            // URL input
            loadUrlBtn.addEventListener('click', handleUrlInput);
            
            // Texture search
            textureSearch.addEventListener('input', debounce(() => updateTextureList(), 300));
            
            // Rotation toggle
            rotateToggle.addEventListener('click', () => {
                isRotating = !isRotating;
                rotateToggle.innerHTML = isRotating ? 
                    '<i class="fas fa-sync-alt me-1"></i> Pause Rotation' : 
                    '<i class="fas fa-play me-1"></i> Resume Rotation';
            });
            
            // Reset view
            resetViewBtn.addEventListener('click', resetView);
            
            // Zoom controls
            zoomInBtn.addEventListener('click', zoomIn);
            zoomOutBtn.addEventListener('click', zoomOut);
            
            // Download skin
            downloadSkinBtn.addEventListener('click', downloadSkin);
            
            // Submit texture button
            submitTextureBtn.addEventListener('click', () => {
                if (!currentSkinTexture) {
                    alert('Please load a skin first before submitting.');
                    return;
                }
                
                // Show the submit modal
                const submitModal = new bootstrap.Modal(document.getElementById('submitModal'));
                submitModal.show();
            });
            
            // Confirm save
            confirmSaveBtn.addEventListener('click', () => {
                const name = document.getElementById('modal-texture-name').value;
                const category = document.getElementById('modal-texture-category').value;
                
                if (!currentSkinTexture) {
                    alert('No skin loaded to save');
                    return;
                }
                
                addTexture(name, currentSkinTexture.image.src, category, skinType);
                
                // Hide the modal
                bootstrap.Modal.getInstance(document.getElementById('saveModal')).hide();
            });
            
            // Confirm submit
            confirmSubmitBtn.addEventListener('click', () => {
                const name = document.getElementById('submit-texture-name').value;
                const author = document.getElementById('submit-texture-author').value;
                const description = document.getElementById('submit-texture-description').value;
                const termsAgreed = document.getElementById('submit-texture-terms').checked;
                
                if (!name || !author || !termsAgreed) {
                    alert('Please fill in all required fields and agree to the terms.');
                    return;
                }
                
                if (!currentSkinTexture) {
                    alert('No skin loaded to submit');
                    return;
                }
                
                // In a real implementation, this would send the data to a server
                // For now, we'll just add it to the local library
                const texture = addTexture(name, currentSkinTexture.image.src, 'community', skinType);
                
                alert('Your skin has been submitted to the community library! In a real implementation, this would be reviewed before being publicly available.');
                
                // Hide the modal
                bootstrap.Modal.getInstance(document.getElementById('submitModal')).hide();
            });
        }
        
        // Handle file upload
        function handleFileUpload(event) {
            const file = event.target.files[0];
            if (!file) return;
            
            if (!file.type.match('image/png')) {
                errorMessage.textContent = 'Please select a PNG image.';
                return;
            }
            
            const reader = new FileReader();
            reader.onload = (e) => {
                // Get the skin type
                const usedSkinType = slimModelCheckbox.checked ? 'slim' : 'default';
                
                loadSkin(e.target.result, usedSkinType);
                
                // Check if we should save to library
                if (document.getElementById('save-to-library').checked) {
                    const name = document.getElementById('texture-name').value || file.name.replace('.png', '');
                    addTexture(name, e.target.result, 'custom', usedSkinType);
                }
            };
            reader.onerror = () => {
                errorMessage.textContent = 'Failed to read file.';
            };
            reader.readAsDataURL(file);
        }
        
        // Handle URL input
        function handleUrlInput() {
            const url = skinUrlInput.value.trim();
            if (!url) {
                errorMessage.textContent = 'Please enter a skin URL.';
                return;
            }
            
            // Get the skin type
            const usedSkinType = urlSlimModelCheckbox.checked ? 'slim' : 'default';
            
            loadSkin(url, usedSkinType);
            
            // Check if we should save to library
            if (document.getElementById('save-url-to-library').checked) {
                const name = document.getElementById('url-texture-name').value || 'URL Skin';
                addTexture(name, url, 'custom', usedSkinType);
            }
        }
        
        // Load skin from URL
        function loadSkin(url, modelType = 'default') {
            // Show loading message
            loadingMessage.style.display = 'block';
            errorMessage.textContent = '';
            
            // Update current skin type
            skinType = modelType;
            
            // Load the texture
            const textureLoader = new THREE.TextureLoader();
            textureLoader.crossOrigin = 'anonymous';
            textureLoader.load(
                url,
                (texture) => {
                    // Create the 3D model
                    texture.magFilter = THREE.NearestFilter;
                    texture.minFilter = THREE.NearestFilter;
                    
                    currentSkinTexture = texture;
                    createCharacterModel(texture);
                    loadingMessage.style.display = 'none';
                },
                undefined,
                (error) => {
                    console.error('Error loading texture:', error);
                    errorMessage.textContent = 'Failed to load skin. Make sure it\'s a valid PNG image with correct CORS settings.';
                    loadingMessage.style.display = 'none';
                }
            );
        }
        
// Add this code to your existing JavaScript in index2.php

// Additional variables for skin format handling
let skinFormat = 'auto'; // 'auto', 'classic', 'modern'
let autoDetectedFormat = 'modern'; // Will store the detected format when using 'auto'


// Create a skin format section in the UI
function addSkinFormatControls() {
    // Create the skin format control section
    const formatSection = document.createElement('div');
    formatSection.className = 'card mt-3';
    formatSection.innerHTML = `
        <div class="card-header">
            Skin Format Settings
        </div>
        <div class="card-body">
            <h5 class="card-title">Model Type</h5>
            <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="skin-model" id="model-default" checked>
                <label class="form-check-label" for="model-default">
                    Default (Steve) - 4px Arms
                </label>
            </div>
            <div class="form-check mb-3">
                <input class="form-check-input" type="radio" name="skin-model" id="model-slim">
                <label class="form-check-label" for="model-slim">
                    Slim (Alex) - 3px Arms
                </label>
            </div>
            
            <h5 class="card-title">Skin Format</h5>
            <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="skin-format" id="format-auto" checked>
                <label class="form-check-label" for="format-auto">
                    Auto-detect Format
                    <span id="detected-format" class="badge bg-secondary ms-2">Modern (64×64)</span>
                </label>
            </div>
            <div class="form-check mb-2">
                <input class="form-check-input" type="radio" name="skin-format" id="format-modern">
                <label class="form-check-label" for="format-modern">
                    Modern Format (64×64)
                    <small class="form-text text-muted d-block">Supports overlays, 3D elements</small>
                </label>
            </div>
            <div class="form-check mb-3">
                <input class="form-check-input" type="radio" name="skin-format" id="format-classic">
                <label class="form-check-label" for="format-classic">
                    Classic Format (64×32)
                    <small class="form-text text-muted d-block">Legacy format, no overlays</small>
                </label>
            </div>
            
            <div class="alert alert-info" id="format-info">
                <i class="fas fa-info-circle me-2"></i>
                <span>Using auto-detected format: Modern (64×64)</span>
            </div>

            <button id="apply-format" class="btn btn-primary">
                <i class="fas fa-sync me-1"></i> Apply Changes
            </button>
        </div>
    `;
    
    // Find where to insert the format section - right after the background options
    const targetElement = document.querySelector('.card.mt-3');
    targetElement.parentNode.insertBefore(formatSection, targetElement.nextSibling);
    
    // Add event listeners for format controls
    document.getElementById('model-default').addEventListener('change', function() {
        if (this.checked) {
            skinType = 'default';
            updateFormatInfo();
        }
    });
    
    document.getElementById('model-slim').addEventListener('change', function() {
        if (this.checked) {
            skinType = 'slim';
            updateFormatInfo();
        }
    });
    
    document.getElementById('format-auto').addEventListener('change', function() {
        if (this.checked) {
            skinFormat = 'auto';
            updateFormatInfo();
        }
    });
    
    document.getElementById('format-modern').addEventListener('change', function() {
        if (this.checked) {
            skinFormat = 'modern';
            updateFormatInfo();
        }
    });
    
    document.getElementById('format-classic').addEventListener('change', function() {
        if (this.checked) {
            skinFormat = 'classic';
            updateFormatInfo();
        }
    });
    
    document.getElementById('apply-format').addEventListener('click', function() {
        if (currentSkinTexture) {
            // Reload the character model with the selected format settings
            createCharacterModel(currentSkinTexture);
        } else {
            alert('Please load a skin first');
        }
    });
}

// Update format info display
function updateFormatInfo() {
    const infoElement = document.getElementById('format-info');
    
    // Get actual dimensions if available
    let dimensionsText = '';
    if (currentSkinTexture) {
        const width = currentSkinTexture.image.width;
        const height = currentSkinTexture.image.height;
        dimensionsText = ` (Actual size: ${width}×${height})`;
    }
    
    if (skinFormat === 'auto') {
        infoElement.innerHTML = `
            <i class="fas fa-info-circle me-2"></i>
            <span>Using auto-detected format: ${autoDetectedFormat === 'modern' ? 'Modern (64×64)' : 'Classic (64×32)'}${dimensionsText}</span>
        `;
        document.getElementById('detected-format').textContent = 
            autoDetectedFormat === 'modern' ? 'Modern (64×64)' : 'Classic (64×32)';
    } else {
        infoElement.innerHTML = `
            <i class="fas fa-info-circle me-2"></i>
            <span>Using ${skinFormat === 'modern' ? 'Modern (64×64)' : 'Classic (64×32)'} format with 
            ${skinType === 'slim' ? 'slim (Alex)' : 'default (Steve)'} model${dimensionsText}</span>
        `;
    }
}

// This is the corrected createBoxWithTex function that properly maps textures to the 3D model


// Improved skin format detection
function detectSkinFormat(skinTexture) {
    const imageWidth = skinTexture.image.width;
    const imageHeight = skinTexture.image.height;
    
    // Check dimensions
    if (imageWidth === 64 && imageHeight === 64) {
        autoDetectedFormat = 'modern';
        console.log('Detected modern 64x64 skin format');
        return 'modern';
    } else if (imageWidth === 64 && imageHeight === 32) {
        autoDetectedFormat = 'classic';
        console.log('Detected classic 64x32 skin format');
        return 'classic';
    } else {
        // Handle invalid dimensions - most likely not a valid Minecraft skin
        console.warn(`Unexpected skin dimensions: ${imageWidth}x${imageHeight}, defaulting to modern format`);
        autoDetectedFormat = 'modern';
        return 'modern';
    }
}

// This function focuses on fixing the modern skin format while preserving the classic skin rendering

function createCharacterModel(skinTexture) {
    // Remove previous model if it exists
    if (playerModel) {
        scene.remove(playerModel);
    }
    
    // Create a group to hold all parts
    playerModel = new THREE.Group();
    
    // Detect skin format
    const detectedFormat = detectSkinFormat(skinTexture);
    
    // Determine which format to use for rendering
    const renderFormat = (skinFormat === 'auto') ? detectedFormat : skinFormat;
    console.log("Using render format:", renderFormat);
    
    // Get arm width based on skinType
    const armWidth = skinType === 'slim' ? 3 : 4;
    
    // Define UV Mappings - focus on fixing the modern format
    const uvMappings = {
        head: {
        base: {
            up:    [8, 0, 16, 8],
            down:  [16, 0, 24, 8],
            right: [0, 8, 8, 16],
            left:  [16, 8, 24, 16],
            front: [8, 8, 16, 16],
            back:  [24, 8, 32, 16]
        },
        overlay: {
            up:    [40, 0, 48, 8],
            down:  [48, 0, 56, 8],
            right: [32, 8, 40, 16],
            left:  [48, 8, 56, 16],
            front: [40, 8, 48, 16],
            back:  [56, 8, 64, 16]
        }
    },
        body: {
            base: {
                up:    [20, 16, 28, 20],
                down:  [28, 16, 36, 20],
                right: [16, 20, 20, 32],
                left:  [28, 20, 32, 32],
                front: [20, 20, 28, 32],
                back:  [32, 20, 40, 32]
            },
            overlay: renderFormat === 'modern' ? {
                up:    [20, 32, 28, 36],
                down:  [28, 32, 36, 36],
                right: [16, 36, 20, 48],
                left:  [28, 36, 32, 48],
                front: [20, 36, 28, 48],
                back:  [32, 36, 40, 48]
            } : null
        },
        rightArm: {
            base: skinType === 'slim' ? {
                up:    [44, 16, 47, 20],
                down:  [47, 16, 50, 20],
                right: [40, 20, 44, 32],
                left:  [47, 20, 51, 32],
                front: [44, 20, 47, 32],
                back:  [51, 20, 54, 32]
            } : {
                up:    [44, 16, 48, 20],
                down:  [48, 16, 52, 20],
                right: [40, 20, 44, 32],
                left:  [48, 20, 52, 32],
                front: [44, 20, 48, 32],
                back:  [52, 20, 56, 32]
            },
            overlay: renderFormat === 'modern' ? (skinType === 'slim' ? {
                up:    [44, 32, 47, 36],
                down:  [47, 32, 50, 36],
                right: [40, 36, 44, 48],
                left:  [47, 36, 51, 48],
                front: [44, 36, 47, 48],
                back:  [51, 36, 54, 48]
            } : {
                up:    [44, 32, 48, 36],
                down:  [48, 32, 52, 36],
                right: [40, 36, 44, 48],
                left:  [48, 36, 52, 48],
                front: [44, 36, 48, 48],
                back:  [52, 36, 56, 48]
            }) : null
        },
        leftArm: {
            base: renderFormat === 'modern' ? (skinType === 'slim' ? {
                up:    [36, 48, 39, 52],
                down:  [39, 48, 42, 52],
                right: [32, 52, 36, 64],
                left:  [39, 52, 43, 64],
                front: [36, 52, 39, 64],
                back:  [43, 52, 46, 64]
            } : {
                up:    [36, 48, 40, 52],
                down:  [40, 48, 44, 52],
                right: [32, 52, 36, 64],
                left:  [40, 52, 44, 64],
                front: [36, 52, 40, 64],
                back:  [44, 52, 48, 64]
            }) : {
                // For classic skins, mirror the right arm
                up:    skinType === 'slim' ? [44, 16, 47, 20] : [44, 16, 48, 20],
                down:  skinType === 'slim' ? [47, 16, 50, 20] : [48, 16, 52, 20],
                right: skinType === 'slim' ? [40, 20, 44, 32] : [40, 20, 44, 32],
                left:  skinType === 'slim' ? [47, 20, 51, 32] : [48, 20, 52, 32],
                front: skinType === 'slim' ? [44, 20, 47, 32] : [44, 20, 48, 32],
                back:  skinType === 'slim' ? [51, 20, 54, 32] : [52, 20, 56, 32]
            },
            overlay: renderFormat === 'modern' ? (skinType === 'slim' ? {
                up:    [52, 48, 55, 52],
                down:  [55, 48, 58, 52],
                right: [48, 52, 52, 64],
                left:  [55, 52, 59, 64],
                front: [52, 52, 55, 64],
                back:  [59, 52, 62, 64]
            } : {
                up:    [52, 48, 56, 52],
                down:  [56, 48, 60, 52],
                right: [48, 52, 52, 64],
                left:  [56, 52, 60, 64],
                front: [52, 52, 56, 64],
                back:  [60, 52, 64, 64]
            }) : null
        },
        rightLeg: {
            base: {
                up:    [4, 16, 8, 20],
                down:  [8, 16, 12, 20],
                right: [0, 20, 4, 32],
                left:  [8, 20, 12, 32],
                front: [4, 20, 8, 32],
                back:  [12, 20, 16, 32]
            },
            overlay: renderFormat === 'modern' ? {
                up:    [4, 32, 8, 36],
                down:  [8, 32, 12, 36],
                right: [0, 36, 4, 48],
                left:  [8, 36, 12, 48],
                front: [4, 36, 8, 48],
                back:  [12, 36, 16, 48]
            } : null
        },
        leftLeg: {
            base: renderFormat === 'modern' ? {
                up:    [20, 48, 24, 52],
                down:  [24, 48, 28, 52],
                right: [16, 52, 20, 64],
                left:  [24, 52, 28, 64],
                front: [20, 52, 24, 64],
                back:  [28, 52, 32, 64]
            } : {
                // For classic skins, mirror the right leg
                up:    [4, 16, 8, 20],
                down:  [8, 16, 12, 20],
                right: [0, 20, 4, 32],
                left:  [8, 20, 12, 32],
                front: [4, 20, 8, 32],
                back:  [12, 20, 16, 32]
            },
            overlay: renderFormat === 'modern' ? {
                up:    [4, 48, 8, 52],
                down:  [8, 48, 12, 52],
                right: [0, 52, 4, 64],
                left:  [8, 52, 12, 64],
                front: [4, 52, 8, 64],
                back:  [12, 52, 16, 64]
            } : null
        }
    };
    
    // HEAD
    createBoxWithTex(playerModel, 8, 8, 8, 0, 28, 0, skinTexture, uvMappings.head.base);
    
    // HEAD OVERLAY (hat layer)
    createBoxWithTex(playerModel, 9, 9, 9, 0, 28, 0, skinTexture, uvMappings.head.overlay, true);
    
    // BODY
    createBoxWithTex(playerModel, 8, 12, 4, 0, 18, 0, skinTexture, uvMappings.body.base);
    
    // BODY OVERLAY (jacket layer)
    if (uvMappings.body.overlay && renderFormat === 'modern') {
        createBoxWithTex(playerModel, 8.5, 12.5, 4.5, 0, 18, 0, skinTexture, uvMappings.body.overlay, true);
    }
    
    // RIGHT ARM
    createBoxWithTex(
        playerModel, 
        armWidth, 12, 4, 
        -(4 + armWidth/2), 18, 0, 
        skinTexture, 
        uvMappings.rightArm.base
    );
    
    // RIGHT ARM OVERLAY
    if (uvMappings.rightArm.overlay && renderFormat === 'modern') {
        createBoxWithTex(
            playerModel, 
            armWidth + 0.5, 12.5, 4.5, 
            -(4 + armWidth/2), 18, 0, 
            skinTexture, 
            uvMappings.rightArm.overlay, 
            true
        );
    }
    
    // LEFT ARM
    createBoxWithTex(
        playerModel, 
        armWidth, 12, 4, 
        4 + armWidth/2, 18, 0, 
        skinTexture, 
        uvMappings.leftArm.base
    );
    
    // LEFT ARM OVERLAY
    if (uvMappings.leftArm.overlay && renderFormat === 'modern') {
        createBoxWithTex(
            playerModel, 
            armWidth + 0.5, 12.5, 4.5, 
            4 + armWidth/2, 18, 0, 
            skinTexture, 
            uvMappings.leftArm.overlay, 
            true
        );
    }
    
    // RIGHT LEG
    createBoxWithTex(playerModel, 4, 12, 4, -2, 6, 0, skinTexture, uvMappings.rightLeg.base);
    
    // RIGHT LEG OVERLAY
    if (uvMappings.rightLeg.overlay && renderFormat === 'modern') {
        createBoxWithTex(playerModel, 4.5, 12.5, 4.5, -2, 6, 0, skinTexture, uvMappings.rightLeg.overlay, true);
    }
    
    // LEFT LEG
    createBoxWithTex(playerModel, 4, 12, 4, 2, 6, 0, skinTexture, uvMappings.leftLeg.base);
    
    // LEFT LEG OVERLAY
    if (uvMappings.leftLeg.overlay && renderFormat === 'modern') {
        createBoxWithTex(playerModel, 4.5, 12.5, 4.5, 2, 6, 0, skinTexture, uvMappings.leftLeg.overlay, true);
    }
    
    // Add cape if enabled
    if (document.getElementById('show-cape') && document.getElementById('show-cape').checked) {
        addCape(playerModel);
    }
    
    // Add the complete model to the scene
    scene.add(playerModel);
    
    // Reset error message
    errorMessage.textContent = '';
    
    // Center camera on the model
    resetView();
}

// Helper function for UV mapping debug (optional but helpful)
function debugUVMapping(skinTexture, uvMap, part) {
    console.log(`UV Mapping for ${part}:`);
    Object.keys(uvMap).forEach(face => {
        console.log(`  ${face}: [${uvMap[face].join(', ')}]`);
    });
}

// Improved createBoxWithTex function that fixes texture mapping issues
function createBoxWithTex(parent, width, height, depth, x, y, z, skinTexture, uvMap, transparent = false) {
    // Create a box geometry
    const geometry = new THREE.BoxGeometry(width, height, depth);
    
    // Get texture dimensions
    const textureWidth = skinTexture.image.width;
    const textureHeight = skinTexture.image.height;
    
    // Create materials array for the 6 faces
    const materials = [];
    
    // Face order in THREE.js: right, left, top, bottom, front, back
    const faceIndices = ['right', 'left', 'up', 'down', 'front', 'back'];
    
    for (let i = 0; i < 6; i++) {
        const faceName = faceIndices[i];
        const faceUV = uvMap[faceName];
        
        if (!faceUV) {
            console.warn(`Missing UV mapping for face: ${faceName}`);
            materials.push(new THREE.MeshBasicMaterial({ color: 0xff00ff, transparent: true, opacity: 0.5 }));
            continue;
        }
        
        // Create a clone of the texture for this face
        const faceTexture = skinTexture.clone();
        faceTexture.needsUpdate = true;
        
        // Extract UV coordinates
        const [x1, y1, x2, y2] = faceUV;
        
        // Convert pixel coordinates to normalized UV coordinates (0-1)
        const u1 = x1 / textureWidth;
        const v1 = 1 - y2 / textureHeight;  // Flip Y-coordinate
        const u2 = x2 / textureWidth;
        const v2 = 1 - y1 / textureHeight;  // Flip Y-coordinate
        
        // Set texture mapping
        faceTexture.offset.set(u1, v1);
        faceTexture.repeat.set(u2 - u1, v2 - v1);
        
        // Create material with the mapped texture
        const material = new THREE.MeshLambertMaterial({
            map: faceTexture,
            transparent: transparent,
            alphaTest: 0.5,  // Use alpha test to avoid rendering artifacts
            side: THREE.FrontSide
        });
        
        materials.push(material);
    }
    
    // Create the mesh with all materials
    const mesh = new THREE.Mesh(geometry, materials);
    mesh.position.set(x, y, z);
    parent.add(mesh);
    
    return mesh;
}


// Modify the loadSkin function to handle format detection
function loadSkin(url, modelType = 'default') {
    // Show loading message
    loadingMessage.style.display = 'block';
    errorMessage.textContent = '';
    
    // Update current skin type
    skinType = modelType;
    
    // Update the model type radio buttons
    if (document.getElementById('model-default') && document.getElementById('model-slim')) {
        document.getElementById('model-default').checked = (skinType === 'default');
        document.getElementById('model-slim').checked = (skinType === 'slim');
    }
    
    // Load the texture
    const textureLoader = new THREE.TextureLoader();
    textureLoader.crossOrigin = 'anonymous';
    textureLoader.load(
        url,
        (texture) => {
            // Create the 3D model
            texture.magFilter = THREE.NearestFilter;
            texture.minFilter = THREE.NearestFilter;
            
            currentSkinTexture = texture;
            
            // Detect skin format
            detectSkinFormat(texture);
            updateFormatInfo();
            
            // Create character model
            createCharacterModel(texture);
            loadingMessage.style.display = 'none';
        },
        undefined,
        (error) => {
            console.error('Error loading texture:', error);
            errorMessage.textContent = 'Failed to load skin. Make sure it\'s a valid PNG image with correct CORS settings.';
            loadingMessage.style.display = 'none';
        }
    );
}




        
        // Add cape to the player model
        function addCape(playerModel) {
            // Check if cape texture is available (either use current texture or a separate cape texture)
            if (!currentCapeTexture) {
                // No cape texture available, try to use default cape
                if (defaultCapeTexture) {
                    currentCapeTexture = defaultCapeTexture;
                } else {
                    // Try to load a default cape
                    loadDefaultCape();
                    return; // We'll add the cape when the texture loads
                }
            }
            
            // Create cape
            const capeWidth = 10;
            const capeHeight = 16;
            const capeDepth = 1;
            
            // Cape is a simple plane with the cape texture
            const capeGeometry = new THREE.PlaneGeometry(capeWidth, capeHeight);
            
            // Create material with the cape texture
            const capeMaterial = new THREE.MeshLambertMaterial({
                map: currentCapeTexture,
                side: THREE.DoubleSide,
                transparent: true,
                alphaTest: 0.5
            });
            
            const cape = new THREE.Mesh(capeGeometry, capeMaterial);
            
            // Position cape behind the player
            cape.position.set(0, 20, -3);
            
            // Make the cape swing slightly
            cape.rotation.x = Math.PI * 0.1; // Tilt forward slightly
            
            playerModel.add(cape);
            
            // Animate cape swinging
            const animateCape = () => {
                if (!cape) return;
                
                // Swing cape gently
                cape.rotation.x = Math.PI * 0.1 + Math.sin(Date.now() / 1000) * 0.05;
                
                requestAnimationFrame(animateCape);
            };
            
            animateCape();
        }
        
        // Load default cape texture
        function loadDefaultCape() {
            const textureLoader = new THREE.TextureLoader();
            textureLoader.crossOrigin = 'anonymous';
            
            // Default cape URL - you can change this to any cape texture
            const defaultCapeUrl = 'https://i.imgur.com/XxmVGmR.png'; // Example Minecraft cape
            
            textureLoader.load(
                defaultCapeUrl,
                (texture) => {
                    texture.magFilter = THREE.NearestFilter;
                    texture.minFilter = THREE.NearestFilter;
                    
                    defaultCapeTexture = texture;
                    currentCapeTexture = texture;
                    
                    // If player model exists, add cape
                    if (playerModel) {
                        addCape(playerModel);
                    }
                },
                undefined,
                (error) => {
                    console.error('Error loading cape texture:', error);
                }
            );
        }
        
        // Load a custom cape texture
        function loadCape(url) {
            const textureLoader = new THREE.TextureLoader();
            textureLoader.crossOrigin = 'anonymous';
            
            textureLoader.load(
                url,
                (texture) => {
                    texture.magFilter = THREE.NearestFilter;
                    texture.minFilter = THREE.NearestFilter;
                    
                    currentCapeTexture = texture;
                    
                    // Reload character model to update cape
                    if (currentSkinTexture) {
                        createCharacterModel(currentSkinTexture);
                    }
                },
                undefined,
                (error) => {
                    console.error('Error loading cape texture:', error);
                    errorMessage.textContent = 'Failed to load cape texture.';
                }
            );
        }
        
      

        // Add UI elements for cape functionality
        function addCapeUI() {
            // Add cape section to the UI
            const capeSection = document.createElement('div');
            capeSection.className = 'card mt-3';
            capeSection.innerHTML = `
                <div class="card-header">
                    Cape Settings
                </div>
                <div class="card-body">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="show-cape" checked>
                        <label class="form-check-label" for="show-cape">
                            Show Cape
                        </label>
                    </div>
                    
                    <div class="mb-3">
                        <label for="cape-url" class="form-label">Cape URL</label>
                        <input type="text" class="form-control" id="cape-url" placeholder="https://example.com/cape.png">
                    </div>
                    
                    <button id="load-cape" class="btn btn-primary">Load Cape</button>
                    
                    <div class="mt-3">
                        <label class="form-label">Select Preset Cape</label>
                        <div class="preset-grid" id="cape-grid">
                            <!-- Cape presets will be added here by JavaScript -->
                        </div>
                    </div>
                </div>
            `;
            
            // Find where to insert the cape section
            const targetElement = document.querySelector('.card.mt-4'); // The presets section
            targetElement.parentNode.insertBefore(capeSection, targetElement);
            
            // Add event listeners for cape controls
            document.getElementById('show-cape').addEventListener('change', function() {
                if (playerModel) {
                    // Find cape in player model
                    playerModel.traverse(child => {
                        if (child instanceof THREE.Mesh && child.material && 
                            child.position.z < -2) { // Identify cape by position
                            child.visible = this.checked;
                        }
                    });
                }
            });
            
            document.getElementById('load-cape').addEventListener('click', function() {
                const capeUrl = document.getElementById('cape-url').value.trim();
                if (capeUrl) {
                    loadCape(capeUrl);
                }
            });
            
// Set up cape presets
const capePresets = [
    { name: "Minecon 2011", url: "https://education.minecraft.net/wp-content/uploads/Minecon2011_Cape.png" },
    { name: "Minecon 2012", url: "https://education.minecraft.net/wp-content/uploads/Minecon2012_Cape.png" },
    { name: "Minecon 2013", url: "https://education.minecraft.net/wp-content/uploads/Minecon2013_Cape.png" },
    { name: "Mojang", url: "https://education.minecraft.net/wp-content/uploads/Mojang_Cape.png" }
];
            
            const capeGrid = document.getElementById('cape-grid');
            capePresets.forEach(cape => {
                const div = document.createElement('div');
                div.className = 'preset-item';
                div.innerHTML = `
                    <img src="${cape.url}" alt="${cape.name}">
                    <div class="preset-name">${cape.name}</div>
                `;
                div.addEventListener('click', () => {
                    // Remove active class from all items
                    capeGrid.querySelectorAll('.preset-item').forEach(item => {
                        item.classList.remove('active');
                    });
                    
                    // Add active class to clicked item
                    div.classList.add('active');
                    
                    // Load the cape
                    loadCape(cape.url);
                });
                capeGrid.appendChild(div);
            });
        }

        // Initialize cape functionality
        function initCape() {
            // Initialize cape variables
            currentCapeTexture = null;
            defaultCapeTexture = null;
            
            // Load default cape
            loadDefaultCape();
            
            // Add cape UI
            addCapeUI();
        }


        
        // Helper function to create a box with textures for each face
       // Debugging function to log UV mapping details
function debugUVMapping(skinTexture, uvMap) {
    const textureWidth = skinTexture.image.width;
    const textureHeight = skinTexture.image.height;
    
    console.log('Texture Dimensions:', textureWidth, 'x', textureHeight);
    
    // Log each UV mapping
    Object.keys(uvMap).forEach(face => {
        const [x1, y1, x2, y2] = uvMap[face];
        console.log(`${face} UV:`, 
            `Pixels: [${x1}, ${y1}, ${x2}, ${y2}]`, 
            `Normalized: [${x1/textureWidth}, ${y1/textureHeight}, ${x2/textureWidth}, ${y2/textureHeight}]`
        );
    });
}


        // Helper function to extract a section of the texture
        function getTextureSection(texture, x1, y1, x2, y2) {
            const canvas = document.createElement('canvas');
            const width = x2 - x1;
            const height = y2 - y1;
            canvas.width = width;
            canvas.height = height;
            
            const ctx = canvas.getContext('2d');
            ctx.drawImage(
                texture.image,
                x1, y1, width, height,
                0, 0, width, height
            );
            
            return ctx.getImageData(0, 0, width, height);
        }
        
        // Helper function to check if an image data has non-transparent pixels
        function hasNonTransparentPixels(imageData) {
            const data = imageData.data;
            for (let i = 3; i < data.length; i += 4) {
                if (data[i] > 0) { // Alpha channel is not zero
                    return true;
                }
            }
            return false;
        }
        
        // Helper function to create a material with proper UV mapping
        function createMaterial(skinTexture, uvMapping, transparent = false) {
            const textureWidth = skinTexture.image.width;
            const textureHeight = skinTexture.image.height;
            
            // Create a new texture for this specific part
            const partTexture = skinTexture.clone();
            partTexture.needsUpdate = true;
            
            // Adjust material settings
            const material = new THREE.MeshLambertMaterial({
                map: partTexture,
                transparent: transparent,
                alphaTest: 0.1, // Helps avoid rendering artifacts
                side: THREE.FrontSide
            });
            
            // Create materials for each side of the cube
            const materials = [];
            
            for (let i = 0; i < 6; i++) {
                // Clone the base material
                const faceMaterial = material.clone();
                faceMaterial.map = partTexture.clone();
                faceMaterial.map.needsUpdate = true;
                
                // Set up UVs for this face
                const [x1, y1, x2, y2] = uvMapping[i];
                
                // Calculate UV coordinates (from 0 to 1)
                const u1 = x2 / textureWidth;
                const v1 = 1 - y1 / textureHeight;
                const u2 = x1 / textureWidth;
                const v2 = 1 - y2 / textureHeight;
                
                // Set mapping
                faceMaterial.map.offset.set(u2, v2);
                faceMaterial.map.repeat.set(u1 - u2, v1 - v2);
                
                materials.push(faceMaterial);
            }
            
            return materials;
        }
        
        // Helper to map skin texture to cube faces
        function mapSkinToFaces(geometry, uvMapping, skinTexture) {
            const textureWidth = skinTexture.image.width;
            const textureHeight = skinTexture.image.height;
            
            let uvs = geometry.attributes.uv;
            
            for (let i = 0; i < 6; i++) {
                const [x1, y1, x2, y2] = uvMapping[i];
                
                // Convert pixel coordinates to UV coordinates (0-1)
                const u1 = x1 / textureWidth;
                const v1 = 1 - y1 / textureHeight;
                const u2 = x2 / textureWidth;
                const v2 = 1 - y2 / textureHeight;
                
                // Update UVs for current face (4 vertices per face)
                const idx = i * 4;
                
                // UV coordinates for each vertex of the face
                // THREE.js has a different winding order than what we need
                // so we have to set the coordinates in a specific order
                uvs.setXY(idx, u1, v2);     // Bottom left
                uvs.setXY(idx + 1, u2, v2); // Bottom right
                uvs.setXY(idx + 2, u1, v1); // Top left
                uvs.setXY(idx + 3, u2, v1); // Top right
            }
        }
        
        // Set background
        function setBackground(type, value) {
            currentBackground = value;
            
            if (type === 'color') {
                scene.background = new THREE.Color(value);
            } else if (type === 'scene') {
                createSceneBackground(value);
            }
        }
        
        // Create scene background
        function createSceneBackground(sceneType) {
            // Clear any existing background objects except for our main models
            const objectsToRemove = [];
            scene.traverse(object => {
                if (object !== playerModel && 
                    object.type !== 'AmbientLight' && object.type !== 'DirectionalLight') {
                    objectsToRemove.push(object);
                }
            });
            
            objectsToRemove.forEach(object => {
                scene.remove(object);
            });
            
            // Set initial background color
            scene.background = new THREE.Color(0x000000);
            
            if (sceneType === 'space') {
                // Create space background
                const starCount = 1000;
                const starGeometry = new THREE.BufferGeometry();
                const starPositions = new Float32Array(starCount * 3);
                
                for (let i = 0; i < starCount * 3; i += 3) {
                    starPositions[i] = (Math.random() - 0.5) * 1000;
                    starPositions[i + 1] = (Math.random() - 0.5) * 1000;
                    starPositions[i + 2] = (Math.random() - 0.5) * 1000;
                }
                
                starGeometry.setAttribute('position', new THREE.BufferAttribute(starPositions, 3));
                
                const starMaterial = new THREE.PointsMaterial({
                    color: 0xFFFFFF,
                    size: 1,
                    sizeAttenuation: false
                });
                
                const stars = new THREE.Points(starGeometry, starMaterial);
                scene.add(stars);
                
            } else if (sceneType === 'castle') {
                // Create castle background
                scene.background = new THREE.Color(0x87CEEB); // Sky blue
                
                // Ground
                const groundGeometry = new THREE.PlaneGeometry(1000, 1000);
                const groundMaterial = new THREE.MeshLambertMaterial({ color: 0x888888 });
                const ground = new THREE.Mesh(groundGeometry, groundMaterial);
                ground.rotation.x = -Math.PI / 2;
                ground.position.y = -20;
                scene.add(ground);
                
                // Simple castle walls
                const wallMaterial = new THREE.MeshLambertMaterial({ color: 0xAAAAAA });
                
                // Back wall
                const backWall = new THREE.Mesh(
                    new THREE.BoxGeometry(100, 40, 5),
                    wallMaterial
                );
                backWall.position.set(0, 0, -50);
                scene.add(backWall);
                
                // Side walls
                const leftWall = new THREE.Mesh(
                    new THREE.BoxGeometry(5, 40, 100),
                    wallMaterial
                );
                leftWall.position.set(-50, 0, 0);
                scene.add(leftWall);
                
                const rightWall = new THREE.Mesh(
                    new THREE.BoxGeometry(5, 40, 100),
                    wallMaterial
                );
                rightWall.position.set(50, 0, 0);
                scene.add(rightWall);
                
            } else if (sceneType === 'forest') {
                // Create forest background
                scene.background = new THREE.Color(0x87CEEB); // Sky blue
                
                // Ground
                const groundGeometry = new THREE.PlaneGeometry(1000, 1000);
                const groundMaterial = new THREE.MeshLambertMaterial({ color: 0x77AA77 });
                const ground = new THREE.Mesh(groundGeometry, groundMaterial);
                ground.rotation.x = -Math.PI / 2;
                ground.position.y = -20;
                scene.add(ground);
                
                // Add trees
                for (let i = 0; i < 20; i++) {
                    const tree = createTree();
                    tree.position.set(
                        (Math.random() - 0.5) * 200,
                        -20,
                        (Math.random() - 0.5) * 200 - 50
                    );
                    scene.add(tree);
                }
                
            } else if (sceneType === 'nether') {
                // Create nether background
                scene.background = new THREE.Color(0x330000);
                
                // Ground
                const groundGeometry = new THREE.PlaneGeometry(1000, 1000);
                const groundMaterial = new THREE.MeshLambertMaterial({ color: 0x993333 });
                const ground = new THREE.Mesh(groundGeometry, groundMaterial);
                ground.rotation.x = -Math.PI / 2;
                ground.position.y = -20;
                scene.add(ground);
                
                // Add lava particles
                const particleCount = 100;
                const particleGeometry = new THREE.BufferGeometry();
                const particlePositions = new Float32Array(particleCount * 3);
                
                for (let i = 0; i < particleCount * 3; i += 3) {
                    particlePositions[i] = (Math.random() - 0.5) * 200;
                    particlePositions[i + 1] = Math.random() * 100 - 20;
                    particlePositions[i + 2] = (Math.random() - 0.5) * 200;
                }
                
                particleGeometry.setAttribute('position', new THREE.BufferAttribute(particlePositions, 3));
                
                const particleMaterial = new THREE.PointsMaterial({
                    color: 0xFF5500,
                    size: 2,
                    sizeAttenuation: true
                });
                
                const particles = new THREE.Points(particleGeometry, particleMaterial);
                scene.add(particles);
            }
        }
        
        // Create a simple tree for forest scene
        function createTree() {
            const tree = new THREE.Group();
            
            // Trunk
            const trunkGeometry = new THREE.CylinderGeometry(1, 1.5, 20, 8);
            const trunkMaterial = new THREE.MeshLambertMaterial({ color: 0x8B4513 });
            const trunk = new THREE.Mesh(trunkGeometry, trunkMaterial);
            trunk.position.y = 10;
            tree.add(trunk);
            
            
// Leaves
            const leavesGeometry = new THREE.ConeGeometry(10, 25, 8);
            const leavesMaterial = new THREE.MeshLambertMaterial({ color: 0x228B22 });
            const leaves = new THREE.Mesh(leavesGeometry, leavesMaterial);
            leaves.position.y = 25;
            tree.add(leaves);
            
            return tree;
        }
        
        // Reset the camera view
        function resetView() {
            camera.position.set(0, 25, 100);
            camera.lookAt(0, 20, 0);
        }
                // Add to the init function

        // Zoom controls
        function zoomIn() {
            if (camera.position.z > 30) {
                camera.position.z -= 10;
            }
        }
        // Updated functions for handling skin uploads and library management

// Function to upload a skin file
function uploadSkin(file, name, skinType = 'default', saveToLibrary = true) {
    showLoading('Uploading skin...');
    
    const formData = new FormData();
    formData.append('skin', file);
    formData.append('name', name || file.name.replace('.png', ''));
    formData.append('skinType', skinType);
    formData.append('category', 'custom');
    
    // Send the file to the server
    fetch('upload.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        
        if (data.success) {
            // Load the skin in the preview
            loadSkin(data.url, skinType);
            
            // If saving to library is enabled, update the texture library UI
            if (saveToLibrary) {
                fetchSkins();
                
                // Show success message
                showMessage('success', `Skin "${data.name}" uploaded successfully and added to your library.`);
            } else {
                showMessage('success', 'Skin uploaded successfully.');
            }
        } else {
            showMessage('error', data.message || 'Failed to upload skin.');
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Error uploading skin:', error);
        showMessage('error', 'An error occurred while uploading the skin.');
    });
}

// Function to upload a skin from URL
function uploadSkinFromUrl(url, name, skinType = 'default', saveToLibrary = true) {
    showLoading('Downloading skin...');
    
    const formData = new FormData();
    formData.append('url', url);
    formData.append('name', name || 'URL Skin');
    formData.append('skinType', skinType);
    formData.append('category', 'custom');
    
    // Send the request to the server
    fetch('upload.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        
        if (data.success) {
            // Load the skin in the preview
            loadSkin(data.url, skinType);
            
            // If saving to library is enabled, update the texture library UI
            if (saveToLibrary) {
                fetchSkins();
                
                // Show success message
                showMessage('success', `Skin "${data.name}" downloaded successfully and added to your library.`);
            } else {
                showMessage('success', 'Skin downloaded successfully.');
            }
        } else {
            showMessage('error', data.message || 'Failed to download skin.');
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Error downloading skin:', error);
        showMessage('error', 'An error occurred while downloading the skin.');
    });
}

// Function to fetch skins from the server
function fetchSkins(category = null) {
    showLoading('Loading skins...');
    
    // Build URL with optional category filter
    let url = 'get-skins.php?action=list';
    if (category) {
        url += `&category=${category}`;
    }
    
    // Fetch skins from the server
    fetch(url)
    .then(response => response.json())
    .then(data => {
        hideLoading();
        
        if (data.success) {
            // Store the skins in the texture library
            textureLibrary = data.skins;
            
            // Update the UI
            updateTextureList();
            updateCommunityPresets();
            updateOfficialPresets();
        } else {
            console.warn('No skins found:', data.message);
            textureLibrary = [];
            updateTextureList();
            updateCommunityPresets();
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Error fetching skins:', error);
        showMessage('error', 'An error occurred while fetching skins.');
    });
}

// Function to delete a skin
function deleteSkin(id) {
    if (!confirm('Are you sure you want to delete this skin?')) {
        return;
    }
    
    showLoading('Deleting skin...');
    
    // Send delete request to the server
    fetch(`get-skins.php?action=delete&id=${id}`)
    .then(response => response.json())
    .then(data => {
        hideLoading();
        
        if (data.success) {
            // Show success message
            showMessage('success', data.message || 'Skin deleted successfully.');
            
            // Refresh the skin list
            fetchSkins();
        } else {
            showMessage('error', data.message || 'Failed to delete skin.');
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Error deleting skin:', error);
        showMessage('error', 'An error occurred while deleting the skin.');
    });
}

// Function to submit a skin to the community library
function submitSkinToCommunity(id, name, author, description) {
    showLoading('Submitting skin...');
    
    const formData = new FormData();
    formData.append('id', id);
    formData.append('name', name);
    formData.append('author', author);
    formData.append('description', description);
    
    // Send the submission request to the server
    fetch('get-skins.php?action=submit', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        
        if (data.success) {
            // Hide the submit modal
            const submitModal = bootstrap.Modal.getInstance(document.getElementById('submitModal'));
            if (submitModal) {
                submitModal.hide();
            }
            
            // Show success message
            showMessage('success', data.message || 'Skin submitted to community library successfully.');
            
            // Refresh the skin lists
            fetchSkins();
        } else {
            showMessage('error', data.message || 'Failed to submit skin to community library.');
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Error submitting skin:', error);
        showMessage('error', 'An error occurred while submitting the skin.');
    });
}

// Function to update the texture list in the UI
function updateTextureList() {
    const searchTerm = textureSearch ? textureSearch.value.toLowerCase() : '';
    const filteredTextures = textureLibrary.filter(texture => 
        texture.name.toLowerCase().includes(searchTerm)
    );
    
    textureList.innerHTML = '';
    
    if (filteredTextures.length === 0) {
        noTexturesMessage.style.display = 'block';
    } else {
        noTexturesMessage.style.display = 'none';
        
        filteredTextures.forEach(texture => {
            const textureItem = document.createElement('div');
            textureItem.className = 'texture-item';
            textureItem.innerHTML = `
                <img src="${texture.url}" alt="${texture.name}">
                <div class="flex-grow-1">
                    <div>${texture.name}</div>
                    <small class="text-muted">
                        ${texture.category || 'Custom'} 
                        ${texture.skinType === 'slim' ? '(Slim)' : '(Default)'}
                        ${texture.author ? `by ${texture.author}` : ''}
                    </small>
                </div>
                <button class="btn btn-sm btn-danger delete-texture" data-id="${texture.id}">
                    <i class="fas fa-trash"></i>
                </button>
            `;
            
            // Add click event to load the skin
            textureItem.addEventListener('click', function(e) {
                // Don't trigger if clicking the delete button
                if (!e.target.closest('.delete-texture')) {
                    loadSkin(texture.url, texture.skinType || 'default');
                    
                    // Store the current skin ID for submission
                    currentSkinId = texture.id;
                    
                    // Mark as active
                    document.querySelectorAll('.texture-item').forEach(item => {
                        item.classList.remove('active');
                    });
                    textureItem.classList.add('active');
                }
            });
            
            // Add delete functionality
            const deleteBtn = textureItem.querySelector('.delete-texture');
            deleteBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                const textureId = this.getAttribute('data-id');
                deleteSkin(textureId);
            });
            
            textureList.appendChild(textureItem);
        });
    }
}

// Function to update community presets from the library
function updateCommunityPresets() {
    const communityGrid = document.getElementById('community-grid');
    if (!communityGrid) return;
    
    communityGrid.innerHTML = '';
    
    const communitySkins = textureLibrary.filter(texture => 
        texture.category === 'community'
    );
    
    if (communitySkins.length === 0) {
        communityGrid.innerHTML = `
            <div class="alert alert-info w-100 text-center">
                No community skins available yet. Upload your own to get started!
            </div>
        `;
        return;
    }
    
    communitySkins.forEach(skin => {
        const div = document.createElement('div');
        div.className = 'preset-item';
        div.innerHTML = `
            <img src="${skin.url}" alt="${skin.name}">
            <div class="preset-name">${skin.name}</div>
            <div class="preset-author">by ${skin.author || 'Unknown'}</div>
        `;
        div.addEventListener('click', () => {
            // Remove active class from all items in this grid
            communityGrid.querySelectorAll('.preset-item').forEach(item => {
                item.classList.remove('active');
            });
            
            // Add active class to clicked item
            div.classList.add('active');
            
            // Store the current skin ID for submission
            currentSkinId = skin.id;
            
            // Load the skin
            loadSkin(skin.url, skin.skinType || 'default');
        });
        communityGrid.appendChild(div);
    });
}

// Function to update official presets from the library
function updateOfficialPresets() {
    const officialGrid = document.getElementById('official-grid');
    if (!officialGrid) return;
    
    // Keep any existing presets
    const existingOfficialSkins = officialSkins.map(skin => skin.url);
    
    // Add skins from the library that are marked as official
    const officialLibrarySkins = textureLibrary.filter(texture => 
        texture.category === 'official' && !existingOfficialSkins.includes(texture.url)
    );
    
    // Add the official library skins to the grid
    officialLibrarySkins.forEach(skin => {
        const div = document.createElement('div');
        div.className = 'preset-item';
        div.innerHTML = `
            <img src="${skin.url}" alt="${skin.name}">
            <div class="preset-name">${skin.name}</div>
        `;
        div.addEventListener('click', () => {
            // Remove active class from all items
            officialGrid.querySelectorAll('.preset-item').forEach(item => {
                item.classList.remove('active');
            });
            
            // Add active class to clicked item
            div.classList.add('active');
            
            // Store the current skin ID for submission
            currentSkinId = skin.id;
            
            // Load the skin
            loadSkin(skin.url, skin.skinType || 'default');
        });
        officialGrid.appendChild(div);
    });
}

// Helper function to show loading message
function showLoading(message = 'Loading...') {
    loadingMessage.innerHTML = `
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2">${message}</p>
    `;
    loadingMessage.style.display = 'block';
}

// Helper function to hide loading message
function hideLoading() {
    loadingMessage.style.display = 'none';
}

// Helper function to show message
function showMessage(type, message) {
    // Create or get message container
    let messageContainer = document.getElementById('message-container');
    
    if (!messageContainer) {
        messageContainer = document.createElement('div');
        messageContainer.id = 'message-container';
        messageContainer.className = 'message-container';
        messageContainer.style.position = 'fixed';
        messageContainer.style.top = '20px';
        messageContainer.style.right = '20px';
        messageContainer.style.zIndex = '1050';
        messageContainer.style.maxWidth = '350px';
        document.body.appendChild(messageContainer);
    }
    
    // Create message element
    const messageElement = document.createElement('div');
    messageElement.className = `alert alert-${type === 'error' ? 'danger' : 'success'} alert-dismissible fade show`;
    messageElement.role = 'alert';
    messageElement.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    // Add to container
    messageContainer.appendChild(messageElement);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        messageElement.classList.remove('show');
        setTimeout(() => messageElement.remove(), 150);
    }, 5000);
}

// Modified event listeners for file upload
function initEventListeners() {
    // File upload
    skinFileInput.addEventListener('change', handleFileUpload);
    
    // URL input
    loadUrlBtn.addEventListener('click', handleUrlInput);
    
    // Texture search
    textureSearch.addEventListener('input', debounce(() => updateTextureList(), 300));
    
    // Rotation toggle
    rotateToggle.addEventListener('click', () => {
        isRotating = !isRotating;
        rotateToggle.innerHTML = isRotating ? 
            '<i class="fas fa-sync-alt me-1"></i> Pause Rotation' : 
            '<i class="fas fa-play me-1"></i> Resume Rotation';
    });
    
    // Reset view
    resetViewBtn.addEventListener('click', resetView);
    
    // Zoom controls
    zoomInBtn.addEventListener('click', zoomIn);
    zoomOutBtn.addEventListener('click', zoomOut);
    
    // Download skin
    downloadSkinBtn.addEventListener('click', downloadSkin);
    
    // Submit texture button
    submitTextureBtn.addEventListener('click', () => {
        if (!currentSkinTexture) {
            showMessage('error', 'Please load a skin first before submitting.');
            return;
        }
        
        // Pre-fill the form with current skin info if available
        const currentSkin = textureLibrary.find(skin => skin.id === currentSkinId);
        if (currentSkin) {
            document.getElementById('submit-texture-name').value = currentSkin.name || '';
            document.getElementById('submit-texture-author').value = currentSkin.author || '';
            document.getElementById('submit-texture-description').value = currentSkin.description || '';
        }
        
        // Show the submit modal
        const submitModal = new bootstrap.Modal(document.getElementById('submitModal'));
        submitModal.show();
    });
    
    // Confirm save
    confirmSaveBtn.addEventListener('click', () => {
        const name = document.getElementById('modal-texture-name').value;
        const category = document.getElementById('modal-texture-category').value;
        
        if (!currentSkinTexture) {
            showMessage('error', 'No skin loaded to save');
            return;
        }
        
        // Create a Blob from the current skin texture
        const canvas = document.createElement('canvas');
        canvas.width = currentSkinTexture.image.width;
        canvas.height = currentSkinTexture.image.height;
        
        const ctx = canvas.getContext('2d');
        ctx.drawImage(currentSkinTexture.image, 0, 0);
        
        canvas.toBlob(blob => {
            // Create a File object
            const file = new File([blob], name + '.png', { type: 'image/png' });
            
            // Upload the file
            uploadSkin(file, name, skinType, true);
            
            // Hide the modal
            bootstrap.Modal.getInstance(document.getElementById('saveModal')).hide();
        }, 'image/png');
    });
    
    // Confirm submit
    confirmSubmitBtn.addEventListener('click', () => {
        const name = document.getElementById('submit-texture-name').value;
        const author = document.getElementById('submit-texture-author').value;
        const description = document.getElementById('submit-texture-description').value;
        const termsAgreed = document.getElementById('submit-texture-terms').checked;
        
        if (!name || !author || !termsAgreed) {
            showMessage('error', 'Please fill in all required fields and agree to the terms.');
            return;
        }
        
        if (!currentSkinId) {
            showMessage('error', 'No skin selected to submit. Please save your skin to your library first.');
            return;
        }
        
        // Submit to community
        submitSkinToCommunity(currentSkinId, name, author, description);
    });
    
    // Initialize tabs
    const inputTabs = document.getElementById('inputTabs');
    if (inputTabs) {
        const tabs = inputTabs.querySelectorAll('[data-bs-toggle="tab"]');
        tabs.forEach(tab => {
            tab.addEventListener('shown.bs.tab', e => {
                // Focus first input in active tab
                const targetId = e.target.getAttribute('data-bs-target');
                const targetPane = document.querySelector(targetId);
                const firstInput = targetPane.querySelector('input, textarea');
                if (firstInput) {
                    firstInput.focus();
                }
            });
        });
    }
}

// Modified handleFileUpload function
function handleFileUpload(event) {
    const file = event.target.files[0];
    if (!file) return;
    
    if (!file.type.match('image/png')) {
        showMessage('error', 'Please select a PNG image.');
        return;
    }
    
    // Get the skin type
    const usedSkinType = slimModelCheckbox.checked ? 'slim' : 'default';
    
    // Get the name for the skin
    const name = document.getElementById('texture-name').value || file.name.replace('.png', '');
    
    // Check if we should save to library
    const saveToLibrary = document.getElementById('save-to-library').checked;
    
    // Upload the skin
    uploadSkin(file, name, usedSkinType, saveToLibrary);
}

// Modified handleUrlInput function
function handleUrlInput() {
    const url = skinUrlInput.value.trim();
    if (!url) {
        showMessage('error', 'Please enter a skin URL.');
        return;
    }
    
    // Get the skin type
    const usedSkinType = urlSlimModelCheckbox.checked ? 'slim' : 'default';
    
    // Get the name for the skin
    const name = document.getElementById('url-texture-name').value || 'URL Skin';
    
    // Check if we should save to library
    const saveToLibrary = document.getElementById('save-url-to-library').checked;
    
    // Upload the skin from URL
    uploadSkinFromUrl(url, name, usedSkinType, saveToLibrary);
}

// Vote system functions

// Function to vote for a skin
function voteSkin(id) {
    showLoading('Recording vote...');
    
    const formData = new FormData();
    formData.append('id', id);
    
    // Send the vote request to the server
    fetch('get-skins.php?action=vote', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        
        if (data.success) {
            // Update the vote count in the UI
            const voteCountElements = document.querySelectorAll(`.vote-count[data-id="${id}"]`);
            voteCountElements.forEach(element => {
                element.textContent = data.votes;
            });
            
            // Show success message
            showMessage('success', 'Thanks for your vote!');
            
            // Disable the vote button to prevent multiple votes
            const voteButtons = document.querySelectorAll(`.vote-button[data-id="${id}"]`);
            voteButtons.forEach(button => {
                button.disabled = true;
                button.classList.add('voted');
                button.innerHTML = '<i class="fas fa-check"></i>';
            });
            
            // Store voted skin IDs in localStorage
            storeVotedSkin(id);
        } else {
            showMessage('error', data.message || 'Failed to record vote.');
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Error voting for skin:', error);
        showMessage('error', 'An error occurred while voting.');
    });
}

// Function to store voted skin IDs in localStorage
function storeVotedSkin(id) {
    // Get existing voted skin IDs
    let votedSkins = localStorage.getItem('voted-skins');
    votedSkins = votedSkins ? JSON.parse(votedSkins) : [];
    
    // Add current skin ID if not already voted
    if (!votedSkins.includes(id)) {
        votedSkins.push(id);
    }
    
    // Save back to localStorage
    localStorage.setItem('voted-skins', JSON.stringify(votedSkins));
}

// Function to check if a skin has been voted
function hasVotedForSkin(id) {
    // Get existing voted skin IDs
    let votedSkins = localStorage.getItem('voted-skins');
    votedSkins = votedSkins ? JSON.parse(votedSkins) : [];
    
    // Check if current skin ID is in the list
    return votedSkins.includes(id);
}

// Updated function to create community skin items with voting
function updateCommunityPresets() {
    const communityGrid = document.getElementById('community-grid');
    if (!communityGrid) return;
    
    communityGrid.innerHTML = '';
    
    // Add sort controls
    const sortingControls = document.createElement('div');
    sortingControls.className = 'sorting-controls mb-3';
    sortingControls.innerHTML = `
        <div class="btn-group" role="group" aria-label="Sort skins">
            <button type="button" class="btn btn-outline-primary active" id="sort-newest">
                <i class="fas fa-clock me-1"></i> Newest
            </button>
            <button type="button" class="btn btn-outline-primary" id="sort-popular">
                <i class="fas fa-fire me-1"></i> Most Popular
            </button>
        </div>
    `;
    communityGrid.appendChild(sortingControls);
    
    // Add event listeners for sorting
    document.getElementById('sort-newest').addEventListener('click', function() {
        this.classList.add('active');
        document.getElementById('sort-popular').classList.remove('active');
        fetchSkins('community'); // Default sorting by date
    });
    
    document.getElementById('sort-popular').addEventListener('click', function() {
        this.classList.add('active');
        document.getElementById('sort-newest').classList.remove('active');
        fetchSkins('community', 'votes');
    });
    
    // Create container for skin items
    const skinsContainer = document.createElement('div');
    skinsContainer.className = 'preset-grid';
    skinsContainer.id = 'community-skins-container';
    communityGrid.appendChild(skinsContainer);
    
    // Filter community skins
    const communitySkins = textureLibrary.filter(texture => 
        texture.category === 'community'
    );
    
    if (communitySkins.length === 0) {
        skinsContainer.innerHTML = `
            <div class="alert alert-info w-100 text-center">
                No community skins available yet. Upload your own to get started!
            </div>
        `;
        return;
    }
    
    communitySkins.forEach(skin => {
        const div = document.createElement('div');
        div.className = 'preset-item';
        
        // Check if user has already voted
        const hasVoted = hasVotedForSkin(skin.id);
        const voteCount = skin.votes || 0;
        
        div.innerHTML = `
            <img src="${skin.url}" alt="${skin.name}">
            <div class="preset-name">${skin.name}</div>
            <div class="preset-author">by ${skin.author || 'Unknown'}</div>
            <div class="vote-container mt-2">
                <span class="vote-count" data-id="${skin.id}">${voteCount}</span>
                <button class="vote-button btn btn-sm btn-outline-primary${hasVoted ? ' voted' : ''}" data-id="${skin.id}" ${hasVoted ? 'disabled' : ''}>
                    ${hasVoted ? '<i class="fas fa-check"></i>' : '<i class="fas fa-thumbs-up"></i>'}
                </button>
            </div>
        `;
        
        // Skin selection functionality
        div.addEventListener('click', function(e) {
            // Don't trigger skin selection if clicking the vote button
            if (!e.target.closest('.vote-button')) {
                // Remove active class from all items in this grid
                skinsContainer.querySelectorAll('.preset-item').forEach(item => {
                    item.classList.remove('active');
                });
                
                // Add active class to clicked item
                div.classList.add('active');
                
                // Store the current skin ID for submission
                currentSkinId = skin.id;
                
                // Load the skin
                loadSkin(skin.url, skin.skinType || 'default');
            }
        });
        
        // Add vote button functionality
        const voteButton = div.querySelector('.vote-button');
        voteButton.addEventListener('click', function(e) {
            e.stopPropagation(); // Prevent triggering the skin selection
            voteSkin(skin.id);
        });
        
        skinsContainer.appendChild(div);
    });
}

// Add CSS for vote styling
function addVotingStyles() {
    const style = document.createElement('style');
    style.innerHTML = `
        .vote-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }
        
        .vote-count {
            font-weight: bold;
            color: #3498db;
            min-width: 25px;
            text-align: center;
        }
        
        .vote-button {
            padding: 2px 8px;
            border-radius: 20px;
            transition: all 0.2s ease;
        }
        
        .vote-button:hover:not([disabled]) {
            background-color: #3498db;
            color: white;
            transform: scale(1.05);
        }
        
        .vote-button.voted {
            background-color: #2ecc71;
            color: white;
            border-color: #2ecc71;
        }
        
        .preset-item {
            position: relative;
            transition: transform 0.2s ease;
        }
        
        .preset-item:hover {
            transform: translateY(-5px);
        }
        
        .preset-author {
            font-size: 10px;
            color: #777;
            margin-top: -5px;
        }
        
        .sorting-controls {
            display: flex;
            justify-content: center;
            width: 100%;
        }
    `;
    document.head.appendChild(style);
}

// Update the init function to add voting styles
function init() {
    // Initialize variables
    currentSkinId = null;
    textureLibrary = [];
    
    // Initialize UI components
    initScene();
    initBackgrounds();
    initPresets();
    initEventListeners();
    initCape();
    addSkinFormatControls();
    addVotingStyles();
    
    // Fetch skins from server
    fetchSkins();
    
    // Start with Steve skin
    loadSkin(officialSkins[0].url, officialSkins[0].type);
    
    // Initialize skin format detection
    updateFormatInfo();
}

// Updated fetchSkins function to handle sorting
function fetchSkins(category = null, sortBy = null) {
    showLoading('Loading skins...');
    
    // Build URL with optional category filter and sorting
    let url = 'get-skins.php?action=list';
    if (category) {
        url += `&category=${category}`;
    }
    if (sortBy) {
        url += `&sort=${sortBy}`;
    }
    
    // Fetch skins from the server
    fetch(url)
    .then(response => response.json())
    .then(data => {
        hideLoading();
        
        if (data.success) {
            // Store the skins in the texture library
            textureLibrary = data.skins;
            
            // Update the UI
            updateTextureList();
            updateCommunityPresets();
            updateOfficialPresets();
        } else {
            console.warn('No skins found:', data.message);
            textureLibrary = [];
            updateTextureList();
            updateCommunityPresets();
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Error fetching skins:', error);
        showMessage('error', 'An error occurred while fetching skins.');
    });
}

// Call init when the page loads
window.addEventListener('DOMContentLoaded', init);
        function zoomOut() {
            if (camera.position.z < 150) {
                camera.position.z += 10;
            }
        }
        
        // Download skin
        function downloadSkin() {
            if (!currentSkinTexture) {
                alert('No skin loaded to download.');
                return;
            }
            
            // Create a link and trigger download
            const link = document.createElement('a');
            link.href = currentSkinTexture.image.src;
            link.download = 'minecraft-skin.png';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
        
        // Initialize everything when the page loads
        window.addEventListener('DOMContentLoaded', init);
</script>
</body>
</html>