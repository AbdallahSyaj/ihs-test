// Confirmation Message On Button :
$('.confirm').click(function () {
    var message = (window.dashboardTranslations && window.dashboardTranslations.confirmDelete) ||
        'Are you sure to delete? All associated data will be deleted';
    return confirm(message);
});

// Aceptance Message On Button :
$('.accept').click(function () {
    return confirm('Are you sure to accept');
});
// Rejecte Message On Button :
$('.reject').click(function () {
    return confirm('Are you sure to reject');
});

// Rejecte Message On Button :
$('.resend').click(function () {
    return confirm('Are you sure to resend');
});

// Return Message On Button :
$('.process').click(function () {
    return confirm('Are you sure to perform this process?');
});


// unarchive Message On Button :
$('.unarchive').click(function () {
    return confirm('Are you sure about canceling the archive?');
});


// stop Job Message On Button :
$('.stopJob').click(function () {
    return confirm('هل انت متاكد من ايقاف الوظيفة ؟');
});
// active Job Message On Button :
$('.activeJob').click(function () {
    return confirm('هل انت متاكد من نشر  الوظيفة ؟');
});
// soft Delete Job Job Message On Button :
$('.softDeleteJob').click(function () {
    return confirm('هل انت متأكد من الحذف؟ سيتم حذف جميع البيانات المرتبطة ؟');
});



// disable right click
// document.addEventListener('contextmenu', event => event.preventDefault());
// document.onkeydown = function (e) {
//     // disable F12 key
//     if (e.keyCode == 123) {
//         return false;
//     }
//     // disable I key
//     if (e.ctrlKey && e.shiftKey && e.keyCode == 73) {
//         return false;
//     }
//     // disable J key
//     if (e.ctrlKey && e.shiftKey && e.keyCode == 74) {
//         return false;
//     }
//     // disable U key
//     if (e.ctrlKey && e.keyCode == 85) {
//         return false;
//     }
// }









