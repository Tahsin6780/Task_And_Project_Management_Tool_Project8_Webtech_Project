function validateJoin(){
    let code = document.getElementById("code").value.trim();
    document.getElementById("codeJsError").innerHTML = "";
    if(code.length != 6){
        document.getElementById("codeJsError").innerHTML = "Invite code must be exactly 6 characters";
        return false;
    }
    let pattern = /^[A-Z0-9]+$/;
    if(!pattern.test(code.toUpperCase())){
        document.getElementById("codeJsError").innerHTML = "Invite code must be letters and digits only";
        return false;
    }
    return true;
}
