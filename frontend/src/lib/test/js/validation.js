
function validateEmail(form) {
    let email = form.email.value;
    let isValid = true;
    const regEmail = new RegExp(/^[A-Za-z0-9_!#$%&'*+\/=?`{|}~^.-]+@[A-Za-z0-9.-]+$/, "gm");
    if(!regEmail.test(email)){
        //flash("Please enter a valid e-mail address.", "danger");
        isValid = false;
    }
    // Validate email
    if (email.trim() === "") {
        //flash("Email is required.", "danger");
        isValid = false;
    }
    console.log("passed email checks");
    return isValid;
}

function validatePassword(form) {
    let pw = form.password.value;
    let con = form.confirmPassword.value;
    let isValid = true;
    if (pw !== con) {
        //flash("Password and Confirm Password must match.", "danger");
        isValid = false;
    }
    if (pw.length < 8) {
        //flash("Password must be at least 8 characters long.", "danger");
        isValid = false;
    }
    console.log("passed pass checks");
    return isValid;
    
}

function validateForm(form) {
    console.log("Validating form...");

    validateEmail(form);
    validatePassword(form);

    if (!validateEmail(form) || !validatePassword(form)) {
        return false; 
    }
    console.log("passed FORM checks");
    return true; 
}
