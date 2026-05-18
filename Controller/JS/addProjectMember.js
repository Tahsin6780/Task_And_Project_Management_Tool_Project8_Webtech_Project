function addProjectMember(projectId){
    let select = document.getElementById("availableMembers");
    if(select.value == "" || select.value == null){
        document.getElementById("projectMemberResponse").innerHTML = "Select a member first";
        return;
    }
    let userId = select.value;
    let userLabel = select.options[select.selectedIndex].text;

    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function(){
        if(this.readyState == 4 && this.status == 200){
            try{
                let res = JSON.parse(this.responseText);
                if(res.ok){
                    let table = document.getElementById("projectMembersTable");
                    let newRow = table.insertRow(-1);
                    newRow.id = "pmRow_" + userId;
                    let parts = userLabel.split(" (");
                    let name = parts[0];
                    let email = parts[1] ? parts[1].replace(")", "") : "";
                    newRow.innerHTML = "<td>" + name + "</td><td>" + email + "</td>" +
                        "<td><button onclick='removeProjectMember(" + projectId + "," + userId + ")'>Remove</button></td>";
                    select.remove(select.selectedIndex);
                    document.getElementById("projectMemberResponse").innerHTML = "<span style='color:green'>Member added.</span>";
                }else{
                    document.getElementById("projectMemberResponse").innerHTML = res.message || "Failed";
                }
            }catch(e){
                document.getElementById("projectMemberResponse").innerHTML = "Bad server response";
            }
        }
    };
    xhttp.open("POST", "../Controller/addProjectMember.php", true);
    xhttp.setRequestHeader("content-type", "application/x-www-form-urlencoded");
    xhttp.send("project_id=" + projectId + "&user_id=" + userId);
}