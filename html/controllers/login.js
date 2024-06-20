$( document ).ready(function()
{
    init();
});

function init()
{
    $.post( "../models/check_session.php", function( data )
    {
        switch (data['code'])
        {
            case 0: // Não logado
                login_forgot_password();
                login_register();
                login_submit();
                break;
                
            case 1: // Logado
                redirect();
                break;
        }
    },'json');
}

function login_forgot_password()
{
    $("#login_forgot_password").on("click", function()
    {
        redirect("views/forgot_password.html");
    });
}

function login_register()
{
    $("#login_register").on("click", function()
    {
        redirect("views/register.html");
    });
}

function login_submit()
{
    $("#login_submit").on("click", function(event)
    {
        event.preventDefault();
        
        clear_errors();
        
        $.post("../models/login_submit.php", { login_form: $("#login_form").serializeArray() }, function(data)
        {
            for ( i = 0; i < data.length; i++ )
            {
                switch ( data[i]['code'] )
                {
                    case 0:
                        redirect("views/login_request.html");
                        break;
                    case 1: 
                        error(data[i]['input'], data[i]['error']);
                        break;
                }
            }
        }, 'json');
    });
}

function clear_errors()
{
    $(".border-danger").removeClass("border-danger");
    $(".text-danger").remove();
}

function error(input, error)
{
    $("#" + input).parent().append("<div class='text-danger'> " + error + " </div>");
    $("#" + input).addClass("border-danger");
}

function redirect(page)
{
    if ( page )
    {
        window.location.replace("http://atw2024.ddns.net/" + page);
    }
    else
    {
        window.location.replace("http://atw2024.ddns.net/");
    }
}