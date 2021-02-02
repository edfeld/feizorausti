/*
|	active_song
|		This is the current song playing. No matter if it's playlist or
|		single song, this is the reference.
|
|	volume
|		The song volume ranges between 0 and 1 which correspond accordingly 
|		to 0% to 100%
|
|	pre_mute_volume
|		Handles the last volume that was set on amplitude so when the
|		current song is un-muted it goes back to the volume it was
|		previously at.
|
|	list_playing_index
|		Saves the current index of the song that's playing so it can 
|		be referenced for visual display updates.
|
|	auto_play
|		If autoplay is set to true, amplitude will start playing the start
|		song on page load.
|
|	songs
|		JSON object containing all of the songs that amplitude uses
|
|	shuffle
|		If shuffle is turned on, this is set to true. Amplitude uses
|		the shuffle_list if this is set to true.
|
|	shuffle_list
|		The container for the songs when they are shuffled.  After
|		shuffle this list is populated and is used in the next and
|		previous calls.		
|
|	start_song
|		Initial start song allows for the user to start a song out
|		of the song list. If not defined, plays the first song on 
|		the list.
|
|	volume_up_amount
|		When the user presses the volume up button, how much does
|		it increase. Defaults to 10 since it's a 1-100 range for
|		volume.
|
|	volume_down_amount
|		When the user presses the volume down button, how much does
|		it decrease. Defaults to 10 since it's a 1-100 range for
|		volume.
|	
|	continue_next
|
|	active_song_information
|
|	**********************
|	All callbacks are null unless defined by the user. This should
|	just be the string representation of the function name.
|
|	before_play_callback
|		Called before the play function is called.	
|
|	after_play_callback
|		Called after the play function is called.
|
|	before_stop_callback
|		Called before the stop function is called.
|		
|	after_stop_callback
|		Called after the stop function is called.
|
|	before_next_callback
|		Called before the next function is called for
|		the next song.
|
|	after_next_callback
|		Called after the next function is called.
|
|	before_prev_callback
|		Called before the previous function is called.
|
|	after_prev_callback
|		Called after the previous function is called.
|
|	before_pause_callback
|		Called before the pause function is called.
|
|	after_pause_callback
|		Called after the pause function is called.
|
|	before_shuffle_callback
|		Called before the shuffle function is called.
|
|	after_shuffle_callback
|		Called after the shuffle function is called.
|
|	before_volume_change_callback
|		Called before the volume is changed.
|
|	after_volume_change_callback
|		Called after the volume is changed.
|
|	before_mute_callback
|		Called before the mute function is called.
|
|	after_mute_callback
|		Called after the mute function is called.
|
|	before_time_update_callback
|		Called before time is updated.
|
|	after_time_update_callback
|		Called after time is updated.
|
|	before_song_information_set_callback
|		Called before song information is set.
|
|	after_song_information_set_callback
|		Called after song information is set.
|	
|	before_song_added_callback
|		Called before song is added to the songs array
|
|	after_song_added_callback
|		Called after a song is added to the songs array
*/


var active_config = {
	"active_song": null,
	"volume": .5,
	"pre_mute_volume": .5,

	"list_playing_index": null,
	"auto_play": false,
	"songs": {},

	"shuffle": false,
	"shuffle_list": {},

	"start_song": null,
	"volume_up_amount": 10,
	"volume_down_amount": 10,
	"continue_next": false,

	"active_song_information": {},

	"before_play_callback": null,
	"after_play_callback": null,

	"before_stop_callback": null,
	"after_stop_callback": null,

	"before_next_callback": null,
	"after_next_callback": null,

	"before_prev_callback": null,
	"after_prev_callback": null,

	"before_pause_callback": null,
	"after_pause_callback": null,

	"before_shuffle_callback": null,
	"after_shuffle_callback": null,

	"before_volume_change_callback": null,
	"after_volume_change_callback": null,

	"before_mute_callback": null,
	"after_mute_callback": null,

	"before_time_update_callback": null,
	"after_time_update_callback": null,

	"before_song_information_set_callback": null,
	"after_song_information_set_callback": null,

	"before_song_added_callback": null,
	"after_song_added_callback": null
};

/*
|--------------------------------------------------------------------------
| Active Song Information
|--------------------------------------------------------------------------
| Contains the information for the active song. This makes it accessible
| to the application utilizing AmplitudeJS
|	
|	cover_art_url
|	
|	artist
|
|	album
|
|	song_title
|
|	song_url
|	
|	live
|
|	visual_id
|
|
*/
var active_song_information = { };

/*
|--------------------------------------------------------------------------
| Initializers
|--------------------------------------------------------------------------
| Sets up amplitude on load. 
|	
|	hook_functions
|		Calls the method that binds a group of events to the onload of the
|		window function.  This will pick up the amplitude elements and bind
|		them to certain events.
|		
|		Thanks to: http://www.htmlgoodies.com/beyond/javascript/article.php/3724571/Using-Multiple-JavaScript-Onload-Functions.htm
|
|	configuration
|		Sets up amplitude to work with the user config provided. This sets
|		up songs and 
|
|	web_desktop
|		Binds events to elements for clicks
|	
|	web_mobile
|		Binds events to elements for touch
|
|	
*/

hook_functions( configure_variables );

//If mobile, bind touch events, otherwise bind click.
if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
	hook_functions( web_mobile );
}else{
	hook_functions( web_desktop );
}

hook_functions( start );

function hook_functions( func ) {
	var oldonload = window.onload;
	
	if (typeof window.onload != 'function') {
		window.onload = func;
	} else {
		window.onload = function() {
			if (oldonload) {
				oldonload();
			}
			func();
		}
	}
}

/*
|--------------------------------------------------------------------------
| Initialize
|--------------------------------------------------------------------------
| Configures the environment based off of the users preferences.
|	
*/
function configure_variables(){
	//http://www.binarytides.com/using-html5-audio-element-javascript/
	active_config.active_song = new Audio( );

	//Binds the time updating for the active song. Used for display and time tracking.
	bind_time_update();

	//Sets the internal config songs 
	if( config.songs != undefined ){
		active_config.songs = config.songs;
	}

	//Sets the default volume
	if( config.volume != undefined ){
		active_config.volume = config.volume / 100;
		active_config.pre_mute_volume = config.volume / 100;

		if(document.getElementById('volume-slider')){
			document.getElementById('volume-slider').value = active_config.volume * 100;
		}

		active_config.active_song.volume = active_config.volume;
	}

	if( config.pre_mute_volume != undefined ){
		active_config.pre_mute_volume = config.pre_mute_volume;
	}

	if( config.auto_play != undefined ){
		active_config.auto_play = config.auto_play;
	}

	if( config.start_song != undefined ){
		active_config.start_song = config.start_song;
	}

	if( config.before_play_callback != undefined ){
		active_config.before_play_callback = config.before_play_callback;
	}

	if( config.after_play_callback != undefined ){
		active_config.after_play_callback = config.after_play_callback;
	}

	if( config.before_stop_callback != undefined ){
		active_config.before_stop_callback = config.before_stop_callback;
	}

	if( config.after_stop_callback != undefined ){
		active_config.after_stop_callback = config.after_stop_callback;
	}

	if( config.before_next_callback != undefined ){
		active_config.before_next_callback = config.before_next_callback;
	}

	if( config.after_next_callback != undefined ){
		active_config.after_next_callback = config.after_next_callback;
	}

	if( config.before_prev_callback != undefined ){
		active_config.before_prev_callback = config.before_prev_callback;
	}

	if( config.after_prev_callback != undefined ){
		active_config.after_prev_callback = config.after_prev_callback;
	}

	if( config.after_pause_callback != undefined ){
		active_config.after_pause_callback = config.after_pause_callback;
	}

	if( config.before_pause_callback != undefined ){
		active_config.before_pause_callback = config.before_pause_callback;
	}

	if( config.after_shuffle_callback != undefined ){
		active_config.after_shuffle_callback = config.after_shuffle_callback;
	}

	if( config.before_shuffle_callback != undefined ){
		active_config.before_shuffle_callback = config.before_shuffle_callback;
	}

	if( config.before_volume_change_callback != undefined ){
		active_config.before_volume_change_callback = config.before_volume_change_callback;
	}

	if( config.after_volume_change_callback != undefined ){
		active_config.after_volume_change_callback = config.after_volume_change_callback;
	}

	if( config.before_mute_callback != undefined ){
		active_config.before_mute_callback = config.before_mute_callback;
	}

	if( config.after_mute_callback != undefined ){
		active_config.after_mute_callback = config.after_mute_callback;
	}

	if( config.before_time_update_callback != undefined ){
		active_config.before_time_update_callback = config.before_time_update_callback;
	}

	if( config.after_time_update_callback != undefined ){
		active_config.after_time_update_callback = config.after_time_update_callback;
	}

	if( config.before_song_information_set_callback != undefined ){
		active_config.before_song_information_set_callback = config.before_song_information_set_callback;
	}

	if( config.after_song_information_set_callback != undefined ){
		active_config.after_song_information_set_callback = config.after_song_information_set_callback;
	}

	if( config.before_song_added_callback != undefined ){
		active_config.before_song_added_callback = config.before_song_added_callback;
	}

	if( config.after_song_added_callback != undefined ){
		active_config.after_song_added_callback = config.after_song_added_callback;
	}

	if( config.volume_up_amount != undefined ){
		active_config.volume_up_amount = config.volume_up_amount;
	}

	if( config.volume_down_amount != undefined ){
		active_config.volume_down_amount = config.volume_down_amount;
	}

	if( config.continue_next != undefined ){
		active_config.continue_next = config.continue_next;
	}

	if( active_config.start_song != null ){

		active_config.active_song.src = active_config.songs[active_config.start_song].url;
		set_active_song_information( active_config.songs[active_config.start_song] );
		active_config.list_playing_index = active_config.start_song;

		if( active_config.start_song.live == 'undefined' ){
			active_config.start_song.live = false;
		}

	}else{
		if( active_config.songs.length != 0 ){
			active_config.active_song.src = active_config.songs[0].url;
			set_active_song_information( active_config.songs[0] );
			active_config.list_playing_index = 0;
		}else{
			console.log("Please define a an array of songs!");
		}
	}

	bind_song_additions();
}


/*
|--------------------------------------------------------------------------
| Web Desktop Event Handlers
|--------------------------------------------------------------------------
| Binds Amplitude events for web desktop ( click events )
|	List of IDs that AmplitudeJS looks for to bind events to:
|
|	play
|	
|	stop
|
|	pause
|
|	play-pause
|
|	mute
|
|	shuffle
|
|	next
|
|	previous
|
|	song-slider
|
|	volume-slider
|
|	volume-up
|
|	volume-down
|
|	"ended" event listener
|
|	play-pause classes
|
|	song-slider classes
|
*/
function web_desktop() {
	//Sets the Play button functionality
	if(document.getElementById('play')){
		document.getElementById('play').addEventListener('click', function() {
			//Need to add information regarding what we should play. AKA playlist-song or song
			play_song();
		});
	}

	//Sets the Stop button functionality
	if(document.getElementById('stop')){
		document.getElementById('stop').addEventListener('click', function(){
			stop_song();
		});
	}

	//Sets the Pause button functionality
	if(document.getElementById('pause')){
		document.getElementById('pause').addEventListener('click', function() {
			pause_song();
		});
	}

	//Sets the Play/Pause toggle functionality
	if(document.getElementById('play-pause')){
		document.getElementById('play-pause').addEventListener('click', function(){
			if ( active_config.active_song.paused ){
				var play_pause_button_new_class = ' playing';

				this.className = this.className.replace('paused', '');

				this.className = this.className.replace(play_pause_button_new_class, '');
				this.className = this.className + play_pause_button_new_class;
			}else{

				var play_pause_button_new_class = ' paused';

				this.className = this.className.replace('playing', '');

				this.className = this.className.replace(play_pause_button_new_class, '');
				this.className = this.className + play_pause_button_new_class;		
			}   
			play_pause();
		});
	}
	
	//Mute button functionality
	if(document.getElementById('mute')){
		document.getElementById('mute').addEventListener('click', function(obj){
			mute();
		});
	}

	//Initializes shuffle button
	if(document.getElementById('shuffle')){
		document.getElementById("shuffle").classList.add('shuffle-off');

		document.getElementById('shuffle').addEventListener('click', function(){
			if( active_config.shuffle ){
				this.classList.add('shuffle-off');
				this.classList.remove('shuffle-on');
			}else{
				this.classList.add('shuffle-on');
				this.classList.remove('shuffle-off');
			}
			shuffle_playlist();
		});
	}

	//Initializes next button
	if(document.getElementById('next')){
		document.getElementById('next').addEventListener('click', function(){
			next_song();
		});
	}

	//Initializes previous button
	if(document.getElementById('previous')){
		document.getElementById('previous').addEventListener('click', function(){
			previous_song();
		});
	}

	//Initializes the song slider
	if(document.getElementById('song-slider')){
		document.getElementById('song-slider').addEventListener('input', handle_song_sliders);
	}

	//Initializes the song volume slider
	if(document.getElementById('volume-slider')){
		document.getElementById('volume-slider').addEventListener('input', function(){
			volume_update( this.value );
		});
	}
	//Sets the volume up button functionality
	if(document.getElementById('volume-up')){
		document.getElementById('volume-up').addEventListener('click', function(){
			change_volume( 'up' ); 
		});
	}

	//Sets the volume down button functionality
	if(document.getElementById('volume-down')){
		document.getElementById('volume-down').addEventListener('click', function(){
			change_volume( 'down' ); 
		});
	}

	//Binds to ending of a song if the user wants to continue to the next song upon completion.
	if( active_config.continue_next ){
		active_config.active_song.addEventListener("ended", function() {
			next_song();
		});
	}

	//Binds play_pause to the classes for multiple play and pause for multiple songs and playlist
	var play_pause_classes = document.getElementsByClassName("play-pause");

    for( var i = 0; i < play_pause_classes.length; i++ ){
        play_pause_classes[i].addEventListener('click', handle_play_pause_classes );
    }

    //Binds to multiple track sliders for multiple song integrations
    var song_sliders = document.getElementsByClassName("song-slider");

    for( var i = 0; i < song_sliders.length; i++ ){
    	song_sliders[i].addEventListener('input', handle_song_sliders );
    }
    /*
    active_config.playlist.sort(function(a, b) {
		return compareStrings(a.name, b.name);
	});
    console.log( active_config.playlist );
    */
}

/*
|--------------------------------------------------------------------------
| Web Mobile Event Handlers
|--------------------------------------------------------------------------
| Binds Amplitude events for web mobile ( touchstart events )
|	List of IDs that AmplitudeJS looks for to bind events to:
|	
|	play
|
|	stop
|
|	pause
|
|	play-pause
|
|	mute
|
|	shuffle
|
|	next
|
|	previous
|
|	song-slider
|
|	volume-slider
|
|	volume-up
|
|	volume-down
|
|	"ended" event listener
|
|	play-pause classes
|
|	song-slider classes
|
*/
function web_mobile( ){
	//Sets the Play button functionality
	if(document.getElementById('play')){
		document.getElementById('play').addEventListener('touchstart', function() {
			//Need to add information regarding what we should play. AKA playlist-song or song
			play_song();
		});
	}

	//Sets the Stop button functionality
	if(document.getElementById('stop')){
		document.getElementById('stop').addEventListener('touchstart', function(){
			stop_song();
		});
	}

	//Sets the Pause button functionality
	if(document.getElementById('pause')){
		document.getElementById('pause').addEventListener('touchstart', function() {
			pause_song();
		});
	}

	//Sets the Play/Pause toggle functionality
	if(document.getElementById('play-pause')){
		document.getElementById('play-pause').addEventListener('touchstart', function(){
			if ( active_config.active_song.paused ){
				var play_pause_button_new_class = ' playing';

				this.className = this.className.replace('paused', '');

				this.className = this.className.replace(play_pause_button_new_class, '');
				this.className = this.className + play_pause_button_new_class;
			}else{

				var play_pause_button_new_class = ' paused';

				this.className = this.className.replace('playing', '');

				this.className = this.className.replace(play_pause_button_new_class, '');
				this.className = this.className + play_pause_button_new_class;		
			}   
			play_pause();
		});
	}
	
	//Mute button functionality
	if(document.getElementById('mute')){
		if( /iPhone|iPad|iPod/i.test(navigator.userAgent) ) {
			console.log( 'iOS does NOT allow volume to be set through javascript: https://developer.apple.com/library/safari/documentation/AudioVideo/Conceptual/Using_HTML5_Audio_Video/Device-SpecificConsiderations/Device-SpecificConsiderations.html#//apple_ref/doc/uid/TP40009523-CH5-SW4' );
		}else{
			document.getElementById('mute').addEventListener('touchstart', function(obj){
				mute();
			});
		}
	}

	//Initializes shuffle button
	if(document.getElementById('shuffle')){
		document.getElementById("shuffle").classList.add('shuffle-off');

		document.getElementById('shuffle').addEventListener('touchstart', function(){
			if( active_config.shuffle ){
				this.classList.add('shuffle-off');
				this.classList.remove('shuffle-on');
			}else{
				this.classList.add('shuffle-on');
				this.classList.remove('shuffle-off');
			}
			shuffle_playlist();
		});
	}

	//Initializes next button
	if(document.getElementById('next')){
		document.getElementById('next').addEventListener('touchstart', function(){
			next_song();
		});
	}

	//Initializes previous button
	if(document.getElementById('previous')){
		document.getElementById('previous').addEventListener('touchstart', function(){
			previous_song();
		});
	}

	//Initializes the song slider
	if(document.getElementById('song-slider')){
		document.getElementById('song-slider').addEventListener('input', handle_song_sliders);
	}

	//Initializes the song volume slider
	if(document.getElementById('volume-slider')){
		if( /iPhone|iPad|iPod/i.test(navigator.userAgent) ) {
			console.log( 'iOS does NOT allow volume to be set through javascript: https://developer.apple.com/library/safari/documentation/AudioVideo/Conceptual/Using_HTML5_Audio_Video/Device-SpecificConsiderations/Device-SpecificConsiderations.html#//apple_ref/doc/uid/TP40009523-CH5-SW4' );
		}else{
			document.getElementById('volume-slider').addEventListener('input', function(){
				volume_update( this.value );
			});
		}
	}
	//Sets the volume up button functionality
	if(document.getElementById('volume-up')){
		if( /iPhone|iPad|iPod/i.test(navigator.userAgent) ) {
			console.log( 'iOS does NOT allow volume to be set through javascript: https://developer.apple.com/library/safari/documentation/AudioVideo/Conceptual/Using_HTML5_Audio_Video/Device-SpecificConsiderations/Device-SpecificConsiderations.html#//apple_ref/doc/uid/TP40009523-CH5-SW4' );
		}else{
			document.getElementById('volume-up').addEventListener('touchstart', function(){
				change_volume( 'up' ); 
			});
		}
	}

	//Sets the volume down button functionality
	if(document.getElementById('volume-down')){
		if( /iPhone|iPad|iPod/i.test(navigator.userAgent) ) {
			console.log( 'iOS does NOT allow volume to be set through javascript: https://developer.apple.com/library/safari/documentation/AudioVideo/Conceptual/Using_HTML5_Audio_Video/Device-SpecificConsiderations/Device-SpecificConsiderations.html#//apple_ref/doc/uid/TP40009523-CH5-SW4' );
		}else{
			document.getElementById('volume-down').addEventListener('touchstart', function(){
				change_volume( 'down' ); 
			});
		}
	}

	//Binds to ending of a song if the user wants to continue to the next song upon completion.
	if( active_config.continue_next ){
		active_config.active_song.addEventListener("ended", function() {
			next_song();
		});
	}

	//Binds play_pause to the classes for multiple play and pause for multiple songs and playlist
	var play_pause_classes = document.getElementsByClassName("play-pause");

    for( var i = 0; i < play_pause_classes.length; i++ ){
        play_pause_classes[i].addEventListener('touchstart', handle_play_pause_classes );
    }

     //Binds to multiple track sliders for multiple song integrations
    var song_sliders = document.getElementsByClassName("song-slider");

    for( var i = 0; i < song_sliders.length; i++ ){
    	song_sliders[i].addEventListener('input', handle_song_sliders );
    }
}

/*
|--------------------------------------------------------------------------
| Start
|--------------------------------------------------------------------------
| Begins Amplitude functionality
|	
*/

function start(){
	if( document.getElementById('song-time-visualization') ){
		document.getElementById('song-time-visualization').innerHTML = '<div id="song-time-visualization-status"></div>';
		document.getElementById('song-time-visualization-status').setAttribute( "style", "width:0px"); 
	}
	if( active_config.auto_play ){
		play_pause();
	}
}

/*
|--------------------------------------------------------------------------
| Amplitude Play Function
|--------------------------------------------------------------------------
| Plays the active amplitude song.
*/

function play_song(){
	//Before play is called
	if( active_config.before_play_callback ){
		var before_play_callback_function = window[active_config.before_play_callback];
		before_play_callback_function();
	}

	if( active_config.active_song_information.live != 'undefined' && active_config.active_song_information.live ){
		reconnect_stream();
	}
	
	var song_sliders = document.getElementsByClassName("song-slider");

    for( var i = 0; i < song_sliders.length; i++ ){
    	if( song_sliders[i].getAttribute('song-slider-index') != active_config.list_playing_index){
    		song_sliders[i].value = 0;
    	}
    }

	active_config.active_song.play();
	set_song_info();

	//After play is called
	if( active_config.after_play_callback ){
		var after_play_callback_function = window[active_config.after_play_callback];
		after_play_callback_function();
	}
}

/*
|--------------------------------------------------------------------------
| Amplitude Stop Song
|--------------------------------------------------------------------------
| Stops song by setting the current time to 0 and pausing the song.
| 
*/
function stop_song(){
	//Before stop is called
	if( active_config.before_stop_callback ){
		var before_stop_callback_function = window[active_config.before_stop_callback];
		before_stop_callback_function();
	}

	active_config.active_song.currentTime = 0;
	active_config.active_song.pause();

	if(typeof active_config.active_song.live != 'undefined'){
		if( active_config.active_song.live ){
			disconnect_stream();
		}
	}

	//After stop is called
	if( active_config.after_stop_callback ){
		var after_stop_callback_function = window[active_config.after_stop_callback];
		after_stop_callback_function();
	}
}

/*
|--------------------------------------------------------------------------
| Amplitude Pause Function
|--------------------------------------------------------------------------
| Pauses the song. If the song is live, the stream is disconnected.
*/
function pause_song(){
	//Fires pause callback
	if( active_config.before_pause_callback ){
		var before_pause_callback_function = window[active_config.before_pause_callback];
		before_pause_callback_function();
	}

	active_config.active_song.pause();
	if( active_config.active_song_information.live ){
		disconnect_stream();
	}

	if( active_config.after_pause_callback ){
		var after_pause_callback_function = window[active_config.active_pause_callback];
		after_pause_callback_function();
	}
}

/*
|--------------------------------------------------------------------------
| Amplitude Play/Pause Function
|--------------------------------------------------------------------------
| Play and pause function determines whether or not the song is paused or not.
| If it's paused, the play function is called. If it's playing, the pause function is called.
*/
function play_pause(){
	//Checks to see if the song is paused, if it is, play it from where it left off otherwise pause it.
	if ( active_config.active_song.paused ){	
		play_song();
		var current_now_playing_list_item = document.querySelector('[song-index="'+active_config.list_playing_index+'"]');

		if( current_now_playing_list_item != null ){
			current_now_playing_list_item.classList.add('list-playing');
			current_now_playing_list_item.classList.remove('list-paused');
		}
	}else{
		pause_song();
		var current_now_playing_list_item = document.querySelector('[song-index="'+active_config.list_playing_index+'"]');
		
		if( current_now_playing_list_item != null ){
			current_now_playing_list_item.classList.add('list-paused');
			current_now_playing_list_item.classList.remove('list-playing');
		}
	}   
}

/*
|--------------------------------------------------------------------------
| Amplitude Update Time
|--------------------------------------------------------------------------
| Updates the current time function so it reflects where the user is in the song.
| This function is called whenever the time is updated.  This keeps the visual in sync with the actual time.
*/

function update_time(){
	if( active_config.before_time_update_callback ){
		var before_time_update_callback_function = window[active_config.before_time_update_callback];
		before_time_update_callback_function();
	}

	var current_seconds = ( Math.floor( active_config.active_song.currentTime % 60 ) < 10 ? '0' : '' ) + Math.floor( active_config.active_song.currentTime % 60 );
	var current_minutes = Math.floor( active_config.active_song.currentTime / 60 );

	var song_duration_minutes = Math.floor( active_config.active_song.duration / 60 );
	var song_duration_seconds = ( Math.floor( active_config.active_song.duration % 60 ) < 10 ? '0' : '' ) + Math.floor( active_config.active_song.duration % 60 );

	//Sets the current song location compared to the song duration.
	if( document.getElementById( 'current-time' ) ){
		document.getElementById( 'current-time' ).innerHTML = current_minutes + ":" + current_seconds;
	}
	
	if( document.getElementById( 'audio-duration' ) ){
		if( !isNaN( song_duration_minutes ) ){
			document.getElementById( 'audio-duration' ).innerHTML =  song_duration_minutes + ":" + song_duration_seconds;
		}
	}

	if( document.getElementById( 'song-slider' ) ){
		document.getElementById( 'song-slider' ).value = ( active_config.active_song.currentTime / active_config.active_song.duration ) * 100;
	}

	if( document.getElementById( 'song-time-visualization') ){
		var visualization_width = document.getElementById('song-time-visualization').offsetWidth;

		document.getElementById('song-time-visualization-status').setAttribute("style","width:"+( visualization_width * ( active_config.active_song.currentTime / active_config.active_song.duration ) ) + 'px'); 
	}

	//Multiple songs have multiple sources of control.
	if( active_config.songs.length > 1 ){
		var current_now_playing_song_slider = document.querySelector('[song-slider-index="'+active_config.list_playing_index+'"]');

		if( current_now_playing_song_slider != null ){
			current_now_playing_song_slider.value = ( active_config.active_song.currentTime / active_config.active_song.duration ) * 100;
		}

		var current_time_display = document.querySelector('[current-time-index="'+active_config.list_playing_index+'"]');
		if( current_time_display != null ){
			current_time_display.innerHTML = current_minutes + ":" + current_seconds;
		}

		var current_song_duration = document.querySelector('[audio-duration-index="'+active_config.list_playing_index+'"]');
		if( current_song_duration != null ){
			if( !isNaN(song_duration_minutes) ){
				current_song_duration.innerHTML = song_duration_minutes + ":" + song_duration_seconds;
			}
		}
	}


	if( active_config.after_time_update_callback ){
		var after_time_update_callback_function = window[active_config.after_time_update_callback];
		after_time_update_callback_function();
	}
}

/*
|--------------------------------------------------------------------------
| Amplitude Volume Update
|--------------------------------------------------------------------------
| Updates the volume to a number passed in
*/

function volume_update( number ){
	if( active_config.before_volume_change_callback ){
		var before_volume_change_callback_function = window[active_config.before_volume_change_callback];
		before_volume_change_callback_function();
	}

	active_config.active_song.volume = number / 100;

	active_config.volume = number / 100;

	if( active_config.after_volume_change_callback ){
		var after_volume_change_callback_function = window[active_config.after_volume_change_callback];
		after_volume_change_callback_function();
	}
}

/*
|--------------------------------------------------------------------------
| Amplitude Volume Update
|--------------------------------------------------------------------------
| Changes the volume up or down a specific number
*/

function change_volume( direction ){

	if( active_config.volume >= 0 && direction == "down" ){
		if( ( ( active_config.volume * 100 ) - active_config.volume_down_amount ) > 0 ){
			volume_update( ( ( active_config.volume * 100 ) - active_config.volume_down_amount ) );
		}else{
			volume_update( 0 );
		}
	}

	if( active_config.volume <= 1 && direction == "up" ){
		if( ( ( active_config.volume * 100 ) + active_config.volume_up_amount ) < 100 ){
			volume_update( ( ( active_config.volume * 100 ) + active_config.volume_up_amount ) );
		}else{
			volume_update( 100 );
		}
	}

	if( document.getElementById('volume-slider')){
		document.getElementById('volume-slider').value = ( active_config.volume * 100 );
	}

}

/*
|--------------------------------------------------------------------------
| Amplitude Set Active Song Information
|--------------------------------------------------------------------------
| Sets the active song information to be accessed by the users
*/
function set_active_song_information( song ){
	if( active_config.before_song_information_set_callback ){
		var before_song_information_set_callback_function = window[active_config.before_song_information_set_callback];
		before_song_information_set_callback_function();
	}

	if( song.name != 'undefined' ){
		active_config.active_song_information.song_title = song.name;
	}else{
		active_config.active_song_information.song_title = '';
	}

	if( song.aritst != 'undefined' ){
		active_config.active_song_information.artist = song.artist;
	}else{
		active_config.active_song_information.artist = '';
	}

	if( song.cover_art_url != 'undefined' ){
		active_config.active_song_information.cover_art_url = song.cover_art_url;
	}else{
		active_config.active_song_information.cover_art_url = '';
	}

	if( song.album != 'undefined' ){
		active_config.active_song_information.album = song.album;
	}else{
		active_config.active_song_information.album = '';
	}

	if( song.live != 'undefined' ){
		active_config.active_song_information.live = song.live;
	}else{
		active_config.active_song_information.live = false;
	}

	if( song.url != 'undefined' ){
		active_config.active_song_information.url = song.url;
	}else{
		active_config.active_song_information.url = '';
	}

	if( song.visual_id != 'undefined' ){
		active_config.active_song_information.visual_id = song.visual_id;
	}else{
		active_config.active_song_information.visual_id = '';
	}

	active_song_information = active_config.active_song_information;

	if( active_config.after_song_information_set_callback ){
		var after_song_information_set_callback_function = window[active_config.after_song_information_set_callback];
		after_song_information_set_callback_function();
	}
}

/*
|--------------------------------------------------------------------------
| Amplitude Set Location
|--------------------------------------------------------------------------
| Sets the location of the song based off of the location of the slider.
*/
function set_song_position( value ){
	active_config.active_song.currentTime = active_config.active_song.duration * ( value / 100 );
}


/*
|--------------------------------------------------------------------------
| Amplitude Mute
|--------------------------------------------------------------------------
| Mutes the audio
| 
*/
function mute(){
	if( active_config.before_mute_callback ){
		var before_mute_callback_function = window[active_config.before_mute_callback];
		before_mute_callback_function();
	}

	if( active_config.volume == 0){
		active_config.volume = active_config.pre_mute_volume;
	}else{
		active_config.pre_mute_volume = active_config.volume;
		active_config.volume = 0;
	}

	volume_update( active_config.volume * 100 );
	if( document.getElementById('volume-slider')){
		document.getElementById('volume-slider').value = ( active_config.volume * 100 );
	}

	if( active_config.after_mute_callback ){
		var after_mute_callback_function = window[active_config.after_mute_callback];
		after_mute_callback_function();
	}
}

/*
|--------------------------------------------------------------------------
| Amplitude Set Song Info
|--------------------------------------------------------------------------
| Sets the song info from the active_song_information array so it can be 
| displayed when the song is changed.
| 
*/
function set_song_info() {
	//Sets the information regarding the song playing.
	if(document.getElementById('now-playing-artist')){
		document.getElementById('now-playing-artist').innerHTML = active_config.active_song_information.artist;
	}

	if(document.getElementById('now-playing-title')){
		document.getElementById('now-playing-title').innerHTML = active_config.active_song_information.song_title;
	}

	if(document.getElementById('now-playing-album')){
		document.getElementById('now-playing-album').innerHTML = active_config.active_song_information.album;
	}
	
	//Add Default image
	if(document.getElementById('album-art')){
		if( active_config.active_song_information.cover_art_url != null){
			document.getElementById('album-art').innerHTML ='<img src="'+active_config.active_song_information.cover_art_url+'" class="album-art-image"/>';
		}
	}

	var current_now_playing_item = document.getElementsByClassName('now-playing');

	if( current_now_playing_item.length > 0 ){
		current_now_playing_item[0].classList.remove('now-playing');
	}

	if( active_config.active_song_information.visual_id != undefined ){
		if( document.getElementById( active_config.active_song_information.visual_id ) ){
			document.getElementById( active_config.active_song_information.visual_id ).classList.add('now-playing');
		}
	}
}


/*
|--------------------------------------------------------------------------
| Amplitude Next Song
|--------------------------------------------------------------------------
| Handles next song click.
| 
*/
function next_song() {
	if( active_config.before_next_callback ){
		var before_next_callback_function = window[active_config.before_next_callback];
		before_next_callback_function();
	}
	//If ths shuffle is activated, then go to next song in the shuffle array. Otherwise go down the playlist.
	if( active_config.shuffle ){
		if( typeof active_config.shuffle_list[ parseInt( active_config.playlist_index ) + 1 ] != 'undefined' ){
			active_config.active_song.src = active_config.shuffle_list[ parseInt( active_config.playlist_index ) + 1 ].url;
			active_config.list_playing_index = active_config.shuffle_list[ parseInt( active_config.playlist_index ) + 1 ].original;
			active_config.playlist_index = parseInt( active_config.playlist_index ) + 1;

		}else{
			active_config.active_song.src = active_config.shuffle_list[0].url;
			active_config.playlist_index = 0;

			active_config.list_playing_index = active_config.shuffle_list[0].original;
		}

		set_active_song_information( active_config.shuffle_list[ parseInt( active_config.playlist_index ) ] );
		play_song();
	}else{
		if ( typeof active_config.songs[ parseInt( active_config.playlist_index ) + 1 ] != 'undefined' ) {
			active_config.active_song.src = active_config.songs[ parseInt( active_config.playlist_index ) + 1 ].url;
			active_config.playlist_index = parseInt( active_config.playlist_index ) + 1;
		}else{
			active_config.active_song.src = active_config.songs[0].url;
			active_config.playlist_index = 0;
		}

		set_active_song_information( active_config.songs[ parseInt( active_config.playlist_index ) ] );
		play_song();


		active_config.list_playing_index = parseInt( active_config.playlist_index );
	}


	set_play_pause();
	set_playlist_play_pause();

	if( active_config.after_next_callback ){
		var after_next_callback_function = window[active_config.after_next_callback];
		after_next_callback_function();
	}
}

/*
|--------------------------------------------------------------------------
| Amplitude Previous Song
|--------------------------------------------------------------------------
| Handles previous song click.
| 
*/
function previous_song() {
	if( active_config.before_prev_callback ){
		var before_prev_callback_function = window[active_config.before_prev_callback];
		before_prev_callback_function();
	}
	//If the shuffle is activated, then go to the previous song in the shuffle array.  Otherwise go back in the playlist.
	if( active_config.shuffle ){
		if( typeof active_config.shuffle_list[ parseInt( active_config.playlist_index ) - 1 ] != 'undefined' ){
			active_config.active_song.src = active_config.shuffle_list[ parseInt( active_config.playlist_index ) - 1 ].url;
			active_config.list_playing_index = active_config.shuffle_list[ parseInt( active_config.playlist_index ) - 1 ].original;
			active_config.playlist_index = ( parseInt( active_config.playlist_index ) - 1 );
		}else{
			active_config.active_song.src = active_config.shuffle_list[ active_config.shuffle_list.length - 1 ].url;
			active_config.playlist_index = ( active_config.shuffle_list.length - 1 );

			active_config.list_playing_index = active_config.shuffle_list[( active_config.shuffle_list.length - 1 )].original;
		}
		set_active_song_information( active_config.shuffle_list[ parseInt( active_config.playlist_index ) ] );
		play_song();
	}else{
		if ( typeof active_config.songs[ parseInt( active_config.playlist_index ) - 1 ] != 'undefined' ) {
			active_config.active_song.src = active_config.songs[ parseInt( active_config.playlist_index ) - 1 ].url;
			active_config.playlist_index = ( parseInt( active_config.playlist_index ) - 1 );
		}else{
			active_config.active_song.src = active_config.songs[ active_config.songs.length - 1].url;
			active_config.playlist_index = ( active_config.songs.length - 1 );
		}

		set_active_song_information( active_config.songs[ parseInt( active_config.playlist_index ) ] );
		play_song();

		active_config.list_playing_index = parseInt( active_config.playlist_index );
	}
	
	
	set_play_pause();
	set_playlist_play_pause();

	if( active_config.after_prev_callback ){
		var after_prev_callback_function = window[active_config.after_prev_callback];
		after_prev_callback_function();
	}
}

/*
|--------------------------------------------------------------------------
| Amplitude Set Playlist Play Pause
|--------------------------------------------------------------------------
| After next or previous, we need to visually update the playlist display
| accordingly.
| 
*/
function set_playlist_play_pause(){
	var play_pause_classes = document.getElementsByClassName("play-pause");

	for( var i = 0; i < play_pause_classes.length; i++ ){
    	var play_pause_button_new_class = ' list-paused';

		play_pause_classes[i].className = play_pause_classes[i].className.replace('list-playing', '');

		play_pause_classes[i].className = play_pause_classes[i].className.replace(play_pause_button_new_class, '');
		play_pause_classes[i].className = play_pause_classes[i].className + play_pause_button_new_class;
    }

	var current_now_playing_list_item = document.querySelector('[song-index="'+active_config.list_playing_index+'"]');

	if( current_now_playing_list_item != null ){
		current_now_playing_list_item.classList.add('list-playing');
		current_now_playing_list_item.classList.remove('list-paused');
	}
}

/*
|--------------------------------------------------------------------------
| Amplitude Set Play Pause
|--------------------------------------------------------------------------
| After next or previous, we need to visually update the play pause display
| 
*/
function set_play_pause(){
	var play_pause_button = document.getElementById('play-pause');

	if( play_pause_button != undefined ){
		if ( active_config.active_song.paused ){
			var play_pause_button_new_class = ' paused';

			play_pause_button.className = play_pause_button.className.replace('playing', '');

			play_pause_button.className = play_pause_button.className.replace(play_pause_button_new_class, '');
			play_pause_button.className = play_pause_button.className + play_pause_button_new_class;
		}else{

			var play_pause_button_new_class = ' playing';

			play_pause_button.className = play_pause_button.className.replace('paused', '');

			play_pause_button.className = play_pause_button.className.replace(play_pause_button_new_class, '');
			play_pause_button.className = play_pause_button.className + play_pause_button_new_class;		
		} 
	}
}
/*
|--------------------------------------------------------------------------
| Amplitude Shuffle Playlist
|--------------------------------------------------------------------------
| Handles the shuffling function.
| 
*/
function shuffle_playlist(){
	//If the shuffle button is activated when clicked, turn it off.
	if( active_config.shuffle ){
		active_config.shuffle = false;
		active_config.shuffle_list = {};
		
	}else{
		active_config.shuffle = true;
		shuffle_songs();
	}
}

/*
|--------------------------------------------------------------------------
| Shuffle Songs
|--------------------------------------------------------------------------
| Shuffles songs.
| Based off of: http://www.codinghorror.com/blog/2007/12/the-danger-of-naivete.html
| 
*/
function shuffle_songs(){
	if( active_config.before_shuffle_callback ){
		var before_shuffle_callback_function = window[active_config.before_shuffle_callback];
		before_shuffle_callback_function();
	}
	var shuffle_playlist_temp = new Array( active_config.songs.length );

	for ( i = 0; i < active_config.songs.length; i++ ) {
		shuffle_playlist_temp[i] = active_config.songs[i];
		shuffle_playlist_temp[i]['original'] = i;
	}

	for ( i = active_config.songs.length - 1; i > 0; i-- ){
		var rand_num = Math.floor( ( Math.random() * active_config.songs.length ) + 1 );
		shuffle_swap( shuffle_playlist_temp, i, rand_num - 1 );
	}

	active_config.shuffle_list = shuffle_playlist_temp;

	if( active_config.after_shuffle_callback ){
		var after_shuffle_callback_function = window[active_config.after_shuffle_callback];
		after_shuffle_callback_function();
	}
}

/*
|--------------------------------------------------------------------------
| Shuffle Swap
|--------------------------------------------------------------------------
| Swaps out certain array indexes.
| Helper for the shuffle_songs function
| 
*/
function shuffle_swap(shuffle_list, original, random) {
	var temp = shuffle_list[ original ];
	shuffle_list[ original ] = shuffle_list[ random ];
	shuffle_list[ random ] = temp;
}

/*
|--------------------------------------------------------------------------
| Binds Time
|--------------------------------------------------------------------------
| When the audio track time changes, the time elements get updated.
| 
*/
function bind_time_update(){
	active_config.active_song.addEventListener('timeupdate', function(){
		update_time();
	});
}


/*
|--------------------------------------------------------------------------
| Prepares list play pause
|--------------------------------------------------------------------------
| Prepares an item out of a list to be played. Fired from a class instead of
| an ID
| 
*/
function prepare_list_play_pause( index ){
	if( index != active_config.list_playing_index ){
		active_config.active_song.src = active_config.songs[ index ].url;
		set_active_song_information( active_config.songs[ index ] );
	}

	active_config.list_playing_index = index;
	
	if( active_config.active_song.paused ){
		play_song();
		if( document.getElementById('play-pause') ){
			var play_pause_button_new_class = 'playing';

			document.getElementById('play-pause').className = document.getElementById('play-pause').className.replace('paused', '');

			document.getElementById('play-pause').className = document.getElementById('play-pause').className.replace(play_pause_button_new_class, '');
			document.getElementById('play-pause').className = document.getElementById('play-pause').className + play_pause_button_new_class;
		}
	}else{
		pause_song();
		if( document.getElementById('play-pause') ){
			var play_pause_button_new_class = 'paused';

			document.getElementById('play-pause').className = document.getElementById('play-pause').className.replace('playing', '');

			document.getElementById('play-pause').className = document.getElementById('play-pause').className.replace(play_pause_button_new_class, '');
			document.getElementById('play-pause').className = document.getElementById('play-pause').className + play_pause_button_new_class;
		}
	}
}

/*
|--------------------------------------------------------------------------
| Add Song
|--------------------------------------------------------------------------
| Dynamically adds a song to the playlist or song list.
| 
*/
function add_song( song ){
	active_config.songs.push( song );
	return active_config.songs.length - 1;
}

/*
|--------------------------------------------------------------------------
| Bind Song Additions
|--------------------------------------------------------------------------
| Called when there is a node inserted into the document. If it's not
| an amplitude album art image (the only other time amplitude inserts
| into the DOM), then rebind the classes for play and pause and song sliders.
| We remove any existing listeners first so everything is updated.
| 
*/
function bind_song_additions(){
	document.addEventListener('DOMNodeInserted', function( e ){

		if( e.target.classList != undefined && e.target.classList[0] != 'album-art-image' ){
			var play_pause_classes = document.getElementsByClassName("play-pause");

	  		for( var i = 0; i < play_pause_classes.length; i++ ){
	  			if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
	  				play_pause_classes[i].removeEventListener('touchstart', handle_play_pause_classes );

	        		play_pause_classes[i].addEventListener('touchstart', handle_play_pause_classes );
	  			}else{
	  				play_pause_classes[i].removeEventListener('click', handle_play_pause_classes );

	        		play_pause_classes[i].addEventListener('click', handle_play_pause_classes );
	        	}
			}

			var song_sliders = document.getElementsByClassName("song-slider");

		    for( var i = 0; i < song_sliders.length; i++ ){
		    	song_sliders[i].removeEventListener('input', handle_song_sliders );
		    	song_sliders[i].addEventListener('input', handle_song_sliders );
		    }
		}
	});
	
}

/*
|--------------------------------------------------------------------------
| Handle Play Pause classes
|--------------------------------------------------------------------------
| When a play pause class is clicked, it determines whether the song should
| play or pause and which elements to update.
| 
*/
function handle_play_pause_classes( ){
	var play_pause_classes = document.getElementsByClassName("play-pause");
			        	
	//If the songs change then we set all visual elements to pause.
	if( this.getAttribute('song-index') != active_config.list_playing_index ){
		for( var i = 0; i < play_pause_classes.length; i++ ){
	    	var play_pause_button_new_class = ' list-paused';

			play_pause_classes[i].className = play_pause_classes[i].className.replace(' list-playing', '');

			play_pause_classes[i].className = play_pause_classes[i].className.replace(play_pause_button_new_class, '');
			play_pause_classes[i].className = play_pause_classes[i].className + play_pause_button_new_class;
	    }
	    //Force set new click to playing. All other classes will be paused.
	    var play_pause_button_new_class = ' list-playing';

		this.className = this.className.replace(' list-paused', '');

		this.className = this.className.replace(play_pause_button_new_class, '');
		this.className = this.className + play_pause_button_new_class;
	}else{

	    if( active_config.active_song.paused ){
			var play_pause_button_new_class = ' list-playing';

			this.className = this.className.replace(' list-paused', '');

			this.className = this.className.replace(play_pause_button_new_class, '');
			this.className = this.className + play_pause_button_new_class;
		}else{
			var play_pause_button_new_class = ' list-paused';

			this.className = this.className.replace(' list-playing', '');

			this.className = this.className.replace(play_pause_button_new_class, '');
			this.className = this.className + play_pause_button_new_class;
		}
	}
	prepare_list_play_pause( this.getAttribute('song-index') );
}
/*
|--------------------------------------------------------------------------
| Handle Song Sliders
|--------------------------------------------------------------------------
| Sets the song position based off of which track is playing and adjusted.
| 
*/
function handle_song_sliders(){
	set_song_position( this.value );
}

/*
|--------------------------------------------------------------------------
| Disconnects from a live stream
|--------------------------------------------------------------------------
| By disconnecting from the live stream (called from stop) the buffering
| stops so the user doesn't download an insane amount of data if they aren't
| listening to the live stream.
| Thanks to help from: http://blog.pearce.org.nz/2010/11/how-to-stop-video-or-audio-element.html
| 
*/

function disconnect_stream(){
	active_config.active_song.pause();
	active_config.active_song.src = ""; 
	active_config.active_song.load(); 
}
/*
|--------------------------------------------------------------------------
| Reconnect from a live stream
|--------------------------------------------------------------------------
| Reconnects to a live stream when a user clicks play. This is so the user
| doesn't get a load of buffering when they aren't listening. Reconnects
| when the user clicks play.
| 
*/
function reconnect_stream(){
	active_config.active_song.src = active_config.active_song_information.url; 
	active_config.active_song.load();
}

/*
function compareStrings(a, b) {
	// Assuming you want case-insensitive comparison
	a = a.toLowerCase();
	b = b.toLowerCase();

	return (a < b) ? -1 : (a > b) ? 1 : 0;
}



function live_callback_hooks(){
if(typeof config != 'undefined'){
if(typeof config.live_checkup != 'undefined'){
if((typeof config.live_checkup.checkup_interval != 'undefined') && (typeof config.live_checkup.checkup_function != 'undefined') ) {
setInterval(function(){
var checkup_function = window[config.live_checkup.checkup_function];
checkup_function(); 
}, config.live_checkup.checkup_interval);
}
}
}
}
*/