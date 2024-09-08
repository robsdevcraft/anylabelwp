<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$new_url = anylabelwp_get_new_url();
?>

<h2>AnylabelWP Settings</h2>

<form method="post" action="options.php">
    <?php settings_fields( 'anylabelwp' ); ?>
    <?php do_settings_sections( 'anylabelwp' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">New URL</th>
        <td>
            <input id="image-url" type="hidden" name="anylabelwp_new_url" value="<?php echo esc_attr( get_option('anylabelwp_new_url') ); ?>" />
            <button type="button" class="upload-image-button button">Upload Image</button>
            <div id="preview-image" style="max-width: 350px; height: auto;">
                <?php if(get_option('anylabelwp_new_url') != ""): ?>
                    <img src="<?php echo esc_attr( get_option('anylabelwp_new_url') ); ?>" style="max-width: 100%; height: auto;">
                <?php endif; ?>
            </div>
        </td>
        </tr>
    </table>
    <?php submit_button('Save Changes'); ?>
</form>

<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function() {
    const uploadButton = document.querySelector('.upload-image-button');
    const imageUrlInput = document.querySelector('#image-url');
    const previewImage = document.querySelector('#preview-image');

    uploadButton.addEventListener('click', function(e) {
        e.preventDefault();
        const customUploader = wp.media({
            title: 'Custom Image',
            button: {
                text: 'Use this image'
            },
            multiple: false  // Set this to true to allow multiple files to be selected
        });

        customUploader.on('select', function() {
            const attachment = customUploader.state().get('selection').first().toJSON();
            imageUrlInput.value = attachment.url;
            previewImage.innerHTML = '<img src="' + attachment.url + '" style="max-width: 100%; height: auto;">';
        });

        customUploader.open();
    });
});
</script>
