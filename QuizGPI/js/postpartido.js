$(document).ready(function() { 
    var span_progress = document.getElementById("puntuacion"),
    pct = parseInt(span_progress.innerHTML),
    top = pct,
    div_loading_progress = document.getElementById("div_loading_progress");

    pct = 0;
    span_progress.innerHTML = "0";

    function display_pct(p) {
        span_progress.innerHTML=""+p+"";
        div_loading_progress.className="c100 center p"+p;
    }

    function update_pct(){
        display_pct(pct++);
            
        if (pct<=top) {
            setTimeout(update_pct,50);
        }
    }

    setTimeout(update_pct,100);
});
