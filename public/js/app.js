function toggleSidebar() {
    const sidebar   = document.getElementById('sidebar');
    const mainArea  = document.querySelector('.main-area');
    const isMobile  = window.innerWidth <= 768;
 
    if (isMobile) {
        sidebar.classList.toggle('sidebar--open');
    } else {
        const hidden = sidebar.classList.toggle('sidebar--hidden');
        mainArea.style.marginLeft = hidden ? '0' : 'var(--sidebar-w)';
    }
}