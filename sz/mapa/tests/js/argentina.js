/**
 * Argentina Interactive Map
 * @author  Gustavo Adrián Salvini
 * @date 20160408
 */

function openURwithTarget( url, target ) {
	var win = window.open(url, target);
	win.focus();
}

window.onload = function () {

	var s 			= Snap("#svgMap");
    
	var map_x_offset = 106, map_y_offset = 50;
	var isFullyCompatibleBrowser =  ! ( $.browser.msie || $.browser.msedge || $.browser["windows phone"] );

	var shadow 		= s.filter(Snap.filter.shadow(0, 2, 3));
	var noShadow 	= s.filter(Snap.filter.shadow(0,0,0));
	var invert 		= s.filter(Snap.filter.invert(1.0));
	// var invertFilterChild = invert.node.firstChild;
	var noInvert 	= s.filter(Snap.filter.invert(0));
	var saturate	= s.filter(Snap.filter.saturate(1.5));
	var saturateFilterChild = saturate.node.firstChild;

	var activeBounds = {};
    
	var defaultRegionURL = '#';
	var regionsMetadata =
		{ 
			'antartida': { 'text': "Antartida (Arg.)", 'url': '', 'target': '_blank', 'label_offset_x': 50, 'label_offset_y': 0, 'label_hover_offset_x': 30, 'label_hover_offset_y': -10, 'color': '#CCCCCC' },
			'malvinas': { 'text': "Islas Malvinas", 'label_offset_x': 0, 'label_offset_y': -20, 'label_hover_offset_x': 10, 'label_hover_offset_y': -10 , 'color': '#0E75B0' },
			'buenos-aires': { 'text': "31.9", 'url': 'https://es.wikipedia.org/wiki/Provincia_de_Buenos_Aires', 'target': '_blank', 'label_offset_x': -2, 'label_offset_y': 0, 'label_hover_offset_x': -5, 'label_hover_offset_y': -20 , 'color': '#FBC40F' },
			'caba': { 'text': "", 'url': '#', 'label_offset_x': 60, 'label_offset_y': 10 , 'color': '#FBC40F' },
			'caba-big': { 'text': "CABA: 11.2", 'url': 'https://es.wikipedia.org/wiki/Buenos_Aires', 'target': '_blank', 'label_offset_x': 0, 'label_offset_y': 70, 'label_hover_offset_y': 55 , 'color': '#FBC40F' },
			'catamarca': { 'text': "26.3", 'label_offset_x': 2, 'label_offset_y': 10, 'rotation': 45 , 'color': '#50B3DC' },
			'chaco': { 'text': "28.8", 'label_offset_x': 12, 'label_offset_y': 28, 'label_hover_offset_x': 7, 'label_hover_offset_y': 10 , 'color': '#AAAEB1' },
			'chubut': { 'text': "27.5", 'label_offset_x': -10, 'label_offset_y': 10, 'rotation': -5 , 'color': '#0E75B0' },
			'cordoba': { 'text': "30.3", 'label_offset_x': -2, 'label_offset_y': 0, 'rotation': 0 , 'color': '#FBC40F' },
			'corrientes': { 'text': "36.8", 'url': '#', 'label_offset_x': 10, 'label_offset_y': 10, 'label_hover_offset_x': 95, 'label_hover_offset_y': 20 , 'color': '#AAAEB1' },
			'entre-rios': { 'text': "34.7", 'label_offset_x': 20, 'label_offset_y': 10, 'rotation': 0, 'label_hover_offset_x': 100, 'label_hover_offset_y': -20 , 'color': '#AAAEB1' },
			'formosa': { 'text': "24.9", 'label_offset_x': 0, 'label_offset_y': 17, 'rotation': 28 , 'color': '#AAAEB1' },
			'jujuy': { 'text': "30.3", 'label_offset_x': 0, 'label_offset_y': 14, 'rotation': 40 , 'color': '#50B3DC' },
			'la-pampa': { 'text': "26.3", 'label_offset_x': 0, 'label_offset_y': 15, 'rotation': 0, 'color': 'red' , 'color': '#0E75B0' },
			'la-rioja': { 'text': "23.5", 'label_offset_x': 0, 'label_offset_y': 0, 'rotation': 45 , 'color': '#50B3DC' },
			'mendoza': { 'text': "27.9", 'label_offset_x': -5, 'label_offset_y': 10, 'rotation': -50 , 'color': '#58585A' },
			'misiones': { 'text': "28.5", 'label_offset_x': 5, 'label_offset_y': 17, 'rotation': -45 , 'color': '#AAAEB1' },
			'tucuman': { 'text': "24.2", 'label_offset_x': 0, 'label_offset_y': 0, 'label_hover_offset_x': 10 , 'label_hover_offset_y': -40 , 'color': '#50B3DC' },
			'neuquen': { 'text': "25.8", 'label_offset_x': 0, 'label_offset_y': 15, 'rotation': -50 , 'color': '#0E75B0' },
			'rio-negro': { 'text': "24.4", 'label_offset_x': 0, 'label_offset_y': 25, 'rotation': -3, 'label_hover_offset_x': 20 , 'color': '#0E75B0' },
			'salta': { 'text': "26.4", 'label_offset_x': 10, 'label_offset_y': 35, 'label_hover_offset_x': 0, 'label_hover_offset_y': 20 , 'color': '#50B3DC' },
			'san-juan': { 'text': "25.4", 'label_offset_x': -10, 'label_offset_y': 20, 'rotation': 0 , 'color': '#58585A' },
			'san-luis': { 'text': "17.9", 'label_offset_x': 8, 'label_offset_y': 10, 'rotation': -90, 'label_hover_offset_x': 0, 'label_hover_offset_y': 5 , 'color': '#58585A' },
			'santa-cruz': { 'text': "18.4", 'label_offset_x': 5, 'label_offset_y': -20, 'rotation': -5, 'label_hover_offset_x': 5, 'label_hover_offset_y': -34 , 'color': '#0E75B0' },
			'santa-fe': { 'text': "30.3", 'label_offset_x': -4, 'label_offset_y': 10, 'rotation': -75 , 'color': '#FBC40F' },
			'santiago-del-estero': { 'text': "44.7", 'label_offset_x': 0, 'label_offset_y': 5, 'rotation': -50 , 'color': '#50B3DC' },
			'tierra-del-fuego': { 'text': "10.4", 'label_offset_x': 45, 'label_offset_y': 0, 'rotation': -7, 'label_hover_offset_x': 40, 'label_hover_offset_y': 0 , 'color': '#0E75B0' },
	};

	Snap.plugin( function( Snap, Element, Paper, global ) {
		Element.prototype.addTransform = function( t ) {
			return this.transform( this.transform().localMatrix.toTransformString() + t );
		};
	});

	// Snap.plugin( function( Snap, Element, Paper, global ) {
	// 	Paper.prototype.multitext = function(x, y, txt, max_width, attributes) {
	// 		var svg = Snap();
	// 		var abc = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
	// 		var temp = svg.text(0, 0, abc);
	// 		temp.attr(attributes);
	// 		var letter_width = temp.getBBox().width / abc.length;
	// 		svg.remove();

	// 		var words = txt.split(" ");
	// 		var width_so_far = 0, current_line=0, lines=[''];
	// 		for (var i = 0; i < words.length; i++) {

	// 			var l = words[i].length;
	// 			if (width_so_far + (l * letter_width) > max_width) {
	// 				lines.push('');
	// 				current_line++;
	// 				width_so_far = 0;
	// 			}
	// 			width_so_far += l * letter_width;
	// 			lines[current_line] += words[i] + " ";
	// 		}

	// 		var t = this.text(x, y, lines).attr(attributes);
	// 		t.selectAll("tspan:nth-child(n+2)").attr({
	// 			dy: "1.2em",
	// 			x: x
	// 		});

	// 		return t;
	// 	};
	// });



	Snap.load("mapa/tests/assets/argentina_map.svg", function (f) {

		/**
		 * [drawBounds description]
		 * @param  {[type]} targetObject [description]
		 * @return {[type]}              [description]
		 
		 */
		
		
		 
		function drawBounds(targetObject) {

			targetId = targetObject.attr('id');
            
							
			
			if ( typeof activeBounds[ targetId ] == 'undefined')  
    			activeBounds[ targetId ] = [];

			var tmp_polyline, tmp_outer_disc, tmp_inner_disc, tmp_innermost_disc;

			var bbox = targetObject.getBBox();

        	// calculate middle point
            var x_center = ( bbox.x2 + bbox.x ) / 2 + map_x_offset; 
            var y_center = ( bbox.y2 + bbox.y ) / 2 + map_y_offset;

            if (false) {


	    		tmp_polyline = s.polyline( bbox.x + map_x_offset, bbox.y + map_y_offset, 
	    									bbox.x2 + map_x_offset, bbox.y + map_y_offset,
	    									bbox.x2 + map_x_offset, bbox.y2 + map_y_offset,
	    									bbox.x + map_x_offset, bbox.y2 + map_y_offset,
	    									bbox.x + map_x_offset, bbox.y + map_y_offset
	    									 )
		    		.attr({
		                stroke: "rgb(206,43,55)",
		                strokeWidth: "2",
		                opacity: "0.3",
		                fill: "none" //none
		            });

		    	activeBounds[ targetId ].push( tmp_polyline );

	            tmp_outer_disc = s.circle(x_center, y_center, bbox.r0 ).attr({
	                fill: "none",
	                stroke: "rgb(0, 146, 70)",
	                strokeWidth: "3",
	                opacity: "0.3",
	            });

		    	activeBounds[ targetId ].push( tmp_outer_disc );

	            tmp_inner_disc = s.circle(x_center, y_center, bbox.r1 ).attr({
	                fill: "none",
	                stroke: "rgb(206,43,55)",
	                strokeWidth: "3",
	                opacity: "0.3",
	            });

		    	activeBounds[ targetId ].push( tmp_inner_disc );
		    	// console.log('activeBounds:', activeBounds);
            }


		}

		/**
		 * [mouseOutSVG description]
		 * @param  {[type]} event [description]
		 * @return {[type]}       [description]
		 */
		function mouseOutSVG(event) {
			// console.log('Out of SVG!!!!!');
			// console.log(event.target);
		}



		/**
		 * [hoverRegion description]
		 * @param  {[type]} event [description]
		 * @return {[type]}       [description]
		 */
		function hoverRegion(event) {
			var currentId = this.attr('id');

			// Ensure previously hovered regions always return to base state.
			for (var rid in regions) {
				if (!regions.hasOwnProperty(rid) || rid === currentId) {
					continue;
				}
				if (regions[rid].snapObject) {
					var b = regions[rid].originalBBox;
					regions[rid].snapObject.stop().attr({ filter: noShadow });
					regions[rid].snapObject.transform("s1,1,"+b.cx+","+b.cy);
				}
				if (regions[rid].snapLabel) {
					var baseT = regions[rid].labelBaseTransform || '';
					regions[rid].snapLabel.stop().transform(baseT).attr({ opacity: '1' });
				}
			}

			if ( isFullyCompatibleBrowser ) {

				if (weakOverlays != null) weakOverlays.animate( { 'opacity': '0' }, 200, mina.easeout );

				// var lastChild = document.querySelectorAll('g#italia_regioni')[0].lastElementChild;
				// var $lastChild = $('#regions > path:last-child');
				var $lastChild = $('#regions').children().last();
				// var lastSnapObject = s.select( '#'+lastChild.id );
				var lastSnapObject = s.select( '#'+$lastChild.attr('id') );
				lastSnapObject.after(this);

				/* regions labels */
				var	label_hover_x = 0,
					label_hover_y = 0;

				if ( typeof regionsMetadata[ this.attr('id') ] != 'undefined') 
				{				

					if ( typeof regionsMetadata[ this.attr('id') ]['label_hover_offset_x'] != 'undefined')
						label_hover_x = regionsMetadata[ this.attr('id') ]['label_hover_offset_x'];
		
					if ( typeof regionsMetadata[ this.attr('id') ]['label_hover_offset_y'] != 'undefined')
						label_hover_y = regionsMetadata[ this.attr('id') ]['label_hover_offset_y'];

				}

    	        console.log( 'Original BBox for:', this );
    	        console.log( regions[ this.attr('id') ].originalBBox);

    	        var originalBBox 		= regions[ this.attr('id') ].originalBBox;
    			var originalTransform 	= regions[ this.attr('id') ].originalTransform; 
				// this.transform( regions[ this.attr('id') ].originalTransform['string'] );
				
            	var that = this;

            	// that.attr({ transform:  });
            	that.attr({ filter: shadow }).animate( { transform: "s1.6,1.6,"+originalBBox.cx+","+originalBBox.cy }, 600, mina.elastic );
				
				regions[ this.attr('id') ].snapLabel.animate( {  }, 300, mina.easeout );
				var baseLabelTransform = regions[ this.attr('id') ].labelBaseTransform || '';
				regions[ this.attr('id') ].snapLabel.transform(baseLabelTransform + ' t'+label_hover_x+','+label_hover_y+' s1.6,1.6');

    	        console.log( 'On Hover BBox:', that.getBBox() );

			}
			else // not fully compatible browser
			{
	            this.attr({ filter: invert });
			}
		}

		/**
		 * [removeBounds description]
		 * @return {[type]} [description]
		 */
		function removeBounds() {

			// cleans the activeBounds Queue
			var nActiveBounds = Object.keys(activeBounds).length;
			// console.log('#Regions with Active Bounds:', nActiveBounds );
			if ( nActiveBounds > 0 )
			{ 
				// console.log('There are things to be cleaned!');
				for (var region in activeBounds) {
					// console.log('#-# region: ', region);
					for ( var i = 0; i < activeBounds[region].length; i++ ) {
						activeBounds[region][i].remove();
					}
					activeBounds[region] = [];
				}
			} 
		}


		/**
		 * [noHoverRegion description]
		 * @param  {[type]} event [description]
		 * @return {[type]}       [description]
		 */
		function noHoverRegion(event) {
			// console.log('mouseOut:', this.attr('id'));
        	// setTimeout( function() { removeBounds() }, 2000 );
			if (weakOverlays != null) weakOverlays.animate( { 'opacity': '1' }, 200, mina.easeout );

			if ( isFullyCompatibleBrowser ) {

				/* regions labels */
				var label_hover_x = 0;
				var label_hover_y = 0;

				if ( typeof regionsMetadata[ this.attr('id') ] != 'undefined') 
				{				
					if ( typeof regionsMetadata[ this.attr('id') ]['label_hover_offset_x'] != 'undefined')
						label_hover_x = -1 * regionsMetadata[ this.attr('id') ]['label_hover_offset_x'];
		
					if ( typeof regionsMetadata[ this.attr('id') ]['label_hover_offset_y'] != 'undefined')
						label_hover_y = -1 * regionsMetadata[ this.attr('id') ]['label_hover_offset_y'];

					// if ( typeof regionsMetadata[ this.attr('id') ]['label_hover_rotation'] != 'undefined')
					// 	label_hover_rotation = -1 * regionsMetadata[ this.attr('id') ]['label_hover_rotation'];

				}

    	        var originalBBox 		= regions[ this.attr('id') ].originalBBox;
    			var originalTransform 	= regions[ this.attr('id') ].originalTransform; 

    	        this.animate({ transform: "s1,1,"+originalBBox.cx+","+originalBBox.cy }, 700, mina.elastic).attr({ filter: noShadow });
                 
				
								 
    	        // console.log( 'Original Transform for:', this );
    	        // console.log( regions[ this.attr('id') ].originalTransform );
				// this.addTransform( regions[ this.attr('id') ].originalTransform['string'] );
                
				regions[ this.attr('id') ].snapLabel.animate( { 'opacity': '1' }, 300, mina.easeout );
				var baseLabelTransform = regions[ this.attr('id') ].labelBaseTransform || '';
				regions[ this.attr('id') ].snapLabel.transform(baseLabelTransform + ' t'+label_hover_x+','+label_hover_y);
			}
			else
			{
    	        this.animate({ transform: "s1,1" }, 700, mina.elastic).attr({ filter: noInvert });
			}


		}



		/**
		 * [clickRegion description]
		 * @param  {[type]} event [description]
		 * @return {[type]}       [description]
		 */
		function clickRegion(event) {

			var regionURL = defaultRegionURL,
				target = '_self'; 

			if ( typeof regionsMetadata[ this.attr('id') ] != 'undefined') 
			{		
				if ( typeof regionsMetadata[ this.attr('id') ]['url'] != 'undefined')
					regionURL = regionsMetadata[ this.attr('id') ]['url'];

				if ( typeof regionsMetadata[ this.attr('id') ]['target'] != 'undefined')
					target = regionsMetadata[ this.attr('id') ]['target'];
			}
			
			// top.location.href = regionURL; 
			openURwithTarget( regionURL, target );
		}

		/* Main callback code for Snap.load ************************************************************************/

		var regionsContainer = f.select("#regions");
		s.append(regionsContainer); 

		var weakOverlays = f.select("#weak-overlays");
		if ( weakOverlays != null)
		{
			s.append(weakOverlays); 
			weakOverlays.attr({ 'pointer-events': 'none' });			
		} 

		// var	elemRegions		= document.querySelectorAll('#regions > path'),
		var	elemRegions		= $('#regions').children(),
			regions			= {};

		// console.log( 'Regiones:', elemRegions.length );

		s.mouseout( function(event) {
			mouseOutSVG(event);
		} );

		var regionBBox;

		for ( var i = 0; i < elemRegions.length; i++ ) {
			regions[ elemRegions[i].id ] = {};
			regions[ elemRegions[i].id ].id = elemRegions[i].id;
			var regionId = regions[ elemRegions[i].id ].id;
			// regions[ elemRegions[i].id ].name = elemRegions[i].getAttribute('data-name');
		   		    
		 
		 if ( typeof regionsMetadata[ elemRegions[i].id ]['color'] != 'undefined')
		   {color1= regionsMetadata[ elemRegions[i].id ]['color'];
		   elemRegions[i].setAttribute('fill', color1 );}
		   
			
			regions[ elemRegions[i].id ].snapObject = s.select( '#'+elemRegions[i].id );
			
						   
			//elemRegions[i].setAttribute('style-fill',color1) //('style', 'float:left; font-weight:bold');
						
			regions[ elemRegions[i].id ].pathLength = Snap.path.getTotalLength( regions[ elemRegions[i].id ].snapObject );
			regions[ elemRegions[i].id ].originalBBox = regions[ elemRegions[i].id ].snapObject.getBBox();
			regions[ elemRegions[i].id ].originalTransform = regions[ elemRegions[i].id ].snapObject.transform();

			// console.log ( elemRegions[i].id,':', regions[ elemRegions[i].id ].originalTransform );
            
			
			/* region bounding box */
			var regionBBox = regions[ elemRegions[i].id ].snapObject.getBBox();
            
            var x_center = ( regionBBox.x2 + regionBBox.x ) / 2 + map_x_offset; 
            var y_center = ( regionBBox.y2 + regionBBox.y ) / 2 + map_y_offset;

			/* event handlers */
			regions[ elemRegions[i].id ].snapObject.hover( hoverRegion, noHoverRegion );
			regions[ elemRegions[i].id ].snapObject.click( clickRegion );

			/* regions labels */
			var label_x = x_center;
			var label_y = y_center;
			var label_text = regionId;
			var label_rotation = 0;

			if ( typeof regionsMetadata[ elemRegions[i].id ] != 'undefined') 
			{				
				if ( typeof regionsMetadata[ elemRegions[i].id ]['label_offset_x'] != 'undefined')
					label_x += regionsMetadata[ elemRegions[i].id ]['label_offset_x'];
	
				if ( typeof regionsMetadata[ elemRegions[i].id ]['label_offset_y'] != 'undefined')
					label_y += regionsMetadata[ elemRegions[i].id ]['label_offset_y'];

				if ( typeof regionsMetadata[ elemRegions[i].id ]['text'] != 'undefined')
					label_text = regionsMetadata[ elemRegions[i].id ]['text'];

				if ( typeof regionsMetadata[ elemRegions[i].id ]['rotation'] != 'undefined')
					label_rotation = regionsMetadata[ elemRegions[i].id ]['rotation'];

			}


			var label 		= s.text(label_x, label_y, label_text);

			// var label1 		= s.multitext(label_x, label_y, label_text, 200, { "font-size": "10px" });

			var labelBBox 	= label.getBBox();
			label.transform('t-'+labelBBox.width/2+',-'+ labelBBox.height/2 +' r' + label_rotation );
			label.attr({ 'pointer-events': 'none' });
			regions[ elemRegions[i].id ].snapLabel = label;
			// regions[ elemRegions[i].id ].snapLabel.remove(); // saque , 'opacity': '0.2'
			regions[ elemRegions[i].id ].snapLabel.attr({'font-size': '14px'});
			regions[ elemRegions[i].id ].snapLabel.attr({'fill': '#000000'}); 

			regions[ elemRegions[i].id ].snapLabelTransform = regions[ elemRegions[i].id ].snapLabel.transform();
			regions[ elemRegions[i].id ].labelBaseTransform = regions[ elemRegions[i].id ].snapLabel.transform().localMatrix.toTransformString();
        	// console.log( 'current label transform:', regions[ elemRegions[i].id ].snapLabelTransform ); 


			// s.append(regions[ elemRegions[i].id ].snapLabel );

		}
		/* /End Main callback code for Snap.load ************************************************************************/


	});
};
