$( document ).ready(function() { init(); });

function init()
{
    $.post( "../models/check_session.php", function( data )
    {
        switch (data['code'])
        {
            case 0: // Não logado
                redirect("views/login.html");
                break;
            case 1: // Logado
                if ( data['adm'] )
                {
                    admin_page(data['user']);
                }
                else
                {
                    redirect();
                }
                break;
        }
    },'json');
}

function admin_page(user)
{
    $("#body").removeAttr("style");
    sidebar(user);
    topbar();
    body();
}

function sidebar(user)
{
    $("#adm_sidebar").append("<a id='adm_artists' class='nav-link' href='#'> <div class='sb-nav-link-icon'> <i class='fa-solid fa-masks-theater'></i> </div> Artists </a>");
    $("#adm_artists").on("click", function() { redirect("views/admin_artists.html"); });
    $("#adm_sidebar").append("<a id='adm_categories' class='nav-link' href='#'> <div class='sb-nav-link-icon'> <i class='fa-solid fa-icons'></i> </div> Categories </a>");
    $("#adm_categories").on("click", function() { redirect("views/admin_categories.html"); });
    $("#adm_sidebar").append("<a id='adm_events' class='nav-link' href='#'> <div class='sb-nav-link-icon'> <i class='fas fa-list-ul'></i> </div> Events </a>");
    $("#adm_events").on("click", function() { redirect("views/admin_events.html"); });
    $("#adm_sidebar").append("<a class='nav-link active' href='#' data-bs-toggle='collapse' data-bs-target='#collapseLayouts' aria-expanded='true' aria-controls='collapseLayouts'> <div class='sb-nav-link-icon'> <i class='fas fa-users'> </i> </div> Users <div class='sb-sidenav-collapse-arrow'> <i class='fas fa-angle-down'> </i> </div> </a> <div class='collapse show' id='collapseLayouts' aria-labelledby='headingOne' data-bs-parent='#sidenavAccordion'> <nav class='sb-sidenav-menu-nested nav'> <a id='adm_users' class='nav-link' href='#'> Active Users </a> <a id='adm_users_deactivated' class='nav-link active' href='#'> Deactivated Users </a> <a id='adm_users_requests' class='nav-link' href='#'> Users Requests </a> </nav> </div>");
    $("#adm_users").on("click", function() { redirect("views/admin_users.html"); });
    $("#adm_users_deactivated").on("click", function() { redirect("views/admin_users_deactivated.html"); });
    $("#adm_users_requests").on("click", function() { redirect("views/admin_users_requests.html"); });
    $("#adm_user").html(user);
}

function topbar()
{
    $("#adm_topbar_homepage").on("click", function() { redirect("views/admin.html"); });
    $("#adm_user_options").append("<li> <a id='adm_user_homepage' class='dropdown-item' href='#'> Home Page </a> </li>");
    $("#adm_user_homepage").on("click", function() { redirect();  });
    $("#adm_user_options").append("<li> <a id='adm_user_settings' class='dropdown-item' href='#'> Settings </a> </li>");
    $("#adm_user_settings").on("click", function() {  });
    $("#adm_user_options").append("<li> <hr class='dropdown-divider' /> </li>");
    $("#adm_user_options").append("<li> <a id='adm_user_logout' class='dropdown-item' href='#'> Logout </a> </li>");
    $("#adm_user_logout").on("click", function() { $.post("../models/logout.php", function() { redirect(); }); });
}

function body()
{
    admin_users_deactivated_list();
}

function admin_users_deactivated_list()
{
    if ( ! $.fn.DataTable.isDataTable('#admin_users_deactivated_list') )
    {
        $('#admin_users_deactivated_list').DataTable(
        {
            rowCallback: function(row, data)
            {
                var user = {
                    id: data[0],
                    name: data[1],
                    username: data[2],
                    email: data[3],
                    register_date: data[4],
                    role: data[5],
                    address: data[7],
                    phone: data[8],
                    cc: data[9],
                    birth_date: data[10]
                };
                $(row).find('#button_active').click( function() { button_active(user); } );
            },
            pageLength: 50,
            autoWidth: false,
            responsive: true,
            processing: true,
            columnDefs: 
            [ 
                { targets: 6, orderable: false }
            ],
            serverSide: true,
            ajax:
            {
                url :'../models/admin_users_deactivated_list.php', // json datasource
                type: 'post'
            }    
        });
    }
    else
    {
        $('#admin_users_deactivated_list').DataTable().ajax.reload();
    }
}

function button_active(user)
{
    // Yes
    $("#user_active_yes").unbind().on("click", function()
    {
        $.post("../models/admin_users_deactivated_active.php", { user_id: user['id'] }, function(data)
        {
            for ( i = 0; i < data.length; i++ )
            {
                switch ( data[i]['code'] )
                {
                    case 0:
                        // Reload datatable
                        admin_users_deactivated_list();
                        // Success warning
                        toastr["success"](data[i]['error']);
                        // Close modal
                        $("#admin_users_active").modal("hide");
                        // Clear form
                        $("#admin_users_active_question").html("");
                        break;
                    case 1:
                        toastr["error"](data[i]['error']);
                        break;
                    default: 
                        // Error
                        toastr["error"](data[i]['error']);
                        break;
                }
            }
        }, 'json');
    });
    // No
    $("#user_active_no").unbind().on("click", function()
    {
        // Reload datatable
        admin_users_deactivated_list();
        // Close modal
        $("#admin_users_active").modal("hide");
        // Clear form
        $("#admin_users_active_question").html("");
    });
    // Question
    $("#admin_users_active_question").html("Are you sure you want to activate the user with id " + user['id'] + "?");
    // Modal - Open
    $("#admin_users_active").modal("show");
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