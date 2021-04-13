
$('#file').change(function() {
    if ($(this).val() != '') $(this).prev().text('Выбрано файлов: ' + $(this)[0].files.length);
    else $(this).prev().text('Выберите файлы');
});

function E(str, code){
		err++;		
		document.getElementById("Users").innerHTML += str+'<br>';
		document.getElementById("Codes").innerHTML += code+'<br>';
	}

function addproject(){
	name1 = document.getElementById("name1").value.trim();
	if (/\s{1,}/.test(name1)){E(str, 'Пробелы в фамилии');return;}
	name1.replace(/(^|\-)[а-я]/,  str => str.toUpperCase())
	
	name2 = document.getElementById("name2").value.trim();
	if (/\s{1,}/.test(name2)){E(str, 'Пробелы в имени');return;}
	name2 = name2[0].toUpperCase();

	name3 = document.getElementById("name3").value.trim();
	if (/\s{1,}/.test(name3)){E(str, 'Пробелы в имени');return;}
	name3 = name2[0].toUpperCase();


$.ajax({
		type: 'POST',
		url: 'PHP/addProject.php',
		data: {
			'name1': name1,
			'name2': name2,
			'link':  document.getElementById("num").value,
			 		},
		success: function (data, textStatus, request) {
			
			if(data == 'OK'){

				 alert('OK');
			}

			
			else	alert(data);
					
				
		},
		error: function (request, textStatus, errorThrown) {
			OnRequestFailed('-2', 'Login request error: ' + errorThrown, true);
		}
	});
return false;
}

