function validateCreateWorkspace(){
    let name = document.getElementById("wsName").value.trim();
    document.getElementById("wsNameJsError").innerHTML = "";
    if(name == ""){
        document.getElementById("wsNameJsError").innerHTML = "Workspace name is required";
        return false;
    }
    if(name.length < 3){
        document.getElementById("wsNameJsError").innerHTML = "Workspace name must be at least 3 characters";
        return false;
    }
    return true;
}
