document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar')

    const closer = document.getElementById('sidebar_closer');
    const expander = document.getElementById('sidebar_expander');

    //check if side bar is hidden
    if (sidebar.classList.contains('hidden')) {
            expander.style.display = 'inline-block';
        } else {
            expander.style.display = 'none';
        }

    // Toggle sidebar visibility on button click
    closer.addEventListener('click', function() {
        sidebar.classList.toggle('hidden');
        if (sidebar.classList.contains('hidden')) {
            expander.style.display = 'inline-block';
        } else {
            expander.style.display = 'none';
        }
    });

    expander.addEventListener('click', function() {
        sidebar.classList.toggle('hidden');
        if (sidebar.classList.contains('hidden')) {
            expander.style.display = 'inline-block';
        } else {
            expander.style.display = 'none';
        }
    });
});