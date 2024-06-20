$( document ).ready(function()
{
    init();
});

function init()
{
    $.post( "./models/check_session.php", function( data )
    {
        switch (data['code'])
        {
            case 0:
                redirect("./views/login.html");
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
                
                home_events();
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
                        
                        $.post("./models/event_delete_association.php", {event_id: user['event_id']}, function(data2)
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
                url :'./models/event_registered.php', // json datasource
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
    window.location.replace("./views/event_display.html?id=" + id);
}

function home_profile()
{
    $('#home_profile').off().on('click', function(event)
    {
        event.preventDefault();
        clear_errors();
        $("#home_users_edit").modal("show");
        $.post("./models/home_user.php",  function(data)
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
        
        $.post("./models/admin_users_update.php", { form: $("#user_edit_form").serializeArray(), id: $("#user_edit_id").val() }, function(data)
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
        $.post("./models/logout.php", function()
        {
            window.location.replace("index.html");
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

function home_event_click(id)
{
    $("#event_"+id).on("click", function()
    {
        window.location.replace("./views/event_display.html?id=" + id);
    });
}

function home_location_click(id)
{
    $("#location_"+id).on("click", function()
    {
        window.location.replace("./views/location_display.html?id=" + id);
    });
}

function home_category_click(id)
{
    $("#category_"+id).on("click", function()
    {
        window.location.replace("./views/page_event.html?id=" + id);
    });
}

function home_location_title()
{
    $("#home_location_title").on("click", function()
    {
        window.location.replace("./views/page_location.html");
    });
}

function home_events()
{
    $.post("./models/home_events.php", function(data)
    {
        var flag = false;
        
        for (var i = 0 ; i < 3 ; i++)
        {
            var rand = Math.floor(Math.random() * 4);
            
            switch (rand)
            {
                case 0:
                    if (data["event"]["Party"].length > 0)
                    {
                        let event_date= data["event"]["Party"][i]["event_date"].split(" ")[0];
                        let eventArr = event_date.split('-');
                        let event_date_formatted = eventArr[2] + "/" + eventArr[1] + "/" + eventArr[0];
                        let id_party = data["event"]["Party"][i]["event_id"];

                        if (!flag)
                        {
                            $("#home_carousel").append('<a href="#" id="event_'+ id_party +'"><div class="carousel-item active darken-image vh-100"> <img src="./assets/img/events/event_'+id_party+'.'+data["event"]["Party"][i]["event_ext_img"]+'" class="d-block w-100" alt="..."> <div class="carousel-caption d-none d-md-block  h-50"> <h1>'+ data["event"]["Party"][i]["event_name"] +'</h1> <h4>'+ event_date_formatted +'</h4></div> </div></a>');
                            flag = true;
                            break;
                        }
                        else
                        {
                            $("#home_carousel").append('<a href="#" id="event_'+ id_party +'"><div class="carousel-item darken-image vh-100"> <img src="./assets/img/events/event_'+id_party+'.'+data["event"]["Party"][i]["event_ext_img"]+'" class="d-block w-100" alt="..."> <div class="carousel-caption d-none d-md-block  h-50"> <h1>'+ data["event"]["Party"][i]["event_name"] +'</h1> <h4>'+ event_date_formatted +'</h4></div> </div></a>');
                            break;
                        }
                    }
                case 1:
                    if (data["event"]["Lecture"].length > 0) 
                    {
                        let event_date_1 = data["event"]["Lecture"][i]["event_date"].split(" ")[0];
                        let eventArr_1 = event_date_1.split('-');
                        let event_date_formatted_1 = eventArr_1[2] + "/" + eventArr_1[1] + "/" + eventArr_1[0];
                        let id_lecture = data["event"]["Lecture"][i]["event_id"];

                        if (!flag)
                        {
                            $("#home_carousel").append('<a href="#" id="event_'+ id_lecture +'"><div class="carousel-item active darken-image vh-100"> <img src="./assets/img/events/event_'+id_lecture+'.'+data["event"]["Lecture"][i]["event_ext_img"]+'" class="d-block w-100" alt="..."> <div class="carousel-caption d-none d-md-block  h-50"> <h1>'+ data["event"]["Lecture"][i]["event_name"] +'</h1> <h4>'+ event_date_formatted_1 +'</h4></div> </div></a>');
                            flag = true;
                            break;
                        }
                        else
                        {
                            $("#home_carousel").append('<a href="#" id="event_'+ id_lecture +'"><div class="carousel-item darken-image vh-100"> <img src="./assets/img/events/event_'+id_lecture+'.'+data["event"]["Lecture"][i]["event_ext_img"]+'" class="d-block w-100" alt="..."> <div class="carousel-caption d-none d-md-block  h-50"> <h1>'+ data["event"]["Lecture"][i]["event_name"] +'</h1> <h4>'+ event_date_formatted_1 +'</h4></div> </div></a>');
                            break;
                        }
                    }
                case 2:
                    if (data["event"]["Club"].length > 0)
                    {
                        let event_date_club= data["event"]["Club"][i]["event_date"].split(" ")[0];
                        let eventArr_club = event_date_club.split('-');
                        let event_date_formatted_club = eventArr_club[2] + "/" + eventArr_club[1] + "/" + eventArr_club[0];
                        let id_club = data["event"]["Club"][i]["event_id"];

                        if (!flag)
                        {
                            $("#home_carousel").append('<a href="#"  id="event_'+ id_club +'"><div class="carousel-item active darken-image vh-100"> <img src="./assets/img/events/event_'+id_club+'.'+data["event"]["Club"][i]["event_ext_img"]+'" class="d-block w-100" alt="..."> <div class="carousel-caption d-none d-md-block  h-50"> <h1>'+ data["event"]["Club"][i]["event_name"] +'</h1> <h4>'+ event_date_formatted_club +'</h4></div> </div></a>');
                            flag = true;
                            break;
                        }
                        else
                        {
                            $("#home_carousel").append('<a href="#"  id="event_'+ id_club +'"><div class="carousel-item darken-image vh-100"> <img src="./assets/img/events/event_'+id_club+'.'+data["event"]["Club"][i]["event_ext_img"]+'" class="d-block w-100" alt="..."> <div class="carousel-caption d-none d-md-block  h-50"> <h1>'+ data["event"]["Club"][i]["event_name"] +'</h1> <h4>'+ event_date_formatted_club +'</h4></div> </div></a>');
                            break;
                        }
                    }
                case 3:
                    if (data["event"]["Fashion"] > 0)
                    {
                        let event_date_fashion = data["event"]["Fashion"][i]["event_date"].split(" ")[0];
                        let eventArr_fashion = event_date_fashion.split('-');
                        let event_date_formatted_fashion = eventArr_fashion[2] + "/" + eventArr_fashion[1] + "/" + eventArr_fashion[0];
                        let id_fashion = data["event"]["Fashion"][i]["event_id"];

                        if (!flag)
                        {
                            $("#home_carousel").append('<a href="#" id="event_'+ id_fashion +'"><div class="carousel-item active darken-image vh-100"> <img src="./assets/img/events/event_'+id_fashion+'.'+data["event"]["Fashion"][i]["event_ext_img"]+'" class="d-block w-100" alt="..."> <div class="carousel-caption d-none d-md-block  h-50"> <h1>'+ data["event"]["Fashion"][i]["event_name"] +'</h1> <h4>'+ event_date_formatted_fashion +'</h4></div> </div></a>');
                            flag = true;
                            break;
                        }
                        else
                        {
                            $("#home_carousel").append('<a href="#" id="event_'+ id_fashion +'"><div class="carousel-item darken-image vh-100"> <img src="./assets/img/events/event_'+id_fashion+'.'+data["event"]["Fashion"][i]["event_ext_img"]+'" class="d-block w-100" alt="..."> <div class="carousel-caption d-none d-md-block  h-50"> <h1>'+ data["event"]["Fashion"][i]["event_name"] +'</h1> <h4>'+ event_date_formatted_fashion +'</h4></div> </div></a>');
                            break;
                        }
                    }
            }
        }
         
        $('#carouselExampleCaptions').carousel({
            interval: 10000, 
            ride: 'carousel'
        });
        
        if (data["location"].length > 0)
        {
            $("#places_to_go_title").append('<a href="#" class="text-decoration-none text-dark" id="home_location_title"><div class="d-flex align-items-center justify-content-between mb-3"><h3 class="text-white"><span class="bg-danger rounded-2 px-4 py-1">Places to go</span></h3><i class="fa-solid fa-chevron-right bg-danger p-2 text-white rounded-2"></i></div></a>');
            home_location_title();
            
            for( let i=0 ; i < data["location"].length ; i++)
            {
                let id_location = data["location"][i]["location_id"];
                $("#home_location").append('<a href="#" id="location_'+ id_location +'" class="text-decoration-none my-lg-0 mb-3 text-dark d-inline-block shadow"> <div class="card" style="width: 15rem; height: 100%;"> <img src="./assets/img/neopop.jpg" class="card-img-top shadow-sm" alt="Event image"> <div class="card-body py-2 px-3"> <h5 class="card-title hide_overflow_text"> ' + data["location"][i]["location_city"] + ', ' + data["location"][i]["location_country"] + ' </h5> </div> </div> </a>');
                home_location_click(id_location);
            }
        }
        if (data["event"]["Party"].length > 0)
        {
            $("#home_party_title").append('<a href="#" class="text-decoration-none text-dark" id="category_'+ data['category']['Party'][0]['category_id'] +'"><div class="d-flex align-items-center justify-content-between mb-3"><h3 class="text-white"><span class="bg-danger rounded-2 px-4 py-1">Upcoming Parties</span></h3><i class="fa-solid fa-chevron-right bg-danger p-2 text-white rounded-2"></i></div></a>');
            home_category_click(data['category']['Party'][0]['category_id']);
            
            for (let i = 0 ; i < data["event"]["Party"].length ; i++)
            {
                let id_party = data["event"]["Party"][i]["event_id"];
                $("#home_party").append(' <a href="#" id="event_'+ id_party +'" class="event my-lg-0 mb-3 text-decoration-none text-dark d-inline-block shadow"> <div class="card" style="width: 18rem; height: 100%;"> <img src="./assets/img/events/event_'+id_party+'.'+data["event"]["Party"][i]["event_ext_img"]+'" style="height:12rem !important;" class="card-img-top shadow-sm" alt="Event image"> <div class="card-body py-2 px-3"> <h5 class="card-title mb-1 hide_overflow_text">' + data["event"]["Party"][i]["event_name"] + '</h5> <p class="card-text">'+ data["event"]["Party"][i]["location_city"] + ", " + data["event"]["Party"][i]["event_address"] +'</p> </div> </div> </a>');
                home_event_click(id_party);
            }
        }
        if (data["event"]["Lecture"].length > 0)
        {
            $("#home_lecture_title").append('<a href="#" class="text-decoration-none text-dark" id="category_'+ data['category']['Lecture'][0]['category_id'] +'"><div class="d-flex align-items-center justify-content-between mb-3"><h3 class="text-white"><span class="bg-danger rounded-2 px-4 py-1">Lectures & Talks</span></h3><i class="fa-solid fa-chevron-right bg-danger p-2 text-white rounded-2"></i></div></a>');
            home_category_click(data['category']['Lecture'][0]['category_id']);
            
            for (let i = 0 ; i < data["event"]["Lecture"].length ; i++)
            {
                let id_lecture = data["event"]["Lecture"][i]["event_id"];
                $("#home_lecture").append(' <a href="#" id="event_'+ id_lecture +'" class="event my-lg-0 mb-3 text-decoration-none text-dark d-inline-block shadow"> <div class="card" style="width: 18rem; height: 100%;"> <img src="./assets/img/events/event_'+id_lecture+'.'+data["event"]["Lecture"][i]["event_ext_img"]+'" style="height:12rem !important;" class="card-img-top shadow-sm" alt="Event image"> <div class="card-body py-2 px-3"> <h5 class="card-title mb-1 hide_overflow_text">' + data["event"]["Lecture"][i]["event_name"] + '</h5> <p class="card-text">'+ data["event"]["Lecture"][i]["location_city"] + ", " + data["event"]["Lecture"][i]["event_address"] +'</p> </div> </div> </a>');
                home_event_click(id_lecture);
            }
        }
        if (data["event"]["Fashion"].length > 0)
        {
            $("#home_fashion_title").append('<a href="#" class="text-decoration-none text-dark" id="category_'+ data['category']['Fashion'][0]['category_id'] +'"><div class="d-flex align-items-center justify-content-between mb-3"><h3 class="text-white"><span class="bg-danger rounded-2 px-4 py-1">Fashion Shows</span></h3><i class="fa-solid fa-chevron-right bg-danger p-2 text-white rounded-2"></i></div></a>');
            home_category_click(data['category']['Fashion'][0]['category_id']);
            
            for (let i = 0 ; i < data["event"]["Fashion"].length ; i++)
            {
                let id_fashion = data["event"]["Fashion"][i]["event_id"];
                $("#home_fashion").append(' <a href="#" id="event_'+ id_fashion +'" class="event my-lg-0 mb-3 text-decoration-none text-dark d-inline-block shadow"> <div class="card" style="width: 18rem; height: 100%;"> <img src="./assets/img/events/event_'+id_fashion+'.'+data["event"]["Fashion"][i]["event_ext_img"]+'" style="height:12rem !important;" class="card-img-top shadow-sm" alt="Event image"> <div class="card-body py-2 px-3"> <h5 class="card-title mb-1 hide_overflow_text">' + data["event"]["Fashion"][i]["event_name"] + '</h5> <p class="card-text">'+ data["event"]["Fashion"][i]["location_city"] + ", " + data["event"]["Fashion"][i]["event_address"] +'</p> </div> </div> </a>');
                home_event_click(id_fashion);
            }
        }
        if (data["event"]["Club"].length)
        {
            $("#home_club_title").append('<a href="#" class="text-decoration-none text-dark" id="category_'+ data['category']['Club'][0]['category_id'] +'"><div class="d-flex align-items-center justify-content-between mb-3"><h3 class="text-white"><span class="bg-danger rounded-2 px-4 py-1">Club Nights</span></h3><i class="fa-solid fa-chevron-right bg-danger p-2 text-white rounded-2"></i></div></a>');
            home_category_click(data['category']['Club'][0]['category_id']);
            
            for (let i = 0 ; i < data["event"]["Club"].length ; i++)
            {
                let id_club = data["event"]["Club"][i]["event_id"];
                $("#home_club").append(' <a href="#" id="event_'+ id_club +'" class="event my-lg-0 mb-3 text-decoration-none text-dark d-inline-block shadow"> <div class="card" style="width: 18rem; height: 100%;"> <img src="./assets/img/events/event_'+id_club+'.'+data["event"]["Club"][i]["event_ext_img"]+'" style="height:12rem !important;" class="card-img-top shadow-sm" alt="Event image"> <div class="card-body py-2 px-3"> <h5 class="card-title mb-1 hide_overflow_text">' + data["event"]["Club"][i]["event_name"] + '</h5> <p class="card-text">'+ data["event"]["Club"][i]["location_city"] + ", " + data["event"]["Club"][i]["event_address"] +'</p> </div> </div> </a>');
                home_event_click(id_club);
            }
        }
        
        
        
        
        
        
        
    }, 'json');
}