function playFullScreenVideo(videoSelector, toggleSelector) {
    const video = document.querySelector(videoSelector);
    const fullscreenToggle = document.querySelector(toggleSelector);

    if (!video || !fullscreenToggle) return;

    video.addEventListener('fullscreenchange', (event) => {
        if (!document.fullscreenElement) {
            video.pause();
        }
    },
        false
    );

    fullscreenToggle.addEventListener('click', () => {
        if (!document.fullscreenElement) {
            if (video.requestFullscreen) {
                video.requestFullscreen();
            } else if (video.mozRequestFullScreen) { // Firefox
                video.mozRequestFullScreen();
            } else if (video.webkitRequestFullscreen) { // Chrome, Safari and Opera
                video.webkitRequestFullscreen();
            } else if (video.msRequestFullscreen) { // IE/Edge
                video.msRequestFullscreen();
            }
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.mozCancelFullScreen) { // Firefox
                document.mozCancelFullScreen();
            } else if (document.webkitExitFullscreen) { // Chrome, Safari and Opera
                document.webkitExitFullscreen();
            } else if (document.msExitFullscreen) { // IE/Edge
                document.msExitFullscreen();
            }
        }

        // Play/Pause Video and Reset Time
        if (video.paused) {
            video.play();
            video.currentTime = 0;
        } else {
            video.pause();
            video.currentTime = 0;
        }
    });
}

playFullScreenVideo("myVideo", "myButton");