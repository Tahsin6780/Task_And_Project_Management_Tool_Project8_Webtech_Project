function validateEditProject(){
    let name = document.getElementById("editName").value.trim();
    let desc = document.getElementById("editDesc").value.trim();
    let ok = true;

    document.getElementById("editNameJsError").innerHTML = "";
    document.getElementById("editDescJsError").innerHTML = "";

    if(name == ""){
        document.getElementById("editNameJsError").innerHTML = "Project name is required";
        ok = false;
    }else if(name.length < 3){
        document.getElementById("editNameJsError").innerHTML = "Project name must be at least 3 characters";
        ok = false;
    }
    if(desc.length > 500){
        document.getElementById("editDescJsError").innerHTML = "Description must be 500 characters or fewer";
        ok = false;
    }
    return ok;
}