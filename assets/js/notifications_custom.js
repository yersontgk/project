document.addEventListener('DOMContentLoaded', () => {
    const closeBtn = document.querySelector('.notification button.close-btn');
    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            const notification = closeBtn.closest('.notification');
            if (notification) {
                notification.style.display = 'none';
            }
        });
    }
});

// Function to create and show a notification with a given message
function showNotification(message) {
    const notificationContainerId = 'notification-container';
    let container = document.getElementById(notificationContainerId);
    if (!container) {
        container = document.createElement('div');
        container.id = notificationContainerId;
        container.style.position = 'fixed';
        container.style.top = '1rem';
        container.style.right = '1rem';
        container.style.zIndex = '9999';
        container.style.display = 'flex';
        container.style.flexDirection = 'column';
        container.style.gap = '0.5rem';
        document.body.appendChild(container);
    }

    const notification = document.createElement('div');
    notification.className = 'notification';
    notification.style.backgroundColor = '#f8d7da';
    notification.style.color = '#721c24';
    notification.style.border = '1px solid #f5c6cb';
    notification.style.padding = '1rem';
    notification.style.borderRadius = '4px';
    notification.style.boxShadow = '0 2px 6px rgba(0,0,0,0.2)';
    notification.style.minWidth = '250px';
    notification.style.position = 'relative';

    const messageElem = document.createElement('div');
    messageElem.textContent = message;
    notification.appendChild(messageElem);

    const closeBtn = document.createElement('button');
    closeBtn.className = 'close-btn';
    closeBtn.textContent = '×';
    closeBtn.style.position = 'absolute';
    closeBtn.style.top = '0.2rem';
    closeBtn.style.right = '0.5rem';
    closeBtn.style.background = 'transparent';
    closeBtn.style.border = 'none';
    closeBtn.style.fontSize = '1.2rem';
    closeBtn.style.cursor = 'pointer';
    closeBtn.style.color = '#721c24';

    closeBtn.addEventListener('click', () => {
        container.removeChild(notification);
        if (container.childElementCount === 0) {
            container.remove();
        }
    });

    notification.appendChild(closeBtn);
    container.appendChild(notification);
}
