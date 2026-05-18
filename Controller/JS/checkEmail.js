function checkEmail(){
    let email = document.getElementById("email").value;
    let xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function(){
        if(this.readyState == 4 && this.status == 200){
            try{
                let res = JSON.parse(this.responseText);
                document.getElementById("emailResponse").innerHTML =
                    "<span style='color:" + (res.available ? "green" : "red") + "'>" + res.message + "</span>";
            }catch(e){
                document.getElementById("emailResponse").innerHTML = this.responseText;
            }
        }
    };
    xhttp.open("POST", "../Controller/checkEmail.php", true);
    xhttp.setRequestHeader("content-type", "application/x-www-form-urlencoded");
    xhttp.send("email=" + encodeURIComponent(email));
}
