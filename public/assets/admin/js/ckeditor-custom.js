document.addEventListener('DOMContentLoaded', function () {
    const editorElement = document.querySelector('#editor');

    if (!editorElement || typeof ClassicEditor === 'undefined') {
        return;
    }

    ClassicEditor.create(editorElement).catch(function (error) {
        console.error(error);
    });
});
