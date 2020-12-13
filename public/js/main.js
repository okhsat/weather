/**
 * Main scripts.
 */

(function (global, $) {
    'use strict';

    var $win = $(window);
    var $doc = $(document);
    var $html = $('html');
    var $body = $('body');
    var $wrapper = $('.c-site-wrapper');
    var $inner = $('.c-site-inner');
    var $header = $('.c-site-header');

    $doc.ready(function () {

        // get target bt element
        var getTargetByElement = function (selector) {
            var href;
            var $this = $(selector).first();
            return $($this.attr('data-target') || (href = $this.attr('href')) && href.replace(/.*(?=#[^\s]+$)/, '')); // strip for ie7
        };

        if($.isFunction($.fn.slick)) {

            // slider api
            $('[data-ride="slider"]').each(function () {
                $(this).slick({
                    slide: '.c-slider__item',
                    fade: false,
                    dots: true,
                    arrows: false,
                    infinite: true,
                    speed: 600,
                    slidesToShow: 1,
                    adaptiveHeight: true
                });
            });

            // home slider
            $('#home-slider').each(function () {
                $(this).slick({
                    slide: '.c-slider__item',
                    autoplay: true,
                    autoplaySpeed: 8000,
                    dots: false,
                    // appendDots: '#home-slider-dots',
                    arrows: false,
                    infinite: true,
                    speed: 600,
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    responsive: [
                        {
                            breakpoint: 991,
                            settings: {
                                fade: false
                            }
                        }
                    ]
                });
            });

            // home slider
            $('#testimonials-slider').each(function () {
                $(this).slick({
                    slide: '.c-slider__item',
                    dots: true,
                    appendDots: '#testimonials-slider-dots',
                    arrows: false,
                    infinite: true,
                    speed: 600,
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    responsive: [
                        {
                            breakpoint: 991,
                            settings: {
                                fade: false
                            }
                        }
                    ]
                });
            });

            // home icons carousel
            $('#home-icons-carousel').each(function () {
                $(this).slick({
                    slide: '.c-slider__item',
                    dots: true,
                    appendDots: '#home-icons-carousel-dots',
                    arrows: false,
                    infinite: true,
                    speed: 600,
                    slidesToShow: 3,
                    slidesToScroll: 3,
                    responsive: [
                        {
                            breakpoint: 1023,
                            settings: {
                                slidesToShow: 1,
                                slidesToScroll: 1
                            }
                        }
                    ]
                });
            });

            // footer boxes carousel
            $('#footer-boxes-carousel').each(function () {
                $(this).slick({
                    slide: '.c-slider__item',
                    dots: true,
                    appendDots: '#footer-boxes-carousel-dots',
                    arrows: false,
                    infinite: true,
                    speed: 600,
                    slidesToShow: 3,
                    slidesToScroll: 3,
                    responsive: [
                        {
                            breakpoint: 1023,
                            settings: {
                                slidesToShow: 2,
                                slidesToScroll: 1
                            }
                        },
                        {
                            breakpoint: 767,
                            settings: {
                                slidesToShow: 1,
                                slidesToScroll: 1
                            }
                        },
                    ]
                });
            });

        }

        // next-slide api
        (function(){
            var handleClick = function(e){
                var href;
                var $this = $(this);
                var $target = getTargetByElement($this);
                if($this.is('a')) e.preventDefault();
                if($target.length > 0) $target.slick($this.is('[data-toggle="next-slide"]') ? 'slickNext' : 'slickPrev');
            };
            $doc.on('click', '[data-toggle="next-slide"]', handleClick);
            $doc.on('click', '[data-toggle="prev-slide"]', handleClick);
        })();

        var scrollTo = function(selector){
            $([document.documentElement, document.body]).animate({
                scrollTop: $(selector).offset().top
            }, 800, 'easeOutExpo');
        };

        // next section link
        $doc.on('click', '[data-toggle="nex-section"]', function(e){
            e.preventDefault();

            var $this = $(this);
            var $section = $this.closest('.c-section');
            var $target = $section.next('.c-section');
            if($target.length > 0) {
                scrollTo($target);
            }
        });

        $body.magnificPopup(
            {
                delegate: '.js-video-lightbox',
                // modal: true,
                type: 'iframe',

                removalDelay: 300,
                mainClass: 'mfp-adimadim',

                preloader: false
            }
        );

        // modal triggers
        $('.modal').each(function(){
            var $this = $(this);
            var id = $this.attr('id');
            var $trigger = $('<a class="u-hidden js-modal-trigger" data-toggle="modal" href="#'+id+'"></a>');
            if(id) {
                $trigger.prependTo($this);
                $this.on('show.adimadim.modal', function(){
                    $trigger.trigger('click');
                });
                $this.on('hide.adimadim.modal', function(){
                    $.magnificPopup.close();
                });
            }
        });

        // data-toggle="modal" api
        $('[data-toggle="modal"]')
            .each(function(){
                var $modal = $(this);
                var defaults = {
                    modal: true,
                    type: 'inline',

                    removalDelay: 300,
                    mainClass: 'mfp-adimadim',

                    preloader: false
                };
                var data = $modal.data();
                var options = $.extend({}, defaults, data);
                $modal
                    .magnificPopup(options)
                    .on('mfpOpen', function (e) {
                        $html.addClass('html--lock');

                        // prevent page vertical jump
                        // $body.css('margin-right', scrollbarWidth);
                    })
                    .on('mfpBeforeClose', function (e) {
                        $html.addClass('html--unlocking');
                    })
                    .on('mfpClose', function (e) {
                        $html.removeClass('html--lock html--unlocking');

                        // reset body scrollbar margin
                        // $body.css('margin-right', '');
                    })
                ;
            });

        $doc.on('click', '[data-dismiss="modal"]', function(){
            $(this).closest('.modal').trigger('hide');
        });

        // close modal on click to blank area of modal
        $doc.on('click', function(e){
            var $target = $(e.target);
            if($target.is('.modal')) {
                $.magnificPopup.close();
            }
        });

        /**
         * Off Canvas
         */
        (function() {

            var bodyEl = document.body,
                content = document.querySelector( '.c-site-wrapper' ),
                openbtn = document.getElementById( 'open-off-canvas' ),
                closebtn = document.getElementById( 'c-off-canvas__close' ),
                isOpen = false,

                morphEl = document.getElementById( 'c-off-canvas__morph-shape' ),
                s = Snap( morphEl.querySelector( 'svg' ) );
            var path = s.select( 'path' );
            var initialPath = path.attr('d'),
                steps = morphEl.getAttribute( 'data-morph-open' ).split(';');
            var stepsTotal = steps.length;
            var isAnimating = false;

            function init() {
                initEvents();
            }

            function initEvents() {
                openbtn.addEventListener( 'click', toggleMenu );
                if( closebtn ) {
                    closebtn.addEventListener( 'click', toggleMenu );
                }

                // close the menu element if the target it´s not the menu element or one of its descendants..
                content.addEventListener( 'click', function(ev) {
                    var target = ev.target;
                    if( isOpen && target !== openbtn && $(target).closest('.c-off-canvas').length === 0 ) {
                        toggleMenu();
                    }
                } );
            }

            var scrollTop;
            var scrollTopClasses = ['is-scrolled', 'is-scrolled--ready', 'is-scrolled--to-top'];
            var scrollTopClassesBackup = [];
            function saveScrollTop(){
                scrollTop = $win.scrollTop();
                $.each(scrollTopClasses, function(i ,v){
                    if($html.is('.' + v)) {
                        scrollTopClassesBackup.push(v);
                    }
                });
            }
            function repairScrollTop(){
                setTimeout(function(){
                    $win.scrollTop(scrollTop);
                    setTimeout(function(){
                        $html.addClass(scrollTopClassesBackup.join(' '));
                    }, 300);
                }, 10);
            }

            function toggleMenu() {
                if( isAnimating ) return false;
                isAnimating = true;
                if( isOpen ) {
                    classie.remove( bodyEl, 'show-off-canvas' );
                    repairScrollTop();
                    // animate path
                    setTimeout( function() {
                        // reset path
                        path.attr( 'd', initialPath );
                        isAnimating = false;
                    }, 300 );
                }
                else {
                    saveScrollTop();
                    classie.add( bodyEl, 'show-off-canvas' );
                    // animate path
                    var pos = 0,
                        nextStep = function( pos ) {
                            if( pos > stepsTotal - 1 ) {
                                isAnimating = false;
                                return;
                            }
                            path.animate( { 'path' : steps[pos] }, pos === 0 ? 400 : 500, pos === 0 ? mina.easein : mina.elastic, function() { nextStep(pos); } );
                            pos++;
                        };

                    nextStep(pos);
                }
                isOpen = !isOpen;
            }

            init();

        })();

        /**
         * select2
         */
        (function(){
            if(!$.fn.select2) return;

            $.fn.select2.defaults.set("theme", "adimadim");
            //$.fn.select2.defaults.set("language", "tr");

            $('[data-ride="select2"]').each(function () {
                var $this = $(this);
                var $county = $($this.data('county-target'));

                // mobile helper
                var $mobile = $this
                    .clone()
                    .attr({
                        'class': 'select2__mobile-helper ignore',
                        'data-rule-required': 'false'
                    })
                    .removeAttr('id')
                    .removeAttr('name')
                    .removeAttr('data-placeholder')
                    .insertBefore($this);

                $this
                    .select2()
                    .on('focus', function () {
                        if($body.outerWidth() >= 992) {
                            $(this).select2('open');
                        }
                    });

                $this.on("select2:close", function (e) {
                    if($.validator) {
                        $this.valid();
                    }
                });

                $this.on("change", function (e) {
                    $mobile.val($(this).val());

                    if($county.length > 0) {
                        var plaka = Number($(this).val());
                        var matches = [];
                        var fill = function(options){
                            $county
                                .find('option')
                                .not(':first-child')
                                .remove();

                            $county
                                .select2({ data: options });

                            $county.prev('.select2__mobile-helper').html( $county.html() );
                        };

                        if(plaka === '') {
                            fill([]);
                            return;
                        }

                        $county.prop('disabled', true);

                        $.ajax({
                            dataType: "json",
                            url: 'json/counties.json',
                            data: {},
                            success: function(response){
                                if($.isArray(response)) {
                                    $.each(response, function(index, city){
                                        if(city.plaka + '' === plaka + '') {
                                            matches.push({
                                                id: "" + city.id,
                                                text: "" + city.ilce
                                            });
                                        }
                                    });

                                    fill(matches);
                                }
                            },
                            complete: function(){
                                $county.prop('disabled', false);
                            }
                        });
                    }

                });

                $mobile.on("change", function(){
                    $this.val($mobile.val()).trigger('change');
                    if($.validator) {
                        $this.valid();
                    }
                });
            });
        })();

        /**
         * Depend Api
         */

        $('[data-depend-on]').each(function(){
            var $this = $(this);

            var $target = $( $this.data('depend-on') );
            if($target.length == 0) {
                return true; // continue
            }

            var dependValue = $this.data('depend-value');
            var dependClass = $this.data('depend-class');

            var value = $target.val();
            if($target.is('[type=checkbox]') || $target.is('[type=radio]')) {
                if(!$target.is(':checked')) {
                    value = '';
                }
            }

            var $targets = $target;
            if($target.is('[type=radio]')) {
                $targets = $target.closest('form').find('input[name="'+$target.attr('name')+'"]');
            }

            if(dependClass) {
                $this.toggleClass(dependClass, dependValue == value);
            } else {
                $this.toggleClass('u-hidden', dependValue != value);
            }

            var $equalHeightParent = $this.closest('.js-equal-height, [data-equal-height]');

            $targets.on('change.adimadim.dependable', function(e){
                var $input = $(this);
                var inputValue = $input.val();

                if($input.is('[type=checkbox]') || $input.is('[type=radio]')) {
                    if(!$target.is(':checked')) {
                        inputValue = '';
                    }
                }

                console.log('inputValue', inputValue);

                if(dependClass) {
                    $this.toggleClass(dependClass, dependValue == inputValue);
                } else {
                    $this.toggleClass('u-hidden', dependValue != inputValue);
                }

                if($equalHeightParent.length > 0) {
                    $.fn.matchHeight._update();
                }
            });
        });

        /**
         * Next Field Data API
         */

        $doc.on('input', '[data-next-field]', function(){
            var $this = $(this);
            var val = $this.val();
            var group = $this.data('next-field');
            var $all = $('[data-next-field="'+group+'"]')
            var index = $all.index( $this );
            var limit = $this.attr('maxlength') || 4;

            // Go to next
            if(val.length >= limit && $all.eq(index + 1).length > 0) {
                $all.eq(index + 1).focus();
            }

            // Go to prev
            if(val.length == 0 && index > 0 && $all.eq(index - 1).length > 0) {
                $all.eq(index - 1).focus();
            }
        });

        /**
         * Promo Code
         */

        $('.c-promo-code').each(function(){
            var $wrapper = $(this);

            var $step1 = $wrapper.find('.c-promo-code__step--1');
            var $step2 = $wrapper.find('.c-promo-code__step--2');

            var $promoCodeTriger = $wrapper.find('.c-promo-code__trigger');
            var $promoCodeApply = $wrapper.find('.c-promo-code__btn--apply');
            var $promoCodeDelete = $wrapper.find('.c-promo-code__btn--delete');

            var $input = $wrapper.find('.c-promo-code__input');

            $promoCodeTriger.on('click', function(){
                $step1.addClass('u-hidden');
                $step2.removeClass('u-hidden');
                $input.focus();
            });

            $promoCodeApply.on('click', function(){
                if($input.val().length === 0) {
                    return false;
                }

                $input.prop('readonly', true);
                $promoCodeApply.addClass('u-hidden');
                $promoCodeDelete.removeClass('u-hidden');
            });

            $promoCodeDelete.on('click', function(){
                $step1.removeClass('u-hidden');
                $step2.addClass('u-hidden');

                $input.prop('readonly', false).val('');

                $promoCodeApply.removeClass('u-hidden');
                $promoCodeDelete.addClass('u-hidden');
            });
        });

        /**
         * Prevent disabled links
         */

        $doc.on('click', 'a[disabled]', function(e){
            e.preventDefault();
        });

        /**
         * Accordion
         */

        $doc.on('click', '.c-accordion__header', function(){
            var $this = $(this);
            var $item = $this.closest('.c-accordion__item');
            var $body = $item.find('.c-accordion__body');

            if($body.is('is-collapsing')) {
                return false;
            }

            var $orderItems = $item.siblings();
            var $orderBodies = $orderItems.find('.c-accordion__body').filter('.is-in');
            var isActive = $item.is('.is-active');

            $item.toggleClass('is-active', !isActive);
            $body.collapse(!isActive ? 'show' : 'hide');
            if(!isActive) {
                $orderItems.removeClass('is-active');
                $orderBodies.collapse('hide');
            }
        });

        /**
         * Order Step 1
         */

        $('.js-child-form').each(function(){
            var $form = $(this);

            var $day = $form.find('.js-day');
            var $month = $form.find('.js-month');
            var $year = $form.find('.js-year');

            $form.on('change', 'select', function(){
                var isBirthdayValid = !!$day.val() && !!$month.val() && !!$year.val();
                $form.toggleClass('has-valid-birthday', isBirthdayValid);
            });

            $form.on('change', '.js-add-twins', function(){
                $form.toggleClass('has-twins', !!$(this).filter(':checked').val());
            });
        });

        /**
         * State Data API
         */

        $doc.on('click', '[data-toggle-state]', function(e){
            var $this = $(this);
            if($this.is('a')) {
                e.preventDefault();
            }

            var state = $this.data('toggle-state');
            var $wrapper = $this.closest('.o-states');
            var $target = $wrapper.find('[data-state-id="'+state+'"]');
            if($target.length == 0) {
                return;
            }

            $target.addClass('is-active');
            $wrapper.find('[data-state-id]').not($target).removeClass('is-active');
            $target.find('input,select').trigger('change');
        });

        /**
         * Equal Height
         */

        $.fn.matchHeight._maintainScroll = true;

        $('.js-equal-height').matchHeight({
            byRow: true,
            property: 'height',
            target: null,
            remove: false
        });

        var dataEqualHeightGroups = [];
        $('[data-equal-height]').each(function(){
            var $this = $(this);

            var group = $this.data('equal-height');
            if(dataEqualHeightGroups.indexOf(group) !== -1) {
                return true; // continue
            }
            dataEqualHeightGroups.push(group);

            $('[data-equal-height="'+group+'"]').matchHeight({
                byRow: true,
                property: 'height',
                target: null,
                remove: false
            });
        });

        /**
         * Tooltips & Popovers
         */

        $(function () {
            $('[data-toggle="popover"]').popover();
            $('[data-toggle="tooltip"]').tooltip();
        })

        /**
         * Child Selector
         */

        $('.c-child-selector').each(function(){
            var $wrapper = $(this);

            var $editButton = $wrapper.find('.js-edit-child');
            var $addButton = $wrapper.find('.js-add-child');
        });


        /**
         * Box Radios
         */

        $doc.on('click', '.c-box-radios__header', function(e){

            if($(e.target).is('[data-toggle="modal"]') || $(e.target).closest('[data-toggle="modal"]').length > 0) {
                return;
            }

            var $this = $(this);
            var $item = $this.closest('.c-box-radios__item');
            var $body = $item.find('.c-box-radios__body');
            if($body.is('.is-collapsing')) {
                return;
            }

            var $input = $this.find('input:radio');
            var isActive = $input.is(':checked');
            $input.prop('checked', !isActive);
            $item.toggleClass('is-active', !isActive);
            if(!isActive) {
                $body.collapse('show');
                $item.siblings().removeClass('is-active').find('.c-box-radios__body').collapse('hide');
            } else {
                $body.collapse('hide');
            }
        });

        /*
         *   Music Play Func
         */

        $doc.on('click', '[data-play-music]', function(e){
            var $this       = $(this);
            var icon        = $this.find('img');
            var music       = '/music/loops/'+$this.data('play-music')+'.mp3';
            var audio       = document.getElementById('audio');
            var audiofile   = audio.src.split(/[/]+/).pop();
            var aynimuzik   = false;
            var farklimuzik = false;

            if($this.is('a')) {
                e.preventDefault();
            }
            
            if (audiofile != $this.data('play-music')+'.mp3') { 
                farklimuzik = true;
            }

            if (audiofile == $this.data('play-music')+'.mp3') {
                aynimuzik = true;
            }

            if ((farklimuzik == true) && (audio.paused == true)) {
                $('img[src="/img/icon_pause.png"]').attr("src", "/img/icon_play.png");
                icon.attr("src","/img/icon_pause.png");
                audio.setAttribute('src', music);
                audio.play();
                return;
            }

            if ((farklimuzik == true) && (audio.paused == false)) {
                $('img[src="/img/icon_pause.png"]').attr("src", "/img/icon_play.png");
                icon.attr("src","/img/icon_pause.png");
                audio.setAttribute('src', music);
                audio.play();
                return;
            }

            if ((aynimuzik == true) && (audio.paused == false)) {
                $('img[src="/img/icon_pause.png"]').attr("src", "/img/icon_play.png");
                icon.attr("src","/img/icon_play.png");
                audio.pause();
                audio.currentTime = 0
                return;
            }

            if ((aynimuzik == true) && (audio.paused == true)) {
                $('img[src="/img/icon_pause.png"]').attr("src", "/img/icon_play.png");
                icon.attr("src","/img/icon_pause.png");
                audio.setAttribute('src', music);
                audio.play();
                return;
            }              
        });


        $doc.on('click', '[data-down-music]', function(e){
            var $this       = $(this);
            var url         = "/music/parts/"+$this.data('down-music')+".mp3"; 
           
            window.open(url, '_blank');
        });
        
        /**
         * Page scroll events
         */

        var lastScrollTop = 0;
        var lastScrollTopTime;
        var isRelative = $header.css('position') === 'relative';
        $win.on('scroll', function(){
            var headerHeight = $header.height();
            var scrollLimit = headerHeight;
            var scrollTop = $win.scrollTop();
            var isScrolled = scrollTop > scrollLimit;
            var isScrolledToTop = scrollTop < lastScrollTop;
            var isActive = $header.hasClass('c-site-header--sticky') || ($header.hasClass('c-site-header--sticky@mobile') && $body.outerWidth() < 768);

            $html.toggleClass('is-scrolled', isScrolled);
            $html.offsetWidth;
            setTimeout(function(){
                $html.toggleClass('is-scrolled--ready', isScrolled);
            }, 10);

            clearTimeout(lastScrollTopTime);
            lastScrollTopTime = setTimeout(
                function(){
                    $html.toggleClass('is-scrolled--to-top', isScrolled && isScrolledToTop);
                },
                0
            );

            if(isActive) {
                $inner.css('padding-top', isRelative && isScrolled ? headerHeight : '');
            }

            lastScrollTop = scrollTop;
        });



        /**
         * Page js done
         */
        setTimeout(function () {
            // Animations can start
            $html.addClass('js-done');
        }, 300);

    });

})(this, jQuery);