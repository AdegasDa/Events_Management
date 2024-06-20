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
                redirect("../views/login.html");
                break;
            case 1:
                if (data['adm'])
                {
                    home_nav_items_adm();
                }
                else
                {
                    home_nav_items();
                }
                
                event_get_data();
                break;
        }
    },'json');
    
}

function redirect(path)
{
    window.location.replace(path);
}

function home_nav_items_adm()
{
    $("#nav_items").append('<ul class="navbar-nav ms-1 me-lg-5 me-2"> <li class="nav-item dropdown"> <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a> <ul id="home_user_options" class="dropdown-menu dropdown-menu-end p-2" aria-labelledby="navbarDropdown"></ul> </li> </ul>');
    $("#home_user_options").append('<li class="text-start mb-1"><i class="fa-solid fa-user-gear me-2"></i><a href="#" id="home_profile" class="text-decoration-none text-dark">Profile</a></li>');
    $("#home_user_options").append('<li class="text-start mb-1"><i class="fa-solid fa-table-columns me-2"></i><a href="#" id="home_dashboard" class="text-decoration-none text-dark">Administration</a></li>');
    $("#home_user_options").append('<li class="text-start mb-1"><i class="fa-solid fa-note-sticky me-2"></i><a href="#" id="home_events_registered_menu" class="text-decoration-none text-dark">Events</a></li>');
    $("#home_user_options").append('<li class="text-start"> <i class="fa-solid fa-right-from-bracket me-2"></i> <a href="#" id="home_logout" class="text-decoration-none text-dark"> Logout </a> </li>');

    home_profile();
    home_dashboard();
    home_registered_events();
    home_logout();
}

function home_nav_items()
{    
    $("#nav_items").append('<ul class="navbar-nav ms-1 me-lg-5 me-2"> <li class="nav-item dropdown"> <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a> <ul id="home_user_options" class="dropdown-menu dropdown-menu-end p-2" aria-labelledby="navbarDropdown"></ul> </li> </ul>');
    $("#home_user_options").append('<li class="text-start mb-1"><i class="fa-solid fa-user-gear me-2"></i><a href="#" id="home_profile" class="text-decoration-none text-dark">Profile</a></li>');
    $("#home_user_options").append('<li class="text-start mb-1"><i class="fa-solid fa-note-sticky me-2"></i><a href="#" id="home_events_registered_menu" class="text-decoration-none text-dark">Events</a></li>');
    $("#home_user_options").append('<li class="text-start"> <i class="fa-solid fa-right-from-bracket me-2"></i> <a href="#" id="home_logout" class="text-decoration-none text-dark"> Logout </a> </li>');

    home_profile();
    home_registered_events();
    home_logout();
}

function home_registered_events()
{
    $("#home_events_registered_menu").off().on('click', function(event)
    {
        event.preventDefault();
        
        $("#home_events_registered").modal("show");
        
        home_list_events();
    });
    
    $("#home_event_registered_close").on('click', function(event)
    {
        event.preventDefault();
        
        $("#home_events_registered").modal("hide");
    });
}

function home_list_events()
{
    if ( ! $.fn.DataTable.isDataTable('#home_event_list') )
    {
        $('#home_event_list').DataTable(
        {
            rowCallback: function(row, data)
            {
                var user = {
                    event_id: data[0],
                    name: data[1],
                    address: data[2],
                    price: data[3],
                    date: data[4],
                };
                $(row).find('#btn_display').on('click', function(event)
                {
                    event.preventDefault();
                    home_event_button(user['event_id']);
                });
                $(row).find('#btn_delete').on('click', function(event)
                {
                    event.preventDefault();
                    
                    //verificar data de registo para poder cancelar inscricao
                    $("#home_events_registered").modal("hide");
                    $("#home_association_delete").modal("show");
                    
                    $("#admin_users_delete_question").append("<h4>Are you sure you want to unsubscribe from the event "+user['name'].toString().toLocaleLowerCase() + "?</h4>");
                    
                    $("#user_delete_yes").off('click').on('click', function(event)
                    {
                        event.preventDefault();
                        let id = new URLSearchParams(window.location.search) + "";
                        id = id.replace(/^id=/, '');
                        
                        $.post("../models/event_delete_association.php", {event_id: user['event_id']}, function(data2)
                        {
                            if (data2['code'] === 0)
                            {
                                toastr['error'](data2['error']);
                                $("#home_association_delete").modal("hide");
                            }
                            else
                            {
                                toastr['success'](data2['error']);
                                $("#home_association_delete").modal("hide");
                            }
                        }, 'json');
                    });
                    
                    $("#user_delete_no").off('click').on('click', function()
                    {
                        $("#home_association_delete").modal("hide");
                        $("#home_events_registered").modal("show");
                    });
                });
            },
            pageLength: 50,
            autoWidth: false,
            responsive: true,
            processing: true,
            columnDefs: 
            [ 
                { targets: 5, orderable: false },
                { targets: 6, orderable: false },
            ],
            serverSide: true,
            ajax:
            {
                url :'../models/event_registered.php', // json datasource
                type: 'post'
            }    
        }, 'json');
    }
    else
    {
        $('#home_event_list').DataTable().ajax.reload();
    }
}

function home_event_button(id)
{
    window.location.replace("../views/event_display.html?id=" + id);
}

function home_profile()
{
    $('#home_profile').off().on('click', function(event)
    {
        event.preventDefault();
        clear_errors();
        $("#home_users_edit").modal("show");
        $.post("../models/home_user.php",  function(data)
        {
            // Fill - Form
            $("#user_edit_id").val(data[0]['user_id']);
            $("#user_edit_register_date").val(data[0]['user_register_date']);
            $("#user_edit_name").val(data[0]['user_name']);
            $("#user_edit_username").val(data[0]['user_username']);
            $("#user_edit_email").val(data[0]['user_email']);
            $("#user_edit_role").val(data[0]['user_role']);
            $("#user_edit_address").val(data[0]['user_address']);
            $("#user_edit_phone").val(data[0]['user_phone']);
            $("#user_edit_cc").val(data[0]['user_cc']);
            $("#user_edit_birth_date").val(data[0]['user_birth_date']);
        }, 'json');
    });
    $("#user_edit_close").off('click').on("click", function(event)
    {
        event.preventDefault();
        
        $("#home_users_edit").modal("hide");
    });
    $("#user_edit_save").off('click').on("click", function(event)
    {
        event.preventDefault();
        
        clear_errors();
        
        $.post("../models/admin_users_update.php", { form: $("#user_edit_form").serializeArray(), id: $("#user_edit_id").val() }, function(data)
        {
            for ( i = 0; i < data.length; i++ )
            {
                switch ( data[i]['code'] )
                {
                    case 0:
                        // Close modal
                        $("#home_users_edit").modal("hide");
                        toastr['success']('User successfully edited');
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

function home_logout()
{
    $("#home_logout").on("click", function()
    {
        $.post("../models/logout.php", function()
        {
            window.location.replace("../views/login.html");
        });
    });
}

function home_dashboard()
{
    $("#home_dashboard").on("click", function()
    {
        window.location.replace("../views/admin.html");
    });
}

function event_click(id)
{
    $("#event_"+id).on("click", function()
    {
        window.location.replace("../views/event_display.html?id=" + id);
    });
}

function event_get_data()
{
    let id = new URLSearchParams(window.location.search) + "";

    $.post("../models/page_event.php", {id: id}, function(data)
    {
        if (data['code'] === 0)
        {
//            redirect("../index.html");
            toastr['error'](data['error']);
            return;
        }
        else
        {
            switch (data["category"]["category_name"])
            {
                case "Party":
                    $("#page_title").append("<h1 class='mt-5 text_shadow'>Upcoming Parties</h1>");
                    break
                case "Lecture":
                    $("#page_title").append("<h1 class='mt-5 text_shadow'>Lectures & Talks</h1>");
                    break
                case "Fashion":
                    $("#page_title").append("<h1 class='mt-5 text_shadow'>Fashion Shows</h1>");
                    break
                case "Club":
                    $("#page_title").append("<h1 class='mt-5 text_shadow'>Club Nights</h1>");
                    break
            }

            for (var i=0 ; i<data["events"].length ; i++)
            {
                var id_event = data["events"][i]["event_id"];
                $("#event_load").append(' <a href="#" id="event_'+ id_event +'" class="event text-decoration-none text-dark d-inline-block shadow mb-4"> <div class="card" style="width: 25rem; height: 100%;"> <img src="../assets/img/events/event_'+id_event+'.'+data["events"][i]["event_ext_img"]+'" class="card-img-top shadow-sm" style="height:16rem !important;" alt="Event image"> <div class="card-body py-2 px-3"> <h5 class="card-title mb-1 hide_overflow_text">' + data["events"][i]["event_name"] + '</h5> <p class="card-text">'+ data["events"][i]["location_city"] + ", " + data["events"][i]["event_address"] +'</p> </div> </div> </a>');
                event_click(id_event);
            }

            for(var i=0 ; i<data["artist"].length ; i++)
            {
                $("#artists").append("<div class='me-3 badge text-bg-danger text-start py-3 mb-3 px-3'> <h5 class='mb-2'>" + data["artist"][i]["artist_name"] + "</h5> <p class='m-0 text-center'> " + data["artist"][i]["location_country"] + " | " + data["artist"][i]["category_name"] + "</p> </div>");
            }

            $("#tags").append('<p class="badge text-bg-danger py-2 px-3 rounded-4 me-3 mb-0">' + data["tags"]["event"]["category_name"] + '</p>');
        }
    }, 'json');
}