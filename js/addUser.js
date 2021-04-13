function AddUser(){
	document.getElementById("Ok").innerHTML = '';
	document.getElementById("Codes").innerHTML = '';
	document.getElementById("Users").innerHTML = '';
	var val = $('#info').val();
	document.getElementById('info').value = "";
	val = val.replace(/\r/g,'').replace(/\n{1,}/g,'\n').replace(/^\n/,'').replace(/\n$/,'');
	var text = val.split('\n');
	var users = [];
	var passwords= [];
	var emails= [];
	var count = 0;
	var err = 0;
	var accessLevel = '';
	function genPassword(len){
   		var password = "";
   		var symbols = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    		for (var i = 0; i < len; i++){
    			password += symbols.charAt(Math.floor(Math.random() * symbols.length));     
   		}
    		return password;
	}
	function E(str, code){
		err++;		
		document.getElementById("Users").innerHTML += str+'<br>';
		document.getElementById("Codes").innerHTML += code+'<br>';
	}

 	if (document.getElementById('option1').checked) accessLevel+='#addUser';
	if (document.getElementById('option2').checked) accessLevel+='#addVote';
	if (document.getElementById('option3').checked) accessLevel+='#getResults';
	if (document.getElementById('option4').checked) accessLevel+='#editUser';
	if (document.getElementById('option5').checked) accessLevel+='#editVote';
	if (document.getElementById('option6').checked) accessLevel+='#archive';

	function newUser(str){
		var buf = str.replace(/\s{1,}/g,' ').replace(/^\s/,'').replace(/\s$/,'').split(' ');
		if (buf.length < 2){E(str, 'Не все поля заполнены');return;}
		else{
			var re = /[^a-zA-Z0-9.!?;%:*-+=\_\(\)]/;
			if (buf[0].length < 4){E(str, 'Логин слишком короткий');return;}
			if (buf[0].length > 20){E(str, 'Логин слишком длинный');return;}
			if (re.test(buf[0])){E(str, 'Запрещенные символы ['+re.exec(buf[0])+']в логине');return;}
			re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
			if (!re.test(buf[1])){E(str, 'Неверный формат почтового адреса');return;}
			if (buf.length > 2){
				re = /[^a-zA-Z0-9.!?;%:*-+=\_\(\)]/;
				if (buf[2].length < 4){E(str, 'Пароль слишком короткий');return;}
				if (buf[2].length > 64){E(str, 'Пароль слишком длинный');return;}
				if (re.test(buf[2])){E(str, 'Запрещенные символы ['+re.exec(buf[0])+']в пароле');return;}
				passwords[count] = buf[2];
			}
			else passwords[count] = genPassword(6);
			users[count] = buf[0];
			emails[count] = buf[1];
			count++;
		}
	}
	text.forEach(element=>newUser(element));
	$('body').addClass("loading");
	$.ajax({
		type: 'POST',
		url: 'PHP/User.php',
		data: {
			'users': users,
			'passwords': passwords,
			'emails': emails,
			'count': count,
			'action': 'addUser',
			'accessLevel': accessLevel
		},
		success: function (data, textStatus, request) {
			$('body').removeClass("loading");
			for (i = 0; i<count; i++ ){
				if (data[i]=='0'){
					document.getElementById("Ok").innerHTML += users[i]+' '+passwords[i]+'<br>';
				}
				else {
					document.getElementById("Users").innerHTML += users[i]+' '+emails[i]+'<br>';
					document.getElementById("Codes").innerHTML += data[i]+'<br>';
				}
			}
		},
		error: function (request, textStatus, errorThrown) {
			$body.removeClass("loading");
			OnRequestFailed('-2', 'Login request error: ' + errorThrown, true);
			
		}
	});
}