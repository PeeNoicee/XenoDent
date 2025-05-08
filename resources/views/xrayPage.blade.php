@extends('layouts.xrayapp')

@section('content')

<div class="container my-4">
    <div class="row">
        <!-- Left Side (Buttons) -->
        <div class="col-md-4">
            <div class="button-container">
                <button type="button" data-bs-toggle="modal" data-bs-target="#exampleModal" class="btn btn-outline-success mb-3 w-100">UPLOAD</button>
                <button class="btn btn-outline-primary w-100 mb-3">ANALYZE</button>
                <button id="toggle-gallery" class="btn btn-outline-secondary w-100">Show X-ray Gallery</button>
            </div>
        </div>

        <!-- Right Side (X-ray Preview) -->
        <div class="col-md-8">
            <div id="xray-preview" class="xray-preview">
                <p>No Image Selected</p>
            </div>
            <div id="gallery-dropdown" class="gallery-dropdown"></div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Image Upload</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="xray-upload-form" enctype="multipart/form-data">
                <div class="modal-body">
                    @csrf
                    <input type="file" name="image" id="image-input" class="form-control mt-2">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('style')
<style>
    .xray-preview {
        background-color: #f4f4f4;
        height: 500px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        border: 2px dashed #ccc;
        margin-top: 20px;
    }

    .gallery-dropdown {
        display: none;
        max-height: 300px;
        overflow-y: auto;
        border: 1px solid #ccc;
        padding: 10px;
        margin-top: 10px;
        border-radius: 8px;
    }

    .gallery-dropdown img {
        width: 100px;
        margin: 5px;
        cursor: pointer;
        border-radius: 4px;
        transition: transform 0.2s;
    }

    .gallery-dropdown img:hover {
        transform: scale(1.1);
    }

    .button-container {
        margin-top: 20px;
        padding-right: 15px;
    }

    .btn-outline-success, .btn-outline-primary, .btn-outline-secondary {
        margin-bottom: 10px;
    }
</style>
@endsection



@section('script')

<script>




//UPLOAD IMAGES
document.getElementById('image-input').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        // Just validate the file type if needed
        if (!file.type.startsWith('image/')) {
            alert('Please select an image file');
            this.value = ''; // Clear the file input
        }
    }
});


//PREVIEW IMAGE
document.getElementById('xray-upload-form').addEventListener('submit', function(e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);
    const fileInput = form.querySelector('#image-input');
    
    if (!fileInput.files.length) {
        alert('Please select a file to upload');
        return;
    }

    const file = fileInput.files[0];
    
    if (!file.type.startsWith('image/')) {
        alert('Please select an image file');
        return;
    }

    const submitButton = form.querySelector('.btn-primary');
    if (!submitButton) {
        console.error('Submit button not found');
        return;
    }

    const originalButtonText = submitButton.textContent;
    submitButton.textContent = 'Processing...';
    submitButton.disabled = true;

    const resizeImage = (file) => {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = (event) => {
                const img = new Image();
                img.src = event.target.result;
                img.onload = () => {
                    const canvas = document.createElement('canvas');
                    let width = img.width;
                    let height = img.height;
                    
                    const MAX_WIDTH = 1920;
                    const MAX_HEIGHT = 1080;
                    
                    if (width > height) {
                        if (width > MAX_WIDTH) {
                            height *= MAX_WIDTH / width;
                            width = MAX_WIDTH;
                        }
                    } else {
                        if (height > MAX_HEIGHT) {
                            width *= MAX_HEIGHT / height;
                            height = MAX_HEIGHT;
                        }
                    }
                    
                    canvas.width = width;
                    canvas.height = height;
                    
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);
                    
                    canvas.toBlob((blob) => {
                        const resizedFile = new File([blob], file.name, {
                            type: 'image/jpeg',
                            lastModified: Date.now()
                        });
                        resolve(resizedFile);
                    }, 'image/jpeg', 0.8);
                };
                img.onerror = reject;
            };
            reader.onerror = reject;
        });
    };

    resizeImage(file)
        .then(resizedFile => {
            const newFormData = new FormData();
            newFormData.append('image', resizedFile);
            newFormData.append('_token', '{{ csrf_token() }}');

            return fetch("{{ route('upload') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: newFormData
            });
        })
        .then(async res => {
            if (res.status === 413) {
                throw new Error('File size too large even after resizing. Please try a smaller image.');
            }
            const data = await res.json();
            if (!res.ok) {
                throw new Error(data.error || `HTTP error! status: ${res.status}`);
            }
            return data;
        })
        .then(data => {
            if (data.png_path) {
                const preview = document.getElementById('xray-preview');
                if (!preview) {
                    console.error('Preview element not found');
                    return;
                }

                preview.innerHTML = '';
                
                const img = document.createElement('img');
                img.src = data.png_path;
                img.style.maxWidth = '100%';
                img.style.maxHeight = '100%';
                img.style.objectFit = 'contain';
                img.alt = "X-ray preview";
                
                preview.appendChild(img);
            }
        })
        .catch(error => {
            alert(error.message || 'An error occurred during upload');
        })
        .finally(() => {
            if (submitButton) {
                submitButton.textContent = originalButtonText;
                submitButton.disabled = false;
            }
            const modal = bootstrap.Modal.getInstance(document.getElementById('exampleModal'));
            if (modal) {
                modal.hide();
            }
        });
});


//GET IMAGES
document.addEventListener('DOMContentLoaded', () => {
    const toggleButton = document.getElementById('toggle-gallery');
    const gallery = document.getElementById('gallery-dropdown');
    
    gallery.style.display = 'none';

    toggleButton.addEventListener('click', function () {
        if (gallery.style.display === 'none' || gallery.style.display === '') {
            gallery.style.display = 'block';
        } else {
            gallery.style.display = 'none';
        }
    });
});


// Add analyze button click handler
document.addEventListener('DOMContentLoaded', () => {
    const analyzeButton = document.querySelector('.btn-outline-primary');
    if (analyzeButton) {
        analyzeButton.addEventListener('click', function() {
            const preview = document.getElementById('xray-preview');
            const img = preview.querySelector('img');
            
            if (!img || !img.src) {
                alert('Please upload an X-ray image first');
                return;
            }

            const originalText = this.textContent;
            this.textContent = 'Analyzing...';
            this.disabled = true;

            const imagePath = img.src.split('/').pop();

            fetch("{{ route('analyze') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    image_path: imagePath
                })
            })
            .then(async response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    if (data.flask_analysis && data.flask_analysis.image) {
                        const analyzedImg = document.createElement('img');
                        analyzedImg.src = 'data:image/png;base64,' + data.flask_analysis.image;
                        analyzedImg.style.maxWidth = '100%';
                        analyzedImg.style.maxHeight = '100%';
                        analyzedImg.style.objectFit = 'contain';
                        analyzedImg.alt = "Analyzed X-ray";
                        
                        analyzedImg.onload = function() {
                            preview.innerHTML = '';
                            preview.appendChild(analyzedImg);

                            // Add the analyzed image to the gallery
                            const gallery = document.getElementById('gallery-dropdown');
                            const galleryImg = document.createElement('img');
                            galleryImg.src = analyzedImg.src;
                            galleryImg.alt = 'Analyzed X-ray';
                            galleryImg.style.width = '100px';
                            galleryImg.style.margin = '5px';
                            galleryImg.style.cursor = 'pointer';

                            galleryImg.addEventListener('click', () => {
                                preview.innerHTML = '';
                                const fullImg = document.createElement('img');
                                fullImg.src = galleryImg.src;
                                fullImg.alt = 'Analyzed X-ray';
                                fullImg.style.maxWidth = '100%';
                                fullImg.style.maxHeight = '100%';
                                fullImg.style.objectFit = 'contain';
                                preview.appendChild(fullImg);
                            });

                            gallery.appendChild(galleryImg);
                        };
                        
                        analyzedImg.onerror = function() {
                            alert('Failed to load analyzed image. Please try again.');
                        };
                    } else if (data.api_error) {
                        alert('Could not connect to analysis server. Please make sure the Flask server is running.');
                    }
                } else {
                    throw new Error(data.error || 'Analysis failed');
                }
            })
            .catch(error => {
                alert('Analysis failed: ' + error.message);
            })
            .finally(() => {
                this.textContent = originalText;
                this.disabled = false;
            });
        });
    }
});


</script>

@endsection