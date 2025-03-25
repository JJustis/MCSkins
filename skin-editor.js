// Improved Skin Editor with Pixel Grid

// 1. First, let's create a separate JS file to load our dependencies
function loadEditorDependencies() {
    return new Promise((resolve, reject) => {
        // Check if THREE is already loaded
        if (typeof THREE === 'undefined') {
            console.error('THREE.js is required for the skin editor');
            reject('THREE.js not found');
            return;
        }
        
        // Load OrbitControls if not already loaded
        if (typeof THREE.OrbitControls === 'undefined') {
            const orbitScript = document.createElement('script');
            orbitScript.src = 'https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js';
            orbitScript.onload = () => {
                console.log('OrbitControls loaded');
                resolve();
            };
            orbitScript.onerror = () => reject('Failed to load OrbitControls');
            document.head.appendChild(orbitScript);
        } else {
            resolve();
        }
    });
}

// 2. Add button to enter edit mode
function addSkinEditorButton() {
    // Find the controls container
    const controlsContainer = document.querySelector('.d-flex.flex-wrap.gap-2.mt-3');
    if (!controlsContainer) {
        console.error('Controls container not found');
        return;
    }
    
    // Create the edit button
    const editButton = document.createElement('button');
    editButton.id = 'edit-skin-button';
    editButton.className = 'btn btn-warning';
    editButton.innerHTML = '<i class="fas fa-paint-brush me-1"></i> Edit Skin';
    
    // Add button to controls
    controlsContainer.appendChild(editButton);
    
    // Add event listener
    editButton.addEventListener('click', openSkinEditor);
}

// 3. Create the editor UI overlay
function createSkinEditorUI() {
    // Create editor container
    const editorContainer = document.createElement('div');
    editorContainer.id = 'skin-editor-container';
    editorContainer.className = 'd-none';
    editorContainer.innerHTML = `
        <div class="skin-editor-overlay">
            <div class="skin-editor-toolbar">
                <div class="tool-section">
                    <span class="editor-title">Skin Editor</span>
                    <div class="btn-group">
                        <button id="editor-zoom-in" class="btn btn-sm btn-dark" title="Zoom In">
                            <i class="fas fa-search-plus"></i>
                        </button>
                        <button id="editor-zoom-out" class="btn btn-sm btn-dark" title="Zoom Out">
                            <i class="fas fa-search-minus"></i>
                        </button>
                        <button id="editor-reset-view" class="btn btn-sm btn-dark" title="Reset View">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
                
                <div class="tool-section">
                    <div class="btn-group me-2">
                        <button id="editor-pencil" class="btn btn-sm btn-dark active" title="Pencil Tool">
                            <i class="fas fa-pencil-alt"></i>
                        </button>
                        <button id="editor-eraser" class="btn btn-sm btn-dark" title="Eraser Tool">
                            <i class="fas fa-eraser"></i>
                        </button>
                        <button id="editor-fill" class="btn btn-sm btn-dark" title="Fill Tool">
                            <i class="fas fa-fill-drip"></i>
                        </button>
                    </div>
                    
                    <div class="color-picker-container me-2">
                        <input type="color" id="editor-color" value="#FF0000">
                        <span class="color-label">Color</span>
                    </div>
                    
                    <div class="toggle-section me-2">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="editor-grid-toggle" checked>
                            <label class="form-check-label text-white" for="editor-grid-toggle">Show Grid</label>
                        </div>
                    </div>
                    
                    <div class="btn-group">
                        <button id="editor-save" class="btn btn-sm btn-success" title="Save Edits">
                            <i class="fas fa-save me-1"></i> Save
                        </button>
                        <button id="editor-cancel" class="btn btn-sm btn-danger" title="Cancel Edits">
                            <i class="fas fa-times me-1"></i> Cancel
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="editor-workspace">
                <div id="skin-3d-editor"></div>
                <div class="editor-sidebar">
                    <div class="skin-texture-preview">
                        <h6>Texture Preview</h6>
                        <canvas id="texture-preview-canvas"></canvas>
                    </div>
                    <div class="editor-info">
                        <h6>Pixel Information</h6>
                        <div id="pixel-info">
                            <p>Position: <span id="pixel-position">-</span></p>
                            <p>Color: <span id="pixel-color">-</span></p>
                            <p>Body Part: <span id="pixel-body-part">-</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Add CSS styles
    const styles = document.createElement('style');
    styles.textContent = `
        #skin-editor-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 9999;
            background-color: rgba(0, 0, 0, 0.9);
        }
        
        .skin-editor-overlay {
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        
        .skin-editor-toolbar {
            background-color: #333;
            color: white;
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #555;
        }
        
        .tool-section {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .editor-title {
            font-size: 18px;
            font-weight: bold;
            margin-right: 15px;
        }
        
        .color-picker-container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        #editor-color {
            width: 40px;
            height: 30px;
            padding: 2px;
            border: none;
            cursor: pointer;
        }
        
        .color-label {
            font-size: 10px;
            color: #ccc;
        }
        
        .editor-workspace {
            flex: 1;
            display: flex;
            overflow: hidden;
        }
        
        #skin-3d-editor {
            flex: 1;
            position: relative;
        }
        
        .editor-sidebar {
            width: 1199px;
            background-color: #2a2a2a;
            padding: 15px;
            border-left: 1px solid #444;
            overflow-y: auto;
        }
        
        .skin-texture-preview {
            margin-bottom: 20px;
        }
        
        #texture-preview-canvas {
            width: 1192px;
            height: 1192px;
            background-color: #333;
            border: 1px solid #555;
            image-rendering: pixelated;
        }
        
        .editor-info {
            color: #ddd;
        }
        
        .editor-info h6, .skin-texture-preview h6 {
            color: #fff;
            margin-bottom: 10px;
            border-bottom: 1px solid #555;
            padding-bottom: 5px;
        }
        
        #pixel-info p {
            margin-bottom: 5px;
            font-size: 14px;
        }
    `;
    
    // Add to the document
    document.body.appendChild(editorContainer);
    document.head.appendChild(styles);
}

// 4. Variables and state for the skin editor
const SkinEditor = {
    isActive: false,
    renderer: null,
    scene: null,
    camera: null,
    controls: null,
    original3DModel: null,
    editorModel: null,
    skinTexture: null,
    textureCanvas: null,
    textureContext: null,
    originalTextureData: null,
    pixelGridHelper: null,
    raycaster: new THREE.Raycaster(),
    mouse: new THREE.Vector2(),
    painting: false,
    currentTool: 'pencil',
    highlightedMesh: null,
    highlightedFace: null,
    showGrid: true,
    
    // Helper methods to manage the editor
    initialize: function() {
        this.setupEventListeners();
    },
    
    setupEventListeners: function() {
        // Tool buttons
        document.getElementById('editor-pencil').addEventListener('click', () => this.setTool('pencil'));
        document.getElementById('editor-eraser').addEventListener('click', () => this.setTool('eraser'));
        document.getElementById('editor-fill').addEventListener('click', () => this.setTool('fill'));
        
        // Camera controls
        document.getElementById('editor-zoom-in').addEventListener('click', () => this.zoomCamera(0.8));
        document.getElementById('editor-zoom-out').addEventListener('click', () => this.zoomCamera(1.2));
        document.getElementById('editor-reset-view').addEventListener('click', () => this.resetCameraView());
        
        // Grid toggle
        document.getElementById('editor-grid-toggle').addEventListener('change', (e) => {
            this.showGrid = e.target.checked;
            if (this.pixelGridHelper) {
                this.pixelGridHelper.visible = this.showGrid;
            }
        });
        
        // Save and cancel buttons
        document.getElementById('editor-save').addEventListener('click', () => this.saveEdits());
        document.getElementById('editor-cancel').addEventListener('click', () => this.close());
    },
    
    open: function(skinTexture) {
        if (!skinTexture) {
            alert('Please load a skin before editing');
            return;
        }
        
        this.isActive = true;
        
        // Show the editor
        const editorContainer = document.getElementById('skin-editor-container');
        editorContainer.classList.remove('d-none');
        
        // Clone the texture and save original state
        this.skinTexture = skinTexture.clone();
        
        // Create a canvas for editing
        this.textureCanvas = document.createElement('canvas');
        this.textureCanvas.width = this.skinTexture.image.width;
        this.textureCanvas.height = this.skinTexture.image.height;
        
        // Get context and draw original texture
        this.textureContext = this.textureCanvas.getContext('2d');
        this.textureContext.drawImage(this.skinTexture.image, 0, 0);
        
        // Store original pixel data for the eraser tool
        this.originalTextureData = this.textureContext.getImageData(
            0, 0, this.textureCanvas.width, this.textureCanvas.height
        );
        
        // Setup 3D view
        this.setup3DView();
        
        // Update texture preview
        this.updateTexturePreview();
        
        // Start render loop
        this.animate();
    },
    
    setup3DView: function() {
        const container = document.getElementById('skin-3d-editor');
        
        // Create scene
        this.scene = new THREE.Scene();
        this.scene.background = new THREE.Color(0x333333);
        
        // Create camera
        this.camera = new THREE.PerspectiveCamera(
            45,
            container.clientWidth / container.clientHeight,
            0.1,
            1000
        );
        this.camera.position.set(0, 20, 40);
        
        // Create renderer
        this.renderer = new THREE.WebGLRenderer({ antialias: true });
        this.renderer.setSize(container.clientWidth, container.clientHeight);
        container.appendChild(this.renderer.domElement);
        
        // Add lights
        const ambientLight = new THREE.AmbientLight(0xffffff, 0.7);
        this.scene.add(ambientLight);
        
        const directionalLight = new THREE.DirectionalLight(0xffffff, 0.8);
        directionalLight.position.set(1, 1, 1);
        this.scene.add(directionalLight);
        
        // Add orbit controls
        this.controls = new THREE.OrbitControls(this.camera, this.renderer.domElement);
        this.controls.enableDamping = true;
        this.controls.dampingFactor = 0.25;
        
        // Create a texture from the canvas
        const texture = new THREE.Texture(this.textureCanvas);
        texture.magFilter = THREE.NearestFilter;
        texture.minFilter = THREE.NearestFilter;
        texture.needsUpdate = true;
        
        // Create the skin model with the editable texture
        this.createSkinModel(texture);
        
        // Add pixel grid
        this.createPixelGrid();
        
        // Add event listeners for painting
        this.renderer.domElement.addEventListener('mousedown', this.onMouseDown.bind(this));
        this.renderer.domElement.addEventListener('mousemove', this.onMouseMove.bind(this));
        this.renderer.domElement.addEventListener('mouseup', this.onMouseUp.bind(this));
        this.renderer.domElement.addEventListener('mouseleave', this.onMouseLeave.bind(this));
        
        // Handle window resize
        window.addEventListener('resize', this.onWindowResize.bind(this));
    },
    
    createSkinModel: function(texture) {
        // Create a group to hold all parts
        this.editorModel = new THREE.Group();
        
        // Determine the skin format and type
        const skinFormat = detectSkinFormat(texture);
        const armWidth = skinType === 'slim' ? 3 : 4;
        
        // Create model parts with the editable texture
        // For brevity, we'll focus on the head first since it's the most visible part
        
        // HEAD - creating with individual face meshes for better editing
        this.createEditableCuboid(
            8, 8, 8,        // width, height, depth
            0, 28, 0,       // position x, y, z
            texture,        // texture
            uvMappings.head.base,  // UV mapping
            'head'          // part name
        );
        
        // HEAD OVERLAY
        this.createEditableCuboid(
            8.5, 8.5, 8.5,  // slightly larger
            0, 28, 0,       // same position
            texture,
            uvMappings.head.overlay,
            'head-overlay',
            true            // transparent
        );
        
        // BODY
        this.createEditableCuboid(
            8, 12, 4,
            0, 18, 0,
            texture,
            uvMappings.body.base,
            'body'
        );
        
        // Add other body parts similarly...
        // RIGHT ARM, LEFT ARM, RIGHT LEG, LEFT LEG, etc.
        
        // Add the model to the scene
        this.scene.add(this.editorModel);
    },
    
    createEditableCuboid: function(width, height, depth, x, y, z, texture, uvMap, partName, transparent = false) {
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
            
            // Calculate texture coordinates
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
                uvRect: uvCoords  // Store pixel coordinates for texture mapping
            };
            
            // Create mesh
            const mesh = new THREE.Mesh(geometry, material);
            mesh.name = `${partName}-${face.name}`;
            
            // Store mesh metadata
            mesh.userData = {
                partName: partName,
                faceName: face.name
            };
            
            group.add(mesh);
        });
        
        this.editorModel.add(group);
        return group;
    },
    
    createPixelGrid: function() {
        // Remove existing grid if any
        if (this.pixelGridHelper) {
            this.scene.remove(this.pixelGridHelper);
        }
        
        // Create a group to hold all grid lines
        this.pixelGridHelper = new THREE.Group();
        this.pixelGridHelper.name = 'pixelGridHelper';
        
        // Create grid material
        const gridMaterial = new THREE.LineBasicMaterial({ 
            color: 0xffffff, 
            transparent: true, 
            opacity: 0.3,
            depthTest: false
        });
        
        // For each part of the model, add grid lines
        this.editorModel.traverse(child => {
            if (child.isMesh) {
                const geometry = child.geometry;
                const position = child.position.clone();
                const rotation = child.rotation.clone();
                const scale = child.scale.clone();
                
                // Get the bounds of this mesh
                geometry.computeBoundingBox();
                const box = geometry.boundingBox;
                
                // Create grid lines on the face
                const gridGeometry = new THREE.BufferGeometry();
                const positions = [];
                
                // Calculate grid size based on UV mapping
                // This is a simplified approach - in a real implementation,
                // you'd calculate the precise grid based on the texture UV mapping
                const gridSize = 1; // 1 unit = 1 pixel in texture space
                
                // Get size in each dimension
                const sizeX = box.max.x - box.min.x;
                const sizeY = box.max.y - box.min.y;
                const sizeZ = box.max.z - box.min.z;
                
                // Helper function to add grid lines on a face
                const addGridLines = (start, end, axis, count) => {
                    const step = (end - start) / count;
                    const a1 = (axis + 1) % 3;
                    const a2 = (axis + 2) % 3;
                    
                    for (let i = 0; i <= count; i++) {
                        const pos = start + i * step;
                        const line = [0, 0, 0, 0, 0, 0];
                        
                        line[axis] = pos;
                        line[a1] = box.min[Object.keys(box.min)[a1]];
                        line[a2] = box.min[Object.keys(box.min)[a2]];
                        
                        line[axis + 3] = pos;
                        line[a1 + 3] = box.max[Object.keys(box.max)[a1]];
                        line[a2 + 3] = box.min[Object.keys(box.min)[a2]];
                        
                        positions.push(...line);
                        
                        line[axis] = pos;
                        line[a1] = box.min[Object.keys(box.min)[a1]];
                        line[a2] = box.min[Object.keys(box.min)[a2]];
                        
                        line[axis + 3] = pos;
                        line[a1 + 3] = box.min[Object.keys(box.min)[a1]];
                        line[a2 + 3] = box.max[Object.keys(box.max)[a2]];
                        
                        positions.push(...line);
                    }
                };
                
                // Add grid lines on each axis
                if (sizeX > 0.1) addGridLines(box.min.x, box.max.x, 0, Math.round(sizeX / gridSize));
                if (sizeY > 0.1) addGridLines(box.min.y, box.max.y, 1, Math.round(sizeY / gridSize));
                if (sizeZ > 0.1) addGridLines(box.min.z, box.max.z, 2, Math.round(sizeZ / gridSize));
                
                // Create the grid geometry
                gridGeometry.setAttribute(
                    'position', 
                    new THREE.Float32BufferAttribute(positions, 3)
                );
                
                // Create grid lines
                const gridLines = new THREE.LineSegments(gridGeometry, gridMaterial);
                gridLines.position.copy(position);
                gridLines.rotation.copy(rotation);
                gridLines.scale.copy(scale);
                
                this.pixelGridHelper.add(gridLines);
            }
        });
        
        // Add the grid to the scene
        this.scene.add(this.pixelGridHelper);
    },
    
    onMouseDown: function(event) {
        this.painting = true;
        this.updateMousePosition(event);
        this.performPaintAction();
    },
    
    onMouseMove: function(event) {
        this.updateMousePosition(event);
        
        // Raycast to highlight the pixel under the cursor
        this.highlightPixel();
        
        // If painting, perform the paint action
        if (this.painting) {
            this.performPaintAction();
        }
    },
    
    onMouseUp: function() {
        this.painting = false;
    },
    
    onMouseLeave: function() {
        this.painting = false;
    },
    
    updateMousePosition: function(event) {
        const rect = this.renderer.domElement.getBoundingClientRect();
        this.mouse.x = ((event.clientX - rect.left) / rect.width) * 2 - 1;
        this.mouse.y = -((event.clientY - rect.top) / rect.height) * 2 + 1;
    },
    
    highlightPixel: function() {
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
    },
    
    updatePixelInfo: function(intersect) {
        const mesh = intersect.object;
        const uv = intersect.uv;
        
        // Get material data
        const material = mesh.material;
        if (!material || !material.userData) return;
        
        const { partName, faceName, uvRect } = material.userData;
        
        // Calculate pixel coordinates
        if (uvRect) {
            const [x1, y1, x2, y2] = uvRect;
            const textureX = Math.floor(x1 + (x2 - x1) * uv.x);
            const textureY = Math.floor(y1 + (y2 - y1) * (1 - uv.y));
            
            // Get pixel color
            const pixelData = this.getPixelColor(textureX, textureY);
            const rgba = `rgba(${pixelData[0]}, ${pixelData[1]}, ${pixelData[2]}, ${pixelData[3]/255})`;
            const hex = this.rgbaToHex(pixelData[0], pixelData[1], pixelData[2]);
            
            // Update pixel info display
            document.getElementById('pixel-position').textContent = `${textureX}, ${textureY}`;
            document.getElementById('pixel-color').textContent = `${hex} (${rgba})`;
            document.getElementById('pixel-body-part').textContent = `${partName} (${faceName})`;
        }
    },
    
    clearPixelInfo: function() {
        document.getElementById('pixel-position').textContent = '-';
        document.getElementById('pixel-color').textContent = '-';
        document.getElementById('pixel-body-part').textContent = '-';
    },
    
    performPaintAction: function() {
        if (!this.highlightedMesh || !this.highlightedFace) return;
        
        // Get intersection information
        const mesh = this.highlightedMesh;
        const material = mesh.material;
        
        if (!material || !material.userData || !material.userData.uvRect) return;
        
        // Get UV mapping and calculate texture coordinates
        const uv = this.raycaster.intersectObject(mesh)[0].uv;
        const uvRect = material.userData.uvRect;
        const [x1, y1, x2, y2] = uvRect;
        
        // Calculate the exact pixel
        const textureX = Math.floor(x1 + (x2 - x1) * uv.x);
        const textureY = Math.floor(y1 + (y2 - y1) * (1 - uv.y)); // Flip Y coordinate
        
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
    },
    
    drawPixel: function(x, y) {
        const color = document.getElementById('editor-color').value;
        const r = parseInt(color.substring(1, 3), 16);
        const g = parseInt(color.substring(3, 5), 16);
        const b = parseInt(color.substring(5, 7), 16);
        
        // Draw a pixel on the canvas
        this.textureContext.fillStyle = color;
        this.textureContext.fillRect(x, y, 1, 1);
    },
    
    erasePixel: function(x, y) {
        // Get original pixel color
        const originalData = this.originalTextureData.data;
        const index = (y * this.textureCanvas.width + x) * 4;
        
        // Restore the original pixel
        this.textureContext.fillStyle = `rgba(${originalData[index]}, ${originalData[index+1]}, ${originalData[index+2]}, ${originalData[index+3]/255})`;
        this.textureContext.fillRect(x, y, 1, 1);
    },
	fillArea: function(x, y) {
        // Get current canvas data
        const imageData = this.textureContext.getImageData(0, 0, this.textureCanvas.width, this.textureCanvas.height);
        const data = imageData.data;
        const width = this.textureCanvas.width;
        
        // Get target color (the color we're replacing)
        const targetIndex = (y * width + x) * 4;
        const targetR = data[targetIndex];
        const targetG = data[targetIndex + 1];
        const targetB = data[targetIndex + 2];
        const targetA = data[targetIndex + 3];
        
        // Get fill color
        const fillColor = document.getElementById('editor-color').value;
        const fillR = parseInt(fillColor.substring(1, 3), 16);
        const fillG = parseInt(fillColor.substring(3, 5), 16);
        const fillB = parseInt(fillColor.substring(5, 7), 16);
        const fillA = 255;
        
        // If target color is already fill color, do nothing
        if (targetR === fillR && targetG === fillG && targetB === fillB && targetA === fillA) {
            return;
        }
        
        // Flood fill algorithm using queue
        const stack = [{x, y}];
        const visited = new Set();
        const height = this.textureCanvas.height;
        
        while (stack.length > 0) {
            const {x, y} = stack.pop();
            const key = `${x},${y}`;
            
            // Skip if outside canvas or already visited
            if (x < 0 || x >= width || y < 0 || y >= height || visited.has(key)) {
                continue;
            }
            
            // Mark as visited
            visited.add(key);
            
            // Check if this pixel matches the target color
            const pixelIndex = (y * width + x) * 4;
            const r = data[pixelIndex];
            const g = data[pixelIndex + 1];
            const b = data[pixelIndex + 2];
            const a = data[pixelIndex + 3];
            
            if (r === targetR && g === targetG && b === targetB && a === targetA) {
                // Fill this pixel
                data[pixelIndex] = fillR;
                data[pixelIndex + 1] = fillG;
                data[pixelIndex + 2] = fillB;
                data[pixelIndex + 3] = fillA;
                
                // Add neighbors to stack
                stack.push({x: x + 1, y});
                stack.push({x: x - 1, y});
                stack.push({x, y: y + 1});
                stack.push({x, y: y - 1});
            }
        }
        
        // Update the image data on the canvas
        this.textureContext.putImageData(imageData, 0, 0);
    },
    
    getPixelColor: function(x, y) {
        // Get the color of a specific pixel
        const pixelData = this.textureContext.getImageData(x, y, 1, 1).data;
        return [pixelData[0], pixelData[1], pixelData[2], pixelData[3]];
    },
    
    rgbaToHex: function(r, g, b) {
        return '#' + [r, g, b].map(x => {
            const hex = x.toString(16);
            return hex.length === 1 ? '0' + hex : hex;
        }).join('');
    },
    
    updateTexture: function() {
        // Update all textures in the model
        this.editorModel.traverse(child => {
            if (child.isMesh && child.material && child.material.map) {
                child.material.map.needsUpdate = true;
            }
        });
        
        // Update texture preview
        this.updateTexturePreview();
    },
    
    updateTexturePreview: function() {
        // Draw the current texture to the preview canvas
        const previewCanvas = document.getElementById('texture-preview-canvas');
        if (!previewCanvas) return;
        
        const ctx = previewCanvas.getContext('2d');
        
        // Set canvas dimensions to match the texture
        previewCanvas.width = this.textureCanvas.width;
        previewCanvas.height = this.textureCanvas.height;
        
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
    },
    
    setTool: function(tool) {
        this.currentTool = tool;
        
        // Update UI
        document.getElementById('editor-pencil').classList.remove('active');
        document.getElementById('editor-eraser').classList.remove('active');
        document.getElementById('editor-fill').classList.remove('active');
        
        document.getElementById(`editor-${tool}`).classList.add('active');
    },
    
    zoomCamera: function(factor) {
        if (!this.camera) return;
        
        this.camera.position.multiplyScalar(factor);
        this.controls.update();
    },
    
    resetCameraView: function() {
        if (!this.camera) return;
        
        this.camera.position.set(0, 20, 40);
        this.camera.lookAt(0, 0, 0);
        this.controls.update();
    },
    
    onWindowResize: function() {
        if (!this.camera || !this.renderer) return;
        
        const container = document.getElementById('skin-3d-editor');
        
        this.camera.aspect = container.clientWidth / container.clientHeight;
        this.camera.updateProjectionMatrix();
        this.renderer.setSize(container.clientWidth, container.clientHeight);
    },
    
    animate: function() {
        if (!this.isActive) return;
        
        requestAnimationFrame(this.animate.bind(this));
        
        // Update controls
        if (this.controls) {
            this.controls.update();
        }
        
        // Render scene
        if (this.renderer && this.scene && this.camera) {
            this.renderer.render(this.scene, this.camera);
        }
    },
    
    saveEdits: function() {
        // Create a new texture from the edited canvas
        const newTexture = new THREE.Texture(this.textureCanvas);
        newTexture.magFilter = THREE.NearestFilter;
        newTexture.minFilter = THREE.NearestFilter;
        newTexture.needsUpdate = true;
        
        // Update current skin texture in the main application
        currentSkinTexture = newTexture;
        
        // Recreate the main character model with the new texture
        createCharacterModel(newTexture);
        
        // Create a download link for the edited skin
        const downloadLink = document.createElement('a');
        downloadLink.href = this.textureCanvas.toDataURL('image/png');
        downloadLink.download = 'edited-minecraft-skin.png';
        
        // Offer to download the edited skin
        if (confirm('Your edits have been applied! Would you like to download the edited skin?')) {
            downloadLink.click();
        }
        
        // Close the editor
        this.close();
    },
    
    close: function() {
        this.isActive = false;
        
        // Hide editor container
        const editorContainer = document.getElementById('skin-editor-container');
        editorContainer.classList.add('d-none');
        
        // Remove event listeners
        window.removeEventListener('resize', this.onWindowResize.bind(this));
        
        // Clean up resources
        if (this.renderer) {
            this.renderer.dispose();
            const container = document.getElementById('skin-3d-editor');
            if (container && container.contains(this.renderer.domElement)) {
                container.removeChild(this.renderer.domElement);
            }
        }
    }
};

// Function to open the skin editor
function openSkinEditor() {
    if (!currentSkinTexture) {
        alert('Please load a skin first before editing.');
        return;
    }
    
    loadEditorDependencies().then(() => {
        SkinEditor.open(currentSkinTexture);
    }).catch(error => {
        console.error('Failed to load editor dependencies:', error);
        alert('Failed to initialize the skin editor. Please check the console for details.');
    });
}

// Initialize the skin editor when the document is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Create editor UI
    createSkinEditorUI();
    
    // Add editor button
    addSkinEditorButton();
    
    // Initialize editor
    SkinEditor.initialize();
});