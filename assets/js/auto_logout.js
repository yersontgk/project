(function() {
    // Use navigator.sendBeacon if available for reliable unload requests
    function sendLogout() {
        var url = '../views/logout.php';
        if (navigator.sendBeacon) {
            navigator.sendBeacon(url);
        } else {
            // Fallback to synchronous XMLHttpRequest
            var xhr = new XMLHttpRequest();
            xhr.open('GET', url, false); // false for synchronous request
            try {
                xhr.send(null);
            } catch (e) {
                // Ignore errors
            }
        }
    }

    // Flag to detect if the page is being unloaded due to navigation within the site
    let isInternalNavigation = false;

    // Listen for clicks on internal links to set the flag
    document.addEventListener('click', function(event) {
        let target = event.target;
        while (target && target.tagName !== 'A') {
            target = target.parentElement;
        }
        if (target && target.tagName === 'A') {
            const href = target.getAttribute('href');
            if (href && !href.startsWith('http') && !href.startsWith('https') && !href.startsWith('mailto:') && !href.startsWith('#')) {
                isInternalNavigation = true;
            }
        }
    });

    window.addEventListener('beforeunload', function(event) {
        if (!isInternalNavigation) {
            sendLogout();
        }
    });
})();
