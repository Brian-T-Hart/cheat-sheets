/**
 * Adds a click listener to an element that opens and plays a video in fullscreen
 * @param {HTMLElement} video
 * @param {HTMLElement} toggle
 * @returns
 */
function playFullScreenVideo(video, toggle) {
    
    // Add console warning if either argument is not an html element
    if (!(video instanceof HTMLElement) || !(toggle instanceof HTMLElement)) {
        console.error("playFullScreenVideo: one or both arguments are not HTMLElements");
        return;
    }

    // Pause video when exiting fullscreen
    video.addEventListener('fullscreenchange', (event) => {
        if (!document.fullscreenElement) {
            video.pause();
        }
    },
        false
    );

    // Toggle fullscreen and play/pause video on button click
    toggle.addEventListener('click', () => {
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

const videoEl = document.querySelector('#myVideo');
const toggleEl = document.querySelector('#myBtn');
playFullScreenVideo(videoEl, toggleEl);