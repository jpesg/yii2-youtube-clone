$(function () {
    $('#videoFile').change(e => {
        $(e.target).closest('form').trigger('submit');
    })
});