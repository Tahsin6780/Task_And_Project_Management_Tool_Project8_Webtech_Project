function validateEditTask(){
    let title = document.getElementById("editTaskTitle").value.trim();
    let desc = document.getElementById("editTaskDesc").value.trim();
    let ok = true;

    document.getElementById("editTaskTitleJsError").innerHTML = "";
    document.getElementById("editTaskDescJsError").innerHTML = "";

    if(title == ""){
        document.getElementById("editTaskTitleJsError").innerHTML = "Title is required";
        ok = false;
    }else if(title.length < 3){
        document.getElementById("editTaskTitleJsError").innerHTML = "Title must be at least 3 characters";
        ok = false;
    }
    if(desc.length > 1000){
        document.getElementById("editTaskDescJsError").innerHTML = "Description must be 1000 characters or fewer";
        ok = false;
    }
    return ok;
}
