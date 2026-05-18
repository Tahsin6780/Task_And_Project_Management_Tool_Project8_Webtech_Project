function deleteProject(projectId){
    if(!confirm("Delete this project? This will also delete all its tasks and comments.")){
        return;
    }
    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function(){
        if(this.readyState == 4 && this.status == 200){
            try{
                let res = JSON.parse(this.responseText);
                if(res.ok){
                    window.location.href = "projects.php";
                }else{
                    document.getElementById("projectActionResponse").innerHTML = res.message || "Failed";
                }
            }catch(e){
                document.getElementById("projectActionResponse").innerHTML = "Bad server response";
            }
        }
    };
    xhttp.open("DELETE", "../Controller/deleteProject.php?id=" + projectId, true);
    xhttp.send();
}