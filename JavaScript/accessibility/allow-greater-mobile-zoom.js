/**
 * Updates viewport meta value to allow for zooming on mobile
 */
function allow_greater_mobile_zoom() {
    const viewportMeta = document.querySelector('meta[name=viewport]');

    if (viewportMeta) {
        viewportMeta.setAttribute('content', 'width=device-width, initial-scale=1, maximum-scale=5, user-scalable=1');
    }
}//allow_greater_mobile_zoom