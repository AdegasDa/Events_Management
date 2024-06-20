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
            case 0:
                token_submit();
                token_login();
                token_resend();
                break;
                
            case 1: // Logado
                redirect();
                break;
        }
    },'json');
}

function token_login()
{
    $('#token_cancel').off('click').on('click', function()
    {
        redirect("views/login.html");
    });
}

function token_submit()
{
    $('#token_submit').off('click').on('click', function(event)
    {
        event.preventDefault();
        
        clear_errors();
        
        $.post("../models/login_request.php", { token_form: $('#token_form').serializeArray() }, function(data)
        {
            for ( i = 0; i < data.length; i++ )
            {
                switch ( data[i]['code'] )
                {
                    case 0:
                        redirect();
                        break;
                    case 1: 
                        error(data[i]['input'], data[i]['error']);
                        break;
                }
            }
        }, 'json');
    });
}

function token_resend()
{
    $("#resend_email").on("click", function(event)
    {
        event.preventDefault();
        
        clear_errors();
        
        $.post("../models/login_request_resend.php",{}, function(data)
        {
            for ( i = 0; i < data.length; i++ )
            {
                switch ( data[i]['code'] )
                {
                    case 0:
                        toastr['success'](data[i]['error']);
                        break;
                    case 1: 
                        error(data[i]['input'], data[i]['error']);
                        toastr['error'](data[i]['error']);
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
