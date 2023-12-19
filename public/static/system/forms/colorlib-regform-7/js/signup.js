var agreeElement = document.getElementById('agree-term');
function checkAgreeTerm(){
    var btn = document.getElementById('btn-signup');
    var status = false;
    if(agreeElement){
        if (agreeElement.checked) {
            status = true;
        }
    }
    if(status){
        btn.disabled = false;
    }
    else{
        btn.disabled = true;
    }
}
agreeElement.addEventListener("change", function(){
    setTimeout(checkAgreeTerm, 100);
});
checkAgreeTerm()
