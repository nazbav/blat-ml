$(document).ready(function () {
    $('td:contains("new")').parent().addClass('new').next(".grouplist").addClass('new').find(".group").toggleClass('group-new');
});
