document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');
    const toggleButton = document.querySelector('.sidebar-toggle');
    const menuTexts = document.querySelectorAll('.menu-text');

    function toggleSidebar() {
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('expanded');
        
        if (sidebar.classList.contains('collapsed')) {
            menuTexts.forEach(text => {
                text.style.display = 'none';
            });
        } else {
            setTimeout(() => {
                menuTexts.forEach(text => {
                    text.style.display = 'block';
                });
            }, 100);
        }
    }

    if (toggleButton) {
        toggleButton.addEventListener('click', toggleSidebar);
    }
});