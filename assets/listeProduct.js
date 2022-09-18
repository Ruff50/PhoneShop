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
			let btnsuppr = document.querySelector('#btnSupprArticle');
			btnsuppr.dataset.article = idUser;


			//let btnsuppr = document.querySelector('#btnSupprUser');	
			btnsuppr.addEventListener("click",

				function supprUser() {
					let id = btnsuppr.dataset.article;
					ajx.open("GET", "/admin/product/" + id + "/delete");
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
										document.getElementById("deleteArticleModal").removeAttribute("show");
										document.getElementById("deleteArticleModal").style.display = "none";
										document.getElementById("deleteArticleModal").removeAttribute("dialog");
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


});
