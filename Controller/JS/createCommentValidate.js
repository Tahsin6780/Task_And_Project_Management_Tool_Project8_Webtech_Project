function validateCreateComment(){
    let body = document.getElementById("commentBody").value.trim();
    document.getElementById("commentJsError").innerHTML = "";
    if(body == ""){
        document.getElementById("commentJsError").innerHTML = "Comment cannot be empty";
        return false;
    }
    if(body.length > 1000){
        document.getElementById("commentJsError").innerHTML = "Comment must be 1000 characters or fewer";
        return false;
    }
    return true;
}
