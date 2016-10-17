/**
 * Created by snark on 8/7/15.
 */

(function ($) {
    $.Customernotes = {
        localization: null,
        waAppUrl: null,
        waUrl: null,
        init: function () {
            jQuery.ajaxSetup({ cache:false });
//            $.Customernotes.rateObject.rate($('.rate'));

            var saveButton = $('#customernotesAddNoteButton').click(function() {
                var form = $('#customernotesAddNote').serialize();

                $.post('?plugin=customernotes&action=addnote', form, function (d) {
                    if (parseInt(d.data.note.order_id) > 0) {
                        $('.customernotesNoteLi').each(function() {
                                if ($(this).data('noteid') == d.data.note.id) {
                                    $(this).html(d.data.note_template);
                                }
                            }
                        );
                        $(saveButton).addClass('green');
                    }
                }, 'json');
            });

            $('.s-toggle-status').iButton( { labelOn : "", labelOff : "", className: 'mini' } ).change(function() {
                var self = $(this);
                var enabled = self.is(':checked');
                if (enabled) {
                    $( "#customernotesContent" ).hide(200);
                } else {
                    $( "#customernotesContent" ).show(200);

                }
            });
        },
        rateObject: {
            urlRate : '?plugin=customernotes&action=rate',
            rate : function(e) {
//                obj.on('click', function(e) {
                    var thisObj = $(e);
                    var thisType = thisObj.hasClass('rateUp') ? 'up' : 'down';
                    var thisOrder = thisObj.attr('data-order');
                    jQuery.getJSON($.Customernotes.rateObject.urlRate, { type : thisType, order_id : thisOrder }, function(d) {
                        if (!d.error) {
                            $($('.customernotesNoteLi[data-noteid=' + d.data.note.id + ']')).html(d.data.note_template);
                            $('span.rateUpN').html(parseInt(d.data.note.up, 10));
                            $('span.rateDownN').html(parseInt(d.data.note.down, 10));
                            $('span.rate').removeClass('active');
                            thisObj.addClass('active');
                        }
                    });
//                    e.preventDefault();
//                });
            }
        },
        deleteNote: function(e) {
            var li = $(e).closest('li');
            var note_id = li.find('a').data('note_id')
            $.post('?plugin=customernotes&action=deletenote', { note_id : note_id }, function (d) {
                $('#customernotesForm').html(d.data.form_template);
                li.remove();
            }, 'json');
        }
    }
})(jQuery);

