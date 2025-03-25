// ===== SKIN EDITOR PATCH =====
// This patch fixes issues with model rendering and ensures skin textures load correctly

// First, make sure we have the model type and UV mapping information available


// Fix skin editor initialization
function initSkinEditor() {
    if (!currentSkinTexture) {
        alert('Please load a skin first before editing.');
        return;
    }
    
    console.log("Initializing skin editor...");
    console.log("Current skin type:", skinType);
    
    // Make sure necessary global variables are defined
    window.currentSkinTexture = currentSkinTexture;
    window.skinType = skinType || 'default';
    
    // Define the UV mappings if not already defined
    if (typeof window.uvMappings === 'undefined') {
        console.log("Defining UV mappings...");
       // ===== FIXED SKIN EDITOR PATCH =====
// This patch fixes the "window.uvMappings is undefined" error

// Define UV mappings first - this MUST happen before the editor opens
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

// Wait for SkinEditor to be available
document.addEventListener('DOMContentLoaded', function() {
    // Wait for SkinEditor to be defined
    const checkInterval = setInterval(function() {
        if (typeof SkinEditor !== 'undefined') {
            clearInterval(checkInterval);
            patchSkinEditor();
            ensureSingleEditButton();
        }
    }, 100);
    
    // Stop checking after 5 seconds
    setTimeout(function() {
        clearInterval(checkInterval);
    }, 5000);
});

// Ensure there's only one Edit Skin button
function ensureSingleEditButton() {
    // Remove any existing buttons first
    const existingButtons = document.querySelectorAll('#edit-skin-button');
    existingButtons.forEach(button => button.remove());
    
    // Create a single button
    const controlsContainer = document.querySelector('.d-flex.flex-wrap.gap-2.mt-3');
    if (controlsContainer) {
        const editButton = document.createElement('button');
        editButton.id = 'edit-skin-button';
        editButton.className = 'btn btn-warning';
        editButton.innerHTML = '<i class="fas fa-paint-brush me-1"></i> Edit Skin';
        editButton.onclick = function() {
            if (!window.currentSkinTexture) {
                alert('Please load a skin first before editing.');
                return;
            }
            
            // Make sure we have the current skin type set
            window.skinType = window.skinType || 'default';
            
            // Open the editor
            if (typeof SkinEditor !== 'undefined' && SkinEditor.open) {
                SkinEditor.open(window.currentSkinTexture);
            }
        };
        
        // Insert before the download button
        const downloadButton = document.getElementById('download-skin');
        if (downloadButton) {
            controlsContainer.insertBefore(editButton, downloadButton);
        } else {
            controlsContainer.appendChild(editButton);
        }
    }
}

// Patch the SkinEditor object
function patchSkinEditor() {
    console.log("Applying comprehensive fix to SkinEditor...");
    
    // 1. Fix the createSkinModel method to use proper model parts
    SkinEditor.createSkinModel = function(texture) {
        console.log("Creating properly aligned 3D skin model");
        
        // Remove any existing model
        if (this.editorModel) {
            this.scene.remove(this.editorModel);
        }
        
        // Create a new group to hold all parts
        this.editorModel = new THREE.Group();
        
        // Get current skin type
        const currentSkinType = window.skinType || 'default';
        console.log('Creating skin model with type:', currentSkinType);
        
        // Determine the arm width
        const armWidth = currentSkinType === 'slim' ? 3 : 4;
        
        // Create all body parts using BoxGeometry
        // HEAD - Base
        const headGeometry = new THREE.BoxGeometry(8, 8, 8);
        const headMaterial = this.createMaterialWithMappedTexture(texture, window.uvMappings.head.base);
        const head = new THREE.Mesh(headGeometry, headMaterial);
        head.position.set(0, 28, 0);
        head.name = 'head';
        this.editorModel.add(head);
        
        // HEAD - Overlay
        const headOverlayGeometry = new THREE.BoxGeometry(8.5, 8.5, 8.5);
        const headOverlayMaterial = this.createMaterialWithMappedTexture(texture, window.uvMappings.head.overlay, true);
        const headOverlay = new THREE.Mesh(headOverlayGeometry, headOverlayMaterial);
        headOverlay.position.set(0, 28, 0);
        headOverlay.name = 'head-overlay';
        this.editorModel.add(headOverlay);
        
        // BODY - Base
        const bodyGeometry = new THREE.BoxGeometry(8, 12, 4);
        const bodyMaterial = this.createMaterialWithMappedTexture(texture, window.uvMappings.body.base);
        const body = new THREE.Mesh(bodyGeometry, bodyMaterial);
        body.position.set(0, 18, 0);
        body.name = 'body';
        this.editorModel.add(body);
        
        // BODY - Overlay
        const bodyOverlayGeometry = new THREE.BoxGeometry(8.5, 12.5, 4.5);
        const bodyOverlayMaterial = this.createMaterialWithMappedTexture(texture, window.uvMappings.body.overlay, true);
        const bodyOverlay = new THREE.Mesh(bodyOverlayGeometry, bodyOverlayMaterial);
        bodyOverlay.position.set(0, 18, 0);
        bodyOverlay.name = 'body-overlay';
        this.editorModel.add(bodyOverlay);
        
        // RIGHT ARM - Base
        const rightArmGeometry = new THREE.BoxGeometry(armWidth, 12, 4);
        const rightArmMaterial = this.createMaterialWithMappedTexture(texture, window.uvMappings.rightArm.base);
        const rightArm = new THREE.Mesh(rightArmGeometry, rightArmMaterial);
        rightArm.position.set(-(4 + armWidth/2), 18, 0);
        rightArm.name = 'right-arm';
        this.editorModel.add(rightArm);
        
        // RIGHT ARM - Overlay
        const rightArmOverlayGeometry = new THREE.BoxGeometry(armWidth + 0.5, 12.5, 4.5);
        const rightArmOverlayMaterial = this.createMaterialWithMappedTexture(texture, window.uvMappings.rightArm.overlay, true);
        const rightArmOverlay = new THREE.Mesh(rightArmOverlayGeometry, rightArmOverlayMaterial);
        rightArmOverlay.position.set(-(4 + armWidth/2), 18, 0);
        rightArmOverlay.name = 'right-arm-overlay';
        this.editorModel.add(rightArmOverlay);
        
        // LEFT ARM - Base
        const leftArmGeometry = new THREE.BoxGeometry(armWidth, 12, 4);
        const leftArmMaterial = this.createMaterialWithMappedTexture(texture, window.uvMappings.leftArm.base);
        const leftArm = new THREE.Mesh(leftArmGeometry, leftArmMaterial);
        leftArm.position.set(4 + armWidth/2, 18, 0);
        leftArm.name = 'left-arm';
        this.editorModel.add(leftArm);
        
        // LEFT ARM - Overlay
        const leftArmOverlayGeometry = new THREE.BoxGeometry(armWidth + 0.5, 12.5, 4.5);
        const leftArmOverlayMaterial = this.createMaterialWithMappedTexture(texture, window.uvMappings.leftArm.overlay, true);
        const leftArmOverlay = new THREE.Mesh(leftArmOverlayGeometry, leftArmOverlayMaterial);
        leftArmOverlay.position.set(4 + armWidth/2, 18, 0);
        leftArmOverlay.name = 'left-arm-overlay';
        this.editorModel.add(leftArmOverlay);
        
        // RIGHT LEG - Base
        const rightLegGeometry = new THREE.BoxGeometry(4, 12, 4);
        const rightLegMaterial = this.createMaterialWithMappedTexture(texture, window.uvMappings.rightLeg.base);
        const rightLeg = new THREE.Mesh(rightLegGeometry, rightLegMaterial);
        rightLeg.position.set(-2, 6, 0);
        rightLeg.name = 'right-leg';
        this.editorModel.add(rightLeg);
        
        // RIGHT LEG - Overlay
        const rightLegOverlayGeometry = new THREE.BoxGeometry(4.5, 12.5, 4.5);
        const rightLegOverlayMaterial = this.createMaterialWithMappedTexture(texture, window.uvMappings.rightLeg.overlay, true);
        const rightLegOverlay = new THREE.Mesh(rightLegOverlayGeometry, rightLegOverlayMaterial);
        rightLegOverlay.position.set(-2, 6, 0);
        rightLegOverlay.name = 'right-leg-overlay';
        this.editorModel.add(rightLegOverlay);
        
        // LEFT LEG - Base
        const leftLegGeometry = new THREE.BoxGeometry(4, 12, 4);
        const leftLegMaterial = this.createMaterialWithMappedTexture(texture, window.uvMappings.leftLeg.base);
        const leftLeg = new THREE.Mesh(leftLegGeometry, leftLegMaterial);
        leftLeg.position.set(2, 6, 0);
        leftLeg.name = 'left-leg';
        this.editorModel.add(leftLeg);
        
        // LEFT LEG - Overlay
        const leftLegOverlayGeometry = new THREE.BoxGeometry(4.5, 12.5, 4.5);
        const leftLegOverlayMaterial = this.createMaterialWithMappedTexture(texture, window.uvMappings.leftLeg.overlay, true);
        const leftLegOverlay = new THREE.Mesh(leftLegOverlayGeometry, leftLegOverlayMaterial);
        leftLegOverlay.position.set(2, 6, 0);
        leftLegOverlay.name = 'left-leg-overlay';
        this.editorModel.add(leftLegOverlay);
        
        // Add the model to the scene
        this.scene.add(this.editorModel);
        
        console.log('3D skin model created with all body parts');
    };
    
    // 2. Add a helper method to create materials with proper UV mapping
    SkinEditor.createMaterialWithMappedTexture = function(texture, uvMap, transparent = false) {
        const materials = [];
        
        // Create a material for each face
        // BoxGeometry faces order: right, left, top, bottom, front, back
        const faceNames = ['right', 'left', 'up', 'down', 'front', 'back'];
        
        for (let i = 0; i < faceNames.length; i++) {
            const faceName = faceNames[i];
            const faceUV = uvMap[faceName];
            
            if (!faceUV) {
                console.error(`Missing UV mapping for face: ${faceName}`);
                materials.push(new THREE.MeshBasicMaterial({ color: 0xff00ff }));
                continue;
            }
            
            // Clone the texture for this face
            const faceTexture = texture.clone();
            
            // Extract UV coordinates
            const [x1, y1, x2, y2] = faceUV;
            
            // Get texture dimensions
            const textureWidth = texture.image.width;
            const textureHeight = texture.image.height;
            
            // Convert pixel coordinates to normalized UV coordinates (0-1)
            const u1 = x1 / textureWidth;
            const v1 = 1 - y2 / textureHeight;  // Flip Y-coordinate
            const u2 = x2 / textureWidth;
            const v2 = 1 - y1 / textureHeight;  // Flip Y-coordinate
            
            // Set texture mapping
            faceTexture.offset.set(u1, v1);
            faceTexture.repeat.set(u2 - u1, v2 - v1);
            
            // Create material
            const material = new THREE.MeshLambertMaterial({
                map: faceTexture,
                transparent: transparent,
                alphaTest: 0.5
            });
            
            // Store the UV mapping in the material's userData for later reference
            material.userData = {
                uvRect: faceUV,
                faceName: faceName
            };
            
            materials.push(material);
        }
        
        return materials;
    };
    
    // 3. Fix the updatePixelInfo method for proper 3D raycasting
    SkinEditor.updatePixelInfo = function(intersect) {
        if (!intersect || !intersect.object) return;
        
        const mesh = intersect.object;
        const faceIndex = Math.floor(intersect.faceIndex / 2);
        const materialIndex = intersect.face.materialIndex;
        
        // Get material and UV map
        if (!mesh.material || !Array.isArray(mesh.material) || !mesh.material[materialIndex]) {
            return;
        }
        
        const material = mesh.material[materialIndex];
        if (!material.userData || !material.userData.uvRect) {
            return;
        }
        
        // Get the UV rect from the material
        const uvRect = material.userData.uvRect;
        
        // Calculate the exact pixel position using the intersection UV
        const uv = intersect.uv;
        const [x1, y1, x2, y2] = uvRect;
        const textureX = Math.floor(x1 + (x2 - x1) * uv.x);
        const textureY = Math.floor(y1 + (y2 - y1) * (1 - uv.y)); // Flip Y
        
        // Get the pixel color
        const pixelData = this.getPixelColor(textureX, textureY);
        const rgba = `rgba(${pixelData[0]}, ${pixelData[1]}, ${pixelData[2]}, ${pixelData[3]/255})`;
        const hex = this.rgbaToHex(pixelData[0], pixelData[1], pixelData[2]);
        
        // Update pixel info display
        const partName = mesh.name || 'unknown';
        const faceName = material.userData.faceName || 'unknown';
        
        document.getElementById('pixel-position').textContent = `${textureX}, ${textureY}`;
        document.getElementById('pixel-color').textContent = `${hex} (${rgba})`;
        document.getElementById('pixel-body-part').textContent = `${partName} (${faceName})`;
    };
    
    // 4. Fix the performPaintAction method for proper 3D editing
    SkinEditor.performPaintAction = function() {
        if (!this.highlightedMesh || !this.highlightedFace) return;
        
        const intersect = this.raycaster.intersectObject(this.highlightedMesh)[0];
        if (!intersect) return;
        
        const materialIndex = intersect.face.materialIndex;
        
        // Get material and UV data
        if (!Array.isArray(this.highlightedMesh.material) || !this.highlightedMesh.material[materialIndex]) {
            return;
        }
        
        const material = this.highlightedMesh.material[materialIndex];
        if (!material.userData || !material.userData.uvRect) {
            return;
        }
        
        // Get the UV rect from the material
        const uvRect = material.userData.uvRect;
        
        // Calculate the exact pixel position using the intersection UV
        const uv = intersect.uv;
        const [x1, y1, x2, y2] = uvRect;
        const textureX = Math.floor(x1 + (x2 - x1) * uv.x);
        const textureY = Math.floor(y1 + (y2 - y1) * (1 - uv.y)); // Flip Y
        
        // Apply the current tool
        if (this.currentTool === 'pencil') {
            this.drawPixel(textureX, textureY);
        } else if (this.currentTool === 'eraser') {
            this.erasePixel(textureX, textureY);
        } else if (this.currentTool === 'fill') {
            this.fillArea(textureX, textureY);
        }
        
        // Update the texture and preview
        this.updateTexture();
    };
    
    // 5. Fix the open method to ensure proper cleanup and initialization
    const originalOpen = SkinEditor.open;
    SkinEditor.open = function(skinTexture) {
        // Set proper model type
        window.skinType = window.skinType || 'default';
        
        // Clean up any previous editor instance
        if (this.isActive) {
            this.close();
        }
        
        // Call the original open method
        originalOpen.call(this, skinTexture);
        
        // Show clean UI
        const editorContainer = document.getElementById('skin-editor-container');
        if (editorContainer) {
            editorContainer.classList.remove('d-none');
        }
        
        // Set proper position for camera
        if (this.camera) {
            this.camera.position.set(0, 25, 100);
            this.camera.lookAt(0, 20, 0);
        }
    };
    
    console.log("Comprehensive fixes applied to SkinEditor");
}

// Remove any duplicate buttons on load
window.addEventListener('load', function() {
    // Short delay to ensure DOM is fully loaded
    setTimeout(function() {
        const editButtons = document.querySelectorAll('#edit-skin-button');
        if (editButtons.length > 1) {
            console.log(`Found ${editButtons.length} edit buttons on load, removing duplicates...`);
            // Keep only the first button
            for (let i = 1; i < editButtons.length; i++) {
                editButtons[i].remove();
            }
        }
    }, 100);
});