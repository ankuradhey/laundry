/*jshint undef: false */

var isResponsive = function() {
    return ($(window).width() < 940);
};

// Initialize Foundation JS
$(document).foundation();

// Initialize Modernizr
Modernizr.load([{
    test: Modernizr.mq('only all'), // Test if the browser understands Media Queries?
    nope: 'js/vendor/respond.js' // If not, load the respond.js file
}]);

// Run only in bigger screens
if (!isResponsive()) {
    $(document).ready(function() {
        // Initialize Magnific Popup
        $('.popup').each(function() {
            $(this).magnificPopup({
                delegate: 'a',
                type: 'image',
                gallery: {
                    enabled: true
                }
            });
        });
    });

    // On scroll
    var $backTop, $parallax;
    $(document).on('scroll', function() {
        if (!isResponsive()) {
            var $el = $(this);
            $backTop($el);
            $parallax($el);
        }
    });
    $backTop = function($el) {
        if ($el.scrollTop() >= 200) {
            $('#back-top').stop().fadeTo(0, 1);
        } else {
            $('#back-top').stop().fadeTo(0, 0, function() {
                $(this).hide();
            });
        }
    };
    $parallax = function($el) {
        $('#hero').css('backgroundPosition', 'center ' + $(window).scrollTop() / 3 + 'px');

        $('#hero-message').removeClass('fadeInDown');
        if ($el.scrollTop() >= 250) {
            $('#hero-message').removeClass('fadeInUp').addClass('fadeOutDown');
        } else if ($el.scrollTop() < 250 && $('#hero-message').hasClass('fadeOutDown')) {
            $('#hero-message').removeClass('fadeOutDown').addClass('fadeInUp');
        }
    };

    // Popup image overlay
    $(document).on('mouseenter', '.popup a img', function() {
        if (!isResponsive()) {
            $(this).before('<div class="popup-overlay"><i class="icon-zoom-in"></i></div>');

            var $overlay = $('.popup-overlay');
            $overlay.stop(true, true).fadeIn(300);

            var $overlayHeight = $(this).height(),
                $icon = $overlay.find('i');
            $overlay.css('fontSize', $overlayHeight * 0.2);
            $icon.css('top', ($overlayHeight / 2) - ($icon.height() / 2));
        }
    });
    $(document).on('mouseleave', '.popup a ', function() {
        $('.popup-overlay').stop(true, true).fadeOut(150, function() {
            $(this).remove();
        });
    });

    // Back to top
    $(document).on('click', '#back-top', function(e) {
        e.preventDefault();
        $('html,body').animate({
            scrollTop: 0
        }, 600);
    });
}
