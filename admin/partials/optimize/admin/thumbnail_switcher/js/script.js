(function($) {
    
    "use strict";
    
    if( typeof ts_ets === 'undefined' ) {
        window.ts_ets = {};
        ts_ets.upload_frame = false;
    }
    
    $(document).on( 'click', 'button.ts-ets-remove', function() {
        
        ts_ets.tmp_id = $(this).data('id');
        ts_ets.tmp_parent = $(this).closest('td.ts-ets-option');
        
        if( !confirm( ets_strings.confirm ) ) {
            return;
        }
        
        $.ajax({
            url: ajaxurl,
            method: "POST",
            data: {
                action: 'ts_ets_remove',
                nonce: $('#ts_ets_nonce').val(),
                post_id: ts_ets.tmp_id
            },
            success: function( data ) {
                if( data != '' ) {
                    ts_ets.tmp_parent.html( data );
                }
            }
        });
        
    });
    
    $(document).ready(function() {
        
        ts_ets.upload_frame = wp.media({
            title: ets_strings.upload_title,
            button: {
                text: ets_strings.upload_add,
            },
            multiple: false
        });

        ts_ets.upload_frame.on( 'select', function() {

            ts_ets.selection = ts_ets.upload_frame.state().get('selection');
            
            ts_ets.selection.map( function( attachment ) {
                if( attachment.id ) {
                    $.ajax({
                        url: ajaxurl,
                        method: "POST",
                        data: {
                            action: 'ts_ets_update',
                            nonce: $('#ts_ets_nonce').val(),
                            post_id: ts_ets.tmp_id,
                            thumb_id: attachment.id
                        },
                        success: function( data ) {
                            if( data != '' ) {
                                ts_ets.tmp_parent.html( data );
                            }
                        }
                    });
                }
            });
            
        });
        
    });
    
    $(document).on( 'click', 'button.ts-ets-add', function(e) {
        
        e.preventDefault();
        
        ts_ets.tmp_id = $(this).data('id');
        ts_ets.tmp_parent = $(this).closest('td.ts-ets-option');
        
        if( ts_ets.upload_frame ) {
            ts_ets.upload_frame.open();
        }
        
    });
    
})(jQuery);