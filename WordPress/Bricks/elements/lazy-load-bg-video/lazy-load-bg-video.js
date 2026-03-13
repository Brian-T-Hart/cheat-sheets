(function () {
    // It's safer to use 'DOMContentLoaded' or just 'load' - usually 'load' is better for heavy assets
    window.addEventListener('load', () => {
        const loadVideo = (video) => {
            if (video && video.dataset.src) {
                const src = video.dataset.src;
                const srcMobile = typeof video.dataset.srcMobile !== 'undefined' ? video.dataset.srcMobile : null;
                const mobileBreakpoint = typeof video.dataset.mobileBreakpoint !== 'undefined' ? parseInt(video.dataset.mobileBreakpoint, 10) : 768;
                const sourceEl = document.createElement('source');
                sourceEl.src = src;
                sourceEl.type = getVideoType(src);

                if (srcMobile !== null) {
                    sourceEl.media = `(min-width: ${mobileBreakpoint}px)`;
                    const sourceElMobile = document.createElement('source');
                    sourceElMobile.src = srcMobile;
                    sourceElMobile.type = getVideoType(srcMobile);
                    sourceElMobile.media = `(max-width: ${mobileBreakpoint - 1}px)`;
                    video.appendChild(sourceElMobile);
                }
                video.appendChild(sourceEl);
                const fallback = document.createElement('p');
                fallback.textContent = "Your browser does not support the video tag.";
                video.appendChild(fallback);
                video.load();
                video.play().catch(error => {
                    console.warn("Autoplay prevented or video interrupted:", error);
                });
                video.classList.add('loaded');
                delete video.dataset.src;
                delete video.dataset.srcMobile;
                delete video.dataset.targets;
                delete video.dataset.trigger;
                
            }
        };

        // Helper function to determine video MIME type based on file extension
        const getVideoType = (url) => {
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

        const loadVideos = document.querySelectorAll('.brxe-lazy-load-bg-video[data-trigger="load"]');
        if (loadVideos.length > 0) {
            loadVideos.forEach(video => {
                loadVideo(video);
            });
        }
        
        const triggerEvents = ['pointerdown', 'pointermove', 'keydown', 'scroll'];
        const interactionVideos = document.querySelectorAll('.brxe-lazy-load-bg-video[data-trigger="interaction"]');
        if (interactionVideos.length > 0) {
            const triggerInteractionLoad = () => {
                interactionVideos.forEach(video => {
                    loadVideo(video);
                });
                triggerEvents.forEach(event => {
                    removeEventListener(event, triggerInteractionLoad);
                });
            };
            triggerEvents.forEach(event => {
                addEventListener(event, triggerInteractionLoad, { passive: true, capture: true, once: true });
            });
        }

        const targetedVideos = document.querySelectorAll('.brxe-lazy-load-bg-video[data-targets]');
        if (targetedVideos.length > 0) {
            targetedVideos.forEach(video => {
                const targets = video.dataset.targets;
                if (!targets) return;
                const targetElements = document.querySelectorAll(targets);
                const triggerLoad = () => {
                    loadVideo(video);
                    targetElements.forEach(el => {
                        triggerEvents.forEach(event => {
                            el.removeEventListener(event, triggerLoad);
                        });
                    });
                };
                targetElements.forEach(targetElement => {
                    triggerEvents.forEach(event => {
                        targetElement.addEventListener(event, triggerLoad, { once: true });
                    });
                });
            });
        }

        const intersectionVideos = document.querySelectorAll('.brxe-lazy-load-bg-video[data-trigger="intersection"]');
        const videoObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
            if (entry.isIntersecting) {
                const video = entry.target;
                loadVideo(video);

                // Stop watching this specific video once it's loaded
                observer.unobserve(video);
            }
            });
        }, {
            // Start loading when top of the video is visible
            threshold: 0,
            // Or load it 200px before it enters the viewport for a smoother feel
            rootMargin: "0px 0px 200px 0px" 
        });
        intersectionVideos.forEach(video => videoObserver.observe(video));

    });
})();