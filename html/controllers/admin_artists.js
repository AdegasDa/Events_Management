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
    $("#adm_sidebar").append("<a id='adm_artists' class='nav-link active' href='#'> <div class='sb-nav-link-icon'> <i class='fa-solid fa-masks-theater'></i> </div> Artists </a>");
    $("#adm_artists").on("click", function() { redirect("views/admin_artists.html"); });
    $("#adm_sidebar").append("<a id='adm_categories' class='nav-link' href='#'> <div class='sb-nav-link-icon'> <i class='fa-solid fa-icons'></i> </div> Categories </a>");
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
    admin_artists_list();
}

function admin_artists_list()
{
    if ( ! $.fn.DataTable.isDataTable('#admin_artists_list') )
    {
        $('#admin_artists_list').DataTable(
        {
            rowCallback: function(row, data)
            {
                var artist = {
                    id: data[0],
                    name: data[1],
                    location: data[7],
                    category: data[8]
                };
                $(row).find('#button_edit').click( function() { button_edit(artist); } );
                $(row).find('#button_delete').click( function() { button_delete(artist); } );
            },
            pageLength: 50,
            autoWidth: false,
            responsive: true,
            processing: true,
            columnDefs: 
            [ 
                { targets: 5, orderable: false },
                { targets: 6, orderable: false }
            ],
            serverSide: true,
            ajax:
            {
                url :'../models/admin_artists_list.php', // json datasource
                type: 'post'
            }    
        });
    }
    else
    {
        $('#admin_artists_list').DataTable().ajax.reload();
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
        $.post("../models/admin_artists_dropdowns.php", function(dropdowns)
        {
            // Fill categories
            $("#artist_add_category").html("");

            var category = dropdowns['categories'];

            for ( i = 0; i < category.length; i++ )
            {
                $("#artist_add_category").append("<option value='" + category[i]['id'] + "'>" + category[i]['name'] + "</option>");
            }
            
            // Fill locations
            $("#artist_add_location").html("");

            var location = dropdowns['locations'];

            for ( i = 0; i < location.length; i++ )
            {
                $("#artist_add_location").append("<option value='" + location[i]['id'] + "'>" + location[i]['name'] + "</option>");
            }
            
            // Add
            $("#artist_add_save").unbind().on("click", function()
            {
                clear_errors();

                $.post("../models/admin_artists_insert.php", { form: $("#artist_add_form").serializeArray() }, function(data)
                {
                    for ( i = 0; i < data.length; i++ )
                    {
                        switch ( data[i]['code'] )
                        {
                            case 0:
                                // Reload datatable
                                admin_artists_list();
                                // Success warning
                                toastr["success"](data[i]['error']);
                                // Close modal
                                $("#admin_artists_add").modal("hide");
                                // Clear form
                                $("#artist_add_form input").val("");
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
            $("#artist_add_close").unbind().on("click", function()
            {
                // Clear errors
                clear_errors();
                // Reload datatable
                admin_artists_list();
                // Close modal
                $("#admin_artists_add").modal("hide");
                // Clear form
                $("#artist_add_form input").val("");
            });
            // Modal - Open
            $("#admin_artists_add").modal("show");
        }, 'json');
    });
}

function button_edit(artist)
{
    $.post("../models/admin_artists_dropdowns.php", function(dropdowns)
    {
        // Fill categories
        $("#artist_edit_category").html("");

        var category = dropdowns['categories'];

        for ( i = 0; i < category.length; i++ )
        {
            $("#artist_edit_category").append("<option value='" + category[i]['id'] + "'>" + category[i]['name'] + "</option>");
        }
        
        // Fill locations
        $("#artist_edit_location").html("");

        var location = dropdowns['locations'];

        for ( i = 0; i < location.length; i++ )
        {
            $("#artist_edit_location").append("<option value='" + location[i]['id'] + "'>" + location[i]['name'] + "</option>");
        }
        
        // Fill - Form
        $("#artist_edit_id").val(artist['id']);
        $("#artist_edit_name").val(artist['name']);
        $("#artist_edit_location").val(artist['location']);
        $("#artist_edit_category").val(artist['category']);
        // Save
        $("#artist_edit_save").unbind().on("click", function()
        {
            clear_errors();

            $.post("../models/admin_artists_update.php", { form: $("#artist_edit_form").serializeArray(), id: artist['id'] }, function(data)
            {
                for ( i = 0; i < data.length; i++ )
                {
                    switch ( data[i]['code'] )
                    {
                        case 0:
                            // Reload datatable
                            admin_artists_list();
                            // Success warning
                            toastr["success"](data[i]['error']);
                            // Close modal
                            $("#admin_artists_edit").modal("hide");
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
        $("#artist_edit_close").unbind().on("click", function()
        {
            // Clear errors
            clear_errors();
            // Reload datatable
            admin_artists_list();
            // Close modal
            $("#admin_artists_edit").modal("hide");
        });
        // Modal - Open
        $("#admin_artists_edit").modal("show");
    }, 'json');
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

function button_delete(artist)
{
    // Yes
    $("#artist_delete_yes").unbind().on("click", function()
    {
        $.post("../models/admin_artists_delete.php", { id: artist['id'] }, function(data)
        {
            for ( i = 0; i < data.length; i++ )
            {
                switch ( data[i]['code'] )
                {
                    case 0:
                        // Reload datatable
                        admin_artists_list();
                        // Success warning
                        toastr["success"](data[i]['error']);
                        // Close modal
                        $("#admin_artists_delete").modal("hide");
                        // Clear form
                        $("#admin_artists_delete_question").html("");
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
    $("#artist_delete_no").unbind().on("click", function()
    {
        // Reload datatable
        admin_artists_list();
        // Close modal
        $("#admin_artists_delete").modal("hide");
        // Clear form
        $("#admin_artists_delete_question").html("");
    });
    // Question
    $("#admin_artists_delete_question").html("Are you sure you want to delete the artist with id " + artist['id'] + "?");
    // Modal - Open
    $("#admin_artists_delete").modal("show");
}