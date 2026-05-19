function moveTask(taskId, newStatus){
    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function(){
        if(this.readyState == 4 && this.status == 200){
            try{
                let res = JSON.parse(this.responseText);
                if(res.ok){
                    // Reload the board so columns recalc cleanly
                    window.location.reload();
                }else{
                    alert(res.message || "Failed to move task");
                }
            }catch(e){
                alert("Bad server response");
            }
        }
    };
    xhttp.open("POST", "../Controller/updateTaskStatus.php", true);
    xhttp.setRequestHeader("content-type", "application/x-www-form-urlencoded");
    xhttp.send("id=" + taskId + "&status=" + encodeURIComponent(newStatus));
}
