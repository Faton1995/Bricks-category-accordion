jQuery(document).ready(function($) {
    $('.category-accordion').each(function() {
        var $accordion = $(this);
        var closedIcon = $accordion.data('icon-closed') || 'fa-solid fa-chevron-right';
        var openIcon = $accordion.data('icon-open') || 'fa-solid fa-chevron-down';

        $accordion.find('li.category-item > a').each(function() {
            var $link = $(this);
            if ($link.siblings('ul').length > 0) {
                $link.append(' <i class="toggle-icon ' + closedIcon + '"></i>');
                $link.on('click', function(e) {
                    e.preventDefault();
                    var $parentLi = $(this).parent('li');
                    $parentLi.toggleClass('open');
                    var $icon = $(this).find('.toggle-icon');
                    if ($parentLi.hasClass('open')) {
                        $icon.removeClass(closedIcon).addClass(openIcon);
                    } else {
                        $icon.removeClass(openIcon).addClass(closedIcon);
                    }
                });
            }
        });
    });
});