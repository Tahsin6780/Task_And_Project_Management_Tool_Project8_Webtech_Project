function toggleArchive(projectId, newState){
    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function(){
        if(this.readyState == 4 && this.status == 200){
            try{
                let res = JSON.parse(this.responseText);
                if(res.ok){
                    document.getElementById("archiveBtn").innerHTML = (newState == 1) ? "Unarchive" : "Archive";
                    document.getElementById("archiveBtn").setAttribute("onclick", "toggleArchive(" + projectId + "," + ((newState == 1) ? 0 : 1) + ")");
                    document.getElementById("projectActionResponse").innerHTML = "<span style='color:green'>" + (newState == 1 ? "Archived." : "Unarchived.") + "</span>";
                }else{
                    document.getElementById("projectActionResponse").innerHTML = res.message || "Failed";
                }
            }catch(e){
                document.getElementById("projectActionResponse").innerHTML = "Bad server response";
            }
        }
    };
    xhttp.open("POST", "../Controller/archiveProject.php", true);
    xhttp.setRequestHeader("content-type", "application/x-www-form-urlencoded");
    xhttp.send("id=" + projectId + "&archived=" + newState);
}