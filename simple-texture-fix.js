// ===== SIMPLE TEXTURE ORIENTATION FIX =====
// This script fixes the upside-down texture issue in the skin editor
// by properly mapping the UV coordinates

// Wait for SkinEditor to be available
document.addEventListener('DOMContentLoaded', function() {
    // Check for SkinEditor at regular intervals
    const checkInterval = setInterval(function() {
        if (typeof SkinEditor !== 'undefined') {
            clearInterval(checkInterval);
            applySimpleTextureOrientationFix();
        }
    }, 100);
    
    // Stop checking after 5 seconds
    setTimeout(function() {
        clearInterval(checkInterval);
    }, 5000);
});

// Apply the simple texture orientation fix
function applySimpleTextureOrientationFix() {
    console.log("Applying simple texture orientation fix...");
    
    // Override the createSkinModel method
    SkinEditor.createSkinModel = function(texture) {
        console.log("Creating skin model with fixed orientation");
        
        // Remove any existing model
        if (this.editorModel) {
            this.scene.remove(this.editorModel);
        }
        
        // Create a group to hold all parts
        this.editorModel = new THREE.Group();
        
        // Get current skin type
        const currentSkinType = window.skinType || 'default';
        const armWidth = currentSkinType === 'slim' ? 3 : 4;
        
        // Helper function to create a box with correct UV mapping
        const createBox = (width, height, depth, x, y, z, name, uvMap, isOverlay = false) => {
            // Create geometry and apply UVs
            const geometry = new THREE.BoxGeometry(width, height, depth);
            applyCorrectUVs(geometry, uvMap);
            
            // Create material
            const material = new THREE.MeshLambertMaterial({
                map: texture,
                transparent: isOverlay,
                alphaTest: 0.5
            });
            
            // Create mesh
            const mesh = new THREE.Mesh(geometry, material);
            mesh.position.set(x, y, z);
            mesh.name = name;
            
            // Add to model group
            this.editorModel.add(mesh);
            
            return mesh;
        };
        
        // Helper function to apply correct UVs
        function applyCorrectUVs(geometry, uvMap) {
            // Check if we have proper UV attribute
            if (!geometry.attributes.uv) return;
            
            // Get UV attribute
            const uvs = geometry.attributes.uv;
            const faceOrder = ['right', 'left', 'up', 'down', 'front', 'back'];
            
            // BoxGeometry in THREE.js has 6 faces, each with 4 vertices
            // The order is: right, left, top, bottom, front, back
            for (let i = 0; i < 6; i++) {
                const faceName = faceOrder[i];
                const faceUVs = uvMap[faceName];
                
                if (!faceUVs) continue;
                
                // Each face has 4 vertices, and each vertex has u,v coordinates
                // So we need to set 8 values (4 vertices * 2 coordinates)
                const faceVertexOffset = i * 4 * 2;
                
                // Convert UV coordinates from pixel space to 0-1 range
                const [x1, y1, x2, y2] = faceUVs;
                const textureWidth = texture.image.width;
                const textureHeight = texture.image.height;
                
                // Calculate normalized coordinates - and invert V to fix orientation
                const u1 = x1 / textureWidth;
                const v1 = 1 - (y1 / textureHeight);
                const u2 = x2 / textureWidth;
                const v2 = 1 - (y2 / textureHeight);
                
                // Manually set UVs for this face's vertices
                // Bottom left
                uvs.array[faceVertexOffset + 0] = u1;
                uvs.array[faceVertexOffset + 1] = v2;
                
                // Bottom right
                uvs.array[faceVertexOffset + 2] = u2;
                uvs.array[faceVertexOffset + 3] = v2;
                
                // Top left
                uvs.array[faceVertexOffset + 4] = u1;
                uvs.array[faceVertexOffset + 5] = v1;
                
                // Top right
                uvs.array[faceVertexOffset + 6] = u2;
                uvs.array[faceVertexOffset + 7] = v1;
            }
            
            // Mark UVs as needing update
            uvs.needsUpdate = true;
        }
        
        // Create all body parts
        // Head (base)
        createBox(8, 8, 8, 0, 28, 0, 'head', window.uvMappings.head.base);
        
        // Head (overlay)
        createBox(8.5, 8.5, 8.5, 0, 28, 0, 'head-overlay', window.uvMappings.head.overlay, true);
        
        // Body (base)
        createBox(8, 12, 4, 0, 18, 0, 'body', window.uvMappings.body.base);
        
        // Body (overlay)
        createBox(8.5, 12.5, 4.5, 0, 18, 0, 'body-overlay', window.uvMappings.body.overlay, true);
        
        // Right arm (base)
        createBox(armWidth, 12, 4, -(4 + armWidth/2), 18, 0, 'right-arm', window.uvMappings.rightArm.base);
        
        // Right arm (overlay)
        createBox(armWidth + 0.5, 12.5, 4.5, -(4 + armWidth/2), 18, 0, 'right-arm-overlay', window.uvMappings.rightArm.overlay, true);
        
        // Left arm (base)
        createBox(armWidth, 12, 4, 4 + armWidth/2, 18, 0, 'left-arm', window.uvMappings.leftArm.base);
        
        // Left arm (overlay)
        createBox(armWidth + 0.5, 12.5, 4.5, 4 + armWidth/2, 18, 0, 'left-arm-overlay', window.uvMappings.leftArm.overlay, true);
        
        // Right leg (base)
        createBox(4, 12, 4, -2, 6, 0, 'right-leg', window.uvMappings.rightLeg.base);
        
        // Right leg (overlay)
        createBox(4.5, 12.5, 4.5, -2, 6, 0, 'right-leg-overlay', window.uvMappings.rightLeg.overlay, true);
        
        // Left leg (base)
        createBox(4, 12, 4, 2, 6, 0, 'left-leg', window.uvMappings.leftLeg.base);
        
        // Left leg (overlay)
        createBox(4.5, 12.5, 4.5, 2, 6, 0, 'left-leg-overlay', window.uvMappings.leftLeg.overlay, true);
        
        // Add the model to the scene
        this.scene.add(this.editorModel);
        
        // Position camera
        if (this.camera) {
            this.camera.position.set(0, 25, 100);
            this.camera.lookAt(0, 20, 0);
        }
        
        console.log('Skin model created with fixed orientation');
    };
    
    // Fix the interaction with the model
    SkinEditor.highlightPixel = function() {
        // Raycast to find intersected object
        this.raycaster.setFromCamera(this.mouse, this.camera);
        const intersects = this.raycaster.intersectObjects(this.editorModel.children, true);
        
        // Remove previous highlight if any
        if (this.highlightedMesh && this.highlightedMesh.material) {
            this.highlightedMesh.material.emissive.setRGB(0, 0, 0);
        }
        
        if (intersects.length > 0) {
            const intersect = intersects[0];
            const mesh = intersect.object;
            
            // Store highlighted mesh and face
            this.highlightedMesh = mesh;
            this.highlightedFace = intersect.face;
            
            // Highlight the mesh
            if (mesh.material) {
                mesh.material.emissive.setRGB(0.2, 0.2, 0.2);
            }
            
            // Update pixel info
            this.updatePixelInfo(intersect);
        } else {
            // Clear pixel info if no intersection
            this.clearPixelInfo();
        }
    };// Fix the updatePixelInfo method for the corrected model
    SkinEditor.updatePixelInfo = function(intersect) {
        if (!intersect || !intersect.face) return;
        
        const mesh = intersect.object;
        const uv = intersect.uv;
        const faceIndex = Math.floor(intersect.faceIndex / 2);
        
        // Determine which face we hit
        const faceNames = ['right', 'left', 'up', 'down', 'front', 'back'];
        const faceName = faceNames[faceIndex];
        
        // Get UV coordinates for this face from the UV mapping
        let uvMap;
        const meshName = mesh.name.toLowerCase();
        
        if (meshName.includes('head')) {
            uvMap = meshName.includes('overlay') ? window.uvMappings.head.overlay : window.uvMappings.head.base;
        } else if (meshName.includes('body')) {
            uvMap = meshName.includes('overlay') ? window.uvMappings.body.overlay : window.uvMappings.body.base;
        } else if (meshName.includes('right-arm')) {
            uvMap = meshName.includes('overlay') ? window.uvMappings.rightArm.overlay : window.uvMappings.rightArm.base;
        } else if (meshName.includes('left-arm')) {
            uvMap = meshName.includes('overlay') ? window.uvMappings.leftArm.overlay : window.uvMappings.leftArm.base;
        } else if (meshName.includes('right-leg')) {
            uvMap = meshName.includes('overlay') ? window.uvMappings.rightLeg.overlay : window.uvMappings.rightLeg.base;
        } else if (meshName.includes('left-leg')) {
            uvMap = meshName.includes('overlay') ? window.uvMappings.leftLeg.overlay : window.uvMappings.leftLeg.base;
        }
        
        if (!uvMap || !uvMap[faceName]) {
            console.log('Could not find UV mapping for face:', faceName, 'of part:', meshName);
            return;
        }
        
        // Get the UV boundaries for this face
        const [x1, y1, x2, y2] = uvMap[faceName];
        
        // Calculate texture position
        const textureX = Math.floor(x1 + (x2 - x1) * uv.x);
        const textureY = Math.floor(y1 + (y2 - y1) * (1 - uv.y));
        
        // Get pixel color
        const pixelData = this.getPixelColor(textureX, textureY);
        const rgba = `rgba(${pixelData[0]}, ${pixelData[1]}, ${pixelData[2]}, ${pixelData[3]/255})`;
        const hex = this.rgbaToHex(pixelData[0], pixelData[1], pixelData[2]);
        
        // Update pixel info display
        document.getElementById('pixel-position').textContent = `${textureX}, ${textureY}`;
        document.getElementById('pixel-color').textContent = `${hex} (${rgba})`;
        document.getElementById('pixel-body-part').textContent = `${mesh.name} (${faceName})`;
    };
    
    // Fix the performPaintAction method for proper editing
    SkinEditor.performPaintAction = function() {
        if (!this.highlightedMesh || !this.highlightedFace) return;
        
        // Get intersection info
        const mesh = this.highlightedMesh;
        const face = this.highlightedFace;
        const meshName = mesh.name.toLowerCase();
        
        // Get newest intersection data
        const intersects = this.raycaster.intersectObject(mesh);
        if (intersects.length === 0) return;
        
        const intersect = intersects[0];
        const uv = intersect.uv;
        const faceIndex = Math.floor(intersect.faceIndex / 2);
        
        // Determine which face we hit
        const faceNames = ['right', 'left', 'up', 'down', 'front', 'back'];
        const faceName = faceNames[faceIndex];
        
        // Get UV coordinates for this face from the UV mapping
        let uvMap;
        
        if (meshName.includes('head')) {
            uvMap = meshName.includes('overlay') ? window.uvMappings.head.overlay : window.uvMappings.head.base;
        } else if (meshName.includes('body')) {
            uvMap = meshName.includes('overlay') ? window.uvMappings.body.overlay : window.uvMappings.body.base;
        } else if (meshName.includes('right-arm')) {
            uvMap = meshName.includes('overlay') ? window.uvMappings.rightArm.overlay : window.uvMappings.rightArm.base;
        } else if (meshName.includes('left-arm')) {
            uvMap = meshName.includes('overlay') ? window.uvMappings.leftArm.overlay : window.uvMappings.leftArm.base;
        } else if (meshName.includes('right-leg')) {
            uvMap = meshName.includes('overlay') ? window.uvMappings.rightLeg.overlay : window.uvMappings.rightLeg.base;
        } else if (meshName.includes('left-leg')) {
            uvMap = meshName.includes('overlay') ? window.uvMappings.leftLeg.overlay : window.uvMappings.leftLeg.base;
        }
        
        if (!uvMap || !uvMap[faceName]) {
            console.log('Could not find UV mapping for face:', faceName, 'of part:', meshName);
            return;
        }
        
        // Get the UV boundaries for this face
        const [x1, y1, x2, y2] = uvMap[faceName];
        
        // Calculate texture position
        const textureX = Math.floor(x1 + (x2 - x1) * uv.x);
        const textureY = Math.floor(y1 + (y2 - y1) * (1 - uv.y));
        
        // Apply the current tool
        if (this.currentTool === 'pencil') {
            this.drawPixel(textureX, textureY);
        } else if (this.currentTool === 'eraser') {
            this.erasePixel(textureX, textureY);
        } else if (this.currentTool === 'fill') {
            this.fillArea(textureX, textureY);
        }
        
        // Update the texture
        this.updateTexture();
    };
    
    // Refresh the model if the editor is already open
    if (SkinEditor.isActive && window.currentSkinTexture) {
        SkinEditor.createSkinModel(window.currentSkinTexture);
    }
    
    console.log("Simple texture orientation fix applied successfully");
}
