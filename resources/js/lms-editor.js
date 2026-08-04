import tinymce from 'tinymce/tinymce';
import 'tinymce/models/dom';
import 'tinymce/themes/silver';
import 'tinymce/icons/default';
import 'tinymce/plugins/image';
import 'tinymce/plugins/code';
import 'tinymce/plugins/codesample';
import 'tinymce/plugins/lists';
import 'tinymce/plugins/link';
import 'tinymce/plugins/table';
import 'tinymce/skins/ui/oxide/skin.css';
import contentCss from 'tinymce/skins/content/default/content.css?url';

const textarea = document.getElementById('lms-content-editor');

if (textarea) {
    tinymce.init({
        selector: '#lms-content-editor',
        license_key: 'gpl',
        skin: false,
        content_css: [contentCss],
        plugins: 'image code codesample lists link table',
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic forecolor backcolor | bullist numlist | link image codesample | table | code',
        font_size_formats: '8pt 10pt 12pt 14pt 16pt 18pt 24pt 36pt 48pt',
        height: 500,
        menubar: false,
        branding: false,
        images_upload_handler: (blobInfo) =>
            new Promise((resolve, reject) => {
                const formData = new FormData();
                formData.append('file', blobInfo.blob(), blobInfo.filename());

                window.axios
                    .post(textarea.dataset.uploadUrl, formData, {
                        headers: { 'Content-Type': 'multipart/form-data' },
                    })
                    .then((response) => resolve(response.data.location))
                    .catch((error) => reject(error?.response?.data?.message || 'Image upload failed'));
            }),
        setup(editor) {
            editor.on('change', () => {
                editor.save();
            });
        },
    });
}
