Aloha.ready(function() {
    var $ = Aloha.jQuery;
    $('.editable').aloha();
});
$(document).ready(function() {
    $('#message').delay(3000).fadeOut();
        $(".navbar-toggle").click(function(event) {
            $(".navbar-collapse").toggle('in');
        });
    $("#accordion").accordion({
        collapsible: true
    });
    $("#sortable").sortable({
        key: "sort",
        update: function(event, ui) {
            $("#sortable").sortable();
            var data = $('#sortable').sortable('serialize');
        }
    });
    //show html for aloha editor
    $('#showtextarea').change(function() {
        $('#pagebody').toggle();
    });
    //copy contents of hidden textarea to aloha div for save
    $('#pagebody').bind('input propertychange', function() {
        $('#pagebody-aloha').html(this.value)
    });
    $('#add-itin').click(function() {
        //we select the box clone it and insert it after the box
        if ($('#itinerary article:last-of-type') != null) {
            $('<article><button class="remove" type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-minus"></span></button><input style="width:80%;" class="itinitems" id="" type="text" value="" name="itin[]"  autocomplete="off"></input><br></article>').appendTo($('#itinerary'));
        } else {
            $('#itinerary article:last-of-type').clone().insertAfter("#itinerary article:last-of-type");
        }
    });
    $('#add-tour').click(function() {
        //we select the box clone it and insert it after the box
        if ($('#tour_facts article:last-of-type') != null) {
            $('<article><button class="remove" type="button" class="btn btn-default btn-sm"><span class="glyphicon glyphicon-minus"></span></button><input style="width:80%;" class="itinitems" id="" type="text" value="" name="tour_facts[]"  autocomplete="off"></input><br></article>').appendTo($('#tour_facts'));
        } else {
            $('#tour_facts article:last-of-type').clone().insertAfter("#tour_facts article:last-of-type");
        }
    });

    $("#itinerary").on("click", ".remove", function() {
        $(this).parent().remove();
    });
    $("#tour_facts").on("click", ".remove", function() {
        $(this).parent().remove();
    });
    $('input[type=text][title],input[type=password][title],textarea[title]').each(function(i) {
        $(this).addClass('input-prompt-' + i);
        var promptSpan = $('<span class="input-prompt"/>');
        $(promptSpan).attr('id', 'input-prompt-' + i);
        $(promptSpan).append($(this).attr('title'));
        $(promptSpan).click(function() {
            $(this).hide();
            $('.' + $(this).attr('id')).focus();
        });
        if ($(this).val() != '') {
            $(promptSpan).hide();
        }
        $(this).before(promptSpan);
        $(this).focus(function() {
            $('#input-prompt-' + i).hide();
        });
        $(this).blur(function() {
            if ($(this).val() == '') {
                $('#input-prompt-' + i).show();
            }
        });
    });
});