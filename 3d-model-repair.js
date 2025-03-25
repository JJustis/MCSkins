// ===== 3D MODEL REPAIR =====
// This script specifically fixes the flat model issue by recreating the model using BoxGeometry

// Apply the fix when the document is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Check for SkinEditor at regular intervals
    const checkInterval = setInterval(function() {
        if (typeof SkinEditor !== 'undefined') {
            clearInterval(checkInterval);
            apply3DModelRepair();
        }
    }, 100);
    
    // Stop checking after 5 seconds
    setTimeout(function() {
        clearInterval(checkInterval);
    }, 5000);
});

function apply3DModelRepair() {
    console.log("Applying 3D model repair...");
    
    // Completely replace the createSkinModel method with a proper 3D version
    SkinEditor.createSkinModel = function(texture) {
        console.log("Creating proper 3D skin model");
        
        // Remove any existing model
        if (this.editorModel) {
            this.scene.remove(this.editorModel);
        }
        
        // Create a new group to hold all parts
        this.editorModel = new THREE.Group();
        
        // Get current skin type
        const currentSkinType = window.skinType || 'default';
        console.log(`Creating model with ${currentSkinType} arm type`);
        
        // Determine the arm width
        const armWidth = currentSkinType === 'slim' ? 3 : 4;
        
        // Get UV mappings
        const uvMappings = window.uvMappings;
        
        // Create each body part using BoxGeometry
        // HEAD - Base
        this.createBodyPart(8, 8, 8, 0, 28, 0, texture, uvMappings.head.base, 'head', false);
        
        // HEAD - Overlay
        this.createBodyPart(8.5, 8.5, 8.5, 0, 28, 0, texture, uvMappings.head.overlay, 'head-overlay', true);
        
        // BODY - Base
        this.createBodyPart(8, 12, 4, 0, 18, 0, texture, uvMappings.body.base, 'body', false);
        
        // BODY - Overlay
        this.createBodyPart(8.5, 12.5, 4.5, 0, 18, 0, texture, uvMappings.body.overlay, 'body-overlay', true);
        
        // RIGHT ARM - Base
        this.createBodyPart(armWidth, 12, 4, -(4 + armWidth/2), 18, 0, texture, uvMappings.rightArm.base, 'right-arm', false);
        
        // RIGHT ARM - Overlay
        this.createBodyPart(armWidth + 0.5, 12.5, 4.5, -(4 + armWidth/2), 18, 0, texture, uvMappings.rightArm.overlay, 'right-arm-overlay', true);
        
        // LEFT ARM - Base
        this.createBodyPart(armWidth, 12, 4, 4 + armWidth/2, 18, 0, texture, uvMappings.leftArm.base, 'left-arm', false);
        
        // LEFT ARM - Overlay
        this.createBodyPart(armWidth + 0.5, 12.5, 4.5, 4 + armWidth/2, 18, 0, texture, uvMappings.leftArm.overlay, 'left-arm-overlay', true);
        
        // RIGHT LEG - Base
        this.createBodyPart(4, 12, 4, -2, 6, 0, texture, uvMappings.rightLeg.base, 'right-leg', false);
        
        // RIGHT LEG - Overlay
        this.createBodyPart(4.5, 12.5, 4.5, -2, 6, 0, texture, uvMappings.rightLeg.overlay, 'right-leg-overlay', true);
        
        // LEFT LEG - Base
        this.createBodyPart(4, 12, 4, 2, 6, 0, texture, uvMappings.leftLeg.base, 'left-leg', false);
        
        // LEFT LEG - Overlay
        this.createBodyPart(4.5, 12.5, 4.5, 2, 6, 0, texture, uvMappings.leftLeg.overlay, 'left-leg-overlay', true);
        
        // Add the model to the scene
        this.scene.add(this.editorModel);
        
        // Position camera correctly
        if (this.camera) {
            this.camera.position.set(0, 25, 100);
            this.camera.lookAt(0, 20, 0);
        }
        
        console.log("3D skin model created successfully");
    };
    
    // Add createBodyPart method to replace createEditableCuboid
    SkinEditor.createBodyPart = function(width, height, depth, x, y, z, texture, uvMap, name, transparent) {
        // Create geometry
        const geometry = new THREE.BoxGeometry(width, height, depth);
        
        // Create materials for each face
        const materials = [];
        
        // Face order in THREE.js: right (+X), left (-X), top (+Y), bottom (-Y), front (+Z), back (-Z)
        const faceOrder = ['right', 'left', 'up', 'down', 'front', 'back'];
        
        for (let i = 0; i < faceOrder.length; i++) {
            const faceName = faceOrder[i];
            
            // Get UV coordinates for this face
            const faceUV = uvMap[faceName];
            
            if (!faceUV) {
                console.error(`Missing UV mapping for face: ${faceName}`);
                materials.push(new THREE.MeshBasicMaterial({ color: 0xff00ff }));
                continue;
            }
            
            // Clone the texture for this face
            const faceTexture = texture.clone();
            faceTexture.needsUpdate = true;
            
            // Get texture dimensions
            const textureWidth = texture.image.width;
            const textureHeight = texture.image.height;
            
            // Extract UV coordinates
            const [x1, y1, x2, y2] = faceUV;
            
            // Convert to normalized UV coordinates
            const u1 = x1 / textureWidth;
            const v1 = 1 - y2 / textureHeight;
            const u2 = x2 / textureWidth;
            const v2 = 1 - y1 / textureHeight;
            
            // Set texture mapping
            faceTexture.offset.set(u1, v1);
            faceTexture.repeat.set(u2 - u1, v2 - v1);
            
            // Create material
            const material = new THREE.MeshLambertMaterial({
                map: faceTexture,
                transparent: transparent,
                alphaTest: 0.5,
                side: THREE.FrontSide
            });
            
            // Store metadata for raycasting and editing
            material.userData = {
                partName: name,
                faceName: faceName,
                faceIndex: i,
                uvRect: faceUV
            };
            
            materials.push(material);
        }
        
        // Create mesh with materials array (one material per face)
        const mesh = new THREE.Mesh(geometry, materials);
        mesh.position.set(x, y, z);
        mesh.name = name;
        
        // Store part information for interaction
        mesh.userData = {
            partName: name,
            uvMap: uvMap
        };
        
        // Add to model
        this.editorModel.add(mesh);
        
        return mesh;
    };
    
    // Fix the highlightPixel method to work with the new BoxGeometry model
    SkinEditor.highlightPixel = function() {
        // Raycast to find intersected object
        this.raycaster.setFromCamera(this.mouse, this.camera);
        const intersects = this.raycaster.intersectObjects(this.editorModel.children, true);
        
        // Remove previous highlight if any
        if (this.highlightedMesh && this.highlightedMesh.material) {
            if (Array.isArray(this.highlightedMesh.material)) {
                this.highlightedMesh.material.forEach(mat => {
                    if (mat.emissive) mat.emissive.setRGB(0, 0, 0);
                });
            } else if (this.highlightedMesh.material.emissive) {
                this.highlightedMesh.material.emissive.setRGB(0, 0, 0);
            }
        }
        
        if (intersects.length > 0) {
            const intersect = intersects[0];
            const mesh = intersect.object;
            
            // Store highlighted mesh and face
            this.highlightedMesh = mesh;
            this.highlightedFace = intersect.face;
            
            // Highlight the mesh
            if (mesh.material) {
                if (Array.isArray(mesh.material)) {
                    // Determine which face was hit
                    const faceIndex = Math.floor(intersect.faceIndex / 2);
                    
                    // Highlight only the hit face
                    if (mesh.material[faceIndex] && mesh.material[faceIndex].emissive) {
                        mesh.material[faceIndex].emissive.setRGB(0.2, 0.2, 0.2);
                    }
                } else if (mesh.material.emissive) {
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
    
    // Fix the updatePixelInfo method to work with the new BoxGeometry model
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
    
    // Fix the performPaintAction method to work with the new BoxGeometry model
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
        
        console.log(`Drawing at position: ${textureX}, ${textureY}`);
        
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
    
    // If the editor is already open, refresh it
    if (SkinEditor.isActive && window.currentSkinTexture) {
        console.log("Reapplying 3D model fix to currently open editor");
        const currentTexture = window.currentSkinTexture;
        SkinEditor.close();
        setTimeout(() => {
            SkinEditor.open(currentTexture);
        }, 100);
    }
    
    console.log("3D model repair applied successfully");
}
