function removeMember(memberRowId){
    if(!confirm("Remove this member from the workspace?")){
        return;
    }
    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function(){
        if(this.readyState == 4){
            if(this.status == 200){
                try{
                    let res = JSON.parse(this.responseText);
                    if(res.ok){
                        let row = document.getElementById("memberRow_" + memberRowId);
                        row.className = "fading";
                        setTimeout(function(){
                            row.parentNode.removeChild(row);
                        }, 700);
                    }else{
                        document.getElementById("removeResponse").innerHTML = res.message || "Failed to remove member";
                    }
                }catch(e){
                    document.getElementById("removeResponse").innerHTML = "Bad server response";
                }
            }else{
                document.getElementById("removeResponse").innerHTML = "Server error (" + this.status + ")";
            }
        }
    };
    xhttp.open("DELETE", "../Controller/removeMember.php?id=" + memberRowId, true);
    xhttp.send();
}
