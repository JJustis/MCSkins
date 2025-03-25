// ===== FIX DOUBLE CANVAS LOADING =====
// This script fixes the issue of the canvas being loaded twice in the editor

// Apply the fix when the document is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Check for SkinEditor at regular intervals
    const checkInterval = setInterval(function() {
        if (typeof SkinEditor !== 'undefined') {
            clearInterval(checkInterval);
            fixDoubleCanvasIssue();
        }
    }, 100);
    
    // Stop checking after 5 seconds
    setTimeout(function() {
        clearInterval(checkInterval);
    }, 5000);
});

// Fix the double canvas issue
function fixDoubleCanvasIssue() {
    console.log("Applying fix for double canvas loading...");
    
    // Store the original setup3DView method
    const originalSetup3DView = SkinEditor.setup3DView;
    
    // Override the setup3DView method to prevent duplicate canvas creation
    SkinEditor.setup3DView = function() {
        // Get the container
        const container = document.getElementById('skin-3d-editor');
        
        // Check if a renderer already exists in the container
        const existingCanvas = container.querySelector('canvas');
        if (existingCanvas) {
            console.log("Canvas already exists, cleaning up before creating a new one");
            container.removeChild(existingCanvas);
        }
        
        // Call the original method
        originalSetup3DView.call(this);
    };
    
    // Override the open method to clean up any existing canvas
    const originalOpen = SkinEditor.open;
    
    SkinEditor.open = function(skinTexture) {
        console.log("Opening editor with canvas cleanup");
        
        // If the editor is already active, close it first to clean up
        if (this.isActive) {
            this.close();
        }
        
        // Reset key properties
        this.renderer = null;
        this.scene = null;
        this.camera = null;
        this.controls = null;
        this.editorModel = null;
        
        // Clean up any leftover canvases
        const container = document.getElementById('skin-3d-editor');
        const canvases = container.querySelectorAll('canvas');
        canvases.forEach(canvas => {
            container.removeChild(canvas);
        });
        
        // Call the original open method
        originalOpen.call(this, skinTexture);
    };
    
    // Also fix the close method to ensure proper cleanup
    const originalClose = SkinEditor.close;
    
    SkinEditor.close = function() {
        // Make sure we properly dispose of WebGL context and resources
        if (this.renderer) {
            this.renderer.forceContextLoss();
            this.renderer.dispose();
            
            // Remove the canvas
            const container = document.getElementById('skin-3d-editor');
            if (container && this.renderer.domElement && container.contains(this.renderer.domElement)) {
                container.removeChild(this.renderer.domElement);
            }
            
            this.renderer = null;
        }
        
        // Call the original close method
        originalClose.call(this);
    };
    
    console.log("Double canvas fix applied successfully");
}
