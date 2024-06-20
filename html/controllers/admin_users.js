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
    $("#adm_sidebar").append("<a class='nav-link active' href='#' data-bs-toggle='collapse' data-bs-target='#collapseLayouts' aria-expanded='true' aria-controls='collapseLayouts'> <div class='sb-nav-link-icon'> <i class='fas fa-users'> </i> </div> Users <div class='sb-sidenav-collapse-arrow'> <i class='fas fa-angle-down'> </i> </div> </a> <div class='collapse show' id='collapseLayouts' aria-labelledby='headingOne' data-bs-parent='#sidenavAccordion'> <nav class='sb-sidenav-menu-nested nav'> <a id='adm_users' class='nav-link active' href='#'> Active Users </a> <a id='adm_users_deactivated' class='nav-link' href='#'> Deactivated Users </a> <a id='adm_users_requests' class='nav-link' href='#'> Users Requests </a> </nav> </div>");
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
    // Title
//    $("#adm_body").append("<h1 class='mt-4'> Users </h1>");
    // Description
//    $("#adm_body").append("<ol class='breadcrumb mb-4'> <li class='breadcrumb-item active'> Dashboard </li> </ol>");
    // Body
//    $("#adm_body").append("<div class='card mb-4'> <div class='card-header'> <i class='fas fa-users me-1'></i> Users List </div> <div id='adm_content' class='card-body'> </div> </div>");
    // Content
//    $("#adm_content").append("TESTE DO IVO");
    button_add();
    admin_users_list();
}

function admin_users_list()
{
    if ( ! $.fn.DataTable.isDataTable('#admin_users_list') )
    {
        $('#admin_users_list').DataTable(
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
                    address: data[8],
                    phone: data[9],
                    cc: data[10],
                    birth_date: data[11]
                };
                $(row).find('#button_edit').click( function() { button_edit(user); } );
                $(row).find('#button_delete').click( function() { button_delete(user); } );
    //            $(row).find('td').dblclick(function() { work_details(data[17],data[21],data[23],data[24]); });
            },
            pageLength: 50,
            autoWidth: false,
            responsive: true,
            processing: true,
            columnDefs: 
            [ 
                { targets: 6, orderable: false },
                { targets: 7, orderable: false }
            ],
            serverSide: true,
            ajax:
            {
                url :'../models/admin_users_list.php', // json datasource
                type: 'post'
            }    
        });
    }
    else
    {
        $('#admin_users_list').DataTable().ajax.reload();
    }
}

function button_add()
{
    $("#button_add").unbind().on("click", function()
    {
        // Add
        $("#user_add_save").unbind().on("click", function()
        {
            clear_errors();
            
            $.post("../models/admin_users_insert.php", { form: $("#user_add_form").serializeArray() }, function(data)
            {
                for ( i = 0; i < data.length; i++ )
                {
                    switch ( data[i]['code'] )
                    {
                        case 0:
                            // Reload datatable
                            admin_users_list();
                            // Success warning
                            toastr["success"](data[i]['error']);
                            // Close modal
                            $("#admin_users_add").modal("hide");
                            // Clear form
                            $("#user_add_form input").val("");
                            break;
                        case 1: 
                            error(data[i]['input'], data[i]['error']);
                            break;
                        default: 
                            // Error
                            toastr["error"](data[i]['error']);
                            break;
                    }
                }
            }, 'json');
        });
        // Close
        $("#user_add_close").unbind().on("click", function()
        {
            // Clear errors
            clear_errors();
            // Reload datatable
            admin_users_list();
            // Close modal
            $("#admin_users_add").modal("hide");
            // Clear form
            $("#user_add_form input").val("");
        });
        // Modal - Open
        $("#admin_users_add").modal("show");
    });
}

function button_edit(user)
{
    // Fill - Form
    $("#user_edit_id").val(user['id']);
    $("#user_edit_register_date").val(user['register_date']);
    $("#user_edit_name").val(user['name']);
    $("#user_edit_username").val(user['username']);
    $("#user_edit_email").val(user['email']);
    $("#user_edit_role").val(user['role']);
    $("#user_edit_address").val(user['address']);
    $("#user_edit_phone").val(user['phone']);
    $("#user_edit_cc").val(user['cc']);
    $("#user_edit_birth_date").val(user['birth_date']);
    // Save
    $("#user_edit_save").unbind().on("click", function()
    {
        clear_errors();
        
        $.post("../models/admin_users_update.php", { form: $("#user_edit_form").serializeArray(), id: user['id'] }, function(data)
        {
            for ( i = 0; i < data.length; i++ )
            {
                switch ( data[i]['code'] )
                {
                    case 0:
                        // Reload datatable
                        admin_users_list();
                        // Success warning
                        toastr["success"](data[i]['error']);
                        // Close modal
                        $("#admin_users_edit").modal("hide");
                        break;
                    case 1: 
                        error(data[i]['input'], data[i]['error']);
                        break;
                    default: 
                        // Error
                        toastr["error"](data[i]['error']);
                        break;
                }
            }
        }, 'json');
    });
    // Close
    $("#user_edit_close").unbind().on("click", function()
    {
        // Clear errors
        clear_errors();
        // Reload datatable
        admin_users_list();
        // Close modal
        $("#admin_users_edit").modal("hide");
    });
    // Modal - Open
    $("#admin_users_edit").modal("show");
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

function button_delete(user)
{
    // Yes
    $("#user_delete_yes").unbind().on("click", function()
    {
        $.post("../models/admin_users_delete.php", { user_id: user['id'] }, function(data)
        {
            for ( i = 0; i < data.length; i++ )
            {
                switch ( data[i]['code'] )
                {
                    case 0:
                        // Reload datatable
                        admin_users_list();
                        // Success warning
                        toastr["success"](data[i]['error']);
                        // Close modal
                        $("#admin_users_delete").modal("hide");
                        // Clear form
                        $("#admin_users_delete_question").html("");
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
    $("#user_delete_no").unbind().on("click", function()
    {
        // Reload datatable
        admin_users_list();
        // Close modal
        $("#admin_users_delete").modal("hide");
        // Clear form
        $("#admin_users_delete_question").html("");
    });
    // Question
    $("#admin_users_delete_question").html("Are you sure you want to delete the user with id " + user['id'] + "?");
    // Modal - Open
    $("#admin_users_delete").modal("show");
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