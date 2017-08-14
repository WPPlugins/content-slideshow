/**
 * Content slideshow script.
 */

var cs = {};
( function( $, wp ) {
	cs = {
		currentId : -1,
		allData : {},
		width : 0,
		height : 0,
		template : {},
		container : {},
		t : 0,
		reloop : false,
		init : function() {
			cs.allData = contentSlideshowData;
			cs.width = window.innerWidth;
			cs.height = window.innerHeight;
			cs.template = wp.template( 'single-image-figure' );
			cs.container = $( 'body' );

			// Extra initial call to pre-load the first image.
			cs.nextImage();
			cs.nextImage();
			cs.t = setInterval( cs.nextImage, 3210 );
		},

		nextImage : function() {
			if ( cs.currentId < cs.allData.length - 1 ) {
				cs.currentId = cs.currentId + 1;
			} else {
				if ( 500 === cs.allData.length ) {
					location.reload(); // There may be more than 500 images in the collection, so reload from PHP to get a new batch.
				} else {
					var fig2 = document.getElementById( 'figure-' + cs.allData[cs.currentId - 1].id );
					fig2.style.top = '100%';
					fig2.style.opacity = '0';
					cs.currentId = 0;
					cs.reloop = true;
				}
			}
			if ( ! cs.reloop ) {
				var figure = cs.template( cs.allData[cs.currentId] );
				cs.container.append( figure );
			}
			cs.showPrevImage();
		},

		showPrevImage: function() {
			if ( 0 === cs.currentId ) {
				return;
			}
			var img = document.getElementById( 'image-' + cs.allData[cs.currentId - 1].id );
			var ratio = img.naturalWidth / img.naturalHeight;
			if ( img.width > cs.width ) {
				img.style.width = cs.width + 'px';
				img.style.height = cs.width / ratio + 'px';
			} else if ( img.height > cs.height ) {
				img.style.height = cs.height + 'px';
				img.style.width = cs.height * ratio + 'px';
			}
			var fig = document.getElementById( 'figure-' + cs.allData[cs.currentId - 1].id );
			fig.style.top = '0';
			fig.style.opacity = '1';
			fig.getElementsByTagName('a')[0].tabIndex = 0;
			if ( 1 === cs.currentId ) {
				return;
			}
			var fig2 = document.getElementById( 'figure-' + cs.allData[cs.currentId - 2].id );
			fig2.style.top = '100%';
			fig2.style.opacity = '0';
			fig2.getElementsByTagName('a')[0].tabIndex = -1;
		}
	}
	
	$(document).ready( function() { cs.init(); } );

} )( jQuery, window.wp );