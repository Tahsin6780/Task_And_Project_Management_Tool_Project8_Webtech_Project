function applyFilters(){
    let assignee = document.getElementById("filterAssignee").value;
    let priority = document.getElementById("filterPriority").value;
    let cards = document.getElementsByClassName("task-card");
    let visible = 0;

    for(let i = 0; i < cards.length; i++){
        let card = cards[i];
        let cardAssignee = card.getAttribute("data-assignee");
        let cardPriority = card.getAttribute("data-priority");
        let show = true;

        if(assignee != ""){
            if(assignee == "unassigned"){
                if(cardAssignee != "") show = false;
            }else{
                if(cardAssignee != assignee) show = false;
            }
        }
        if(priority != "" && cardPriority != priority){
            show = false;
        }

        card.style.display = show ? "" : "none";
        if(show) visible++;
    }

    document.getElementById("filterCount").innerHTML = visible + " task(s) shown";
}
