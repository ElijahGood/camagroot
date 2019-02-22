(function() {
	let cur_page = 1;
	const pagination = 5;
	let numberOfPages = document.getElementsByClassName("page_number");
	const post_page_id = document.getElementsByClassName("photo_container");
	const move_left_page = document.getElementsByClassName("left_page");
	const move_right_page = document.getElementsByClassName("right_page");

	document.addEventListener("DOMContentLoaded", function(e) {
		/* create array from object and monitor (via loop) its events (clicks) 
		   to perform corresponding function */

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

	function changePage() {
		cur_page = parseInt(this.textContent);
		changeDisplayForPage(cur_page);
	}

	/* Changes page accordingly to pagination*/
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

})();