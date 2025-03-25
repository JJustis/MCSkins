// ===== DIRECT MODEL DRAWING FIX =====
// This script enables direct drawing on the 3D model and ensures texture updates properly

// Apply the fix when the document is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Check for SkinEditor at regular intervals
    const checkInterval = setInterval(function() {
        if (typeof SkinEditor !== 'undefined') {
            clearInterval(checkInterval);
            applyDirectDrawingFix();
        }
    }, 100);
    
    // Stop checking after 5 seconds
    setTimeout(function() {
        clearInterval(checkInterval);
    }, 5000);
});

function applyDirectDrawingFix() {
    console.log("Applying direct drawing fix...");
    
    // Fix the updateTexture method to ensure textures update properly when drawn on
    SkinEditor.updateTexture = function() {
        console.log("Updating all textures with drawn changes");
        
        // Ensure the texture canvas has been created
        if (!this.textureCanvas) {
            console.error("Texture canvas not available");
            return;
        }
        
        // Get the canvas context
        const ctx = this.textureCanvas.getContext('2d');
        
        // Create a new texture from the updated canvas
        const newTexture = new THREE.Texture(this.textureCanvas);
        newTexture.magFilter = THREE.NearestFilter;
        newTexture.minFilter = THREE.NearestFilter;
        newTexture.needsUpdate = true;
        
        // Update all materials in the model
        this.editorModel.traverse(child => {
            if (child.isMesh && child.material) {
                // Handle array of materials (for BoxGeometry)
                if (Array.isArray(child.material)) {
                    child.material.forEach(material => {
                        if (material && material.map) {
                            // Store the original UV mapping settings
                            const originalOffset = material.map.offset.clone();
                            const originalRepeat = material.map.repeat.clone();
                            
                            // Update the texture
                            material.map = newTexture.clone();
                            
                            // Restore UV mapping settings
                            material.map.offset.copy(originalOffset);
                            material.map.repeat.copy(originalRepeat);
                            material.map.needsUpdate = true;
                        }
                    });
                } 
                // Handle single material
                else if (child.material.map) {
                    // Store the original UV mapping settings
                    const originalOffset = child.material.map.offset.clone();
                    const originalRepeat = child.material.map.repeat.clone();
                    
                    // Update the texture
                    child.material.map = newTexture.clone();
                    
                    // Restore UV mapping settings
                    child.material.map.offset.copy(originalOffset);
                    child.material.map.repeat.copy(originalRepeat);
                    child.material.map.needsUpdate = true;
                }
            }
        });
        
        // Update the texture preview
        this.updateTexturePreview();
    };
    
    // Improve mouse interaction for better drawing
    SkinEditor.onMouseDown = function(event) {
        this.painting = true;
        this.updateMousePosition(event);
        this.performPaintAction();
    };
    
    SkinEditor.onMouseMove = function(event) {
        this.updateMousePosition(event);
        
        // Raycast to highlight the pixel under the cursor
        this.highlightPixel();
        
        // If painting, perform the paint action
        if (this.painting) {
            this.performPaintAction();
        }
    };
    
    // Add continuous drawing support
    const originalPerformPaintAction = SkinEditor.performPaintAction;
    SkinEditor.performPaintAction = function() {
        // Call the original method
        originalPerformPaintAction.call(this);
        
        // Store the last drawn coordinates for continuous drawing
        if (this.highlightedMesh && this.highlightedFace) {
            this.lastDrawnMesh = this.highlightedMesh;
            this.lastDrawnFace = this.highlightedFace;
            this.lastDrawnPosition = this.raycaster.ray.origin.clone();
        }
    };
    
    // Override the setup3DView method to add direct drawing events
    const originalSetup3DView = SkinEditor.setup3DView;
    SkinEditor.setup3DView = function() {
        // Call the original method
        originalSetup3DView.call(this);
        
        // Add event listeners for painting if not already added
        if (this.renderer && this.renderer.domElement) {
            console.log("Adding drawing event listeners to renderer");
            
            // Remove any existing listeners to prevent duplicates
            this.renderer.domElement.removeEventListener('mousedown', this.onMouseDown.bind(this));
            this.renderer.domElement.removeEventListener('mousemove', this.onMouseMove.bind(this));
            this.renderer.domElement.removeEventListener('mouseup', this.onMouseUp.bind(this));
            this.renderer.domElement.removeEventListener('mouseleave', this.onMouseLeave.bind(this));
            
            // Add fresh event listeners
            this.renderer.domElement.addEventListener('mousedown', this.onMouseDown.bind(this));
            this.renderer.domElement.addEventListener('mousemove', this.onMouseMove.bind(this));
            this.renderer.domElement.addEventListener('mouseup', this.onMouseUp.bind(this));
            this.renderer.domElement.addEventListener('mouseleave', this.onMouseLeave.bind(this));
        }
    };
    
    // Fix the drawPixel method to make drawing more responsive
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
        
        // Update the texture immediately to see the changes on the model
        this.updateTexture();
    };
    
    // If the editor is already open, refresh it
    if (SkinEditor.isActive && window.currentSkinTexture) {
        console.log("Reapplying direct drawing fix to currently open editor");
        const currentTexture = window.currentSkinTexture;
        SkinEditor.close();
        setTimeout(() => {
            SkinEditor.open(currentTexture);
        }, 100);
    }
    
    console.log("Direct drawing fix applied successfully");
}
