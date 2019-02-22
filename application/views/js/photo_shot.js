
(function() {
    let new_left = 50;
    let new_top = 70;
    const max_left = 640 - 175;
    const max_top = 480 - 175;
    const video = document.getElementById('video');
    let sticker_picked = false;

    // Get access to the camera!
    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
        navigator.mediaDevices.getUserMedia({ video: true }).then(function(stream) {
            video.srcObject = stream;
            video.play();
        });
    }

    // Elements for taking the snapshot
    let canvas = document.getElementById('canvas');
    let context = canvas.getContext('2d');
    //Elements for sticker
    let sticker_container = document.getElementById("stickers_id");
    let stickerVideoScreen = document.getElementById("video_sticker");

    //elements for uploading photo
    let uploadButton = document.getElementsByClassName("upload-button");
    let video_image = document.getElementById("video_image");

    uploadButton[0].addEventListener('click', function() {
        video.pause();
    });

    uploadButton[0].addEventListener('change', function(evt) {
        let selectedFile = this.files[0];

        if (FileReader && selectedFile && (selectedFile.type === 'image/jpeg' || selectedFile.type === 'image/png')) {

            let reader = new FileReader();            
            reader.onloadend = function() {

                video_image.src = reader.result;
                video_image.style.display = "flex";

                video.style.display = "none";
            };
            reader.readAsDataURL(selectedFile);
    
        } else {
            video.play();
            alert("Invalid file type. Please pick another Image.");
        }
    });

    sticker_container.addEventListener('click', applyStikerToScreen);

    stickerVideoScreen.addEventListener('mousedown', function(evt) {
        stickerVideoScreen.isDown = true;
        stickerVideoScreen.offset = [
            stickerVideoScreen.offsetLeft - evt.clientX,
            stickerVideoScreen.offsetTop - evt.clientY
        ];
    });

    stickerVideoScreen.addEventListener('mouseup', function(evt) {
        stickerVideoScreen.isDown = false;
    });

    stickerVideoScreen.addEventListener('mousemove', function(evt) {
        evt.preventDefault();
        if (stickerVideoScreen.isDown) {
            stickerVideoScreen.mousePosition = {
                x : evt.clientX,
                y : evt.clientY
            };
            new_left = (stickerVideoScreen.mousePosition.x + stickerVideoScreen.offset[0]);
            new_top = (stickerVideoScreen.mousePosition.y + stickerVideoScreen.offset[1]);
            //making sure that sticker will be in the video box
            if (new_left > max_left) {
                new_left = max_left;
            } else if (new_left < 0) {
                new_left = 0;
            }
            if (new_top > max_top) {
                new_top = max_top;
            } else if (new_top < 0) {
                new_top = 0;
            }
            stickerVideoScreen.style.left = new_left + 'px';
            stickerVideoScreen.style.top = new_top + 'px';
        }
    });

    // Trigger photo take
    const snap = document.getElementById("shot");

    snap.addEventListener("click", function() {
        if (video_image.style.display === "flex") {
            context.drawImage(video_image, 0, 0, 640, 480);
        } else {
            context.drawImage(video, 0, 0, 640, 480);
        }
        context.drawImage(stickerVideoScreen, new_left, new_top, 175, 175);
    });

    const clear = document.getElementById("clear_video");
    clear.addEventListener('click', clearScreen);

    const save_poto = document.getElementById('save_photo');

    save_photo.addEventListener('click', function (e) {
        if (stickerVideoScreen.style.display === 'flex') {
            if (sticker_picked = true) {
                const dataURL = canvas.toDataURL();
                const formData = new FormData();
    
                formData.append("img", dataURL);
    
                const XHR = new XMLHttpRequest();
    
                XHR.open('POST', '/save_photo');
    
                //delete me or NOT?
                XHR.addEventListener("load", e => {
                    if (e.target.status !== 200) {
                        alert("Something went wrong. Not Groot fault");
                    } else {
                        alert("Groot saved your photo to gallery.");
                    }
                }, false);
    
                // XHR.addEventListener("error", e => {
                //     console.error('error sending request');
                // });
    
                XHR.send(formData);
            }
        }
    })

    function clearScreen() {
        stickerVideoScreen.style.display = 'none';
        stickerVideoScreen.src = '';
        sticker_picked = false;
        if (video_image.style.display === "flex") {
            video_image.style.display = "none";
            video_image.src = "";
            video.style.display = "flex";
            video.play();
        }
        video.play();
    }

    function applyStikerToScreen(e) {
        sticker_picked = true;
        stickerVideoScreen.style.display = 'flex';
        stickerVideoScreen.src = e.target.src;
    }
})()
