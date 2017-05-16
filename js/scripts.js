;(function ( $ ) {

	"use strict";

	$(document).ready(function(){

		function hide_or_show_fields( _this ){
			var _name = $(_this).attr('name');
			var _val = $(_this).val();
			$( '[data-show-if-'+ _name +']' ).each(function(){
				if( $(this).data('show-if-'+ _name +'') == _val ){
					$(this).slideDown( 200 );
				}
				else{
					$(this).slideUp( 200 );
				}
			});
		}

		$('select, input').on( 'change', function(){
			hide_or_show_fields( this );
		});

		$('select, input').each( function(){
			hide_or_show_fields( this );
		});

		// function hide_or_show_fields( on ){
		// 	$('select, input').on( on, function(){
		// 		var _name = $(this).attr('name');
		// 		var _val = $(this).val();
		// 		$( '[data-show-if-'+ _name +']' ).each(function(){
		// 			if( $(this).data('show-if-'+ _name +'') == _val ){
		// 				$(this).show();
		// 			}
		// 			else{
		// 				$(this).hide();
		// 			}
		// 		});
		// 	});
		// }

		// hide_or_show_fields( 'ready' );
		// hide_or_show_fields( 'change' );

		// Confirmare inainte de a continua un link de stergere a datelor
		$('a[data-confirm-delete]').on( 'click', function(){
			var msg = $(this).data('confirm-delete');
			if( ! confirm( msg ) ){
				return false;
			}
		});

		$('a').tooltip();

		// Nu urmari linkurile care sunt compuse numai din "#".
		$('a[href="#"]').on( 'click', function(event){
			event.preventDefault();
		});

	});

}( jQuery ));