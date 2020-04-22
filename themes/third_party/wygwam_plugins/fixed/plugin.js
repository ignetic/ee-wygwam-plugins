/*
*   Plugin developed by Netbroad, C.B.
*
*   LICENCE: GPL, LGPL, MPL
*   NON-COMMERCIAL PLUGIN.
*
*   Website: netbroad.eu
*   Twitter: @netbroadcb
*   Facebook: Netbroad
*   LinkedIn: Netbroad
*
*/

CKEDITOR.plugins.add( 'fixed', {
	
    init: function( editor ) {
		
		var documentOffsetTop = function (elem) {
			return elem.offsetTop + ( elem.offsetParent ? documentOffsetTop(elem.offsetParent) : 0 );
		}
		
		var documentOffsetLeft = function (elem) {
			var offsetLeft = 0;
			do {
			  if ( !isNaN( elem.offsetLeft ) )
			  {
				  offsetLeft += elem.offsetLeft;
			  }
			} while( elem = elem.offsetParent );
			return offsetLeft;
		}
				
        window.addEventListener('scroll', function(){
			
			var fixedHeaderHeight = 0;
			var fixedHeader = document.getElementsByClassName('nav-global-wrap');

			if (fixedHeader.length > 0) {
				headerStyle = window.getComputedStyle(fixedHeader.item(0));
				if (headerStyle.position == 'fixed') {
					fixedHeaderHeight = fixedHeader.item(0).offsetHeight;
				}
			}		
			var editors = document.getElementsByClassName('cke');
			for (var i=0; i < editors.length; i++) {

				var editor              = editors[i];
				var content             = editor.getElementsByClassName('cke_contents').item(0);
				var toolbar             = editor.getElementsByClassName('cke_top').item(0);
				var inner               = editor.getElementsByClassName('cke_inner').item(0);
				var scrollvalue         = document.documentElement.scrollTop > document.body.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop;
				
				var editorOffset        = documentOffsetTop(editor) - fixedHeaderHeight;

				toolbar.style.top       = "0px";
				toolbar.style.left      = "0px";
				toolbar.style.right     = "0px";
				toolbar.style.margin    = "0 auto";
				toolbar.style.boxSizing = "border-box";
				toolbar.style.background = "#ffffff";
				toolbar.style.borderBottomColor  = "#e6e6e6";
				

				if(editorOffset <= scrollvalue){
					toolbar.style.position   = "fixed";
					toolbar.style.width     = (content.offsetWidth +2) + "px";
					content.style.paddingTop = toolbar.offsetHeight + "px";
					//toolbar.style.borderTop = "1px solid #8195a0";
					toolbar.style.borderLeft = "1px solid #8195a0";
					toolbar.style.borderRight = "1px solid #8195a0";
					toolbar.style.margin    = "0";
					//toolbar.style.left = editor.offsetLeft+"px";
					toolbar.style.left = documentOffsetLeft(editor)+"px";
					toolbar.style.boxShadow = "0px 0px 5px #aaaaaa";
					if (fixedHeaderHeight > 0) {
						toolbar.style.top = fixedHeaderHeight+"px";
					}
				}

				if(editorOffset > scrollvalue && (editorOffset + editor.offsetHeight) >= (scrollvalue + toolbar.offsetHeight)){
					toolbar.style.position   = "relative";
					content.style.paddingTop = "0px";
					toolbar.style.borderLeft = "none";
					toolbar.style.borderRight = "none";
					toolbar.style.width     = "auto";
					toolbar.style.boxShadow = "none";
					toolbar.style.margin    = "0 auto";
				}

				if((editorOffset + editor.offsetHeight) < (scrollvalue + toolbar.offsetHeight)){
					toolbar.style.position = "absolute";
					toolbar.style.top      = "calc(100% - " + toolbar.offsetHeight + "px)";
					inner.style.position   = "relative";
					toolbar.style.borderLeft = "none";
					toolbar.style.borderRight = "none";
					toolbar.style.width     = "auto";
					toolbar.style.boxShadow = "none";
					toolbar.style.margin    = "0 auto";
				}
				
			}
			
        }, false);
		
    }

});