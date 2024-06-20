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
                register_submit();
                register_have_account();
                break;
                
            case 1: // Logado
                redirect();
                break;
        }
    },'json');
}

function register_have_account()
{
    $("#register_have_account").on("click", function()
    {
        redirect("views/login.html");
    });
}

function register_submit()
{
    $("#register_submit").on("click", function(event)
    {
        event.preventDefault();
        
        clear_errors();
        
        $.post("../models/register_submit.php", { register_form: $("#register_form").serializeArray() }, function(data)
        {
            for ( i = 0; i < data.length; i++ )
            {
                switch ( data[i]['code'] )
                {
                    case 0:
                        redirect("views/login.html");
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