function validateSignup() {
    var name = document.getElementById('name').value;
    var email = document.getElementById('email').value;
    var phone = document.getElementById('phone').value;
    var address = document.getElementById('address').value;
    var password = document.getElementById('password').value;
    var cpassword = document.getElementById('cpassword').value;
    
    var isValid = true;

    if (name.trim() == "") {
        document.getElementById('err_name').innerHTML = "* Name is required";
        isValid = false;
    } else {
        document.getElementById('err_name').innerHTML = "";
    }

    if (email.trim() == "") {
        document.getElementById('err_email').innerHTML = "* Email is required";
        isValid = false;
    } else {
        document.getElementById('err_email').innerHTML = "";
    }

    if (phone.trim() == "") {
        document.getElementById('err_phone').innerHTML = "* Phone is required";
        isValid = false;
    } else {
        document.getElementById('err_phone').innerHTML = "";
    }

    if (password.length < 6) {
        document.getElementById('err_password').innerHTML = "* Password must be 6 chars long";
        isValid = false;
    } else {
        document.getElementById('err_password').innerHTML = "";
    }

    if (password != cpassword) {
        document.getElementById('err_cpassword').innerHTML = "* Passwords do not match";
        isValid = false;
    } else {
        document.getElementById('err_cpassword').innerHTML = "";
    }

    return isValid;
}