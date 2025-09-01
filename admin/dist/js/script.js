/**
 * Admin Script Functions
 * Contains required functions for admin panel functionality
 */

// Loader functions
function start_loader() {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Loading...',
            text: 'Please wait',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });
    } else {
        // Fallback if SweetAlert is not available
        console.log('Loading...');
    }
}

function end_loader() {
    if (typeof Swal !== 'undefined' && Swal.isVisible()) {
        Swal.close();
    } else {
        // Fallback if SweetAlert is not available
        console.log('Loading finished');
    }
}

// Alert toast function
function alert_toast(message, type = 'success') {
    if (typeof Toast !== 'undefined') {
        Toast.fire({
            icon: type,
            title: message
        });
    } else if (typeof toastr !== 'undefined') {
        if (type === 'success') {
            toastr.success(message);
        } else if (type === 'error') {
            toastr.error(message);
        } else if (type === 'warning') {
            toastr.warning(message);
        } else {
            toastr.info(message);
        }
    } else {
        // Fallback if no toast library is available
        alert(message);
    }
}

// Redirect function
function redirect(url) {
    location.href = url;
}

// DOM ready function
$(function() {
    console.log('Admin scripts loaded');
});