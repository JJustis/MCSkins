// ===== DIRECT TEXTURE MAPPING FIX =====
// This script directly modifies THREE.js BoxGeometry UV mapping to fix upside-down textures

// Execute when document is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Wait for SkinEditor to be available
    const checkInterval = setInterval(function() {
        if (typeof SkinEditor !== 'undefined') {
            clearInterval(checkInterval);
            applyDirectTextureFix();
        }
    }, 100);
    
    // Stop checking after 5 seconds
    setTimeout(function() {
        clearInterval(checkInterval);
    }, 5000);
});

function applyDirectTextureFix() {
    console.log("Applying direct texture mapping fix...");
    
    // Override THREE.js BoxGeometry to use our custom UV mapping
    const originalBoxGeometry = THREE.BoxGeometry;
    
    THREE.BoxGeometry = function(width, height, depth, widthSegments, heightSegments, depthSegments) {
        // Create the original geometry
        const geometry = new originalBoxGeometry(width, height, depth, widthSegments, heightSegments, depthSegments);
        
        // Flip UV coordinates for all faces
        if (geometry.attributes && geometry.attributes.uv) {
            const uvs = geometry.attributes.uv.array;
            
            // Loop through each face (6 faces, 4 vertices per face, 2 coordinates per vertex)
            for (let i = 0; i < 6; i++) {
                const faceOffset = i * 4 * 2;
                
                // Swap the v coordinates to flip vertically
                // Original: (u1,v1), (u2,v1), (u1,v2), (u2,v2)
                // Flipped:  (u1,v2), (u2,v2), (u1,v1), (u2,v1)
                const v1 = uvs[faceOffset + 1];
                const v2 = uvs[faceOffset + 3];
                const v3 = uvs[faceOffset + 5];
                const v4 = uvs[faceOffset + 7];
                
                // Apply the flipped coordinates
                uvs[faceOffset + 1] = 1 - v1;
                uvs[faceOffset + 3] = 1 - v2;
                uvs[faceOffset + 5] = 1 - v3;
                uvs[faceOffset + 7] = 1 - v4;
            }
            
            // Mark UVs as needing update
            geometry.attributes.uv.needsUpdate = true;
        }
        
        return geometry;
    };
    
    // Add method to create a correct body part
    SkinEditor.createBodyPart = function(width, height, depth, x, y, z, texture, uvMap, name, transparent = false) {
        // Create geometry
        const geometry = new THREE.BoxGeometry(width, height, depth);
        
        // Create materials array for the 6 faces
        const materials = [];
        
        // Face order in THREE.js: right, left, top, bottom, front, back
        const faceNames = ['right', 'left', 'up', 'down', 'front', 'back'];
        
        for (let i = 0; i < 6; i++) {
            const faceName = faceNames[i];
            const faceUV = uvMap[faceName];
            
            if (!faceUV) {
                console.warn(`Missing UV mapping for face: ${faceName}`);
                materials.push(new THREE.MeshBasicMaterial({ color: 0xff00ff }));
                continue;
            }
            
            // Clone the texture for this face
            const faceTexture = texture.clone();
            faceTexture.needsUpdate = true;
            
            // Extract UV coordinates
            const [x1, y1, x2, y2] = faceUV;
            
            // Get texture dimensions
            const textureWidth = texture.image.width;
            const textureHeight = texture.image.height;
            
            // Convert to normalized UV coordinates
            const u1 = x1 / textureWidth;
            const v1 = 1 - (y2 / textureHeight);  // Flip Y
            const u2 = x2 / textureWidth;
            const v2 = 1 - (y1 / textureHeight);  // Flip Y
            
            // Apply mapping
            faceTexture.offset.set(u1, v1);
            faceTexture.repeat.set(u2 - u1, v2 - v1);
            
            // Create material
            const material = new THREE.MeshLambertMaterial({
                map: faceTexture,
                transparent: transparent,
                alphaTest: 0.5
            });
            
            // Store part info
            material.userData = {
                partName: name,
                faceName: faceName,
                uvRect: faceUV
            };
            
            materials.push(material);
        }
        
        // Create mesh
        const mesh = new THREE.Mesh(geometry, materials);
        mesh.position.set(x, y, z);
        mesh.name = name;
        
        // Store UV info in mesh
        mesh.userData = {
            uvMap: uvMap,
            partName: name
        };
        
        return mesh;
    };
    
    // Override the createSkinModel method
    SkinEditor.createSkinModel = function(texture) {
        console.log("Creating skin model with direct texture mapping fix");
        
        // Remove any existing model
        if (this.editorModel) {
            this.scene.remove(this.editorModel);
        }
        
        // Create a group to hold all parts
        this.editorModel = new THREE.Group();
        
        // Get current skin type
        const currentSkinType = window.skinType || 'default';
        const armWidth = currentSkinType === 'slim' ? 3 : 4;
        
        // Create all body parts
        // HEAD - Base
        const head = this.createBodyPart(
            8, 8, 8,
            0, 28, 0,
            texture,
            window.uvMappings.head.base,
            'head'
        );
        this.editorModel.add(head);
        
        // HEAD - Overlay
        const headOverlay = this.createBodyPart(
            8.5, 8.5, 8.5,
            0, 28, 0,
            texture,
            window.uvMappings.head.overlay,
            'head-overlay',
            true
        );
        this.editorModel.add(headOverlay);
        
        // BODY - Base
        const body = this.createBodyPart(
            8, 12, 4,
            0, 18, 0,
            texture,
            window.uvMappings.body.base,
            'body'
        );
        this.editorModel.add(body);
        
        // BODY - Overlay
        const bodyOverlay = this.createBodyPart(
            8.5, 12.5, 4.5,
            0, 18, 0,
            texture,
            window.uvMappings.body.overlay,
            'body-overlay',
            true
        );
        this.editorModel.add(bodyOverlay);
        
        // RIGHT ARM - Base
        const rightArm = this.createBodyPart(
            armWidth, 12, 4,
            -(4 + armWidth/2), 18, 0,
            texture,
            window.uvMappings.rightArm.base,
            'right-arm'
        );
        this.editorModel.add(rightArm);
        
        // RIGHT ARM - Overlay
        const rightArmOverlay = this.createBodyPart(
            armWidth + 0.5, 12.5, 4.5,
            -(4 + armWidth/2), 18, 0,
            texture,
            window.uvMappings.rightArm.overlay,
            'right-arm-overlay',
            true
        );
        this.editorModel.add(rightArmOverlay);
        
        // LEFT ARM - Base
        const leftArm = this.createBodyPart(
            armWidth, 12, 4,
            4 + armWidth/2, 18, 0,
            texture,
            window.uvMappings.leftArm.base,
            'left-arm'
        );
        this.editorModel.add(leftArm);
        
        // LEFT ARM - Overlay
        const leftArmOverlay = this.createBodyPart(
            armWidth + 0.5, 12.5, 4.5,
            4 + armWidth/2, 18, 0,
            texture,
            window.uvMappings.leftArm.overlay,
            'left-arm-overlay',
            true
        );
        this.editorModel.add(leftArmOverlay);
        
        // RIGHT LEG - Base
        const rightLeg = this.createBodyPart(
            4, 12, 4,
            -2, 6, 0,
            texture,
            window.uvMappings.rightLeg.base,
            'right-leg'
        );
        this.editorModel.add(rightLeg);
        
        // RIGHT LEG - Overlay
        const rightLegOverlay = this.createBodyPart(
            4.5, 12.5, 4.5,
            -2, 6, 0,
            texture,
            window.uvMappings.rightLeg.overlay,
            'right-leg-overlay',
            true
        );
        this.editorModel.add(rightLegOverlay);
        
        // LEFT LEG - Base
        const leftLeg = this.createBodyPart(
            4, 12, 4,
            2, 6, 0,
            texture,
            window.uvMappings.leftLeg.base,
            'left-leg'
        );
        this.editorModel.add(leftLeg);
        
        // LEFT LEG - Overlay
        const leftLegOverlay = this.createBodyPart(
            4.5, 12.5, 4.5,
            2, 6, 0,
            texture,
            window.uvMappings.leftLeg.overlay,
            'left-leg-overlay',
            true
        );
        this.editorModel.add(leftLegOverlay);
        
        // Add the model to the scene
        this.scene.add(this.editorModel);
        
        console.log('Skin model created with direct texture mapping fix');
    };
    
    // Fix the highlightPixel and updatePixelInfo methods
    SkinEditor.updatePixelInfo = function(intersect) {
        if (!intersect || !intersect.object) return;
        
        const mesh = intersect.object;
        const faceIndex = Math.floor(intersect.faceIndex / 2);
        
        // Get material for the face we hit
        if (!Array.isArray(mesh.material) || !mesh.material[faceIndex]) {
            return;
        }
        
        const material = mesh.material[faceIndex];
        if (!material.userData || !material.userData.uvRect) {
            return;
        }
        
        // Get UV rect from material
        const uvRect = material.userData.uvRect;
        const uv = intersect.uv;
        
        // Calculate pixel coordinates
        const [x1, y1, x2, y2] = uvRect;
        const textureX = Math.floor(x1 + (x2 - x1) * uv.x);
        const textureY = Math.floor(y1 + (y2 - y1) * (1 - uv.y));
        
        // Get pixel color
        const pixelData = this.getPixelColor(textureX, textureY);
        const rgba = `rgba(${pixelData[0]}, ${pixelData[1]}, ${pixelData[2]}, ${pixelData[3]/255})`;
        const hex = this.rgbaToHex(pixelData[0], pixelData[1], pixelData[2]);
        
        // Update pixel info display
        const partName = material.userData.partName || 'unknown';
        const faceName = material.userData.faceName || 'unknown';
        
        document.getElementById('pixel-position').textContent = `${textureX}, ${textureY}`;
        document.getElementById('pixel-color').textContent = `${hex} (${rgba})`;
        document.getElementById('pixel-body-part').textContent = `${partName} (${faceName})`;
    };
    
    // Fix the performPaintAction method
    SkinEditor.performPaintAction = function() {
        if (!this.highlightedMesh || !this.highlightedFace) return;
        
        const mesh = this.highlightedMesh;
        const faceIndex = Math.floor(this.highlightedFace.faceIndex / 2);
        
        // Get material for the face we hit
        if (!Array.isArray(mesh.material) || !mesh.material[faceIndex]) {
            return;
        }
        
        const material = mesh.material[faceIndex];
        if (!material.userData || !material.userData.uvRect) {
            return;
        }
        
        // Get updated intersection
        const intersects = this.raycaster.intersectObject(mesh);
        if (intersects.length === 0) return;
        
        const intersect = intersects[0];
        const uv = intersect.uv;
        
        // Get UV rect from material
        const uvRect = material.userData.uvRect;
        
        // Calculate pixel coordinates
        const [x1, y1, x2, y2] = uvRect;
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
    
    // Fix the highlightPixel method
    SkinEditor.highlightPixel = function() {
        // Raycast to find intersected object
        this.raycaster.setFromCamera(this.mouse, this.camera);
        const intersects = this.raycaster.intersectObjects(this.editorModel.children, true);
        
        // Remove previous highlight if any
        if (this.highlightedMesh && this.highlightedMesh.material) {
            if (Array.isArray(this.highlightedMesh.material)) {
                this.highlightedMesh.material.forEach(mat => {
                    if (mat) mat.emissive.setRGB(0, 0, 0);
                });
            } else {
                this.highlightedMesh.material.emissive.setRGB(0, 0, 0);
            }
        }
        
        if (intersects.length > 0) {
            const intersect = intersects[0];
            const mesh = intersect.object;
            
            // Store highlighted mesh and face
            this.highlightedMesh = mesh;
            this.highlightedFace = intersect.face;
            
            // Highlight the mesh - add a slight glow to the material
            if (mesh.material) {
                if (Array.isArray(mesh.material)) {
                    const faceIndex = Math.floor(intersect.faceIndex / 2);
                    if (mesh.material[faceIndex]) {
                        mesh.material[faceIndex].emissive.setRGB(0.2, 0.2, 0.2);
                    }
                } else {
                    mesh.material.emissive.setRGB(0.2, 0.2, 0.2);
                }
            }
            
            // Update pixel info
            this.updatePixelInfo(intersect);
        } else {
            // Clear pixel info if no intersection
            this.clearPixelInfo();
        }
    };
    
    // Force refresh of the model if the editor is open
    if (SkinEditor.isActive && window.currentSkinTexture) {
        SkinEditor.createSkinModel(window.currentSkinTexture);
    }
    
    console.log("Direct texture mapping fix applied successfully");
}
