function checkProjectName(){
    let name = document.getElementById("projName").value;
    if(name.trim() == ""){
        document.getElementById("projNameResponse").innerHTML = "";
        return;
    }
    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function(){
        if(this.readyState == 4 && this.status == 200){
            try{
                let res = JSON.parse(this.responseText);
                document.getElementById("projNameResponse").innerHTML =
                    "<span style='color:" + (res.available ? "green" : "red") + "'>" + res.message + "</span>";
            }catch(e){
                document.getElementById("projNameResponse").innerHTML = this.responseText;
            }
        }
    };
    xhttp.open("POST", "../Controller/checkProjectName.php", true);
    xhttp.setRequestHeader("content-type", "application/x-www-form-urlencoded");
    xhttp.send("name=" + encodeURIComponent(name));
}