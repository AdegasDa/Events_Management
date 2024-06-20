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
    $("#adm_sidebar").append("<a id='adm_categories' class='nav-link active' href='#'> <div class='sb-nav-link-icon'> <i class='fa-solid fa-icons'></i> </div> Categories </a>");
    $("#adm_categories").on("click", function() { redirect("views/admin_categories.html"); });
    $("#adm_sidebar").append("<a id='adm_events' class='nav-link' href='#'> <div class='sb-nav-link-icon'> <i class='fas fa-list-ul'></i> </div> Events </a>");
    $("#adm_events").on("click", function() { redirect("views/admin_events.html"); });
    $("#adm_sidebar").append("<a id='adm_users' class='nav-link' href='#'> <div class='sb-nav-link-icon'> <i class='fas fa-users'></i> </div> Users </a>");
    $("#adm_users").on("click", function() { redirect("views/admin_users.html"); });
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
    admin_categories_list();
}

function admin_categories_list()
{
    if ( ! $.fn.DataTable.isDataTable('#admin_categories_list') )
    {
        $('#admin_categories_list').DataTable(
        {
            rowCallback: function(row, data)
            {
                var category = {
                    id: data[0],
                    name: data[1],
                    type: data[5]
                };
                $(row).find('#button_edit').click( function() { button_edit(category); } );
                $(row).find('#button_delete').click( function() { button_delete(category); } );
            },
            pageLength: 50,
            autoWidth: false,
            responsive: true,
            processing: true,
            columnDefs: 
            [ 
                { targets: 3, orderable: false },
                { targets: 4, orderable: false }
            ],
            serverSide: true,
            ajax:
            {
                url :'../models/admin_categories_list.php', // json datasource
                type: 'post'
            }    
        });
    }
    else
    {
        $('#admin_categories_list').DataTable().ajax.reload();
    }
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

function button_add()
{
    $("#button_add").unbind().on("click", function()
    {
        // Add
        $("#category_add_save").unbind().on("click", function()
        {
            clear_errors();
            
            $.post("../models/admin_categories_insert.php", { form: $("#category_add_form").serializeArray() }, function(data)
            {
                for ( i = 0; i < data.length; i++ )
                {
                    switch ( data[i]['code'] )
                    {
                        case 0:
                            // Reload datatable
                            admin_categories_list();
                            // Success warning
                            toastr["success"](data[i]['error']);
                            // Close modal
                            $("#admin_categories_add").modal("hide");
                            // Clear form
                            $("#category_add_form input").val("");
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
        $("#category_add_close").unbind().on("click", function()
        {
            // Clear errors
            clear_errors();
            // Reload datatable
            admin_categories_list();
            // Close modal
            $("#admin_categories_add").modal("hide");
            // Clear form
            $("#category_add_form input").val("");
        });
        // Modal - Open
        $("#admin_categories_add").modal("show");
    });
}

function button_edit(category)
{
    // Fill - Form
    $("#category_edit_name").val(category['name']);
    $("#category_edit_type").val(category['type']);
    // Save
    $("#category_edit_save").unbind().on("click", function()
    {
        clear_errors();
        
        $.post("../models/admin_categories_update.php", { form: $("#category_edit_form").serializeArray(), id: category['id'] }, function(data)
        {
            for ( i = 0; i < data.length; i++ )
            {
                switch ( data[i]['code'] )
                {
                    case 0:
                        // Reload datatable
                        admin_categories_list();
                        // Success warning
                        toastr["success"](data[i]['error']);
                        // Close modal
                        $("#admin_categories_edit").modal("hide");
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
    $("#category_edit_close").unbind().on("click", function()
    {
        // Clear errors
        clear_errors();
        // Reload datatable
        admin_categories_list();
        // Close modal
        $("#admin_categories_edit").modal("hide");
    });
    // Modal - Open
    $("#admin_categories_edit").modal("show");
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

function button_delete(category)
{
    // Yes
    $("#category_delete_yes").unbind().on("click", function()
    {
        $.post("../models/admin_categories_delete.php", { category_id: category['id'] }, function(data)
        {
            for ( i = 0; i < data.length; i++ )
            {
                switch ( data[i]['code'] )
                {
                    case 0:
                        // Reload datatable
                        admin_categories_list();
                        // Success warning
                        toastr["success"](data[i]['error']);
                        // Close modal
                        $("#admin_categories_delete").modal("hide");
                        // Clear form
                        $("#admin_categories_delete_question").html("");
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
    $("#category_delete_no").unbind().on("click", function()
    {
        // Reload datatable
        admin_categories_list();
        // Close modal
        $("#admin_categories_delete").modal("hide");
        // Clear form
        $("#admin_categories_delete_question").html("");
    });
    // Question
    $("#admin_categories_delete_question").html("Are you sure you want to delete the category with id " + category['id'] + "?");
    // Modal - Open
    $("#admin_categories_delete").modal("show");
}