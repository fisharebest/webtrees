/*
Copyright (c) 2006, Gustavo Ribeiro Amigo
All rights reserved.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

    * Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
    * Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
    * Neither the name of the author nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

*/

    function Sound(options) {
        
        this.options = options;
        if(this.options == undefined) this.options = new Object();
        
        if(!this.options.swfLocation) {
            this.options.swfLocation = "js/SoundBridge.swf";
        }
    
        if(Sound.id_count == undefined) {
            Sound.id_count = 1;
        } else {
            Sound.id_count ++;
        }
        
        if(Sound.instances == undefined) {
            Sound.instances = new Object();
        }
        
        this.object_id = 'object_id_' + Sound.id_count;
        
        Sound.instances[this.object_id] = this;
        
        movie_swf = this.options.swfLocation;
        movie_id = this.object_id;
        
        movie_element = "";
        movie_element += '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="0" height="0"'; 
        movie_element += ' id="' + movie_id+ '"'; 
        movie_element += ' align="middle">';
        movie_element += '<param name="movie" value="'+movie_swf+'" />';
        movie_element += '<param name="quality" value="high" />';
        movie_element += '<param name="bgcolor" value="#ffffff" />';
        movie_element += '<param name="FlashVars" value="id='+ movie_id +'"/>';
        movie_element += '<param name="allowScriptAccess" value="allowScriptAccess"/>';
        movie_element += '<embed src="'+movie_swf+'" FlashVars="id='+ movie_id +'"'; 
        movie_element += ' allowScriptAccess="always" quality="high" bgcolor="#ffffff" width="0" height="0"'; 
        movie_element += ' name="' + movie_id + '" align="middle" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />';
        movie_element += '</object>';    

/*
		this.so = new SWFObject(movie_swf, movie_id, "0", "0", "8", "#ffffff");
		this.so.addParam("quality", "high");
		this.so.addParam("FlashVars", "id="+ movie_id);
		this.so.addParam("allowScriptAccess", "always");
*/		
		
        if( document.getElementById('__sound_flash__') == undefined) {
            var element = document.createElement("div");
            element.id = "__sound_flash__";
            document.body.appendChild(element);
        }
        
        document.getElementById('__sound_flash__').innerHTML += movie_element; 
        //this.so.write('__sound_flash__');
        
    }
    
    Sound.prototype.loadSound = function(url, streaming) {
        return Sound.__call('loadSound',this.object_id, url, streaming);
    }
    
    Sound.prototype.start= function() {
        return Sound.__call('start', this.object_id);
    }

    Sound.prototype.stop = function() {
        return Sound.__call('stop', this.object_id);
    }
    
    Sound.prototype.getId3 = function() {
        return Sound.__call('id3', this.object_id);
    }
    
	Sound.prototype.getPan = function() {
    	return Sound.__call('getPan', this.object_id);
	}
	
	Sound.prototype.getTransform = function() {
		return Sound.__call('getTransform', this.object_id);
	}
	
	Sound.prototype.getVolume = function(){
		return Sound.__call('getVolume', this.object_id);
	}
	
	Sound.prototype.setPan = function(value){
    	return Sound.__call('setPan', this.object_id, value);	
	}
	
	Sound.prototype.setTransform = function(transformObject){
    	return Sound.__call('setTransform', this.object_id, transformObject);		
	}
	
	Sound.prototype.setVolume = function(value){
    	return Sound.__call('setVolume', this.object_id, value);			
	}
	
	Sound.prototype.start = function(secondOffset, loops){
	    return Sound.__call('start', this.object_id, secondOffset, loops);
	}
	
	Sound.prototype.getDuration = function(){
	    return Sound.__call('getDuration', this.object_id);
	}
	
	Sound.prototype.setDuration = function(value){
	    return Sound.__call('setDuration', this.object_id, value);	
	}
	
	Sound.prototype.getPosition = function(){
        return Sound.__call('getPosition', this.object_id);		
	}
	
	Sound.prototype.setPosition = function(value){
	    return Sound.__call('setPosition', this.object_id, value);		
	}

	Sound.prototype.getBytesLoaded = function(){
	    return Sound.__call('getBytesLoaded', this.object_id);		
	}
	
	Sound.prototype.getBytesTotal = function(){
        return Sound.__call('getBytesTotal', this.object_id);			
	}
	
	Sound.prototype.onLoad = function(success){
        Sound.trace('Sound:onLoad('+success+') event triggered');
	}	
    
    Sound.onLoad = function(object_id, success) {
        //Sound.trace('Sound.onLoad('+success+') object_id=' + object_id);
        Sound.instances[object_id].onLoad(success);
    }
    
	Sound.prototype.onSoundComplete = function(){
        Sound.trace('Sound:onSoundComplete() event triggered');
	}	    
    
    Sound.onSoundComplete = function(object_id) {
        Sound.instances[object_id].onSoundComplete;
    }   
    
	Sound.prototype.onID3 = function(){
        Sound.trace('Sound:onID3() event triggered');
	}	    
    
    Sound.onID3 = function(object_id) {
        Sound.instances[object_id].onID3();        
    }    
    
    Sound.trace = function(value, isJavascript) {
        if(document.getElementById('sound_tracer') != undefined) {
            if(isJavascript == undefined || isJavascript == true) {
                document.getElementById('sound_tracer').value += 'Javascript: ' + value + '\n';            
            } else {
                document.getElementById('sound_tracer').value += value + '\n';            
            }
        }
    }  
    
    Sound.__thisMovie = function(movieName) {
        if (navigator.appName.indexOf("Microsoft") != -1) {
            return window[movieName]
        }
        else {
            return document[movieName]
        }
    }
    
    Sound.__call = function () {
        Sound.trace('Sound.__call '+ arguments[0]+ ' on object_id ' + arguments[1] );    
        var functionname = arguments[0];
        var object_id = arguments[1];        
        var justArgs = new Array();
        if (arguments.length > 1)   {
           for (var i = 2; i < arguments.length; i++ ) {
             justArgs.push(arguments[i]);
           }
        }     
  
        return Sound.__thisMovie(object_id).proxyMethods(functionname, justArgs);
    } 