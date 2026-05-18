function removeProjectMember(projectId, userId){
    if(!confirm("Remove this member from the project?")){
        return;
    }
    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function(){
        if(this.readyState == 4 && this.status == 200){
            try{
                let res = JSON.parse(this.responseText);
                if(res.ok){
                    let row = document.getElementById("pmRow_" + userId);
                    if(row){
                        row.className = "fading";
                        setTimeout(function(){ row.parentNode.removeChild(row); }, 700);
                    }
                    document.getElementById("projectMemberResponse").innerHTML = "<span style='color:green'>Member removed.</span>";
                }else{
                    document.getElementById("projectMemberResponse").innerHTML = res.message || "Failed";
                }
            }catch(e){
                document.getElementById("projectMemberResponse").innerHTML = "Bad server response";
            }
        }
    };
    xhttp.open("DELETE", "../Controller/removeProjectMember.php?project_id=" + projectId + "&user_id=" + userId, true);
    xhttp.send();
}