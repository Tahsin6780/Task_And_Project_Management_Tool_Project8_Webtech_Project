function validateLogin(){
    let email = document.getElementById("email").value.trim();
    let password = document.getElementById("password").value;
    let ok = true;

    document.getElementById("emailJsError").innerHTML = "";
    document.getElementById("passwordJsError").innerHTML = "";

    let emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if(!emailPattern.test(email)){
        document.getElementById("emailJsError").innerHTML = "Invalid email format";
        ok = false;
    }
    if(password.length < 1){
        document.getElementById("passwordJsError").innerHTML = "Password is required";
        ok = false;
    }
    return ok;
}
