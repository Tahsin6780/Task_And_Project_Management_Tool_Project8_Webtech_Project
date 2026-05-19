function validateCreateTask(){
    let title = document.getElementById("taskTitle").value.trim();
    let desc = document.getElementById("taskDesc").value.trim();
    let due = document.getElementById("taskDue").value;
    let ok = true;

    document.getElementById("taskTitleJsError").innerHTML = "";
    document.getElementById("taskDescJsError").innerHTML = "";
    document.getElementById("taskDueJsError").innerHTML = "";

    if(title == ""){
        document.getElementById("taskTitleJsError").innerHTML = "Title is required";
        ok = false;
    }else if(title.length < 3){
        document.getElementById("taskTitleJsError").innerHTML = "Title must be at least 3 characters";
        ok = false;
    }else if(title.length > 180){
        document.getElementById("taskTitleJsError").innerHTML = "Title must be 180 characters or fewer";
        ok = false;
    }

    if(desc.length > 1000){
        document.getElementById("taskDescJsError").innerHTML = "Description must be 1000 characters or fewer";
        ok = false;
    }

    if(due != ""){
        let today = new Date();
        today.setHours(0,0,0,0);
        let d = new Date(due);
        if(d < today){
            document.getElementById("taskDueJsError").innerHTML = "Due date cannot be in the past";
            ok = false;
        }
    }
    return ok;
}
