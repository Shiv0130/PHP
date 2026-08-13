

document.addEventListener('DOMContentLoaded', function() {

    // Handler for Create Post image preview
    const postImageInput = document.getElementById('postImagePreview');
    const postImageFile = document.querySelector('input[name="post_image"]');

    if (postImageInput && postImageFile) {
        postImageFile.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    postImageInput.src = e.target.result;
                    postImageInput.style.display = 'block'; // Show the preview
                }
                reader.readAsDataURL(file);
            } else {
                postImageInput.src = '#';
                postImageInput.style.display = 'none'; // Hide if no file is selected
            }
        });
    }

    // Handler for Edit Profile image preview
    const profileImageInput = document.getElementById('profileImagePreview');
    const profileImageFile = document.querySelector('input[name="profile_image"]');

    if (profileImageInput && profileImageFile) {
        profileImageFile.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    profileImageInput.src = e.target.result;
                    profileImageInput.style.display = 'block'; // Show the preview
                }
                reader.readAsDataURL(file);
            } else {

                profileImageInput.src = '#';
                profileImageInput.style.display = 'none';
            }
        });
    }
});