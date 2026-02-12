// Remove hover-to-open behavior so dropdowns open only via click (Bootstrap default)
$(document).ready(function(){
    // Ensure any leftover hover handlers are not active
    $(".nav-item.dropdown").off('mouseenter mouseleave');

    // Close open dropdown when clicking outside (uses Bootstrap dropdown API)
    $(document).on('click', function(e){
        var openMenu = document.querySelector('.dropdown-menu.show');
        if(openMenu && !e.target.closest('.dropdown')){
            var toggle = openMenu.closest('.dropdown').querySelector('[data-bs-toggle="dropdown"]');
            if(toggle){
                var dd = bootstrap.Dropdown.getInstance(toggle);
                if(dd) dd.hide();
            }
        }
    });
});