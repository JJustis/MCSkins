// ===== COMPREHENSIVE TEXTURE AND DRAWING FIX =====
// This script fixes both the upside-down texture and the drawing functionality

// Apply the fix when the document is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Check for SkinEditor at regular intervals
    const checkInterval = setInterval(function() {
        if (typeof SkinEditor !== 'undefined') {
            clearInterval(checkInterval);
            applyComprehensiveFix();
        }
    }, 100);
    
    // Stop checking after 5 seconds
    setTimeout(function() {
        clearInterval(checkInterval);
    }, 5000);
});

function applyComprehensiveFix() {
    console.log("Applying comprehensive texture and drawing fix...");
    
    // Save original methods that we'll override
    const originalUpdatePixelInfo = SkinEditor.updatePixelInfo;
    const originalPerformPaintAction = SkinEditor.performPaintAction;
    const originalDrawPixel = SkinEditor.drawPixel;
    const originalCreateEditableCuboid = SkinEditor.createEditableCuboid;
    const originalUpdateTexturePreview = SkinEditor.updateTexturePreview;
    
    // Override the createEditableCuboid method to fix UV mapping
    SkinEditor.createEditableCuboid = function(width, height, depth, x, y, z, texture, uvMap, partName, transparent = false) {
        console.log(`Creating editable cuboid for ${partName}`);
        
        const group = new THREE.Group();
        group.position.set(x, y, z);
        group.name = partName;
        
        // Define the six faces of the cuboid
        const faces = [
            { name: 'right', dir: [1, 0, 0], size: [depth, height], uvKey: 'right' },
            { name: 'left', dir: [-1, 0, 0], size: [depth, height], uvKey: 'left' },
            { name: 'top', dir: [0, 1, 0], size: [width, depth], uvKey: 'up' },
            { name: 'bottom', dir: [0, -1, 0], size: [width, depth], uvKey: 'down' },
            { name: 'front', dir: [0, 0, 1], size: [width, height], uvKey: 'front' },
            { name: 'back', dir: [0, 0, -1], size: [width, height], uvKey: 'back' }
        ];
        
        // Create a mesh for each face
        faces.forEach(face => {
            // Get UV coordinates for this face
            const uvCoords = uvMap[face.uvKey];
            if (!uvCoords) return;
            
            // Create a plane geometry for this face
            const geometry = new THREE.PlaneGeometry(face.size[0], face.size[1]);
            
            // Position and rotate the plane to be in the correct position
            geometry.translate(
                face.dir[0] * width / 2,
                face.dir[1] * height / 2,
                face.dir[2] * depth / 2
            );
            
            // Rotate to face outward
            if (face.dir[0] !== 0) {
                geometry.rotateY(Math.PI / 2 * -face.dir[0]);
            } else if (face.dir[1] !== 0) {
                geometry.rotateX(Math.PI / 2 * face.dir[1]);
            } else if (face.dir[2] === -1) {
                geometry.rotateY(Math.PI);
            }
            
            // Create a clone of the texture for this face
            const faceTexture = texture.clone();
            faceTexture.needsUpdate = true;
            
            // Extract UV coordinates
            const [x1, y1, x2, y2] = uvCoords;
            
            // Get texture dimensions
            const textureWidth = texture.image.width;
            const textureHeight = texture.image.height;
            
            // Convert pixel coordinates to normalized UV coordinates (0-1)
            // FIX: Use this specific calculation that works for Minecraft textures
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
                alphaTest: 0.5,
                side: THREE.FrontSide
            });
            
            // Store metadata for raycasting and editing
            material.userData = {
                partName: partName,
                faceName: face.name,
                uvRect: uvCoords,  // Store pixel coordinates for texture mapping
                faceIndex: faces.indexOf(face)  // Store face index for proper editing
            };
            
            // Create mesh
            const mesh = new THREE.Mesh(geometry, material);
            mesh.name = `${partName}-${face.name}`;
            
            // Store mesh metadata
            mesh.userData = {
                partName: partName,
                faceName: face.name,
                faceIndex: faces.indexOf(face)
            };
            
            group.add(mesh);
        });
        
        this.editorModel.add(group);
        return group;
    };
    
    // Override the updatePixelInfo method to fix coordinate calculations
    SkinEditor.updatePixelInfo = function(intersect) {
        if (!intersect || !intersect.object) return;
        
        const mesh = intersect.object;
        const faceIndex = Math.floor(intersect.faceIndex / 2);
        const uv = intersect.uv;
        
        if (!mesh.material || !mesh.userData) return;
        
        // Get metadata from the material
        const faceName = mesh.userData.faceName;
        const partName = mesh.userData.partName;
        
        // Find corresponding UVs for this face based on part name
        let uvMap, uvRect;
        
        if (partName.includes('head')) {
            uvMap = partName.includes('overlay') ? window.uvMappings.head.overlay : window.uvMappings.head.base;
        } else if (partName.includes('body')) {
            uvMap = partName.includes('overlay') ? window.uvMappings.body.overlay : window.uvMappings.body.base;
        } else if (partName.includes('right-arm')) {
            uvMap = partName.includes('overlay') ? window.uvMappings.rightArm.overlay : window.uvMappings.rightArm.base;
        } else if (partName.includes('left-arm')) {
            uvMap = partName.includes('overlay') ? window.uvMappings.leftArm.overlay : window.uvMappings.leftArm.base;
        } else if (partName.includes('right-leg')) {
            uvMap = partName.includes('overlay') ? window.uvMappings.rightLeg.overlay : window.uvMappings.rightLeg.base;
        } else if (partName.includes('left-leg')) {
            uvMap = partName.includes('overlay') ? window.uvMappings.leftLeg.overlay : window.uvMappings.leftLeg.base;
        }
        
        if (uvMap && faceName) {
            uvRect = uvMap[faceName.toLowerCase()];
        }
        
        if (!uvRect) return;
        
        // Calculate pixel coordinates correctly
        const [x1, y1, x2, y2] = uvRect;
        
        // Convert UV to pixel coordinates
        const textureX = Math.floor(x1 + uv.x * (x2 - x1));
        const textureY = Math.floor(y1 + (1 - uv.y) * (y2 - y1));  // Corrected calculation
        
        // Get pixel color
        const pixelData = this.getPixelColor(textureX, textureY);
        const rgba = `rgba(${pixelData[0]}, ${pixelData[1]}, ${pixelData[2]}, ${pixelData[3]/255})`;
        const hex = this.rgbaToHex(pixelData[0], pixelData[1], pixelData[2]);
        
        // Update pixel info display
        document.getElementById('pixel-position').textContent = `${textureX}, ${textureY}`;
        document.getElementById('pixel-color').textContent = `${hex} (${rgba})`;
        document.getElementById('pixel-body-part').textContent = `${partName} (${faceName})`;
    };
    
    // Override the performPaintAction method to fix coordinate calculations when drawing
    SkinEditor.performPaintAction = function() {
        if (!this.highlightedMesh || !this.highlightedFace) return;
        
        const mesh = this.highlightedMesh;
        
        // Get updated intersection
        const intersects = this.raycaster.intersectObject(mesh);
        if (!intersects.length) return;
        
        const intersect = intersects[0];
        const uv = intersect.uv;
        
        if (!mesh.userData) return;
        
        // Get metadata from the material
        const faceName = mesh.userData.faceName;
        const partName = mesh.userData.partName;
        
        // Find corresponding UVs for this face based on part name
        let uvMap, uvRect;
        
        if (partName.includes('head')) {
            uvMap = partName.includes('overlay') ? window.uvMappings.head.overlay : window.uvMappings.head.base;
        } else if (partName.includes('body')) {
            uvMap = partName.includes('overlay') ? window.uvMappings.body.overlay : window.uvMappings.body.base;
        } else if (partName.includes('right-arm')) {
            uvMap = partName.includes('overlay') ? window.uvMappings.rightArm.overlay : window.uvMappings.rightArm.base;
        } else if (partName.includes('left-arm')) {
            uvMap = partName.includes('overlay') ? window.uvMappings.leftArm.overlay : window.uvMappings.leftArm.base;
        } else if (partName.includes('right-leg')) {
            uvMap = partName.includes('overlay') ? window.uvMappings.rightLeg.overlay : window.uvMappings.rightLeg.base;
        } else if (partName.includes('left-leg')) {
            uvMap = partName.includes('overlay') ? window.uvMappings.leftLeg.overlay : window.uvMappings.leftLeg.base;
        }
        
        if (uvMap && faceName) {
            uvRect = uvMap[faceName.toLowerCase()];
        }
        
        if (!uvRect) return;
        
        // Calculate pixel coordinates correctly
        const [x1, y1, x2, y2] = uvRect;
        
        // Convert UV to pixel coordinates
        const textureX = Math.floor(x1 + uv.x * (x2 - x1));
        const textureY = Math.floor(y1 + (1 - uv.y) * (y2 - y1));  // Corrected calculation
        
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
    
    // Override the drawPixel method to ensure proper drawing
    SkinEditor.drawPixel = function(x, y) {
        if (!this.textureContext) return;
        
        // Ensure coordinates are within bounds
        if (x < 0 || x >= this.textureCanvas.width || 
            y < 0 || y >= this.textureCanvas.height) {
            return;
        }
        
        // Get current color
        const color = document.getElementById('editor-color').value;
        
        // Draw pixel
        this.textureContext.fillStyle = color;
        this.textureContext.fillRect(x, y, 1, 1);
        
        // Update the preview
        this.updateTexturePreview();
    };
    
    // Improve the updateTexturePreview method to show a clearer grid
    SkinEditor.updateTexturePreview = function() {
        // Draw the current texture to the preview canvas
        const previewCanvas = document.getElementById('texture-preview-canvas');
        if (!previewCanvas || !this.textureCanvas) return;
        
        const ctx = previewCanvas.getContext('2d');
        
        // Set canvas dimensions to match the texture
        previewCanvas.width = this.textureCanvas.width;
        previewCanvas.height = this.textureCanvas.height;
        
        // Clear canvas
        ctx.clearRect(0, 0, previewCanvas.width, previewCanvas.height);
        
        // Draw texture to canvas
        ctx.drawImage(this.textureCanvas, 0, 0);
        
        // Add grid lines to make pixels visible
        ctx.beginPath();
        
        // Draw vertical grid lines
        for (let x = 0; x <= this.textureCanvas.width; x++) {
            ctx.moveTo(x + 0.5, 0);
            ctx.lineTo(x + 0.5, this.textureCanvas.height);
        }
        
        // Draw horizontal grid lines
        for (let y = 0; y <= this.textureCanvas.height; y++) {
            ctx.moveTo(0, y + 0.5);
            ctx.lineTo(this.textureCanvas.width, y + 0.5);
        }
        
        ctx.strokeStyle = 'rgba(128, 128, 128, 0.5)';
        ctx.stroke();
    };
    
    // Make sure the editor is properly initialized with a modified createSkinModel
    SkinEditor.createSkinModel = function(texture) {
        console.log("Creating skin model with fixed orientation");
        
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
        
        // Create head (base and overlay)
        this.createEditableCuboid(
            8, 8, 8,
            0, 28, 0,
            texture,
            window.uvMappings.head.base,
            'head'
        );
        
        this.createEditableCuboid(
            8.5, 8.5, 8.5,
            0, 28, 0,
            texture,
            window.uvMappings.head.overlay,
            'head-overlay',
            true
        );
        
        // Create body (base and overlay)
        this.createEditableCuboid(
            8, 12, 4,
            0, 18, 0,
            texture,
            window.uvMappings.body.base,
            'body'
        );
        
        this.createEditableCuboid(
            8.5, 12.5, 4.5,
            0, 18, 0,
            texture,
            window.uvMappings.body.overlay,
            'body-overlay',
            true
        );
        
        // Create right arm (base and overlay)
        this.createEditableCuboid(
            armWidth, 12, 4,
            -(4 + armWidth/2), 18, 0,
            texture,
            window.uvMappings.rightArm.base,
            'right-arm'
        );
        
        this.createEditableCuboid(
            armWidth + 0.5, 12.5, 4.5,
            -(4 + armWidth/2), 18, 0,
            texture,
            window.uvMappings.rightArm.overlay,
            'right-arm-overlay',
            true
        );
        
        // Create left arm (base and overlay)
        this.createEditableCuboid(
            armWidth, 12, 4,
            4 + armWidth/2, 18, 0,
            texture,
            window.uvMappings.leftArm.base,
            'left-arm'
        );
        
        this.createEditableCuboid(
            armWidth + 0.5, 12.5, 4.5,
            4 + armWidth/2, 18, 0,
            texture,
            window.uvMappings.leftArm.overlay,
            'left-arm-overlay',
            true
        );
        
        // Create right leg (base and overlay)
        this.createEditableCuboid(
            4, 12, 4,
            -2, 6, 0,
            texture,
            window.uvMappings.rightLeg.base,
            'right-leg'
        );
        
        this.createEditableCuboid(
            4.5, 12.5, 4.5,
            -2, 6, 0,
            texture,
            window.uvMappings.rightLeg.overlay,
            'right-leg-overlay',
            true
        );
        
        // Create left leg (base and overlay)
        this.createEditableCuboid(
            4, 12, 4,
            2, 6, 0,
            texture,
            window.uvMappings.leftLeg.base,
            'left-leg'
        );
        
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
        
        console.log("Fixed skin model created successfully");
    };
    
    // Make sure the editor UI is properly set up
    if (!document.getElementById('texture-preview-canvas')) {
        console.log("Creating texture preview canvas");
        const previewCanvas = document.createElement('canvas');
        previewCanvas.id = 'texture-preview-canvas';
        previewCanvas.width = 64;
        previewCanvas.height = 64;
        previewCanvas.style.width = '100%';
        previewCanvas.style.height = 'auto';
        previewCanvas.style.imageRendering = 'pixelated';
        
        // Find a place to add it in the editor
        const skinTexturePreview = document.querySelector('.skin-texture-preview');
        if (skinTexturePreview) {
            // Add after the title
            const title = skinTexturePreview.querySelector('h6');
            if (title) {
                title.after(previewCanvas);
            } else {
                skinTexturePreview.appendChild(previewCanvas);
            }
        }
    }
    
    // If the editor is already open, refresh it
    if (SkinEditor.isActive && window.currentSkinTexture) {
        console.log("Editor already open, refreshing with fixes");
        const currentTexture = window.currentSkinTexture;
        SkinEditor.close();
        setTimeout(() => {
            SkinEditor.open(currentTexture);
        }, 100);
    }
    
    console.log("Comprehensive texture and drawing fix applied successfully");
}
