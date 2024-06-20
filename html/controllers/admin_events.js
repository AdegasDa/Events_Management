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
    $("#adm_sidebar").append("<a id='adm_events' class='nav-link active' href='#'> <div class='sb-nav-link-icon'> <i class='fas fa-list-ul'></i> </div> Events </a>");
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
    admin_events_add();
    admin_events_list();
}

function admin_events_list()
{
    if ( ! $.fn.DataTable.isDataTable('#admin_events_list') )
    {
        $('#admin_events_list').DataTable(
        {
            rowCallback: function(row, data)
            {
                var event = {
                    id: data[0],
                    name: data[1],
                    category: data[10],
                    detail: data[11],
                    address: data[12],
                    location: data[13],
                    capacity: data[14],
                    date: data[4],
                    subscription_limit: data[5],
                    price: data[6]
                };
                $(row).find('#button_edit').click( function() { button_edit(event); } );
                $(row).find('#button_delete').click( function() { button_delete(event); } );
                $(row).find('#button_subscribers').click( function() { button_subscribers(event); } );
            },
            pageLength: 50,
            autoWidth: false,
            responsive: true,
            processing: true,
            columnDefs: 
            [ 
                { targets: 7, orderable: false },
                { targets: 8, orderable: false },
                { targets: 9, orderable: false }
            ],
            serverSide: true,
            ajax:
            {
                url :'../models/admin_events_list.php', // json datasource
                type: 'post'
            }    
        });
    }
    else
    {
        $('#admin_events_list').DataTable().ajax.reload();
    }
}

function admin_events_subs_list(event_id)
{
    if ( $.fn.DataTable.isDataTable('#admin_events_subs_list') )
    {
        $('#admin_events_subs_list').DataTable().destroy();
    }
    
    $('#admin_events_subs_list').DataTable(
    {
        rowCallback: function(row, data)
        { },
        pageLength: 50,
        autoWidth: false,
        responsive: true,
        processing: true,
        columnDefs: 
        [ ],
        serverSide: true,
        ajax:
        {
            url :'../models/admin_events_subs_list.php',
            type: 'post',
            data: function(data)
            {
                data.event_id = event_id;
            }
        }    
    });
}

function admin_events_add()
{
    $("#button_add").unbind().on("click", function()
    {
        $.post("../models/admin_events_dropdowns.php", { id: "" }, function(dropdowns)
        {
            // Fill dropdowns
            fill_dropdowns("add", dropdowns);
            // Add
            $("#event_add_save").unbind().on("click", function()
            {
                clear_errors();
                
                // Cria um novo objeto FormData
                var formData = new FormData();
                
                // Adiciona os dados do formulário ao FormData sob a chave 'form'
                var formFields = $("#event_add_form").serializeArray();
                formFields.forEach( function(field, index)
                {
                    formData.append('form[' + index + '][name]', field.name);
                    formData.append('form[' + index + '][value]', field.value);
                });
                
                // Adiciona os artistas ao FormData
                var artists = $("#event_add_artists option:selected").map( function() { return this.value; }).get().join(",");
                formData.append("artists", artists);
                
                // Adiciona a imagem ao FormData
                var fileInput = $('#event_add_img')[0].files[0];
                if ( fileInput ) { formData.append("event_add_img", fileInput); }
                
                $.ajax({
                    url: "../models/admin_events_insert.php",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false, 
                    dataType: "json",
                    success: function(data)
                    {
                        for ( var i = 0; i < data.length; i++ )
                        {
                            switch ( data[i]['code'] )
                            {
                                case 0:
                                    // Reload datatable
                                    admin_events_list();
                                    // Success warning
                                    toastr["success"](data[i]['error']);
                                    // Close modal
                                    $("#admin_events_add").modal("hide");
                                    // Clear form
                                    $("#event_add_form input").val("");
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
                    }
                });
            });
            // Close
            $("#event_add_close").unbind().on("click", function()
            {
                // Clear errors
                clear_errors();
                // Reload datatable
                admin_events_list();
                // Close modal
                $("#admin_events_add").modal("hide");
                // Clear form
                $("#event_add_form input").val("");
            });
            // Modal - Open
            $("#admin_events_add").modal("show");
        }, 'json');
    });
}

function button_edit(event)
{
    $.post("../models/admin_events_dropdowns.php", { id: event['id'] }, function(dropdowns)
    {
        // Fill dropdowns
        fill_dropdowns("edit", dropdowns);
        // Fill - Form
        $("#event_edit_name").val(event['name']);
        $("#event_edit_category").val(event['category']);
        if ( !$("#event_edit_category").val() )
        {
            $("#event_edit_category").val("0");
        }
        $("#event_edit_detail").val(event['detail']);
        $("#event_edit_address").val(event['address']);
        $("#event_edit_location").val(event['location']);
        $("#event_edit_capacity").val(event['capacity']);
        $("#event_edit_date").val(event['date']);
        $("#event_edit_subscription_limit").val(event['subscription_limit']);
        $("#event_edit_price").val(event['price']);
        // Save
        $("#event_edit_save").unbind().on("click", function()
        {
            clear_errors();

            $.post("../models/admin_events_update.php", { form: $("#event_edit_form").serializeArray(), id: event['id'], artists: $("#event_edit_artists option:selected").map(function(){ return this.value; }).get().join(",") }, function(data)
            {
                for ( i = 0; i < data.length; i++ )
                {
                    switch ( data[i]['code'] )
                    {
                        case 0:
                            // Reload datatable
                            admin_events_list();
                            // Success warning
                            toastr["success"](data[i]['error']);
                            // Close modal
                            $("#admin_events_edit").modal("hide");
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
        $("#event_edit_close").unbind().on("click", function()
        {
            // Clear errors
            clear_errors();
            // Reload datatable
            admin_events_list();
            // Close modal
            $("#admin_events_edit").modal("hide");
        });
        // Modal - Open
        $("#admin_events_edit").modal("show");
    }, 'json');
}

function fill_dropdowns(sufix, dropdowns)
{
    // Fill categories
    $("#event_" + sufix + "_category").html("");

    var category = dropdowns['categories'];

    for ( i = 0; i < category.length; i++ )
    {
        $("#event_" + sufix + "_category").append("<option value='" + category[i]['id'] + "'>" + category[i]['name'] + "</option>");
    }

    // Fill locations
    $("#event_" + sufix + "_location").html("");

    var location = dropdowns['locations'];

    for ( i = 0; i < location.length; i++ )
    {
        $("#event_" + sufix + "_location").append("<option value='" + location[i]['id'] + "'>" + location[i]['name'] + "</option>");
    }

    // Fill artists
    $("#event_" + sufix + "_artists_div").html("<label class='small mb-1' for='event_" + sufix + "_artists'> Artists </label> <select class='form-select' multiple='multiple' id='event_" + sufix + "_artists' name='event_" + sufix + "_artists'> </select>");
    
    $("#event_" + sufix + "_artists").html("");

    var art_all = dropdowns['artists']['all'];

    for ( i = 0; i < art_all.length; i++ )
    {
        $("#event_" + sufix + "_artists").append("<option value='" + art_all[i]['id'] + "'>" + art_all[i]['name'] + "</option>");
    }

    var art_sel = dropdowns['artists']['selected'];

    for ( i = 0; i < art_sel.length; i++ )
    {
        $("#event_" + sufix + "_artists option[value='" + art_sel[i]['id'] + "']").prop('selected', true);
    }

    var dualListbox = $("#event_" + sufix + "_artists").bootstrapDualListbox(
    {
        nonSelectedListLabel: 'Select the artists',
        selectedListLabel: 'Selected artists',
        moveOnSelect: false
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

function button_delete(event)
{
    // Yes
    $("#event_delete_yes").unbind().on("click", function()
    {
        $.post("../models/admin_events_delete.php", { event_id: event['id'] }, function(data)
        {
            for ( i = 0; i < data.length; i++ )
            {
                switch ( data[i]['code'] )
                {
                    case 0:
                        // Reload datatable
                        admin_events_list();
                        // Success warning
                        toastr["success"](data[i]['error']);
                        // Close modal
                        $("#admin_events_delete").modal("hide");
                        // Clear form
                        $("#admin_events_delete_question").html("");
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
    $("#event_delete_no").unbind().on("click", function()
    {
        // Reload datatable
        admin_events_list();
        // Close modal
        $("#admin_events_delete").modal("hide");
        // Clear form
        $("#admin_events_delete_question").html("");
    });
    // Question
    $("#admin_events_delete_question").html("Are you sure you want to delete the event with id " + event['id'] + "?");
    // Modal - Open
    $("#admin_events_delete").modal("show");
}

function button_subs_export(event)
{
    $("#event_subs_export").unbind().click( function()
    {
        $.ajax(
        {
            url: '../models/excel.php?id=' + event['id'] + '&name=' + event['name'],
            method: 'GET',
            xhrFields: { responseType: 'blob' },
            success: function(data)
            {
                if ( data.size > 0 )
                {
                    var a = document.createElement('a');
                    var url = window.URL.createObjectURL(data);
                    a.href = url;
                    a.download = 'inscritos_evento.csv';
                    document.body.append(a);
                    a.click();
                    a.remove();
                    window.URL.revokeObjectURL(url);
                }
                else { toastr["error"]("There is no data to export."); }
            },
            error: function(xhr, status, error) { toastr["error"]("Error generating CSV:" + error); }
        });
    });
}

function button_subscribers(event)
{
    admin_events_subs_list(event['id']);
    // Export Subs
    button_subs_export(event);
    // Add/Remove Subscribers
    button_subs_change(event);
    // Subscribers Title
    $("#admin_events_subs_title").html("Subscribers - " + event['name']);
    // Close
    $("#event_subs_close").unbind().on("click", function()
    {
        // Clear errors
        clear_errors();
        // Reload datatable
        admin_events_list();
        // Close modal
        $("#admin_events_subs").modal("hide");
    });
    // Modal - Open
    $("#admin_events_subs").modal("show");
}

function button_subs_change(event)
{
    $("#event_subs_change").unbind().on("click", function()
    {
        $.post("../models/admin_events_dropdowns.php", { id: event['id'] }, function(dropdowns)
        {
            // Subscribers Change Title
            $("#admin_events_subs_change_title").html("Subscribers - " + event['name']);
            // Subscribers List
            subscribers_list(dropdowns);
            // Close
            $("#event_subs_change_close").unbind().on("click", function()
            {
                // Close modal
                $("#admin_events_subs_change").modal("hide");
                // Modal - Open
                $("#admin_events_subs").modal("show");
            });
            // Save
            $("#event_subs_change_save").unbind().on("click", function()
            {
                clear_errors();

                $.post("../models/admin_events_subs_save.php", { id: event['id'], subscribers: $("#event_subs_subscribers option:selected").map(function(){ return this.value; }).get().join(",") }, function(data)
                {
                    for ( i = 0; i < data.length; i++ )
                    {
                        switch ( data[i]['code'] )
                        {
                            case 0:
                                // Reload datatable
                                admin_events_subs_list(event['id']);
                                // Success warning
                                toastr["success"](data[i]['error']);
                                // Close modal
                                $("#admin_events_subs_change").modal("hide");
                                // Modal - Open
                                $("#admin_events_subs").modal("show");
                                // Clear form
                                $("#event_subs_change_form input").val("");
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
            // Close modal
            $("#admin_events_subs").modal("hide");
            // Modal - Open
            $("#admin_events_subs_change").modal("show");
        }, 'json');
    });
}

function subscribers_list(dropdowns)
{
    // Fill subscribers
    $("#event_subs_change_div").html("<label class='small mb-1' for='event_subs_subscribers'> Subscribers </label> <select class='form-select' multiple='multiple' id='event_subs_subscribers' name='event_subs_subscribers'> </select>");
    
    $("#event_subs_subscribers").html("");

    var subs_all = dropdowns['subscribers']['all'];

    for ( i = 0; i < subs_all.length; i++ )
    {
        $("#event_subs_subscribers").append("<option value='" + subs_all[i]['id'] + "'>" + subs_all[i]['name'] + "</option>");
    }

    var subs_sel = dropdowns['subscribers']['selected'];

    for ( i = 0; i < subs_sel.length; i++ )
    {
        $("#event_subs_subscribers option[value='" + subs_sel[i]['id'] + "']").prop('selected', true);
    }

    var dualListbox = $("#event_subs_subscribers").bootstrapDualListbox(
    {
        nonSelectedListLabel: 'Select the subscribers',
        selectedListLabel: 'Selected subscribers',
        moveOnSelect: false
    });
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