function redirectToDashboard() {
    window.location.href = 'admin/login.php';
}

function redirectToAdmin() {
    window.location.href = 'admin/login.php';
    
    // إضافة أنيميشن للزر
    const icon = document.querySelector('.user-icon');
    icon.classList.add('pulse-animation');
    setTimeout(() => {
        icon.classList.remove('pulse-animation');
    }, 500);
}

function redirectToLogin() {
    window.location.href = _base_url_ + 'admin/login.php';
}

/*!
* Start Bootstrap - Shop Homepage v5.0.1 (https://startbootstrap.com/template/shop-homepage)
* Copyright 2013-2021 Start Bootstrap
* Licensed under MIT (https://github.com/StartBootstrap/startbootstrap-shop-homepage/blob/master/LICENSE)
*/
// This file is intentionally blank
// Use this file to add JavaScript to your project