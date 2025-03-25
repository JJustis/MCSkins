// ===== DRAWING AND PREVIEW FIX =====
// This script fixes both the drawing functionality and ensures the preview shows properly

// Apply the fix when the document is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Check for SkinEditor at regular intervals
    const checkInterval = setInterval(function() {
        if (typeof SkinEditor !== 'undefined') {
            clearInterval(checkInterval);
            applyDrawingPreviewFix();
        }
    }, 100);
    
    // Stop checking after 5 seconds
    setTimeout(function() {
        clearInterval(checkInterval);
    }, 5000);
});

function applyDrawingPreviewFix() {
    console.log("Applying drawing and preview fix...");
    
    // Override the updateTexturePreview method for better preview rendering
    SkinEditor.updateTexturePreview = function() {
        // Draw the current texture to the preview canvas
        const previewCanvas = document.getElementById('texture-preview-canvas');
        if (!previewCanvas || !this.textureCanvas) return;
        
        const ctx = previewCanvas.getContext('2d');
        
        // Set canvas dimensions to match the texture
        previewCanvas.width = this.textureCanvas.width;
        previewCanvas.height = this.textureCanvas.height;
        
        // Draw texture to canvas with pixel-perfect mapping
        ctx.imageSmoothingEnabled = false; // Ensures pixelated rendering
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
    
    // Add click-to-edit functionality on the preview canvas
    function setupPreviewCanvasEditing() {
        const previewCanvas = document.getElementById('texture-preview-canvas');
        if (!previewCanvas) return;
        
        // Add event listeners for mouse actions on the preview canvas
        previewCanvas.addEventListener('mousedown', handlePreviewCanvasClick);
        previewCanvas.addEventListener('mousemove', handlePreviewCanvasMove);
        
        // Global state for tracking drawing
        let isDrawing = false;
        
        // Handle click on preview canvas
        function handlePreviewCanvasClick(event) {
            isDrawing = true;
            drawOnPreviewCanvas(event);
        }
        
        // Handle mouse move on preview canvas
        function handlePreviewCanvasMove(event) {
            if (!isDrawing) return;
            drawOnPreviewCanvas(event);
        }
        
        // Stop drawing when mouse is released
        document.addEventListener('mouseup', function() {
            isDrawing = false;
        });
        
        // Draw on the preview canvas
        function drawOnPreviewCanvas(event) {
            if (!SkinEditor.textureCanvas || !SkinEditor.textureContext) return;
            
            // Get canvas position
            const rect = previewCanvas.getBoundingClientRect();
            
            // Calculate pixel coordinates
            const scaleX = previewCanvas.width / rect.width;
            const scaleY = previewCanvas.height / rect.height;
            
            const x = Math.floor((event.clientX - rect.left) * scaleX);
            const y = Math.floor((event.clientY - rect.top) * scaleY);
            
            // Safety check for coordinates
            if (x < 0 || x >= previewCanvas.width || y < 0 || y >= previewCanvas.height) {
                return;
            }
            
            console.log(`Drawing at preview position: ${x}, ${y}`);
            
            // Apply current tool
            if (SkinEditor.currentTool === 'pencil') {
                SkinEditor.drawPixel(x, y);
            } else if (SkinEditor.currentTool === 'eraser') {
                SkinEditor.erasePixel(x, y);
            } else if (SkinEditor.currentTool === 'fill' && !isDrawing) {
                // Only apply fill on initial click, not during drag
                SkinEditor.fillArea(x, y);
            }
            
            // Update the texture on the 3D model
            SkinEditor.updateTexture();
        }
    }
    
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
    
    // Override the open method to setup preview canvas editing
    const originalOpen = SkinEditor.open;
    SkinEditor.open = function(skinTexture) {
        console.log("Opening editor with drawing preview fix");
        
        // Call the original open method
        originalOpen.call(this, skinTexture);
        
        // Setup preview canvas editing after short delay to ensure canvas is created
        setTimeout(setupPreviewCanvasEditing, 500);
    };
    
    // Add direct canvas editing support for the preview canvas
    setupPreviewCanvasEditing();
    
    // If the editor is already open, refresh it
    if (SkinEditor.isActive && window.currentSkinTexture) {
        console.log("Editor already open, refreshing with drawing fix");
        const currentTexture = window.currentSkinTexture;
        SkinEditor.close();
        setTimeout(() => {
            SkinEditor.open(currentTexture);
        }, 100);
    }
    
    console.log("Drawing and preview fix applied successfully");
}
