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
    if(deadline != ""){
        let today = new Date();
        today.setHours(0,0,0,0);
        let d = new Date(deadline);
        if(d < today){
            document.getElementById("projDeadlineJsError").innerHTML = "Deadline cannot be in the past";
            ok = false;
        }
    }
    return ok;
}
