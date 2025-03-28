// video-hover-play.js
document.addEventListener('DOMContentLoaded', () => {
    // Get the hidden div with the ID 'vhp-parameter'
    const vhpParameterDiv = document.getElementById('vhp-parameter');
    if (!vhpParameterDiv) {
        console.error('VHP parameter div not found');
        return;
    }

    // Retrieve the data attributes from the hidden div
    const enableDebug = vhpParameterDiv.getAttribute('data-debug') === '1';
    const thumbnailSelector = vhpParameterDiv.getAttribute('data-thumbnail-selector');
    if (!thumbnailSelector) {
        console.error('Thumbnail selector not provided');
        return;
    }

    // Debugging log function
    function logDebug(message, data = null) {
        if (enableDebug) {
            if (data) {
                console.log(`[VHP Debug] ${message}:`, data);
            } else {
                console.log(`[VHP Debug] ${message}`);
            }
        }
    }

    // Check browser video support
    function checkBrowserSupport() {
        const video = document.createElement('video');
        if (!video.canPlayType) {
            logDebug('Browser does not support HTML5 video');
            return false;
        }
        
        // Check MP4 support
        const canPlayMP4 = video.canPlayType('video/mp4; codecs="avc1.42E01E, mp4a.40.2"');
        if (canPlayMP4 === "") {
            logDebug('Browser does not support MP4 video');
            return false;
        }

        return true;
    }

    // Only proceed if browser supports video
    if (!checkBrowserSupport()) {
        return;
    }

    const thumbnails = document.querySelectorAll(thumbnailSelector);
    
    if (thumbnails.length > 0) {
        logDebug('Found thumbnails:', thumbnails.length);

        // Create style element for hover video
        const style = document.createElement('style');
        style.textContent = `
            .hover-video {
                position: absolute;
                display: none;
                z-index: 1000;
                pointer-events: none;
                object-fit: cover;
                max-width: 100vw;
                max-height: 100vh;
                backface-visibility: hidden;
                -webkit-backface-visibility: hidden;
                transform: translateZ(0);
                -webkit-transform: translateZ(0);
            }
            
            .hover-video::-webkit-media-controls {
                display: none !important;
            }
            
            .hover-video::-webkit-media-controls-enclosure {
                display: none !important;
            }
        `;
        document.head.appendChild(style);

        // Create video element
        const hoverVideo = document.createElement('video');
        hoverVideo.id = 'hover-video';
        hoverVideo.classList.add('hover-video');
        hoverVideo.muted = true;
        hoverVideo.loop = true;
        hoverVideo.preload = 'metadata';
        hoverVideo.playsInline = true;
        hoverVideo.setAttribute('playsinline', '');
        hoverVideo.setAttribute('webkit-playsinline', '');

        // Add error handling for video element
        hoverVideo.addEventListener('error', (e) => {
            const error = e.target.error;
            logDebug('Video error:', {
                code: error.code,
                message: error.message,
                currentSrc: hoverVideo.currentSrc
            });
        });

        document.body.appendChild(hoverVideo);

        let hoverTimeout;
        let currentVideoUrl = '';
        let isVideoPlaying = false;

        // Helper function to check if element is in viewport
        function isInViewport(element) {
            const rect = element.getBoundingClientRect();
            return (
                rect.top >= 0 &&
                rect.left >= 0 &&
                rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
                rect.right <= (window.innerWidth || document.documentElement.clientWidth)
            );
        }

        // Function to handle video positioning and display
        function positionVideo(thumbnail) {
            const rect = thumbnail.getBoundingClientRect();
            const thumbnailStyle = window.getComputedStyle(thumbnail);
            const borderRadius = thumbnailStyle.getPropertyValue('border-radius');

            hoverVideo.style.borderRadius = borderRadius;
            hoverVideo.style.display = 'block';
            hoverVideo.style.top = `${window.scrollY + rect.top}px`;
            hoverVideo.style.left = `${rect.left}px`;
            hoverVideo.style.width = `${rect.width}px`;
            hoverVideo.style.height = `${rect.height}px`;
        }

        // Function to start video playback
        function playVideo(videoUrl) {
            if (currentVideoUrl !== videoUrl) {
                currentVideoUrl = videoUrl;
                hoverVideo.src = videoUrl;
                hoverVideo.load();
            }

            const playPromise = hoverVideo.play();
            if (playPromise !== undefined) {
                playPromise
                    .then(() => {
                        isVideoPlaying = true;
                        logDebug('Video playing successfully');
                    })
                    .catch(error => {
                        isVideoPlaying = false;
                        logDebug('Error playing video:', error);
                    });
            }
        }

        // Function to stop video playback
        function stopVideo() {
            hoverVideo.style.display = 'none';
            if (isVideoPlaying) {
                hoverVideo.pause();
                hoverVideo.currentTime = 0;
                isVideoPlaying = false;
            }
        }

        // Add event listeners to each thumbnail
        thumbnails.forEach(thumbnail => {
            thumbnail.addEventListener('mouseenter', (event) => {
                logDebug('Mouse entered thumbnail');
                
                if (!isInViewport(thumbnail)) {
                    logDebug('Thumbnail not in viewport, skipping video');
                    return;
                }

                const anchor = thumbnail.closest('a');
                if (!anchor) {
                    logDebug('No parent anchor found');
                    return;
                }

                const url = anchor.href;
                const slug = url.replace(/\/$/, "").split('/').pop();
                logDebug('Extracted slug:', slug);

                // Check if video exists
                const xhr = new XMLHttpRequest();
                xhr.open('POST', '/content/plugins/video-hover-play/ajax-handler.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            logDebug('AJAX Response:', response);

                            if (response.status === 'exist') {
                                clearTimeout(hoverTimeout); // Clear any existing timeout
                                hoverTimeout = setTimeout(() => {
                                    logDebug('Starting video playback');
                                    positionVideo(thumbnail);
                                    playVideo(`/files/videos/${slug}.mp4`);
                                }, 1000);
                            } else {
                                logDebug('Video does not exist for slug:', slug);
                            }
                        } catch (e) {
                            logDebug('Error parsing AJAX response:', e);
                        }
                    } else {
                        logDebug('AJAX request failed:', xhr.status);
                    }
                };

                xhr.onerror = function() {
                    logDebug('AJAX request error');
                };

                xhr.send(`action=check_video&video_name=${encodeURIComponent(slug)}`);
            });

            thumbnail.addEventListener('mouseleave', () => {
                logDebug('Mouse left thumbnail');
                clearTimeout(hoverTimeout);
                stopVideo();
            });
        });

        // Handle scroll events to reposition video if needed
        let scrollTimeout;
        window.addEventListener('scroll', () => {
            if (hoverVideo.style.display === 'block') {
                clearTimeout(scrollTimeout);
                hoverVideo.style.display = 'none';
                
                scrollTimeout = setTimeout(() => {
                    const visibleThumbnail = Array.from(thumbnails).find(thumb => 
                        thumb.matches(':hover') && isInViewport(thumb)
                    );
                    
                    if (visibleThumbnail) {
                        positionVideo(visibleThumbnail);
                    }
                }, 150);
            }
        }, { passive: true });

        // Handle window resize
        let resizeTimeout;
        window.addEventListener('resize', () => {
            if (hoverVideo.style.display === 'block') {
                clearTimeout(resizeTimeout);
                hoverVideo.style.display = 'none';
                
                resizeTimeout = setTimeout(() => {
                    const visibleThumbnail = Array.from(thumbnails).find(thumb => 
                        thumb.matches(':hover') && isInViewport(thumb)
                    );
                    
                    if (visibleThumbnail) {
                        positionVideo(visibleThumbnail);
                    }
                }, 150);
            }
        }, { passive: true });
    } else {
        logDebug('No thumbnails found with selector:', thumbnailSelector);
    }
});