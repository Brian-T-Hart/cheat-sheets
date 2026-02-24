(function () {
    let userInteracted = false;

    // Check if any videos should be loaded on window load
    const loadTriggerVideos = document.querySelectorAll('.brxe-lazy-bg-video[data-trigger="load"]');
    if (loadTriggerVideos.length > 0) {
        window.addEventListener('load', () => {
            load_lazy_videos(loadTriggerVideos);
        });
    }

    // Check if any videos should be loaded on user interaction
    const interactionTriggeredVideos = document.querySelectorAll('.brxe-lazy-bg-video[data-trigger="interaction"]');
    if (interactionTriggeredVideos.length > 0) {

        function load_videos_on_interaction() {
            // Prevent multiple calls
            if (userInteracted) return;
            userInteracted = true;

            // Remove all listeners after firing
            document.documentElement.removeEventListener('mouseenter', load_videos_on_interaction);
            document.documentElement.removeEventListener('keydown', load_videos_on_interaction);
            document.documentElement.removeEventListener('touchstart', load_videos_on_interaction, { passive: true });

            load_lazy_videos(interactionTriggeredVideos);
        }

        // Set up event listeners for user interactions to load videos
        document.documentElement.addEventListener('mouseenter', load_videos_on_interaction);
        document.documentElement.addEventListener('keydown', load_videos_on_interaction);
        document.documentElement.addEventListener('touchstart', load_videos_on_interaction, { passive: true });
    }

    // Helper function to create source element based on video URL
    function createSourceElement(src, breakpoint = 768, isDesktop = true) {
        const sourceEl = document.createElement('source');
        sourceEl.src = src;
        sourceEl.type = getVideoType(src);

        if (!isDesktop) {
            sourceEl.media = `(max-width: ${breakpoint}px)`;
        } else {
            sourceEl.media = `(min-width: ${breakpoint + 1}px)`;
        }

        return sourceEl;
    }

    // Helper function to determine video MIME type based on file extension
    function getVideoType(url) {
        const extension = url.split('.').pop().toLowerCase();
        switch (extension) {
            case 'mp4':
                return 'video/mp4';
            case 'webm':
                return 'video/webm';
            case 'ogg':
                return 'video/ogg';
            default:
                return 'video/mp4'; // default fallback
        }
    }

    // Handle click to play/pause video
    function handleVideoClick(video) {
        video.addEventListener('click', () => {
            if (video.paused) {
                video.play();
            } else {
                video.pause();
            }
        });
    }

    // Main function to load videos lazily
    function load_lazy_videos(videos) {
        videos.forEach(video => {
            // if video already has a source, skip
            if (video.querySelector('source')) return;

            // If video has data-src attribute, create a source element and append to video
            const desktopSrc = video.dataset.srcDesktop;
            const mobileSrc = video.dataset.srcMobile;
            const breakpoint = parseInt(video.dataset.breakpoint) || 768;

            // create two source elements for desktop and mobile if both URLs are provided
            if (desktopSrc && typeof desktopSrc === "string" && desktopSrc.trim() !== "") {
                const sourceEl = createSourceElement(desktopSrc, breakpoint, true);
                video.appendChild(sourceEl);
            }

            if (mobileSrc && typeof mobileSrc === "string" && mobileSrc.trim() !== "") {
                const sourceEl = createSourceElement(mobileSrc, breakpoint, false);
                video.appendChild(sourceEl);
            }

            if (video.querySelectorAll('source').length > 0) {
                video.load();
                handleVideoClick(video);
                video.classList.add('loaded');
            }
        });
    }
})();