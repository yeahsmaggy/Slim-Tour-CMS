jQuery(document).ready(function($) {

    $('.carousel').carousel({
        interval: 5000 //changes the speed
    })



    $('.carousel').bcSwipe({
        threshold: 50
    });


    $("#tabs").tabs({
        activate: function(event, ui) {

            var target = event.currentTarget.innerText;
            if (target == 'Jeep') {
                $('#tour-type-quote').replaceWith('<div id="tour-type-quote"><blockquote>Unusual landscapes, Georgian wine and food, ancient fortresses and monasteries, cave complexes and old towns, all this is waiting for you.</blockquote></div>');
            } else if (target == 'Hiking') {
                $('#tour-type-quote').replaceWith('<div id="tour-type-quote"><blockquote>You may think you have trekked it all - Patagonia, the Alps, Nepal but we\'ve got a hidden secret that will fill you with wonder. This isn\'t Georgia in the US, but on the limits of Europe. Magnificent white water weaves it way from Glacier-topped peaks into lush steep-sided valleys, scattered with historical villages. Meet friendly locals who welcome us with open arms. Few outfitters offer trips to the Caucasus but we can guarantee that ours is the best option. No one does it as well as us.</blockquote></div>');
            } else if (target == 'Mountain Biking') {
                $('#tour-type-quote').replaceWith('<div id="tour-type-quote"><blockquote>Whether you are a regular sport cyclist, commuter, enthusiast or infrequent leisure cyclist, we have mountain bike and bicycle tours for all abilities and backgrounds. Our mountain bike and bicycle tours are ideal for the more adventurous who want to explore or those who want to have a gentle, calm and relaxing ride amidst nature back in time to explore the sights.</blockquote></div>');
            } else if (target == 'Trail Running') {
                $('#tour-type-quote').replaceWith('<div id="tour-type-quote"><blockquote>The unlimited supply of wonderful off road trails are almost tailor made for running.</blockquote></div>');
            }
        }
    });
    //hide all of the elements
    function InOut(elem) {
        elem.delay(1000)
            .fadeIn(1000)
            .delay(2000)
            .fadeOut(1000,
                function() {
                    if (elem.next().length > 0) {
                        InOut(elem.next());
                    } else {
                        InOut(elem.siblings(':first'));
                    }
                }
        );
    }
    $('.jumbotron>h1').hide();
    InOut($('.jumbotron>h1:first'));
    $("#slider .slide div").show();
    $("#start_date").datepicker();
    $("#end_date").datepicker();
    //--image gallery--//
    $("a[rel='colorbox']").colorbox({
        maxWidth: "90%",
        maxHeight: "90%",
        opacity: ".5",
    });


    $(".navbar-toggle").click(function(event) {
        $(".navbar-collapse").toggle('in');
    });

    imagesLoaded('#grid', function(instance) {
        console.log('all images are loaded');

        //shuffle
        var $grid = $('#grid'),
            $sizer = $grid.find('.shuffle__sizer');
        $grid.shuffle({
            itemSelector: '.picture-item',
            sizer: $sizer
        });


        $('#sub-bike').hide();
        $('.sort-options').on('change', function() {
            var sort = this.value.toLowerCase();
            if (sort == 'bike') {
                $('#sub-bike').show();
            } else {
                $('#sub-bike').hide();
            }
            $grid.shuffle('shuffle', function($el, shuffle) {
                // console.log(sort);
                if (sort == '') {
                    return $el.data('type');
                } else {
                    return $el.data('type') === sort;
                }
            });
        });
        $('#sub-bike').on('change', function() {
            var sort = this.value.toLowerCase();
            $grid.shuffle('shuffle', function($el, shuffle) {
                // console.log(sort);
                if (sort == '') {
                    return $el.data('type') === 'bike';
                } else {
                    return ($el.data('subtype') === sort && $el.data('type') === 'bike');
                }
            });
        });



    });







    //tabs
    $("#tabs").tabs();
    //hide all of the elements
    function InOut(elem) {
        elem.delay(1000)
            .fadeIn(1000)
            .delay(2000)
            .fadeOut(1000,
                function() {
                    if (elem.next().length > 0) {
                        InOut(elem.next());
                    } else {
                        InOut(elem.siblings(':first'));
                    }
                }
        );
    }

    // $("#slider").carouFredSel({
    // responsive  : true,
    // scroll      : {
    // fx          : "crossfade",
    // duration  : 2000
    // },
    // items       : {
    // visible     : 1,
    // width : 870,
    // height : "50%",
    // },
    // Handler for .ready() called.
    //});
    // $("#testimonials").carouFredSel({
    // //responsive  : false,
    // scroll      : {
    //     fx          : "crossfade"
    // },
    // // items       : {
    // //   visible     : 1,
    // //   width : 400,
    // //   height : "46%",
    // // },
    // // Handler for .ready() called.
    // });

    var texts = [
        'Be astonished by the beauty of the Caucasus Mountains',
        'Visit unspoilt remote and ancient villages',
        'Explore unchartered trails',
        'Feel the warm, friendly culture',
        'Be exhilarated by the descents',
        'Taste authentic cuisine and wine',
    ];
    $(window).on("backstretch.show", function(e, instance) {
        $(".overlay").fadeIn('slow');
        $(".overlay").text(texts[instance.index]);
        setTimeout("$('.overlay').fadeOut('slow');", 3000);
    });

});