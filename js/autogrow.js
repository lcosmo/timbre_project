(function($) {

    /*
     * Auto-growing textareas; technique ripped from Facebook
     */
    $.fn.autogrow = function(options) {
        
        this.filter('textarea').each(function() {
            
            var $this       = $(this),
                minHeight   = $this.height(),
                lineHeight  = $this.css('lineHeight');
            
            var update = function() {
                
            
                var shadow = $('<div></div>').css({
                    position:   'absolute',
                    top:        -100000,
                    left:       -100000,
                    width:      $(this).width(),
                    fontSize:   $this.css('fontSize'),
                    fontFamily: $this.css('fontFamily'),
                    lineHeight: $this.css('lineHeight'),
                    resize:     'none'
                }).appendTo(document.body);

                var val =$(this).val().replace(/</g, '&lt;')
                                    .replace(/>/g, '&gt;')
                                    .replace(/&/g, '&amp;')
                                    .replace(/\n/g, '<br/>');
                
                shadow.html(val);
                $(this).css('height', Math.max(shadow.height() + 20, 1));
            }
            
            $(this).change(update).keydown(update);
                        
            update.apply(this);
            
        });
        
        return this;
        
    }
    
})(jQuery);