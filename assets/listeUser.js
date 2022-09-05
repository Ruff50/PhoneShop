import './styles/app.css';

import './bootstrap';

document.addEventListener('DOMContentLoaded', function () {

	let ajx = new XMLHttpRequest();

	var suppr = document.getElementsByClassName("modalClick");
	for (var i = 0; i < suppr.length; i++) {
		suppr[i].addEventListener('click', RespondClick);

	}

	function RespondClick() {
		let dataID = this.parentNode.parentNode;
		let idUser = dataID.dataset.id;
		if (idUser) {
			let btnsuppr = document.querySelector('#btnSupprUser');
			btnsuppr.dataset.user = idUser;


			//let btnsuppr = document.querySelector('#btnSupprUser');	
			btnsuppr.addEventListener("click",

				function supprUser() {
					let id = btnsuppr.dataset.user;

					ajx.open("GET", "/user/compte/delete/" + id);
					ajx.send();
					ajx.onreadystatechange = alertContents;
					function alertContents() {
						if (ajx.readyState == 4) {
							if (ajx.status == 200) {
								//alert('L\'utilisateur a bien été supprimé !')
								let data = JSON.parse(ajx.responseText);
								if (data.success) {
									let divAlert = document.getElementById('alertSuppr');
									divAlert.style.display = 'block';
									divAlert.innerHTML = data.success;
									setTimeout(fenmodaldisparait, 4000);
									function fenmodaldisparait() {
										document.getElementById("deleteEmployeeModal").removeAttribute("show");
										document.getElementById("deleteEmployeeModal").style.display = "none";
										document.getElementById("deleteEmployeeModal").removeAttribute("dialog");
										const elements = document.getElementsByClassName('modal-backdrop fade show');
										Array.from(elements).forEach(element => element.classList.remove("show"));
										dataID.remove();
									}
									location.reload();
								} else {
								}
								//parent.remove();
							} else {
								console.log("Error code " + ajx.status);
							}
						}
					}
				});
		}
	}

	var btnEdit = document.getElementsByClassName("editModal");
	for (var i = 0; i < btnEdit.length; i++) {
		btnEdit[i].addEventListener('click', editClick);

	}

	function editClick() {
		document.getElementById("motDePasse").value = '';
		document.getElementById("repeatMotDePasse").value = '';
		document.getElementById("NewmotDePasse").value = '';
		document.getElementById('alertEdit').style.display = 'none';
		document.getElementById("toggle-t").innerHTML = "Modifier un Utilisateur";
		let inputModif = document.getElementById("modif-btn");
		inputModif.style = 'display:block;';
		document.getElementById("ajout-btn").style = 'display:none;';
		document.getElementById("toggle-div").style = 'display:block;';
		document.getElementById("NewmotDePasse").required = true;
		let dataID = this.parentNode.parentNode.parentNode;
		let idUser = dataID.dataset.id;
		if (idUser) {
			ajx.open("GET", "/user/compte/edit/" + idUser);
			ajx.send();
			ajx.onreadystatechange = alertContents;
		}

		function alertContents() {
			if (ajx.readyState == 4) {
				if (ajx.status == 200) {
					let data = JSON.parse(ajx.responseText);
					document.getElementById("inpname").value = data['name'];
					document.getElementById("inpemail").value = data['email'];
					let selector = document.getElementById("selrole");
					let collection = selector.options;

					for (let option of collection) {
						if (option.value == data['roles'][0]) {
							option.selected = true;
						}
					}
				};
				//alert('L\'utilisateur a bien été modifié');
			}
		}

		inputModif.onclick = function () {


			let nomsaisi = document.getElementById("inpname").value;
			let emailsaisi = document.getElementById("inpemail").value;
			let mdpsaisi = document.getElementById("motDePasse").value;
			let nmdpsaisi = document.getElementById("NewmotDePasse").value;
			let rnmdpsaisi = document.getElementById("repeatMotDePasse").value;
			let selector = document.getElementById("selrole");
			var rolesaisi = selector.value;

			ajx.open("GET", "/user/compte/store/" + idUser + "?name=" + nomsaisi + "&email=" + emailsaisi + "&motDePasse=" + mdpsaisi + "&NewmotDePasse=" + nmdpsaisi + "&repeatMotDePasse=" + rnmdpsaisi + "&role=" + rolesaisi);
			ajx.send();

			ajx.onreadystatechange = function () {
				if (ajx.readyState == 4) {
					if (ajx.status == 200) {
						let data = JSON.parse(ajx.responseText);
						let divAlert = document.getElementById('alertEdit');
						if (data.error) {
							divAlert.style.display = 'block';
							divAlert.innerHTML = data.error;

						} else {
							alert(data.success)
							divAlert.style.display = 'none';
							document.getElementById("addEmployeeModal").removeAttribute("show");
							document.getElementById("addEmployeeModal").style.display = "none";
							document.getElementById("addEmployeeModal").removeAttribute("dialog");
							const elements = document.getElementsByClassName('modal-backdrop fade show');
							Array.from(elements).forEach(element => element.classList.remove("show"));
							location.reload();
						}

					};
					//alert('L\'utilisateur a bien été modifié');
				}
			}
		}
	};

	let inputajout = document.getElementById("ajout-btn");
	inputajout.onclick = function () {

		let nomsaisi = document.getElementById("inpname").value;
		let emailsaisi = document.getElementById("inpemail").value;
		let mdpsaisi = document.getElementById("motDePasse").value;
		let rmdpsaisi = document.getElementById("repeatMotDePasse").value;
		let selector = document.getElementById("selrole");
		var rolesaisi = selector.value;

		ajx.open("GET", "/user/compte/add?name=" + nomsaisi + "&email=" + emailsaisi + "&motDePasse=" + mdpsaisi + "&repeatMotDePasse=" + rmdpsaisi + "&role=" + rolesaisi);
		ajx.send();
		ajx.onreadystatechange = function () {
			if (ajx.readyState == 4) {
				if (ajx.status == 200) {
					let data = JSON.parse(ajx.responseText);
					let divAlert = document.getElementById('alertEdit');
					if (data.error) {
						divAlert.style.display = 'block';
						divAlert.innerHTML = data.error;

					} else {
						alert(data.success)
						divAlert.style.display = 'none';
						document.getElementById("addEmployeeModal").removeAttribute("show");
						document.getElementById("addEmployeeModal").style.display = "none";
						document.getElementById("addEmployeeModal").removeAttribute("dialog");
						const elements = document.getElementsByClassName('modal-backdrop fade show');
						Array.from(elements).forEach(element => element.classList.remove("show"));
						location.reload();
						//history.go(0);
					};

				}
			}
		}
	};



	let btnadd = document.querySelector('#ajtUtil');
	btnadd.addEventListener("click",
		function () {

			document.getElementById("toggle-t").innerHTML = "Ajouter un Utilisateur";
			let inputModif = document.getElementById("modif-btn");
			inputModif.style = 'display:none;';
			document.getElementById("ajout-btn").style = 'display:block;';
			document.getElementById("toggle-div").style = 'display:none;';
			document.getElementById("NewmotDePasse").required = false;
			document.getElementById("inpname").value = '';
			document.getElementById("inpemail").value = '';
			let selector = document.getElementById("selrole");
			selector.options[0].selected = true;
			document.getElementById("motDePasse").value = '';
			document.getElementById("repeatMotDePasse").value = '';
			let divAlert = document.getElementById('alertEdit');
			divAlert.style.display = 'none';

		});

});
