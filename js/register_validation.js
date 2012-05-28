function return_true(item)
{
	//gjord för att mina fina fält ska kunna användas.
	return true;
}

function validate_register_form(form)
{
//	alert("validate_register_form - version 10");
	var valid = true;
	
	var required_fields = [
	//		{'name':"memberdata[gender]",		'func':checked,	'validation':return_true,		'must_exist':true}, //Dold då vi inte längre frågar om kön.
			{'name':"memberdata[firstName]",	'func':exists,	'validation':return_true,		'must_exist':true},
			{'name':"memberdata[lastName]",		'func':exists,	'validation':return_true,		'must_exist':true},
			{'name':"memberdata[coAddress]",	'func':exists,	'validation':return_true,		'must_exist':false},
			{'name':"memberdata[streetAddress]",'func':exists,	'validation':return_true,		'must_exist':true},
			{'name':"memberdata[zipCode]",		'func':exists,	'validation':return_true,		'must_exist':true},
			{'name':"memberdata[city]",			'func':exists,	'validation':return_true,		'must_exist':true},
			{'name':"memberdata[phoneNr]",		'func':exists,	'validation':phoneNrValidate,	'must_exist':true},
			{'name':"memberdata[altPhoneNr]",	'func':exists,	'validation':phoneNrValidate,	'must_exist':false},
			{'name':"memberdata[eMail]",		'func':exists,	'validation':eMailValidate,		'must_exist':true},
			{'name':"memberdata[eMail_again]",	'func':exists,	'validation':eMailValidate,		'must_exist':true},
			{'name':"seen_rules",				'func':checked,	'validation':return_true}
		];
//	alert("start searching");
	for(var i=0; i<required_fields.length; ++i) {

		if( !required_fields[i].func( form[required_fields[i].name] ) )
		{
			if( required_fields[i].must_exist  )
			{
	//			alert( "Doesn't exist: ".concat( required_fields[i].name).concat(": ").concat(form[required_fields[i].name].value) );
				valid = false;
				document.getElementsByName(required_fields[i].name)[0].setAttribute('style', "border-color:".concat( "red" ).concat(";"));
				document.getElementsByName(required_fields[i].name)[0].setAttribute('title', "Detta fält måste vara ifyllt.");
			}
			else
			{
				//if it do exists but doesn't have to it's correct
				document.getElementsByName(required_fields[i].name)[0].setAttribute('style', "border-color: #00ff00;");
			}
		}
		else if( !required_fields[i].validation( form[required_fields[i].name] ) )
		{
			//något är ogiltigt
	//		alert( "Doesn't validate: ".concat( required_fields[i].name) );
			valid = false;
			document.getElementsByName(required_fields[i].name)[0].setAttribute('title', "Fältet är felaktigt ifyllt.");
		}
		else
		{
			//if nothing is found wrong it's correct
			document.getElementsByName(required_fields[i].name)[0].setAttribute('style', "border-color: #00ff00;");
		}
	}
//	alert("done finding the req fields");
	if( form["memberdata[eMail]"].value !=  form["memberdata[eMail_again]"].value )
	{
		//emailadresserna är inte identiska
//		alert("eMail doesn't match");
		valid = false;
		document.getElementsByName("memberdata[eMail_again]")[0].setAttribute('title', "Upprepa email måste matcha Email");
	}

	return valid;
}

function checked( input )
{
	return input.value != "undefined";
}

function exists( input )
{
	return input.value != null && input.value != "";
}

function phoneNrValidate( input )
{
	var number = String(input.value);
	
	//remove spaces
	number = number.replace(/ /g,"");
	//remove plussign in the beginning
	if( number[0] == '+' )
		number = number.substr(1);
	
	//A number can't be smaller than 6 digits
	if( number.length < 6 )
		return false;
	
	var parts = number.split('-');
	//At the most only one split shall be made
	if( parts.length > 2)
		return false;
	//There must be at least 2 numbers existing in both
	for(var p=0; p<parts.length; ++p)
	{
		if( parts[p].length < 2 || parts[p].replace(/[0-9]/g,"").length != 0 ) // +468-541 324 30 => +468-54132430 => 468-54132430 => (468, 54132430)
		{
			return false;
		}
	}

	return true;
}

function eMailValidate(email)
{
	email = email.value;
	var isValid = true;
	var atIndex = email.lastIndexOf("@");

//	alert("validating email - version 3");

	if ( atIndex == -1 || atIndex == email[email.length-1]) //never occured a @ or is at the last position
	{
		isValid = false;
//		alert("is false");
	}
	else
	{
//		alert("else");
		var domain = email.substr(atIndex+1);
		var local = email.substr(0, atIndex);
		var localLen = local.length;
		var domainLen = domain.length;
//alert("domain: ".concat(domain));		
//alert("local: ".concat(local));
		if (localLen < 1 || localLen > 64)
		{
         // local part length exceeded
			isValid = false;
//			alert("local part length exceeded");
		}
		else if (domainLen < 1 || domainLen > 255)
		{
			// domain part length exceeded
			isValid = false;
//			alert("domain part length exceeded");
		}
		else if (local[0] == '.' || local[localLen-1] == '.')
		{
			// local part starts or ends with '.'
			isValid = false;
//			alert("local part starts or ends with '.'");
		}
		else if (local.match(/\\.\\./))//(preg_match('/\\.\\./', $local))
		{
			// local part has two consecutive dots
			isValid = false;
//			alert("local part has two consecutive dots");
		}
		else if (!domain.match(/^[A-Za-z0-9\\-\\.]+$/))//(!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
		{
			// character not valid in domain part
			isValid = false;
//			alert("character not valid in domain part");
//			alert(domain.match(/^[A-Za-z0-9\\-\\.]+$/));
		}
		else if (domain.match(/\\.\\./))//(preg_match('/\\.\\./', $domain))
		{
			// domain part has two consecutive dots
			isValid = false;
//			alert("domain part has two consecutive dots");
		}
		// Removed for now. too many wierd parts which has to be validated and checked
/*		else if ( !local.replace(\\\\,"").match(/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\*+?^{}|~.-])+$/) ) //apostrophe removed
//(!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\","",$local)))
		{
			// character not valid in local part unless 
			// local part is quoted
/*
			if ( !local.replace("\\\\","").match(/^"(\\\\"|[^"])+"$/) )
//(!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\","",$local)))
			{
				isValid = false;
			}

		}
*/
//		alert("done with else");
		
		
		//missing showing wrong for example whatever@gmailcom (missing . in the part after the last @)
		//work around
		var missing_dot = true;
		for( var i=0; i<domain.length; ++i )
		{
			if( domain[i] == '.' )
			{
				missing_dot = false;
				break;
			}
		}
		if(missing_dot)
		{
			return false;
		}
		
		
	}
//	alert("eMail done");
	
	return isValid;
}

function input_validation(input)
{
//	alert("input_validation - version 18");
	
	if( exists(input) )
	{
		if(input.title = "Detta fält måste vara ifyllt")
			input.setAttribute('title',"");
			
		var valid = true;
		
		if( input.name == "memberdata[phoneNr]" || input.name == "memberdata[altPhoneNr]" )
		{
			if( !phoneNrValidate(input) )
			{
				valid = false;
			}
		}
		else if ( input.name == "memberdata[eMail]")
		{
			if( !eMailValidate(input) )
			{
				valid = false;
			}
		}
		else if( input.name == "memberdata[eMail_again]" )
		{
			var eMail = document.getElementsByName("memberdata[eMail]")[0];
			
			if( input.value != eMail.value || !eMailValidate(input))
			{
				valid = false;
			}
		}
		else
		{
			valid = true;
		}
		
		input.setAttribute('style', "border-color:".concat( valid ? "#00ff00" : "red" ).concat(";"));
		input.setAttribute('title', valid ? "" : "Fältet är felaktigt ifyllt.");
	}
	else
	{
		if( input.title != "Detta fält måste vara ifyllt")
			input.setAttribute('title', "");
			
		if( input.name == "memberdata[coAddress]" || input.name == "memberdata[altPhoneNr]")
		{
			input.setAttribute('style', "border-color: #00ff00;");
		}
		else
		{
			input.setAttribute('style', "");
		}
	}
	
}

