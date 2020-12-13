/**
 * Method to get the URL path
 *
 * return string
 * since  1.0
 */
function getURLPath() {
    var path = window.location.href.split('?')[0].replace(base_url, '');
    path     = $.trim(path);
        
    while ( path.substring(0, 1) == '/' ) {
        path = path.substring(1);
    }
        
    while ( path.substring(path.length - 1, path.length) == '/' ) {
        path = path.substring(0, path.length - 1);
    }
        
    return path;
}

/**
 * Method to set the query string parameter value
 *
 * return string
 * since  1.0
 */
function setQueryStr(p, v) {
	if ( !p ) return false;
	if ( !v ) return false;
	
	var pairs = [], hash;	
    var q     = '';   
    var qs    = window.location.href.split('?')[1];
    var set   = false;
    var isAny = false;
    
    if (qs != undefined) {
        q = qs;
    }
    
    qs = '';
    q  = q.split('&');     
   
    if ( q != undefined ) {
        for (var i = 0; i < q.length; i++) {
            hash = q[i].split('=');
            
            if ( hash[0] != undefined && hash[1] != undefined ) {
                pairs[hash[0]] = hash[0] + '=' + hash[1];
                
                if ( hash[0] == p ) {
                    pairs[p] = p + '=' + v;
                    set      = true;
                }   
                
                if ( hash[0] == 'page' ) {
                    pairs[hash[0]] = hash[0] + '=1';
                }
                
                qs = qs + pairs[hash[0]];
                
                if ( i < q.length - 1 ) {
                    qs = qs + '&';
                } 
                
                isAny = true;
            }                     
        }    	
    }
    
    if ( !set ) {
        if ( isAny ) {
    	    qs = qs + '&';
        }
    	
        qs = qs + p + '=' + v;
    }
        
    window.location.href = window.location.href.split('?')[0] + '?' + qs;
    
    return false;
}

/**
 * Method to get the query string parameter value
 *
 * return string
 * since  1.0
 */
function getUrlParam(name, url) {
    if (!url) url = window.location.href;
    name          = name.replace(/[\[\]]/g, '\\$&');
    var regex     = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'), results = regex.exec(url);
                
    if (!results) return null;
    if (!results[2]) return '';
                
    return decodeURIComponent(results[2].replace(/\+/g, ' '));
}

/**
 * Method to check whether all items in a list are checked
 *
 * return string
 * since  1.0
 */
function listIsChecked( checkbox ) {
    if (!checkbox.form) return false;
                
    checkbox.form.boxchecked.value = checkbox.checked ? parseInt(checkbox.form.boxchecked.value) + 1 : parseInt(checkbox.form.boxchecked.value) - 1;
                
    if ( !checkbox.form.elements[ 'checkall-toggle' ] ) return;
                
    var c = true, i, e, n;
                
    for ( i = 0, n = checkbox.form.elements.length; i < n; i++ ) {
        e = checkbox.form.elements[ i ];
                
        if ( e.type == 'checkbox' && e.name != 'checkall-toggle' && !e.checked ) {
            c = false;
            break;
        }
    }
                
    checkbox.form.elements[ 'checkall-toggle' ].checked = c;
}

/**
 * Method to check all items in a list
 *
 * return string
 * since  1.0
 */                
function listCheckAll( checkbox, stub ) {
    if (!checkbox.form) return false;
                
    stub = stub ? stub : 'cb';
                
    var c = 0, i, e, n;
                
    for ( i = 0, n = checkbox.form.elements.length; i < n; i++ ) {
        e = checkbox.form.elements[ i ];
                
        if ( e.type == checkbox.type && e.id.indexOf( stub ) === 0 ) {
            e.checked = checkbox.checked;
            c += e.checked ? 1 : 0;
        }
    }
                
    if ( checkbox.form.boxchecked ) {
        checkbox.form.boxchecked.value = c;
    }
                 
    return true;
}
                                      
/**
 * Method to check all items in a list
 *
 * return string
 * since  1.0
 */                
ajaxGet = function(fnc, data, success, error) {
    $.ajax({
        url: fnc,
        type: 'POST',
        dataType: 'json',
        data: data,
        async: true,
        success: function (resp) {
            if ( !!success ) {
                success(resp);
            }
        },
        error: function (req, status, err) {
            if ( !!error ) {
                error({
                    req: req,
                    status: status,
                    err: err
                });
            }
        }
    });
};

/**
 * Method to check all items in a list
 *
 * return string
 * since  1.0
 */                
ajaxGetSync = function (fnc, data, success, error) {
    $.ajax({
        url: fnc,
        type: 'POST',
        dataType: 'json',
        data: data,
        async: false,
        success: function (resp) {
            if ( !!success ) {
                success(resp);
            }
        },
        error: function (req, status, err) {
            if ( !!error ) {
                error({
                    req: req,
                    status: status,
                    err: err
                });
            }
        }
    });
};
 
/**
 * Method to check all items in a list
 *
 * return string
 * since  1.0
 */                
uiLock = function(content, elem) {
	if ( !content ) content = '';
    if ( !elem ) elem = 'body';

    if ( content == '' ) {
        content = '<img src=\"/img/input-spinner.gif\" id=\"input-spinner\" />';
    }
			
    $(elem).css({'position': 'relative'});
    $('<div></div>').attr('id', 'uiLockId').css({
		'position': 'absolute',
		'top': 0,
		'left': 0,
		'z-index': 999999,
		'opacity': 0.6,
		'width': '100%',
		'height': '100%',
		'color': 'black',
		'background-color': 'white'
		
	}).html(content).appendTo($(elem));
};

/**
 * Method to check all items in a list
 *
 * return string
 * since  1.0
 */                
uiUnlock = function(elem){
	$('#uiLockId').remove();
};
                
/**
 * Method to throw and display a message (success, notice, warning, error)
 *
 * return string
 * since  1.0
 */                
function message(type, text) {
    if (!type) type = 'notice';     
    if (!text) text = 'There is a message to be displayed.';
        
    var num = Math.floor(Math.random() * 100001);
    var n   = num.toString();
        
    var time_out;
                	
    $('body').append('<div id="message_' + n + '" class="message ' + type + '">' + text + '<button type="button" class="message-close">x</button></div>');
    $('div#message_' + n).animate({top: message_top, left: message_left, opacity: '1'}, 1);
                	
    time_out = setTimeout(function(){ $('div#message_' + n).remove(); }, 3000);
                	
    $('div#message_' + n).hover(	
        function() {
            clearTimeout(time_out);

        }, function() {
            time_out = setTimeout(function(){ $('div#message_' + n).remove(); }, 3000);
        }
    ); 
                	
    $('div#message_' + n + ' button.message-close').click(function() {
        $('div#message_' + n).remove();
        return false;
    });
}

/**
 * Method to get the access token
 *
 * return string
 * since  1.0
 */                
function getAccessToken() {
    if ( typeof $.cookie('access_token') != 'undefined' && $.cookie('access_token') != null && $.cookie('access_token').length > 0 ) {
	    return $.cookie('access_token');
	}
	    
	return '';
}

/**
 * Method to check whether the user is logged in or not
 *
 * return string
 * since  1.0
 */                
function isLoggedIn() {
    var access_token = getAccessToken();

    if ( access_token.length > 0 ) {
	    $.ajax(
            {
                url: token_url,
                method: 'POST',
                async: false,
                headers: { 'Authorization': 'Bearer ' + access_token }
            }
            
        ).done(function(server_data) {
	        var result;

	        if ( server_data !== null && typeof server_data === 'object' ) {
		        result = server_data;

		    } else {			
			    result = $.parseJSON( $.trim(server_data) );
		    }

            if ( result !== null && typeof result === 'object' && typeof result.status !== 'undefined' ) {
	            if ( result.status == 'success' ) {
		            is_logged_in = true;
                }
            }
        });
	}
	    
	return is_logged_in;
}

isLoggedIn();
    
$('document').ready(function() {
	/**
     * Method to handle the profile image uploading
     *
     * since 1.0
     */                
    $('button#upload-profile-image').click( function(e) {
        e.preventDefault();

        var fd    = new FormData();
        var files = $('#profile-image')[0].files;
        
        if ( is_logged_in && files.length > 0 ) {
            fd.append('image', files[0]);

            $.ajax(
                {
                    url: prf_upload_url,
                    method: 'POST',
                    headers: { 'Authorization': 'Bearer ' + getAccessToken() },
                    data: fd,
                    contentType: false,
                    processData: false
                }
            
            ).done(function(server_data) {
	            var result;

	            if ( server_data !== null && typeof server_data === 'object' ) {
		            result = server_data;

	    	    } else {			
	     		    result = $.parseJSON( $.trim(server_data) );
	     	    }

                if ( result !== null && typeof result === 'object' && typeof result.status !== 'undefined' ) {
	                if ( result.status == 'success' ) {
		                message('success', "Uploading the profile image was successful.");
                        $('.profile-img img').attr('src', result.data.src);
                        $('.profile-img').css({'display': 'block'});
                        $('.profile-img.avatar').css({'display': 'inline-block'});
                        
                    } else {
	                    message('error', "Uploading the profile image failed! " + result.msg);
	        	        console.log("Uploading the profile image failed! " + result.msg);
                    }

                } else {
                    message('error', "Uploading the profile image failed! " + JSON.stringify(result));
	        	    console.log("Uploading the profile image failed! " + JSON.stringify(result));
                }
             
            }).fail(function(xhr) {
                message('error', "An error occured: " + xhr.status + " " + xhr.statusText);
	            console.log("An error occured: " + xhr.status + " " + xhr.statusText);
             
            });
        }
    });

	/**
     * Method to handle the coupon submission
     *
     * since 1.0
     */                
    $('button#check-coupon').click( function(e) {
        e.preventDefault();
        
        if ( is_logged_in && $('input#coupon').val().length > 0 ) {
            $.ajax(
                {
                    url: coupon_url,
                    method: 'POST',
                    headers: { 'Authorization': 'Bearer ' + getAccessToken() },
                    data: {
                        coupon: $('input#coupon').val()
                    }
                }
            
            ).done(function(server_data) {
	            var result;

	            if ( server_data !== null && typeof server_data === 'object' ) {
		            result = server_data;

	    	    } else {			
	     		    result = $.parseJSON( $.trim(server_data) );
	     	    }

                if ( result !== null && typeof result === 'object' && typeof result.status !== 'undefined' ) {
	                if ( result.status == 'success' ) {
		                message('success', "Activating the promotion code was successful.");

                        $('form#profile-form .paid-subscriber').css({'display': 'none'});
                        $('form#profile-form .free-subscriber .coupon_added').text(result.data.coupon_added);
                        $('form#profile-form .free-subscriber').css({'display': 'block'});
                        
                    } else {
	                    message('error', "Activating the promotion code failed! " + result.msg);
	        	        console.log("Activating the promotion code failed! " + result.msg);
                    }

                } else {
                    message('error', "Activating the promotion code failed! " + JSON.stringify(result));
	        	    console.log("Activating the promotion code failed! " + JSON.stringify(result));
                }
             
            }).fail(function(xhr) {
                message('error', "An error occured: " + xhr.status + " " + xhr.statusText);
	            console.log("An error occured: " + xhr.status + " " + xhr.statusText);
             
            });
        }
    });
	
	/**
     * Method to handle the profile form submission
     *
     * since 1.0
     */                
    $('form#profile-form button[type=submit]').click( function(e) {
        e.preventDefault();
        
        if ( is_logged_in ) {
            $.ajax(
                {
                    url: user_url,
                    method: 'PUT',
                    headers: { 'Authorization': 'Bearer ' + getAccessToken() },
                    data: {
                        form: $('form#profile-form').serialize()
                    }
                }
            
            ).done(function(server_data) {
	            var result;

	            if ( server_data !== null && typeof server_data === 'object' ) {
		            result = server_data;

	    	    } else {			
	     		    result = $.parseJSON( $.trim(server_data) );
	     	    }

                if ( result !== null && typeof result === 'object' && typeof result.status !== 'undefined' ) {
	                if ( result.status == 'success' && typeof result.data.id !== 'undefined' && result.data.id > 0 ) {
		                message('success', "Saving profile was successful " + result.data.name + "!");
                        
                    } else {
	                    message('error', "Saving profile failed! " + result.msg);
	        	        console.log("Saving profile failed! " + result.msg);
                    }

                } else {
                    message('error', "Saving profile failed! " + JSON.stringify(result));
	        	    console.log("Saving profile failed! " + JSON.stringify(result));
                }
             
            }).fail(function(xhr) {
                message('error', "An error occured: " + xhr.status + " " + xhr.statusText);
	            console.log("An error occured: " + xhr.status + " " + xhr.statusText);
             
            });
        }
    });

	/**
     * Method to handle the register form submission
     *
     * since 1.0
     */                
    $('form#register-form button[type=submit]').click( function(e) {
        e.preventDefault();
        
        $.ajax(
            {
                url: user_url,
                method: 'POST',
                data: {
                    form: $('form#register-form').serialize()
                }
            }
            
        ).done(function(server_data) {
	        var result;

	        if ( server_data !== null && typeof server_data === 'object' ) {
		        result = server_data;

	    	} else {
	     		result = $.parseJSON( $.trim(server_data) );
	     	}

            if ( result !== null && typeof result === 'object' && typeof result.status !== 'undefined' ) {
	            if ( result.status == 'success' && typeof result.data.id !== 'undefined' && result.data.id > 0 ) {
		            message('success', "Register was successful " + result.data.name + "!");

		            time_out = setTimeout(function(){ window.location.replace(base_url + '/login'); }, 4000);
                        
                } else {
	                message('error', "Register failed! " + result.msg);
	    	        console.log("Register failed! " + result.msg);
                }

            } else {
                message('error', "Register failed! " + JSON.stringify(result));
	    	    console.log("Register failed! " + JSON.stringify(result));
            }
             
        }).fail(function(xhr) {
            message('error', "An error occured: " + xhr.status + " " + xhr.statusText);
	        console.log("An error occured: " + xhr.status + " " + xhr.statusText);
        });
    });

    /**
     * Method to handle signing the user out
     *
     * since 1.0
     */                
    $('.user-access a.user-sign-out').click( function(e) {
	    e.preventDefault();
    	$.cookie('access_token', '', {path: '/'});

        is_logged_in = false;

        window.location.replace(base_url);
    });
    
    /**
     * Method to handle the login form submission
     *
     * since 1.0
     */                
    $('form#login-form button[type=submit]').click( function(e) {
        e.preventDefault();
        
        $.ajax(
            {
                url: auth_url,
                method: 'POST',
                data: {
                    client_id: client_id,
                    client_secret: client_secret,
                    grant_type: 'password',
                    username: $('form#login-form input[name=email]').val(),
                    password: $('form#login-form input[name=password]').val()
                }
            }
            
        ).done(function(server_data) {
	        var result;

	        if ( server_data !== null && typeof server_data === 'object' ) {
		        result = server_data;

	    	} else {			
	     		result = $.parseJSON( $.trim(server_data) );
	     	}

            if ( result !== null && typeof result === 'object' && typeof result.status !== 'undefined' && result.status == 'success' && typeof result.data.access_token !== 'undefined' ) {
                $.cookie('access_token', result.data.access_token, {path: '/'});
                        
                if ( typeof result.data.refresh_token !== 'undefined' ) {
                    $.cookie('refresh_token', result.data.refresh_token, {path: '/'});
                }

                $.ajax(
                    {
                        url: user_url,
                        method: 'GET',
                        headers: { 
                         	'Authorization': 'Bearer ' + getAccessToken()
                        }
                    }
            
                ).done(function(server_data) {
	                var result;

	                if ( server_data !== null && typeof server_data === 'object' ) {
		                result = server_data;

	    	        } else {
	     	         	result = $.parseJSON( $.trim(server_data) );
	             	}

                    if ( result !== null && typeof result === 'object' && typeof result.status !== 'undefined' ) {
	                    if ( result.status == 'success' && typeof result.data.id !== 'undefined' && result.data.id > 0 ) {
		                    $.cookie('user_id', result.data.id, {path: '/'});
                            message('success', "Login was successful!");

		                    time_out = setTimeout(function(){ window.location.replace(base_url); }, 3000);

                        } else {
	                        message('error', "Getting profile data failed! " + result.msg);
	    	                console.log("Getting profile data failed! " + result.msg);
                        }

                    } else {
                        message('error', "Getting profile data failed! " + JSON.stringify(result));
	    	            console.log("Getting profile data failed! " + JSON.stringify(result));
                    }
             
                }).fail(function(xhr) {
                    message('error', "An error occured: " + xhr.status + " " + xhr.statusText);
	                console.log("An error occured: " + xhr.status + " " + xhr.statusText);
                });
                        
            } else {
                message('error', "Login failed! " + JSON.stringify(result));
	    	    console.log("Login failed! " + JSON.stringify(result));
            }
             
        }).fail(function(xhr) {
            message('error', "An error occured: " + xhr.status + " " + xhr.statusText);
	        console.log("An error occured: " + xhr.status + " " + xhr.statusText);
        });
    });
        
    /**
     * Handling the visibility of the different partials of the page
     *
     * since 1.0
     */                
    var path       = getURLPath();
    var css_path   = path;
    var css_path_x = path.replace('/', '-');
        
    while ( css_path != css_path_x ) {
        css_path   = css_path_x;
        css_path_x = css_path.replace('/', '-');
    }

	if ( is_logged_in ) {
		if ( $.inArray(path, ['register', 'login']) !== -1 ) {
            window.location.replace('/profile');
        }

	    $('.visitor-access').css({'display': 'none'});

        $('.user-access').each(function() {
	        if ( typeof $(this).attr('display') != 'undefined' ) {
		        $(this).css({'display': $(this).attr('display')});

	        } else {
		        $(this).css({'display': 'inherit'});
        	}
        });
	    
	} else {
		if ( $.inArray(path, ['profile']) !== -1 ) {
            window.location.replace('/');
        }

	    $('.user-access').css({'display': 'none'});

        $('.visitor-access').each(function() {
	        if ( typeof $(this).attr('display') != 'undefined' ) {
		        $(this).css({'display': $(this).attr('display')});

	        } else {
		        $(this).css({'display': 'inherit'});
        	}
        });
	}
    
    $('.c-section').css({'display': 'none'});
        
    if ( css_path != '' && $('.c-section.' + css_path).length > 0 ) {
        $('.c-section.' + css_path).css({'display': 'block'});
            
    } else if ( path != '' ) {
	    $('.c-section.not-found').css({'display': 'block'});
	    message('warning', "Page not found! You will be redirected in 5 seconds.");

		time_out = setTimeout(function(){ window.location.replace(base_url); }, 5000);

    } else if ( is_logged_in ) {
        $('.c-section.cities').css({'display': 'block'});
        
    } else {
        $('.c-section.login').css({'display': 'block'});
    }

    if ( is_logged_in ) {
	   /**
        * Method to get the data for the profile page and etc.
        *
        * since 1.0
        */
	    $.ajax(
            {
                url: user_url,
                method: 'GET',
                headers: { 
                   	'Authorization': 'Bearer ' + getAccessToken()
                }
            }
            
        ).done(function(server_data) {
	        var result;

	        if ( server_data !== null && typeof server_data === 'object' ) {
		        result = server_data;

	        } else {			
	         	result = $.parseJSON( $.trim(server_data) );
	        }

            if ( result !== null && typeof result === 'object' && typeof result.status !== 'undefined' ) {
	            if ( result.status == 'success' && typeof result.data.id !== 'undefined' && result.data.id > 0 ) {
		            $('.c-site-nav__user__name').text(result.data.name);
		
		            if ( path == 'profile' ) {
			            $('span#user-id').text(result.data.id);

                        if ( result.data.image_src != '' ) {
                             $('.profile-img img').attr('src', result.data.image_src);
                             $('.profile-img').css({'display': 'block'});
                             $('.profile-img.avatar').css({'display': 'inline-block'});
	                    }

                        if ( result.data.coupon_id > 0 ) {
	                        $('form#profile-form .paid-subscriber').css({'display': 'none'});
                            $('form#profile-form .free-subscriber .coupon_added').text(result.data.coupon_added);
                            $('form#profile-form .free-subscriber').css({'display': 'block'});
	
                        } else {
	                        $('form#profile-form .paid-subscriber').css({'display': 'block'});
                            $('form#profile-form .free-subscriber').css({'display': 'none'});
                        }

                        $('form#profile-form input, form#profile-form select').each(
                            function(index){ 
	                            if ( typeof result.data[$(this).attr('name')] !== 'undefined' ) {
		                            var field_type;

                                    if ( typeof $(this).attr('type') === 'undefined' ) {
	                                    field_type = this.type.toLowerCase();

                                    } else {
	                                    field_type = $(this).attr('type');
                                    }
		
		                            switch ( field_type )
                                    {		
	                                    case 'text': 
	                                    case 'tel':
                                        case 'email':
	                                    case 'textarea':
	                                    case 'hidden':
	                                        $(this).val(result.data[$(this).attr('name')]); 
		                                    break;
	        
		                                case 'radio':
		                                case 'checkbox':
	                                        if ( $(this).val() == result.data[$(this).attr('name')] ) {
	   	                                        $(this).prop('checked', true);

		                                    } else {
			                                    $(this).prop('checked', false);
	                                    	}
	  			
			                                break;

		                                case 'select-one':
		                                case 'select-multi':
                                        case 'select-multiple':
                                            $(this).prop('selectedIndex', -1);
                                            $(this).children().removeProp('selected');
                                            $(this).val(result.data[$(this).attr('name')]);
                                            $(this).select2().trigger('change');                                                    
			                                break;

		                                 default: 
		                                     break;
		                             }
	                             } 
                             }
                         );	    
                     }
                 	
                 } else {
	                 message('error', "Getting profile data failed! " + result.msg);
	    	         console.log("Getting profile data failed! " + result.msg);
                 }

             } else {
                 message('error', "Getting profile data failed! " + JSON.stringify(result));
	    	     console.log("Getting profile data failed! " + JSON.stringify(result));
             }
             
         }).fail(function(xhr) {
             message('error', "An error occured: " + xhr.status + " " + xhr.statusText);
	         console.log("An error occured: " + xhr.status + " " + xhr.statusText);
         });

         if ( path == '' ) {
	       /**
            * Method to get the data for the home page
            *
            * since 1.0
            */
		    $.ajax(
                {
                    url: cities_url,
                    method: 'GET',
                    headers: { 'Authorization': 'Bearer ' + getAccessToken() }
                }
            
            ).done(function(server_data) {
	            var result;

	            if ( server_data !== null && typeof server_data === 'object' ) {
		            result = server_data;

	        	} else {			
	         		result = $.parseJSON( $.trim(server_data) );
	          	}

                if ( result !== null && typeof result === 'object' && typeof result.status !== 'undefined' ) {
	                if ( result.status == 'success' && result.data.length > 0 ) {
		                $('.c-section.cities .c-content .o-layout').empty();

                        $.each(result.data, function(index, item) {
	                        $('#city-pattern .o-layout__item').attr('id', 'city_' + item.id);
	                        $('#city-pattern .o-layout__item .c-set__title .c-temprature .val').html(item.temprature);
                            $('#city-pattern .o-layout__item .c-set__title .c-name').text(item.name);
                            $('#city-pattern .o-layout__item .c-set__detail .c-weather').text(item.weather);
                            $('#city-pattern .o-layout__item img.c-set__image').attr('src', '/img/cities/' + item.id + '.jpg');
                            $('#city-pattern .o-layout__item img.c-set__image').attr('alt', item.name);
                            $('#city-pattern .o-layout__item input.c-check__input').val(item.id);
                            $('#city-pattern .o-layout__item input.c-check__input').removeAttr('checked');

                            if ( item.selected === true ) {
	                            $('#city-pattern .o-layout__item input.c-check__input').attr('checked', true);
                            }

	                        $('.c-section.cities .c-content .o-layout').append( $('#city-pattern').html() );
                        });

                        $('.c-section.cities .o-layout__item .c-check').on('click', function() {
	                        var input = $(this).find('input.c-check__input');

	                        if ( input.prop("checked") == true ) {
		                        input.prop('checked', false);
		
	                        } else {
		                        input.prop('checked', true);
	                        }

                            $.ajax(
                                {
                                    url: user_url,
                                    type: 'PUT',
                                    data: {
                                        form: $('form#cities-form').serialize()
                                    }
                                }
            
                            ).done(function(server_data) {
	                            var result;

	                            if ( server_data !== null && typeof server_data === 'object' ) {
	                    	        result = server_data;

	                          	} else {
	                          		result = $.parseJSON( $.trim(server_data) );
	                        	}

                                if ( result !== null && typeof result === 'object' && typeof result.status !== 'undefined' ) {
	                                if ( result.status == 'success' ) {
		                                message('success', "The cities updated successfully.");
                        
                                    } else {
	                                    message('error', "Updating the list of selected cities failed! " + result.msg);
	    	                            console.log("Updating the list of selected cities failed! " + result.msg);
                                    }

                                } else {
                                    message('error', "Updating the list of selected cities failed! " + JSON.stringify(result));
	                           	    console.log("Updating the list of selected cities failed! " + JSON.stringify(result));
                                }
             
                            }).fail(function(xhr) {
                                message('error', "An error occured: " + xhr.status + " " + xhr.statusText);
	                            console.log("An error occured: " + xhr.status + " " + xhr.statusText);
                            });
     	                });                         
                                       	
                    } else {
	                    message('error', "Getting cities data failed! " + result.msg);
	    	            console.log("Getting cities data failed! " + result.msg);
                    }

                } else {
                    message('error', "Getting cities data failed! " + JSON.stringify(result));
	    	        console.log("Getting cities data failed! " + JSON.stringify(result));
                }
             
            }).fail(function(xhr) {
                message('error', "An error occured: " + xhr.status + " " + xhr.statusText);
	            console.log("An error occured: " + xhr.status + " " + xhr.statusText);
            });
   	    }
    }
});
