const $body = $('body');

// sidebar
$('.side-nav').metisMenu();
$(document).on('click', '.button-menu-mobile', (e) => {
    e.preventDefault();
    $body.toggleClass('sidebar-enable');
});