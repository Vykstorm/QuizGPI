$(function(){

    /************** LOGIN ***************/
    $(".login-form").css({
        opacity: 1,
        "-webkit-transform": "scale(1)",
        "transform": "scale(1)",
        "-webkit-transition": ".5s",
        "transition": ".5s"
    });
    
    
    
    /************ REGISTER *************/
    $(".register-form").css({
        opacity: 1,
        "-webkit-transform": "scale(1)",
        "transform": "scale(1)",
        "-webkit-transition": ".5s",
        "transition": ".5s"
    });
    
    $("#reg-form").submit(function(event){
        
        var user  = $('#user_login').val();
		var pwd1  = $('#user_password').val();
		var pwd2  = $('#user_password2').val();
        
        if(user.length < 4){
        
            /*console.log("El nombre es demasiado corto");
            $.Notify({
                caption: 'Error',
                content: 'El nombre de usuario debe tener una longitud mínima de 4',
                type: 'alert'
            });*/
            $('#user_login').popover('show');
			event.preventDefault();
            
        }
        
        if(pwd1 != pwd2){
			
            $('#user_password2').popover('show');
			event.preventDefault();

		}else{
        
            if(pwd1.length < 4){
                
                $('#user_password').popover('show');
                event.preventDefault();
            }
        }

        
    });
    
    
});