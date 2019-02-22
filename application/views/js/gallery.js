(function () {
	//get objects of needed classes
	const comments = document.getElementsByClassName('material-icons show_all_comments');
	const likes = document.getElementsByClassName('material-icons md-red');
	const addCommentButton = document.getElementsByClassName('material-icons comment-button');
	const delteCommentButton = document.getElementsByClassName('material-icons del-comment');
	const photoDeleteButton = document.getElementsByClassName('material-icons photo-delete');
	
	/*** For Pagination part ***/
	let cur_page = 1;
	const pagination = 5;
	let numberOfPages = document.getElementsByClassName("page_number");
	const post_page_id = document.getElementsByClassName("post-container");
	const move_left_page = document.getElementsByClassName("left_page");
	const move_right_page = document.getElementsByClassName("right_page");

	//once document is loaded
	document.addEventListener("DOMContentLoaded", function(e) {
		/* create array from object and monitor (via loop) its events (clicks) 
		   to perform corresponding function */
		Array.from(comments).forEach(v => {
			v.addEventListener('click', showHideComments)
		})
		Array.from(likes).forEach(v => {
			v.addEventListener('click', showHideHeart)
		})
		Array.from(addCommentButton).forEach(v => {
			v.addEventListener('click', addComment)
		})
		Array.from(delteCommentButton).forEach(v => {
			v.addEventListener('click', deleteComment)
		})
		Array.from(photoDeleteButton).forEach(v => {
			v.addEventListener('click', deletePhoto);
		})

		/*** Pagination part ***/
		/* Initial display none for nor 1-st page */
		changeDisplayForPage(cur_page);

		Array.from(numberOfPages).forEach(v => {
			v.addEventListener('click', changePage)
		})
		Array.from(move_left_page).forEach(v => {
			v.addEventListener('click', movePageLeft)
		})
		Array.from(move_right_page).forEach(v => {
			v.addEventListener('click', movePageRight)
		})
	})

	function showHideComments() {
		comments_block = this.parentNode.parentNode.getElementsByClassName('comments_container')[0];
		if (comments_block.style.display === 'none') {
			comments_block.style.display = 'inline';
		} else {
			comments_block.style.display = 'none';
		}
	}

	function deletePhoto() {
		if (confirm("Are you sure about deleting this masterpiece?")) {
			number_id_to_hide = parseInt(this.parentNode.parentNode.getAttribute("number_id"));
			photo_div = this.parentNode.parentNode;
			photo_id = photo_div.getElementsByClassName('likes_number')[0].getAttribute('photo_id');
			photo_div.style.display = 'none';
			
			const formData = new FormData();
			const XHR = new XMLHttpRequest();
			
			formData.append('photo_id', parseInt(photo_id));
			XHR.open('POST', '/delete_photo');
			XHR.send(formData);
			rearangePhotosForPaging(number_id_to_hide);
		}
		
	}

	function deleteComment() {
		const comment_to_hide = this.parentNode;
		const formData = new FormData();
		const XHR = new XMLHttpRequest();

		formData.append('comment_id', this.getAttribute('comment_id'));
		XHR.open('POST', '/delete_comment');
		XHR.send(formData);
		//front handle
		comment_to_hide.style.display = 'none';
	}

	//server side fucntionality undone!
	function showHideHeart() {
		let formData = new FormData();
		let XHR = new XMLHttpRequest();

		photoId = this.parentNode.getElementsByClassName('likes_number')[0];
		formData.append('photo_id', photoId.getAttribute('photo_id'));
		if (this.innerHTML === 'favorite') {
			this.innerHTML = 'favorite_border';
			this.style.fontSize = '24px';

			new_like_num = parseInt(photoId.innerHTML) - 1;
			XHR.open('POST', '/delete_like');
		} else {
			this.innerHTML = 'favorite';
			this.style.fontSize = '27px';

			XHR.open('POST', '/add_like');
			new_like_num = parseInt(photoId.innerHTML) + 1;
		}
		photoId.innerHTML = new_like_num;
		XHR.send(formData);
	}

	function addComment () {
		const comment_text = this.parentNode.getElementsByClassName('comment_text')[0];
		const photo_id = this.parentNode.getAttribute('photo_id');
		if (comment_text.value.length > 0) {
			const formData = new FormData();
			const XHR = new XMLHttpRequest();

			formData.append('comment_text', comment_text.value);
			formData.append('photo_id', photo_id);

			XHR.addEventListener("load", e => {
			//	create new element on front
				const json = JSON.parse(e.target.response);
				const new_one = document.createElement("span");
				const name = document.createElement('ins');
				name.appendChild(document.createTextNode(json.username));
				const text = document.createTextNode(": "+comment_text.value);
				const linebreak = document.createElement("br");

				name.style.fontStyle = 'italic';
				new_one.appendChild(name);
				new_one.appendChild(text);
				new_one.appendChild(linebreak);

				const comments_div = this.parentNode.parentNode.getElementsByClassName('prev_comments')[0];
				comments_div.appendChild(new_one);

				comment_text.value = '';
			});

			XHR.open('POST', '/new_comment');
			XHR.send(formData);
		}
	}

	/*** Pagination functions ***/
	function changePage() {
		cur_page = parseInt(this.textContent);
		changeDisplayForPage(cur_page);
	}

	/* Changes page accordingly to pagination (style.display = ? "" : "none";) */
	function changeDisplayForPage(new_page) {
		Array.from(post_page_id).forEach(function(e) {
			//check if post should be on this particular page
			let tmp_number = parseInt(e.getAttribute("number_id"));
			if (tmp_number <= (new_page * pagination) && tmp_number > ((new_page - 1) * pagination)) {
				e.style.display = "";
			} else {
				e.style.display = "none";
			}
		});
	}

	function movePageLeft() {
		if (cur_page > 1) {
			cur_page--;
			changeDisplayForPage(cur_page);
		}
	}

	function movePageRight() {
		/* Determine precise number of pages */
		max_page = Math.ceil(Array.from(post_page_id).length / pagination);

		if (cur_page < max_page) {
			cur_page++;
			changeDisplayForPage(cur_page);
		}
	}

	function rearangePhotosForPaging(number_id_to_delete) {
		let flag = false;
		Array.from(post_page_id).forEach(function(e) {
			let temp_num = parseInt(e.getAttribute("number_id"));
			/*
				shifting all posts after deleted one so every page will be solid
				it will be executed only for elements AFTER deleted element
			*/
			if (flag === true) {
				e.setAttribute("number_id", (temp_num - 1));

			}
			/*
				when we find element that we deleted on server-side
				make invisible and invalid for pagination
			*/
			if (number_id_to_delete === temp_num && flag === false) {
				e.setAttribute("number_id", 0);
				flag = true;
			}
		});
		//update style.display
		changeDisplayForPage(cur_page);
	}
})();

