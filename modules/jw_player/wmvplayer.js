/* wmvplayer.js - PhpGedView Author Brian Holland     
 * @package webtrees
 * @subpackage Module
 * @version $Id$
*/
/****************************************************************************
* JW WMV Player version 1.1, created with M$ Silverlight 1.0
*
* This file contains all logic for the JW WMV Player. For a functional setup,
* the following two files are also needed:
* - silverlight.js (for instantiating the silverlight plugin)
* - wmvplayer.xaml (or another XAML skin describing the player graphics)
*
* More info: http://www.jeroenwijering.com/?item=JW_WMV_Player
****************************************************************************/
if(typeof jeroenwijering == "undefined") {
	var jeroenwijering = new Object();
	jeroenwijering.utils = new Object();
}










/****************************************************************************
* The player wrapper; loads config variables and starts MVC cycle.
****************************************************************************/
jeroenwijering.Player = function(cnt,src,cfg) {
	this.controller;
	this.model;
	this.view;
	this.configuration = {
		backgroundcolor:'FFFFFF',
		windowless:'false',
		file:'',
		height:'260',
		image:'',
		backcolor:'FFFFFF',
		frontcolor:'000000',
		lightcolor:'000000',
		screencolor:'000000',
		width:'320',
		logo:'',
		overstretch:'false',
		shownavigation:'true',
		showstop:'false',
		showdigits:'true',
		usefullscreen:'true',
		usemute:'false',
		autostart:'false',
		bufferlength:'3',
		duration:'0',
		repeat:'false',
		sender:'',
		start:'0',
		volume:'90',
		link:'',
		linkfromdisplay:'false',
		linktarget:'_self'
	};
	for(itm in this.configuration) {
		if(cfg[itm] != undefined) {
			if (itm.indexOf('color') > 0) { 
				this.configuration[itm] = cfg[itm].substr(cfg[itm].length-6);
			} else {
				this.configuration[itm] = cfg[itm];
			}
		}
	}
	Silverlight.createObjectEx({
		source:src,
		parentElement:cnt,
		properties:{
			width:this.configuration['width'],
			height:this.configuration['height'],
			version:'1.0',
			inplaceInstallPrompt:true,
			isWindowless:this.configuration['windowless'],
			background:'#'+this.configuration['backgroundcolor']
		},
		events:{
			onLoad:this.onLoadHandler,
			onError:null
		},
		context:this
	});
}

jeroenwijering.Player.prototype = {
	addListener: function(typ,fcn) {
		this.view.listeners.push({type:typ,func:fcn});
	},

	getConfig: function() { 
		return this.configuration;
	},

	onLoadHandler: function(pid,tgt,sdr) {
		tgt.configuration['sender'] = sdr;
		tgt.controller = new jeroenwijering.Controller(tgt.configuration);
		tgt.view = new jeroenwijering.View(tgt.configuration,tgt.controller);
		tgt.model = new jeroenwijering.Model(tgt.configuration,tgt.controller,tgt.view);
		tgt.controller.startMVC(tgt.view,tgt.model);
	},

	sendEvent: function(typ,prm) {
		switch(typ.toUpperCase()) {
			case 'LINK':
				this.controller.setLink();
				break;
			case 'LOAD':
				this.controller.setLoad(prm);
				break;
			case 'MUTE':
				this.controller.setMute();
				break;
			case 'PLAY':
				this.controller.setPlay();
				break;
			case 'SCRUB':
				this.controller.setScrub(prm);
				break;
			case 'STOP':
				this.controller.setStop();
				break;
			case 'VOLUME':
				this.controller.setVolume(prm);
				break;
		}
	}
}










/****************************************************************************
* The controller of the player MVC triad, which processes all user input.
****************************************************************************/
jeroenwijering.Controller = function(cfg) {
	this.configuration = cfg;
}

jeroenwijering.Controller.prototype = {
	startMVC: function(vie,mdl) {
		this.view = vie;
		this.model = mdl;
		if(this.configuration['usemute'] == 'true') {
			this.view.onVolume(0);
			this.view.onMute(true);
			this.model.goVolume(0);
		} else {
			this.view.onVolume(this.configuration['volume']);
			this.model.goVolume(this.configuration['volume']);
		}
		if(this.configuration['autostart'] == 'true') {
			this.model.goStart();
		} else { 
			this.model.goPause();
		}
	},

	setState: function(old,stt) {
		this.state = stt;
		var pos = this.configuration['start'];
		if(old == 'Closed' && pos > 0) {
			setTimeout(jeroenwijering.utils.delegate(this,this.setScrub),200,pos);
		} 
	},

	setLink: function() {
		if (this.configuration['linktarget'].indexOf('javascript:') == 0) {
			return Function(this.configuration['linktarget']).apply();
		} else if (this.configuration['linktarget'] == '_blank') {
			window.open(this.configuration['link']);
		} else if (this.configuration['linktarget'] != '') {
			window.location = this.configuration['link'];
		}
	},

	setLoad: function(fil) {
		if(this.model.state != "Closed") {
			this.model.goStop(); 
		}
		this.configuration['file'] = fil;
		if(this.configuration['autostart'] == 'true') {
			setTimeout(jeroenwijering.utils.delegate(this.model,this.model.goStart),100);
		}
	},

	setMute: function() {
		if(this.configuration['usemute'] == 'true') {
			this.configuration['usemute'] = 'false';
			this.model.goVolume(this.configuration['volume']);
			this.view.onMute(false);
		} else {
			this.configuration['usemute'] = 'true';
			this.model.goVolume(0);
			this.view.onMute(true);
		}
	},

	setPlay: function() {
		if(this.state == 'Buffering' || this.state == 'Playing') {
			if(this.configuration['duration'] == 0) { 
				this.model.goStop();
			} else { 
				this.model.goPause();
			}
		} else {
			this.model.goStart();
		}
	},

	setScrub: function(sec) {
		if(sec < 2) {
			sec = 0;
		} else if (sec > this.configuration['duration']-4) {
			sec = this.configuration['duration']-4;
		}
		if(this.state == 'Buffering' || this.state == 'Playing') {
			this.model.goStart(sec);
		} else {
			this.model.goPause(sec);
		}
	},

	setStop: function() {
		this.model.goStop();
	},

	setVolume: function(pct) {
		if(pct < 0) { pct = 0; } else if(pct > 100) { pct = 100; }
		this.configuration['volume'] = Math.round(pct);
		this.model.goVolume(pct);
		this.view.onVolume(pct);
		if(this.configuration['usemute'] == 'true') {
			this.configuration['usemute'] = 'false';
			this.view.onMute(false);
		} 
	},

	setFullscreen: function() {
		var fss = !this.configuration['sender'].getHost().content.FullScreen;
		this.configuration['sender'].getHost().content.FullScreen = fss;
		jeroenwijering.utils.delegate(this.view,this.view.onFullscreen);
	}
}










/****************************************************************************
* The view of the player MVC triad, which manages the graphics.
****************************************************************************/
jeroenwijering.View = function(cfg,ctr) {
	this.configuration = cfg;
	this.listeners = Array();
	this.controller = ctr;
	this.fstimeout;
	this.fslistener;
	this.display = this.configuration['sender'].findName("PlayerDisplay");
	this.controlbar = this.configuration['sender'].findName("PlayerControls");
	this.configuration['sender'].getHost().content.onResize = 
		jeroenwijering.utils.delegate(this,this.resizePlayer);
	this.configuration['sender'].getHost().content.onFullScreenChange = 
		jeroenwijering.utils.delegate(this,this.onFullscreen);
	this.assignColorsClicks();
	this.resizePlayer();
}

jeroenwijering.View.prototype = {
	onBuffer: function(pct) {
		var snd = this.configuration['sender'];
		if(pct == 0) { 
			snd.findName("BufferText").Text = null;
		} else { 
			pct < 10 ? pct = "0"+pct: pct = ""+pct;
			snd.findName("BufferText").Text = pct;
		}
		this.delegate('BUFFER',[pct]);
	},

	onFullscreen: function(fss) {
		var snd = this.configuration['sender'];
		var fst = snd.getHost().content.FullScreen;
		if(fst) { 
			this.fstimeout = setTimeout(jeroenwijering.utils.delegate(this,
				this.hideFSControls),2000);
			this.fslistener = this.display.addEventListener('MouseMove',
				jeroenwijering.utils.delegate(this,this.showFSControls));
			snd.findName("FullscreenSymbol").Visibility = "Collapsed";
			snd.findName("FullscreenOffSymbol").Visibility = "Visible";
		} else {
			clearTimeout(this.fstimeout);
			this.display.removeEventListener("MouseMove",this.fslistener);
			this.controlbar.Visibility = "Visible";
			this.display.Cursor = "Hand";
			snd.findName("FullscreenSymbol").Visibility = "Visible";
			snd.findName("FullscreenOffSymbol").Visibility = "Collapsed";
		}
		this.resizePlayer();
		this.delegate('FULLSCREEN');
	},

	showFSControls: function(sdr,arg) {
		var vbt = sdr.findName('PlayerControls');
		var yps = arg.GetPosition(vbt).Y;
		clearTimeout(this.fstimeout);
		this.controlbar.Visibility = "Visible";
		this.display.Cursor = "Hand";
		if(yps < 0) { 
			this.fstimeout = setTimeout(jeroenwijering.utils.delegate(this,
				this.hideFSControls),2000);
		}
	},

	hideFSControls: function() {
		this.controlbar.Visibility = "Collapsed";
		this.display.Cursor = "None";
	},

	onLoad: function(pct) {
		var snd = this.configuration['sender'];
		var max = snd.findName("TimeSlider").Width;
		snd.findName("DownloadProgress").Width = Math.round(max*pct/100);
		this.delegate('LOAD',[pct]);
	},

	onMute: function(mut) {
		var snd = this.configuration['sender'];
		this.configuration['usemute'] = ''+mut;
		if(mut) {
			snd.findName("VolumeHighlight").Visibility = "Collapsed";
			snd.findName("MuteSymbol").Visibility = "Visible";
			snd.findName("MuteOffSymbol").Visibility = "Collapsed";
			if(this.state == 'Playing') {
				snd.findName("MuteIcon").Visibility = "Visible";
			}
		} else {
			snd.findName("VolumeHighlight").Visibility = "Visible";
			snd.findName("MuteSymbol").Visibility = "Collapsed";
			snd.findName("MuteOffSymbol").Visibility = "Visible";
			snd.findName("MuteIcon").Visibility = "Collapsed";
		}
		this.delegate('MUTE');
	},

	onState: function(old,stt) {
		var snd = this.configuration['sender'];
		this.state = stt;
		if(stt == 'Buffering' || stt == 'Playing' || stt == 'Opening') {
			snd.findName("PlayIcon").Visibility = "Collapsed";
			snd.findName("PlaySymbol").Visibility = "Collapsed";
			snd.findName("PlayOffSymbol").Visibility = "Visible";
			if (stt=='Playing') {
				snd.findName("BufferIcon").Visibility = "Collapsed";
				snd.findName("BufferText").Visibility = "Collapsed";
				if(this.configuration['usemute'] == 'true') {
					snd.findName("MuteIcon").Visibility = "Visible";
				}
			} else{
				snd.findName("BufferIcon").Visibility = "Visible";
				snd.findName("BufferText").Visibility = "Visible";
			}
		} else { 
			snd.findName("MuteIcon").Visibility = "Collapsed";
			snd.findName("BufferIcon").Visibility = "Collapsed";
			snd.findName("BufferText").Visibility = "Collapsed";
			snd.findName("PlayOffSymbol").Visibility = "Collapsed";
			snd.findName("PlaySymbol").Visibility = "Visible";
			if(this.configuration['linkfromdisplay'] == 'true') {
				snd.findName("PlayIcon").Visibility = "Collapsed";
			} else { 
				snd.findName("PlayIcon").Visibility = "Visible";
			}
		}
		try {
			if(!(old == 'Completed' && stt == 'Buffering') &&
				!(old == 'Buffering' && stt == 'Paused')) {
				playerStatusChange(old.toUpperCase(),stt.toUpperCase());
			}
		} catch (err) {}
		this.delegate('STATE',[old,stt]);
	},

	onTime: function(elp,dur) {
		var snd = this.configuration['sender'];
		var snd = this.configuration['sender'];
		var max = snd.findName("TimeSlider").Width;
		if(dur > 0) {
			var pos = Math.round(max*elp/dur);
			this.configuration['duration'] = dur;
			snd.findName("ElapsedText").Text = jeroenwijering.utils.timestring(elp);
			snd.findName("RemainingText").Text = jeroenwijering.utils.timestring(dur-elp);
			snd.findName("TimeSymbol").Visibility = "Visible";
			snd.findName("TimeSymbol")['Canvas.Left'] = pos+4;
			snd.findName("TimeHighlight").Width = pos-2;
		} else  { 
			snd.findName("TimeSymbol").Visibility = "Collapsed";
		}
		this.delegate('TIME',[elp,dur]);
	},

	onVolume: function(pct) {
		var snd = this.configuration['sender'];
		snd.findName("VolumeHighlight").Width = Math.round(pct/5);
		this.delegate('VOLUME',[pct]);
	},

	assignColorsClicks: function() {
		this.display.Cursor = "Hand";
		this.display.Background = "#FF"+this.configuration['screencolor'];
		if(this.configuration['linkfromdisplay'] == 'false') { 
			this.display.addEventListener('MouseLeftButtonUp',
				jeroenwijering.utils.delegate(this.controller,
				this.controller.setPlay));
		} else { 
			this.display.addEventListener('MouseLeftButtonUp',
				jeroenwijering.utils.delegate(this.controller,
				this.controller.setLink));
			this.display.findName("PlayIcon").Visibility = "Collapsed";
		}
		if(this.configuration['logo'] != '') {
			this.display.findName('OverlayCanvas').Visibility = "Visible";
			this.display.findName('OverlayLogo').ImageSource = 
				this.configuration['logo'];
		}
		this.controlbar.findName("ControlbarBack").Fill = 
			"#FF"+this.configuration['backcolor'];
		this.assignButton('Play',this.controller.setPlay);
		this.assignButton('Stop',this.controller.setStop);
		this.configuration['sender'].findName('ElapsedText').Foreground = 
			"#FF"+this.configuration['frontcolor'];
		this.assignSlider('Time',this.changeTime);
		this.configuration['sender'].findName('DownloadProgress').Fill = 
			"#FF"+this.configuration['frontcolor'];
		this.configuration['sender'].findName('RemainingText').Foreground = 
			"#FF"+this.configuration['frontcolor'];
		this.assignButton('Link',this.controller.setLink);
		this.assignButton('Fullscreen',this.controller.setFullscreen);
		this.assignButton('Mute',this.controller.setMute);
		this.assignSlider('Volume',this.changeVolume);
	},

	assignButton: function(btn,act) {
		var el1 = this.configuration['sender'].findName(btn+'Button');
		el1.Cursor = "Hand";
		el1.addEventListener('MouseLeftButtonUp',
			jeroenwijering.utils.delegate(this.controller,act));
		el1.addEventListener('MouseEnter',
			jeroenwijering.utils.delegate(this,this.rollOver));
		el1.addEventListener('MouseLeave',
			jeroenwijering.utils.delegate(this,this.rollOut));
		this.configuration['sender'].findName(btn+'Symbol').Fill = 
			"#FF"+this.configuration['frontcolor'];
		try {
			this.configuration['sender'].findName(btn+'OffSymbol').Fill = 
				"#FF"+this.configuration['frontcolor'];
		} catch(e) {}
	},

	assignSlider: function(sld,act) {
		var el1 = this.configuration['sender'].findName(sld+'Button');
		el1.Cursor = "Hand";
		el1.addEventListener('MouseLeftButtonUp',
			jeroenwijering.utils.delegate(this,act));
		el1.addEventListener('MouseEnter',
			jeroenwijering.utils.delegate(this,this.rollOver));
		el1.addEventListener('MouseLeave',
			jeroenwijering.utils.delegate(this,this.rollOut));
		this.configuration['sender'].findName(sld+'Slider').Fill = 
			"#FF"+this.configuration['frontcolor'];
		this.configuration['sender'].findName(sld+'Highlight').Fill = 
			"#FF"+this.configuration['frontcolor'];
		this.configuration['sender'].findName(sld+'Symbol').Fill = 
			"#FF"+this.configuration['frontcolor'];
	},

	delegate: function(typ,arg) {
		for(var i=0; i<this.listeners.length; i++) {
			if(this.listeners[i]['type'].toUpperCase() == typ) {
				this.listeners[i]['func'].apply(null,arg);
			}
		}
	},

	rollOver: function(sdr) {
		var str = sdr.Name.substr(0,sdr.Name.length-6);
		this.configuration['sender'].findName(str+'Symbol').Fill = 
			"#FF"+this.configuration['lightcolor'];
		try {
			this.configuration['sender'].findName(str+'OffSymbol').Fill = 
				"#FF"+this.configuration['lightcolor'];
		} catch(e) {}
	},

	rollOut: function(sdr) {
		var str = sdr.Name.substr(0,sdr.Name.length-6);
		this.configuration['sender'].findName(str+'Symbol').Fill = 
			"#FF"+this.configuration['frontcolor'];
		try {
			this.configuration['sender'].findName(str+'OffSymbol').Fill = 
				"#FF"+this.configuration['frontcolor'];
		} catch(e) {}
	},

	changeTime: function(sdr,arg) {
		var tbt = sdr.findName('TimeSlider');
		var xps = arg.GetPosition(tbt).X;
		var sec = Math.floor(xps/tbt.Width*this.configuration['duration']);
		this.controller.setScrub(sec);
	},

	changeVolume: function(sdr,arg) {
		var vbt = sdr.findName('VolumeButton');
		var xps = arg.GetPosition(vbt).X;
		this.controller.setVolume(xps*5);
	},

	resizePlayer: function() {
		var wid = this.configuration['sender'].getHost().content.actualWidth;
		var hei = this.configuration['sender'].getHost().content.actualHeight;
		var fss = this.configuration['sender'].getHost().content.FullScreen;
		if(this.configuration['shownavigation'] == 'true') {
			if(fss == true) {
				this.resizeDisplay(wid,hei);
				this.controlbar['Canvas.Left'] = Math.round(wid/2-250);
				this.resizeControlbar(500,hei-this.controlbar.Height-16);
				this.controlbar.findName('ControlbarBack')['Opacity'] = 0.5;
			} else { 
				this.resizeDisplay(wid,hei-20);
				this.controlbar['Canvas.Left'] = 0;
				this.resizeControlbar(wid,hei-this.controlbar.Height);
				this.controlbar.findName('ControlbarBack')['Opacity'] = 1;
			}
		} else {
			this.resizeDisplay(wid,hei);
		}
	},

	resizeDisplay: function(wid,hei) {
		this.stretchElement('PlayerDisplay',wid,hei);
		this.stretchElement('VideoWindow',wid,hei);
		this.stretchElement('PlaceholderImage',wid,hei);
		this.centerElement('PlayIcon',wid,hei);
		this.centerElement('MuteIcon',wid,hei);
		this.centerElement('BufferIcon',wid,hei);
		this.centerElement('BufferText',wid,hei);
		this.display.findName('OverlayCanvas')['Canvas.Left'] = wid -
			this.display.findName('OverlayCanvas').Width - 10;
		this.display.Visibility = "Visible";
	},

	resizeControlbar: function(wid,yps,alp) {
		this.controlbar['Canvas.Top'] = yps;
		this.stretchElement('PlayerControls',wid);
		this.stretchElement('ControlbarBack',wid);
		this.placeElement('PlayButton',0);
		var lft = 17;
		this.placeElement('VolumeButton',wid-24);
		this.placeElement('MuteButton',wid-37);
		var rgt = 37;
		if(this.configuration['showstop'] == 'true') {
			this.placeElement('StopButton',lft);
			lft += 17;
		} else {
			this.controlbar.findName('StopButton').Visibility="Collapsed";
		}
		if(this.configuration['usefullscreen'] == 'true') {
			rgt += 18;
			this.placeElement('FullscreenButton',wid-rgt);
		} else {
			this.controlbar.findName('FullscreenButton').Visibility = 
				"Collapsed";
		}
		if(this.configuration['link'] != '') {
			rgt += 18;
			this.placeElement('LinkButton',wid-rgt);
		} else {
			this.controlbar.findName('LinkButton').Visibility="Collapsed";
		}
		if(this.configuration['showdigits'] == 'true' && wid-rgt-lft> 160) {
			rgt += 35;
			this.controlbar.findName('RemainingButton').Visibility="Visible";
			this.controlbar.findName('ElapsedButton').Visibility="Visible";
			this.placeElement('RemainingButton',wid-rgt);
			this.placeElement('ElapsedButton',lft);
			lft +=35;
		} else {
			this.controlbar.findName('RemainingButton').Visibility = 
				"Collapsed";
			this.controlbar.findName('ElapsedButton').Visibility="Collapsed";
		}
		this.placeElement('TimeButton',lft);
		this.stretchElement('TimeButton',wid-lft-rgt);
		this.stretchElement('TimeShadow',wid-lft-rgt);
		this.stretchElement('TimeStroke',wid-lft-rgt);
		this.stretchElement('TimeFill',wid-lft-rgt);
		this.stretchElement('TimeSlider',wid-lft-rgt-10);
		this.stretchElement('DownloadProgress',wid-lft-rgt-10);
		var tsb = this.configuration['sender'].findName('TimeSymbol');
		this.stretchElement('TimeHighlight',tsb['Canvas.Left']-5);
		this.controlbar.Visibility = "Visible";
	},

	centerElement: function(nam,wid,hei) {
		var elm = this.configuration['sender'].findName(nam);
		elm['Canvas.Left'] = Math.round(wid/2 - elm.Width/2);
		elm['Canvas.Top'] = Math.round(hei/2 - elm.Height/2);
	},

	stretchElement: function(nam,wid,hei) {
		var elm = this.configuration['sender'].findName(nam);
		elm.Width = wid;
		if (hei != undefined) { elm.Height = hei; }
	},

	placeElement: function(nam,xps,yps) {
		var elm = this.configuration['sender'].findName(nam);
		elm['Canvas.Left'] = xps;
		if(yps) { elm['Canvas.Top'] = yps; }
	}
}










/****************************************************************************
* The model of the player MVC triad, which stores all playback logic.
****************************************************************************/
jeroenwijering.Model = function(cfg,ctr,vie) {
	this.configuration = cfg;
	this.controller = ctr;
	this.view = vie;
	this.video = this.configuration['sender'].findName("VideoWindow");
	this.preview = this.configuration['sender'].findName("PlaceholderImage");
	var str = {
		'true':'UniformToFill',
		'false':'Uniform',
		'fit':'Fill',
		'none':'None'
	}
	this.state = this.video.CurrentState;
	this.timeint;
	this.video.Stretch = str[this.configuration['overstretch']];
	this.preview.Stretch = str[this.configuration['overstretch']];
	this.video.BufferingTime = 
		jeroenwijering.utils.spanstring(this.configuration['bufferlength']);
	this.video.AutoPlay = true;
	this.video.AddEventListener("CurrentStateChanged",
		jeroenwijering.utils.delegate(this,this.stateChanged));
	this.video.AddEventListener("MediaEnded",
		jeroenwijering.utils.delegate(this,this.mediaEnded));
	this.video.AddEventListener("BufferingProgressChanged",
		jeroenwijering.utils.delegate(this,this.bufferChanged));
	this.video.AddEventListener("DownloadProgressChanged",
		jeroenwijering.utils.delegate(this,this.downloadChanged));
	if(this.configuration['image'] != '') {
		this.preview.Source = this.configuration['image'];
	}
}

jeroenwijering.Model.prototype = {
	goPause: function(sec) {
		this.video.pause();
		if(!isNaN(sec)) {
			this.video.Position = jeroenwijering.utils.spanstring(sec);
		}
		this.timeChanged();
	},

	goStart: function(sec) {
		this.video.Visibility = 'Visible';
		this.preview.Visibility = 'Collapsed';
		if(this.state == "Closed") {
			this.video.Source = this.configuration['file'];
		} else {
			this.video.play();
		}
		if(!isNaN(sec)) {
			this.video.Position = jeroenwijering.utils.spanstring(sec);
		}
	},

	goStop: function() {
		this.video.Visibility = 'Collapsed';
		this.preview.Visibility = 'Visible';
		this.goPause(0);
		this.video.Source = 'null';
		this.view.onBuffer(0);
		clearInterval(this.timeint);
	},

	goVolume: function(pct) {
		this.video.Volume = pct/100;
	},

	stateChanged: function() {
		var stt = this.video.CurrentState;
		if(stt != this.state) {
			this.controller.setState(this.state,stt);
			this.view.onState(this.state,stt);
			this.state = stt;
			this.configuration['duration'] = 
				Math.round(this.video.NaturalDuration.Seconds*10)/10;
			if(stt != "Playing" && stt != "Buffering" && stt != "Opening") {
				clearInterval(this.timeint);
			} else {
				this.timeint = setInterval(jeroenwijering.utils.delegate(
					this,this.timeChanged),100);
			}
		}
	},

	mediaEnded: function() {
		if(this.configuration['repeat'] == 'true') {
			this.goStart(0);
		} else {
			this.state = 'Completed';
			this.view.onState(this.state,'Completed');
			this.video.Visibility = 'Collapsed';
			this.preview.Visibility = 'Visible';
			this.goPause(0);
		}
	},

	bufferChanged: function() {
		var bfr = Math.round(this.video.BufferingProgress*100);
		this.view.onBuffer(bfr);
	},

	downloadChanged: function() {
		var dld = Math.round(this.video.DownloadProgress*100);
		this.view.onLoad(dld);
	},

	timeChanged: function() {
		var pos = Math.round(this.video.Position.Seconds*10)/10;
		this.view.onTime(pos,this.configuration['duration']);
	}
}










/****************************************************************************
* Some utility functions.
****************************************************************************/
jeroenwijering.utils.delegate = function(obj,fcn) {
	return function() {
		return fcn.apply(obj,arguments);
	}
}
jeroenwijering.utils.timestring = function(stp) {
	var hrs = Math.floor(stp/3600);
	var min = Math.floor(stp%3600/60);
	var sec = Math.round(stp%60);
	var str = "";
	sec > 9 ? str += sec: str +='0'+sec;
	min > 9 ? str = min+":"+str: str='0'+min+":"+str;
	hrs > 0 ? str = hrs+":"+str: null;
	return str;
}
jeroenwijering.utils.spanstring = function(stp) {
	var hrs = Math.floor(stp/3600);
	var min = Math.floor(stp%3600/60);
	var sec = Math.round(stp%60*10)/10;
	var str = hrs+':'+min+':'+sec;
	return str;
}