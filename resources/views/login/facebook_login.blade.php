<!DOCTYPE html>
<html>
<head>
	<title>Facebook Login</title>
	<!-- HTTPS required. HTTP will give a 403 forbidden response -->
	<script src="https://sdk.accountkit.com/en_US/sdk.js"></script>
	<link rel="stylesheet" type="text/css" href="{{asset('/css/app.css')}}">

</head>
<body class="container mt-5">

	

    <h3>Facebook Account Kit</h3>

	<input value="+1" id="country_code" type="hidden"/>
	<input placeholder="phone number" id="phone_number" type="hidden"/>
	<button onclick="smsLogin();" class="btn btn-success">Login via SMS</button>
	
	<input placeholder="email" id="email" type="hidden" />
	<button onclick="emailLogin();" class="btn btn-success">Login via Email</button>

	<form action="/account_kit_login" method="post" id="form">
        <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}">
        <input type="text" class="form-control mt-3" name="code" id="code" />
        <!-- <button type="submit">Sumit</button> -->
    </form>

	<br>
	<h3 class="mt-3">Facebook Login</h3>
	<fb:login-button 
		 scope="public_profile,email"
		 onlogin="checkLoginState();">
	</fb:login-button>

	<script>
	  	// initialize Account Kit with CSRF protection
	  	AccountKit_OnInteractive = function(){
	  		AccountKit.init(
		  		{
		  			appId:"609465006186211", 
		  			state:"{{ csrf_token() }}", 
		  			version:"v1.1",
		  			fbAppEventsEnabled:true,
		  			debug:true
		  		}
	  		);
	  	};

		// login callback
		function loginCallback(response) {
			if (response.status === "PARTIALLY_AUTHENTICATED") {
				var code = response.code;
				var csrf = response.state;
		    	// Send code to server to exchange for access token
		    	document.getElementById('code').value = response.code;
			    document.getElementById('_token').value = response.state;
			    // document.getElementById('form').submit();

		    	console.log(response)
		    }
		    else if (response.status === "NOT_AUTHENTICATED") {
		    	// handle authentication failure
		    }
		    else if (response.status === "BAD_PARAMS") {
		      	// handle bad parameters
		    }
		}

		// phone form submission handler
		function smsLogin() {
			var countryCode = document.getElementById("country_code").value;
			var phoneNumber = document.getElementById("phone_number").value;
			AccountKit.login(
				'PHONE', 
		      	{countryCode: countryCode, phoneNumber: phoneNumber}, // will use default values if not specified
		      	loginCallback
		    );
		}


	  	// email form submission handler
	  	function emailLogin() {
	  		var emailAddress = document.getElementById("email").value;
	  		AccountKit.login(
	  			'EMAIL',
	  			{emailAddress: emailAddress},
	  			loginCallback
	  			);
	  	}
	</script>


	<script>
	  	window.fbAsyncInit = function() {
		    FB.init({
		      appId      : '609465006186211',
		      cookie     : true,
		      xfbml      : true,
		      version    : 'v3.2'
		    });
	      
	    	FB.AppEvents.logPageView(); 
	  	};

	  	(function(d, s, id){
		    var js, fjs = d.getElementsByTagName(s)[0];
		    if (d.getElementById(id)) {return;}
		    js = d.createElement(s); js.id = id;
		    js.src = "https://connect.facebook.net/en_US/sdk.js";
		    fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk'));

		function checkLoginState() {
			FB.getLoginStatus(function(response) {
			    statusChangeCallback(response);
			});
		}

		function statusChangeCallback(response) {
            console.log('statusChangeCallback');
            console.log(response);
            // The response object is returned with a status field that lets the
            // app know the current login status of the person.
            // Full docs on the response object can be found in the documentation
            // for FB.getLoginStatus().
            if (response.status === 'connected') {
                // Logged into your app and Facebook.
                console.log('Welcome!  Fetching your information.... ');
                FB.api('/me', function (response) {
                    console.log('Successful login for: ' + response.name);
                    document.getElementById('status').innerHTML =
                      'Thanks for logging in, ' + response.name + '!';
                });
            } else {
                // The person is not logged into your app or we are unable to tell.
                document.getElementById('status').innerHTML = 'Please log ' +
                  'into this app.';
            }
        }	  
	</script>
</body>
</html>