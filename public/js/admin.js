// Jagoan Kue — Admin Panel JS

function openSidebar() {
    document.getElementById('sidebar')?.classList.add('open');
    document.getElementById('sidebarOverlay')?.classList.add('active');
}

function closeSidebar() {
    document.getElementById('sidebar')?.classList.remove('open');
    document.getElementById('sidebarOverlay')?.classList.remove('active');
}

function toggleNotifPanel() {
    const p = document.getElementById('notifPanel');
    if (p) {
        p.classList.toggle('hidden');
    }
}

function markRead(id) {
    fetch('/admin/notifications/' + id + '/read', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '' }
    });
}

document.addEventListener('click', function(e) {
    const panel = document.getElementById('notifPanel');
    if (panel && !panel.contains(e.target) && !e.target.closest('[onclick="toggleNotifPanel()"]')) {
        panel.classList.add('hidden');
    }
});
