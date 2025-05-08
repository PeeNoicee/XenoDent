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
            <div class="modal-body">
                <form id="xray-upload-form" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="image" id="image-input" class="form-control mt-2">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
                </form>
            </div>
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
        const reader = new FileReader();

        reader.onload = function(event) {
            const imgElement = document.getElementById('image-preview');
            imgElement.src = event.target.result; 
            imgElement.style.display = 'block'; 
        }

        reader.readAsDataURL(file); // Read the image file as a data URL
    }
});


//PREVIEW IMAGE
document.getElementById('xray-upload-form').addEventListener('submit', function(e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);

    fetch("{{ route('upload') }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.png_path) {
            const img = document.createElement('img');
            img.src = data.png_path;
            img.style.width = '100%';
            img.style.height = '100%';
            img.alt = "X-ray preview";

            const preview = document.getElementById('xray-preview');
            preview.innerHTML = ''; // Clear previous content
            preview.appendChild(img);

            alert('Upload successful');
            
            // Ensure you are using the correct variable (data.png_path)
            localStorage.setItem('lastXrayPreview', data.png_path);

        } else {
            // If preview is not available, but upload succeeded
            alert('Upload successful, but preview not available.');
        }
    })
    .catch(err => {
        // If there was an error with the upload, log the error and show a fail message
        console.error(err);
        alert('Upload failed.');
    });
});


//GET IMAGES
document.addEventListener('DOMContentLoaded', () => {
    const toggleButton = document.getElementById('toggle-gallery');
    const gallery = document.getElementById('gallery-dropdown');
    const savedState = localStorage.getItem('galleryState');

    if (savedState === 'open') {
        gallery.style.display = 'block';
    } else {
        gallery.style.display = 'none';
    }

    toggleButton.addEventListener('click', function () {
        if (gallery.style.display === 'none' || gallery.style.display === '') {
            // Fetch and show the gallery
            fetch("{{ route('getImages') }}")
                .then(res => res.json())
                .then(data => {
                    gallery.innerHTML = ''; // Clear previous content

                    if (data.images.length === 0) {
                        const message = document.createElement('p');
                        message.textContent = 'No X-ray images found.';
                        message.style.color = '#888';
                        gallery.appendChild(message);
                    } else {
                        data.images.forEach(src => {
                            const img = document.createElement('img');
                            img.src = src;
                            img.alt = 'X-ray';
                            img.style.width = '100px';
                            img.style.margin = '5px';
                            img.style.cursor = 'pointer';

                            img.addEventListener('click', () => {

                                localStorage.setItem('lastXrayPreview', src);

                                const preview = document.getElementById('xray-preview');
                                preview.innerHTML = ''; 

                                const fullImg = document.createElement('img');
                                fullImg.src = src;

                                fullImg.alt = 'X-ray Last Viewed';
                                fullImg.style.maxWidth = '100%';
                                fullImg.style.maxHeight = '100%';
                                fullImg.style.objectFit = 'contain';
                                fullImg.style.borderRadius = '4px';

                                preview.appendChild(fullImg);
                            });

                            gallery.appendChild(img);
                        });
                    }

                    gallery.style.display = 'block';
                    localStorage.setItem('galleryState', 'open'); // Save state
                })
                .catch(err => {
                    console.error('Failed to load gallery:', err);
                    alert('Could not load images.');
                });
        } else {
            
            gallery.style.display = 'none';
            localStorage.setItem('galleryState', 'closed'); // Save state
        }
    });
});


document.addEventListener('DOMContentLoaded', () => {
    const savedPath = localStorage.getItem('lastXrayPreview');
    if (savedPath) {
        const preview = document.getElementById('xray-preview');
        const fullImg = document.createElement('img');
        fullImg.src = savedPath;
        fullImg.alt = 'X-ray Last Viewed';
        fullImg.style.maxWidth = '100%';
        fullImg.style.maxHeight = '100%';
        fullImg.style.objectFit = 'contain';
        fullImg.style.borderRadius = '4px';
        preview.appendChild(fullImg);
    }
});






</script>

@endsection

