function validateRegister(){
    let name = document.getElementById("name").value.trim();
    let email = document.getElementById("email").value.trim();
    let password = document.getElementById("password").value;
    let ok = true;

    document.getElementById("nameJsError").innerHTML = "";
    document.getElementById("passwordJsError").innerHTML = "";

    if(name == ""){
        document.getElementById("nameJsError").innerHTML = "Name is required";
        ok = false;
    }
    let emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if(!emailPattern.test(email)){
        document.getElementById("emailResponse").innerHTML = "Invalid email format";
        ok = false;
    }
    if(password.length < 8){
        document.getElementById("passwordJsError").innerHTML = "Password must be at least 8 characters";
        ok = false;
    }
    return ok;
}
