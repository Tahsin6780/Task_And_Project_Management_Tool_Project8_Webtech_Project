function deleteComment(commentId){
    if(!confirm("Delete this comment?")){
        return;
    }
    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function(){
        if(this.readyState == 4 && this.status == 200){
            try{
                let res = JSON.parse(this.responseText);
                if(res.ok){
                    let row = document.getElementById("comment_" + commentId);
                    if(row){
                        row.style.opacity = 0;
                        setTimeout(function(){ row.parentNode.removeChild(row); }, 600);
                    }
                }else{
                    alert(res.message || "Failed to delete comment");
                }
            }catch(e){
                alert("Bad server response");
            }
        }
    };
    xhttp.open("DELETE", "../Controller/deleteComment.php?id=" + commentId, true);
    xhttp.send();
}
