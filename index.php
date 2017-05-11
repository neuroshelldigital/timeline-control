<!DOCTYPE html>
<html>
	<head>
		<title>MB Timeline</title>
		<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
	</head>
	<body>
		<style>
			body {
				font-family: helvetica, arial, sans-serif;
			}

			#media {
				float:left;
				width:25%;
				background-color: #ddd;
				box-shadow: inset 2px 1px 1px #aaa;
			}

			/*#media ul {
				list-style-type: none;
				padding:0;
				margin:10px;
			}

			#media li {
				box-shadow: 2px 2px 2px #999;
				background:#aaa;
				padding:5px;
				margin:5px;
				width:50%;
				cursor:pointer;
			}*/

			#media div {
				box-shadow: 2px 2px 2px #999;
				
				background: linear-gradient(to bottom, #5c7ce5 0%,#54a3ee 60%,#29cc54 61%,#76e291 100%); 

				padding:5px;
				margin:5px;
				width:40%;
				cursor:pointer;
				float:left;
				clear:both;
				color:#fff;
				text-align: center;
			}

			#media div.ui-draggable-dragging {
				background-color:#99f;
				opacity:0.5;
				border-radius:4px;
				width:100px;
			}

			#workspace {
				float:left;
				width:75%;
			}

			#video-timeline {
				list-style-type: none;
				margin:0; padding:0;
			}

			#video-timeline li {
				background:#afafaf;
				padding:3px;
				box-shadow: inset 2px 1px 1px #666;
			}

			#video-timeline li:after {
				content: "";
				display: table;
				clear: both;
			}

			div.clip {
				float:left;
				height:50px;
				float:left;
				border-radius:4px;
				font:10px/50px helvetica, arial, sans-serif;
				color:#fff;
				box-shadow: 2px 1px 1px #666;
				margin:2px 0 2px 0;
				padding: 0 5px;
				cursor:pointer;

			}

			div.clip.caption {
				background-color:#e51e17;

			}

			div.clip.caption.selected {
				background-color:#ff524d;

			}

			div.clip.audio-overlay {
				background-color:#e5d845;

			}

			div.clip.audio-overlay.selected {
				background-color:#fff480;

			}

			div.clip.title{
				background-color:#cc29c7;
				height:68px;
				line-height:68px;
				text-align: center;
			}

			div.clip.title.selected{
				background-color:#ff66fa;
			}

			div.clip.video {
				height:70px;
				margin:0;
				box-shadow:none;
				border-radius:0;
				padding:0;				
			}

			div.clip.video div {
				margin:2px 0 2px 0;
				padding:0 5px;
				box-shadow: 2px 1px 1px #666;
				text-align:center;
			}

			div.clip.video div.image {
				height:42px;
				line-height:42px;
				border-top-left-radius: 4px;
				border-top-right-radius: 4px;
				background-color: #5c7ce5;
				/*background-image: url(media-library/image/thumb_bud.jpg);*/
				background-repeat: no-repeat;
				background-size: auto 75%;
				background-position:5px;
			}

			div.clip.video.selected .image {
				background-color: #809dff;
			}

			div.clip.video div.sound {	
				height: 24px;
				line-height: 24px;			
				border-bottom-left-radius: 4px;
				border-bottom-right-radius: 4px;
				background-color: #29cc54;
			}

			div.clip.video.selected .sound{
				background-color: #66ff8f;
			}
		</style>


		<script>
			var json = {
				"timelineend":"17",
				"videos":
					[
						{
							"type":"video",
							"mtype":"playable",
							"name":"coke.mp4",
							"url":"media-library/video/coke.mp4",
							"timelinestart":0,
							"start":0, /* clip internal start point */
							"end":6, 
							"track":"video-main", /* associated DOM element */
							"volume":"1",
							"loaded":false,
							"playing":false
						},
						{
							"type":"title",
							"mtype":"text",
							"caption":"Switch DRUGS",
							"timelinestart":5,
							"start":0,
							"end":4,
							"font":"70px Source Sans Pro",
							"top":"200",
							"left":0,
							"fillstyle":"#fff"
						},
						{
							"type":"video",
							"mtype":"playable",
							"name":"bud.mp4",
							"url":"media-library/video/bud.mp4",
							"timelinestart":8,
							"start":6,
							"end":13,
							"track":"video-main",
							"volume":"1",
							"loaded":false,
							"playing":false
						},

					],
				"audios":
					[
					],
				"captions":
					[
					],
				"framecount":0
			};

			console.log("timeline end: " + json.timelineend);



		</script>

		<div id="media">
			<div class="clipinsert" cliplength="8" clipname="newcoke.mp4">coke.mp4</div>
			<div class="clipinsert" cliplength="9" clipname="newbud.mp4">bud.mp4</div>
		</div>

		<div id="workspace">
			<ul id="video-timeline">
				<li id="caption">
					<div class="clip caption selected">caption text</div>
				</li>

				<li id="video-main"></li>

				<li id="audio-overlay">
					<div class="clip audio-overlay selected">
						filename.mp3
					</div>
				</li>
			</ul>
		</div>
		

		<script>
			
			var videoList=$('#media div');
			var videoTimeline=$('#video-main');
			var clipScale=30; //pixels per second of timeline length

			videoList.draggable({
				connectToSortable: '#video-main',
				helper:'clone'
			});	

			var dragClip={};
			var timeSnap=[];

			videoTimeline.sortable({
				placeholder: 'sortable-placeholder',
				items: '> div.clip',
			    over: function(event, ui) {
			        if (ui.item.hasClass("clipinsert")) {
			            // This is a new item
			            ui.item.removeClass("clipinsert");
			            clipContainer=ui.item.addClass('clip video').html('').css({
			            	'width': 'auto',
			            	'height':'70px'
			            }).attr('id', 'newVidClip');
			            
						imgClip = $('<div />', {'class': 'image'}).css({
							'background-image': 'url(media-library/image/thumb_bud.jpg)',
							'width': parseInt(ui.item.attr('cliplength'))*clipScale
						}).html(ui.item.attr('clipname'));
						soundClip = $('<div />', {'class': 'sound'}).css({
							'width': parseInt(ui.item.attr('cliplength'))*clipScale
						}).html(ui.item.attr('clipname'));

						dragClip=clipContainer.append(imgClip).append(soundClip);

			        }
    			},
    			stop: function(event, ui) {;
    				
					var newClip={};

					timeSnap = videoTimeline.sortable("toArray");
    				
    				timeSnapIdParse=[];
    				tempSnap=[];
    				

    				var prevId='';
    				

    				for(i=0;i<timeSnap.length;i++) {
    					timeSnapIdParse=timeSnap[i].split('_');

    					if(timeSnap[i] == 'newVidClip') {

    						newClip.end=dragClip.attr('cliplength');
    						newClip.start=0;
    						tempSnap.push(newClip);
    						timeSnapIdParse[0]="vclip";

    					}
    					
    					// else tempSnap.push(json.videos[parseInt(timeSnap[i].match(/[^_]*$/)[0])]);
    					else tempSnap.push(json.videos[timeSnapIdParse[1]]);
    					
    					
    					if(i==0) tempSnap[i].timelinestart=0;
    					else tempSnap[i].timelinestart=(parseInt(tempSnap[i-1].timelinestart) + parseInt(tempSnap[i-1].end) - parseInt(tempSnap[i-1].start)); 
    					
    					
    					prevId=videoTimeline.find('.clip').eq(i);
    					prevId.attr('id', timeSnapIdParse[0] + '_' + i);
    					
    				}
    				
    				json.videos=tempSnap;
    				console.log(json);

    			}
			
			});

			var clipContainer, imgClip, soundClip;
			

			for(i=0; i<json.videos.length; i++){
				switch(json.videos[i].type) {
					case "video":
						clipContainer = $('<div />', {
							'class': 'clip video',
							'id': 'vclip_' + i
						});
						imgClip = $('<div />', {'class': 'image'}).css({
							'background-image': 'url(media-library/image/thumb_bud.jpg)',
							'width': (json.videos[i].end - json.videos[i].start)*clipScale
						}).html(json.videos[i].name);
						soundClip = $('<div />', {'class': 'sound'}).css({
							'width': (json.videos[i].end - json.videos[i].start)*clipScale
						}).html(json.videos[i].name);

						$('#video-main').append(clipContainer.append(imgClip).append(soundClip));
						break;
					
					case "title":
						clipContainer=$('<div />', {
							'class': 'clip title',
							'id': 'tclip_' + i,
							'width': (json.videos[i].end - json.videos[i].start)*clipScale
						}).html(json.videos[i].caption);
						$('#video-main').append(clipContainer);
						break;
					
					default:
						console.log('unrecognized clip type');
				}
				
			}
		</script>
	</body>
</html> 