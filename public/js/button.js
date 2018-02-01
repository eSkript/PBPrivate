(function ($) {
    $( document ).ready(function() {
        if(PBPrivate.is_on){
            $(".a11y-toolbar ul").append('<li><a href="'+PBPrivate.hide_url+'" role="button" title="hide private sections"><span class="dashicons dashicons-unlock"></span></a></li>');
        }else {
            $(".a11y-toolbar ul").append('<li><a href="'+PBPrivate.show_url+'" role="button" title="show private sections"><span class="dashicons dashicons-lock"></span></a></li>');
        }
    });
})(jQuery);