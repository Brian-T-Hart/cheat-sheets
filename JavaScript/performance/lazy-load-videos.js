(function () {
    const videoElementSelector = '.custom-lazy-video';
    let customLazyVideosLoaded = false;

    function custom_load_lazy_videos() {
        // Prevent multiple calls
        if (customLazyVideosLoaded) return;
        customLazyVideosLoaded = true;

        // Remove all listeners after firing
        document.documentElement.removeEventListener('click', custom_load_lazy_videos);
        document.documentElement.removeEventListener('mouseenter', custom_load_lazy_videos);
        document.documentElement.removeEventListener('keydown', custom_load_lazy_videos);
        document.documentElement.removeEventListener('scroll', custom_load_lazy_videos, { passive: true });
        document.documentElement.removeEventListener('touchstart', custom_load_lazy_videos, { passive: true });

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
    document.documentElement.addEventListener('click', custom_load_lazy_videos);
    document.documentElement.addEventListener('mouseenter', custom_load_lazy_videos);
    document.documentElement.addEventListener('keydown', custom_load_lazy_videos);
    document.documentElement.addEventListener('scroll', custom_load_lazy_videos, { passive: true });
    document.documentElement.addEventListener('touchstart', custom_load_lazy_videos, { passive: true });
})();