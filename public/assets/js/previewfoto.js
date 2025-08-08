function previewImage(event) {
    const file = event.target.files[0];
    const previewContainer = document.getElementById('imagePreviewContainer');
    const previewImage = document.getElementById('imagePreview');
    
    if (file) {
        // Validate file type
        const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!validTypes.includes(file.type)) {
            alert('Please select a valid image file (JPEG, PNG, JPG, GIF)');
            event.target.value = '';
            previewContainer.style.display = 'none';
            return;
        }
        
        // Validate file size (2MB = 2 * 1024 * 1024 bytes)
        const maxSize = 2 * 1024 * 1024;
        if (file.size > maxSize) {
            alert('File size must be less than 2MB');
            event.target.value = '';
            previewContainer.style.display = 'none';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImage.src = e.target.result;
            previewContainer.style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        previewContainer.style.display = 'none';
    }
}

function removePreview() {
    const fileInput = document.getElementById('foto');
    const previewContainer = document.getElementById('imagePreviewContainer');
    const previewImage = document.getElementById('imagePreview');
    
    // Clear the file input
    fileInput.value = '';
    
    // Hide preview container
    previewContainer.style.display = 'none';
    
    // Clear the preview image
    previewImage.src = '';
}