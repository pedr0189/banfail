$("#frmlogin").submit(function( event ){
    event.preventDefault();
    textinputs = $("#frmlogin input");
    textinputs = textinputs.toArray();      //Change object to array
    //Loop sends all items to frontend validate function and stores it in an object to send as ajax
    sanitizedinputs = new Object; 
    textinputs.forEach(textinput => {
        key = $(textinput).attr("type")
        console.log(key);
        sanitizedinputs[key] = validateinput($(textinput).attr("type"),$(textinput).val())
    });
    $.ajax({
        type: "POST",
        url: "./api/api-login.php",
        data: sanitizedinputs,
        success: function(jsonresponse)
        {
            var response = JSON.parse(jsonresponse);
            console.log(response)
            if (response.status == "1") {
                location.href = "home.php";
            }else{
                alert(response.message);
            }
        },
        error: function()
        {
            console.log("Error found");
        }
        
    })
})

function validateinput(type, content){
    switch (type) {
        case 'email':
            return encodeURI(content);
        case 'password':
            return encodeURI(content);
        default:

            break;
    }
}