@extends('layouts.xrayapp')

@section('content')

        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Image upload</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <div id="image-preview-container" style="margin-top: 20px;">
                            <img id="image-preview" src="" alt="Image Preview" style="max-width: 400px; display:none;">
                        </div>
                        
                        <form id="xray-upload-form" enctype="multipart/form-data">
                            @csrf
                            <input type="file" name="image" id="image-input" >
                           
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>

                        </form>


                    </div>
                </div>
            </div>
        </div>





<div>


    <div class = "col" style = "margin-top: 10px">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{url ('dashboard') }}">DENTAL LOGO</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Actions
                </a>
                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="#">Analytics</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#">X-ray AI</a></li>
                    <li><hr class="dropdown-divider"></li>
                    @if($prem === 0)
                        <li><a class="dropdown-item disabled" href="">For Premium user</a></li>
                    @else
                        <li><a class="dropdown-item" href="">Feature Request</a></li>
                    @endif
                </ul>
                </li>
            </ul>

            @if($prem === 0)
                <div class="d-flex">
                    <a href = "{{url ('premiumPage') }}">
                    <button class="btn btn-outline-success">Go PREMIUM</button>
                    </a>
                </div>
            @else

                <div class="d-flex">
                    <a >
                    <button class="btn btn-outline-success">Need Support?</button>
                    </a>
                </div>

            @endif
            </div>
        </div>
        </nav>
    </div>

    <div class = 'container' style = "margin: 20px 0px 0px 5px">

        <div class = 'row '>

            <div class = 'col'>

                <div style = 'height: 500px; width: 650px; margin: 5% 0px 0px 10%;'>
                    <div id="xray-preview" style="margin-top: 20px;"></div>
                </div>

            </div>


            <div class = 'col'>
                <div style = 'background-color: #AEACAC; height: 500px; width: 650px; margin: 5% 0px 0px 0px;'>
                    <h1>IMG HERE</h1>
                </div>
            </div>

            <div class = 'row'>
                <a >
                <button type="button" data-bs-toggle="modal" data-bs-target="#exampleModal" class="btn btn-outline-success" style = "margin: 5% 0px 0px 5%;">UPLOAD</button>
                </a>
            </div>

            <div class = 'row'>
                <a>
                <button class="btn btn-outline-success" style = "margin: 15px 0px 0px 5%;">ANALYZE</button>
                </a>
            </div>

            <div class = 'row'>

                <button id="toggle-gallery" style="margin-top: 20px;">Show X-ray Gallery</button>
                <div id="gallery-dropdown" style="display: none; max-height: 300px; overflow-y: auto; border: 1px solid #ccc; margin-top: 10px; padding: 10px;"></div>

            </div>

        </div>
        
    </div>

</div>

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
            preview.innerHTML = ''; // Clear previous
            preview.appendChild(img);

            alert('Upload successful');

            localStorage.setItem('lastXrayPreview', src);

        } else {
            alert('Upload successful, but preview not available.');
        }
    })
    .catch(err => {
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

