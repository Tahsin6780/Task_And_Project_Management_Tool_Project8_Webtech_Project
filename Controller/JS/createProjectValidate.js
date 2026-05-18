function validateCreateProject(){
    let name = document.getElementById("projName").value.trim();
    let desc = document.getElementById("projDesc").value.trim();
    let deadline = document.getElementById("projDeadline").value;
    let ok = true;

    document.getElementById("projDescJsError").innerHTML = "";
    document.getElementById("projDeadlineJsError").innerHTML = "";

    if(name == ""){
        document.getElementById("projNameResponse").innerHTML = "<span style='color:red'>Project name is required</span>";
        ok = false;
    }else if(name.length < 3){
        document.getElementById("projNameResponse").innerHTML = "<span style='color:red'>Project name must be at least 3 characters</span>";
        ok = false;
    }

    if(desc.length > 500){
        document.getElementById("projDescJsError").innerHTML = "Description must be 500 characters or fewer";
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