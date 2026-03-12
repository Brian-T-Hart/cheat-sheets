(function () {
    const videoElementSelector = '.custom-lazy-video';
    let customLazyVideosLoaded = false;

    function custom_load_lazy_videos() {
        // Prevent multiple calls
        if (customLazyVideosLoaded) return;
        customLazyVideosLoaded = true;

        // Remove all listeners after event fires
        removeEventListener('pointerdown', custom_load_lazy_videos, true);
        removeEventListener('pointermove', custom_load_lazy_videos, true);
        removeEventListener('keydown', custom_load_lazy_videos, true);
        removeEventListener('scroll', custom_load_lazy_videos, true);

        // Lazy load videos
        const customLazyVideos = document.querySelectorAll(videoElementSelector);
        customLazyVideos.forEach(video => {

            // if video already has a source, skip
            if (video.querySelector('source')) return;

            // If video has data-src attribute, create a source element and append to video
            const src = video.dataset.src;
            if (src && typeof src === "string" && src.trim() !== "") {
                const sourceEl = createSourceElement(src);
                video.appendChild(sourceEl);
                video.load();
                handleVideoClick(video);
            } else {
                console.warn('No valid data-src found for video:', video);
            }
        });
    }// custom_load_lazy_videos

    function createSourceElement(src, type = "video/mp4") {
        const sourceEl = document.createElement('source');
        sourceEl.src = src;
        sourceEl.type = type;
        return sourceEl;
    }

    function handleVideoClick(video) {
        video.addEventListener('click', () => {
            if (video.paused) {
                video.play();
            } else {
                video.pause();
            }
        });
    }

    // Set up event listeners
    addEventListener('pointerdown', custom_load_lazy_videos, { passive: true, capture: true });
    addEventListener('pointermove', custom_load_lazy_videos, { passive: true, capture: true });
    addEventListener('keydown', custom_load_lazy_videos, { capture: true });
    addEventListener('scroll', custom_load_lazy_videos, { passive: true, capture: true });
})();