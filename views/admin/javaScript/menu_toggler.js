function toggleMenu(menuId) {
    const menu = document.getElementById(`menu_section${menuId}`);
    const btn = document.getElementById(`expandBtn${menuId}`);

    // Check if the clicked menu is already open
    const isVisible = menu.style.display === 'block';

    // Hide all menus and reset button icons
    document.querySelectorAll('.menu-section').forEach(m => m.style.display = 'none');
    document.querySelectorAll('.expand-btn').forEach(b => b.innerHTML = '<i class="fa-solid fa-chevron-down"></i>');

    // If it was NOT visible, show it; otherwise, leave it hidden
    if (!isVisible) {
        menu.style.display = 'block';
        btn.innerHTML = '<i class="fa-solid fa-chevron-up"></i>';
        btn.setAttribute('title', 'hide menu items');
    }else{
        btn.innerHTML = '<i class="fa-solid fa-chevron-down"></i>';
        btn.setAttribute('title', 'show menu items');
    }
}
