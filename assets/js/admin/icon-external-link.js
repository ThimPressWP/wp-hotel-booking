document.addEventListener('DOMContentLoaded', function() {
    let mediaUploader;
    
    // Add images button
    document.addEventListener('click', function(e) {

        if (e.target.closest( '.wphb-external-icon--add-new' )) {
            e.preventDefault();
            
            const button = e.target;
            const wrapper = button.closest('.icon-gallery-upload-wrapper');
            const hiddenInput = wrapper.querySelector('input[type="hidden"]');
            const galleryList = wrapper.querySelector('.icon-gallery-list');
            
            // Create media uploader with multiple selection
            mediaUploader = wp.media({
                title: wphbIconExternalLinkSettings.uploader_title,
                button: {
                    text: wphbIconExternalLinkSettings.uploader_button_text
                },
                library: {
                    type: 'image'
                },
                multiple: true
            });
            
            // On image select
            mediaUploader.on('select', function() {
                const selection = mediaUploader.state().get('selection');
                const currentIds = hiddenInput.value ? hiddenInput.value.split(',') : [];
                
                selection.forEach(function(attachment) {
                    console.log( attachment );
                    attachment = attachment.toJSON();
                    
                    // Skip if already exists
                    if (currentIds.includes(attachment.id.toString())) {
                        return;
                    }
                    
                    // Add to array
                    currentIds.push(attachment.id);
                    
                    button.closest('.gallery-item').insertAdjacentHTML(
                        'beforebegin',
                        `<li class="gallery-item" data-id="${attachment.id}">
                            <div class="image-container">
                            <img src="${attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url}" alt="">
                            <button type="button" class="remove-image" title="${wphbIconExternalLinkSettings.remove_button_title}">×</button>
                            </div>
                        </li>`
                    );
                });
                
                // Update hidden field
                hiddenInput.value = currentIds.join(',');
            });
            
            mediaUploader.open();
        }
        
        // Remove image button
        if (e.target.classList.contains('remove-image')) {
            e.preventDefault();
            
            const button = e.target;
            const listItem = button.closest('.gallery-item');
            const wrapper = button.closest('.icon-gallery-upload-wrapper');
            const hiddenInput = wrapper.querySelector('input[type="hidden"]');
            const imageId = listItem.getAttribute('data-id');
            
            // Remove from DOM
            listItem.remove();
            
            // Update hidden field
            let ids = hiddenInput.value ? hiddenInput.value.split(',') : [];
            ids = ids.filter(id => id !== imageId);
            hiddenInput.value = ids.join(',');
        }
    });
});
