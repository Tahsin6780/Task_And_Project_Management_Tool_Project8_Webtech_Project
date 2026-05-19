function deleteTask(taskId){
    if(!confirm("Delete this task? This will also delete all its comments.")){
        return;
    }
    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function(){
        if(this.readyState == 4 && this.status == 200){
            try{
                let res = JSON.parse(this.responseText);
                if(res.ok){
                    let card = document.getElementById("task_" + taskId);
                    if(card){
                        card.className = "task-card fading";
                        setTimeout(function(){ card.parentNode.removeChild(card); }, 600);
                    }
                }else{
                    alert(res.message || "Failed to delete task");
                }
            }catch(e){
                alert("Bad server response");
            }
        }
    };
    xhttp.open("DELETE", "../Controller/deleteTask.php?id=" + taskId, true);
    xhttp.send();
}
