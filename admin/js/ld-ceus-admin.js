(function( $ ) {

	'use strict';
    
	$( document ).ready(function() {

        $(".wn-qst-mrk label svg, .wn-qst-mrk label i").click(function(){
            $(this).parent().siblings('p').fadeToggle();
    
        });

        var copyButtons = document.querySelectorAll('[data-copytarget]');
        copyButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                var targetSelector = this.getAttribute('data-copytarget');
                var target = document.querySelector(targetSelector);
                
                if (target) {
                    var range = document.createRange();
                    range.selectNode(target);
                    window.getSelection().removeAllRanges();
                    window.getSelection().addRange(range);
                    
                    try {
                        document.execCommand('copy');
                        alert('Copied!');
                    } catch (err) {
                        console.error('Failed to copy:', err);
                        alert('Failed to copy. please use Ctrl/Cmd+C to copy.');
                    }
                    
                    window.getSelection().removeAllRanges();
                }
            });
        });

    });

})( jQuery );
