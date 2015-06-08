/**
 * Copyright (c)2005-2009 Matt Kruse (javascripttoolbox.com)
 * 
 * Dual licensed under the MIT and GPL licenses. 
 * This basically means you can use this code however you want for
 * free, but don't claim to have written it yourself!
 * Donations always accepted: http://www.JavascriptToolbox.com/donate/
 * 
 * Please do not link to the .js files on javascripttoolbox.com from
 * your site. Copy the files locally to your server instead.
 * 
 */
// Global objects to keep track of DynamicOptionList objects created on the page
var dynamicOptionListCount=0;
var dynamicOptionListObjects = new Array();

// Init call to setup lists after page load. One call to this function sets up all lists.
function initDynamicOptionLists() {
	// init each DynamicOptionList object
	for (var i=0; i<dynamicOptionListObjects.length; i++) {
		var dol = dynamicOptionListObjects[i];

		// Find the form associated with this list
		if (dol.formName!=null) { 
			dol.form = document.forms[dol.formName];
		}
		else if (dol.formIndex!=null) {
			dol.form = document.forms[dol.formIndex];
		}
		else {
			// Form wasn't set manually, so go find it!
			// Search for the first form element name in the lists
			var name = dol.fieldNames[0][0];
			for (var f=0; f<document.forms.length; f++) {
				if (typeof(document.forms[f][name])!="undefined") {
					dol.form = document.forms[f];
					break;
				}
			}
			if (dol.form==null) {
				alert("ERROR: Couldn't find form element "+name+" in any form on the page! Init aborted"); return;
			}
		}

		// Form is found, now set the onchange attributes of each dependent select box
		for (var j=0; j<dol.fieldNames.length; j++) {
			// For each set of field names...
			for (var k=0; k<dol.fieldNames[j].length-1; k++) {
				// For each field in the set...
				var selObj = dol.form[dol.fieldNames[j][k]];
				if (typeof(selObj)=="undefined") { alert("Select box named "+dol.fieldNames[j][k]+" could not be found in the form. Init aborted"); return; }
				// Map the HTML options in the first select into the options we created
				if (k==0) {
					if (selObj.options!=null) {
						for (l=0; l<selObj.options.length; l++) {
							var sopt = selObj.options[l];
							var m = dol.findMatchingOptionInArray(dol.options,sopt.text,sopt.value,false);
							if (m!=null) {
								var reselectForNN6 = sopt.selected;
								var m2 = new Option(sopt.text, sopt.value, sopt.defaultSelected, sopt.selected);
								m2.selected = sopt.selected; // For some reason I need to do this to make NN4 happy
								m2.defaultSelected = sopt.defaultSelected;
								m2.DOLOption = m;
								selObj.options[l] = m2;
								selObj.options[l].selected = reselectForNN6; // Reselect this option for NN6 to be happy. Yuck.
							}
						}
					}
				}
				if (selObj.onchange==null) {
					// We only modify the onChange attribute if it's empty! Otherwise do it yourself in your source!
					selObj.onchange = new Function("dynamicOptionListObjects["+dol.index+"].change(this)");
				}
			}
		}
	}
	// Set the preselectd options on page load 
	resetDynamicOptionLists();
}

// This function populates lists with the preselected values. 
// It's pulled out into a separate function so it can be hooked into a 'reset' button on a form
// Optionally passed a form object which should be the only form reset
function resetDynamicOptionLists(theform) {
	// reset each DynamicOptionList object
	for (var i=0; i<dynamicOptionListObjects.length; i++) {
		var dol = dynamicOptionListObjects[i];
		if (typeof(theform)=="undefined" || theform==null || theform==dol.form) {
			for (var j=0; j<dol.fieldNames.length; j++) {
				dol.change(dol.form[dol.fieldNames[j][0]],true); // Second argument says to use preselected values rather than default values
			}
		}
	}
}

// An object to represent an Option() but just for data-holding
function DOLOption(text,value,defaultSelected,selected) {
	this.text = text;
	this.value = value;
	this.defaultSelected = defaultSelected;
	this.selected = selected;
	this.options = new Array(); // To hold sub-options
	return this;
}

// DynamicOptionList CONSTRUCTOR
function DynamicOptionList() {
	this.form = null;// The form this list belongs to
	this.options = new Array();// Holds the options of dependent lists
	this.longestString = new Array();// Longest string that is currently a potential option (for Netscape)
	this.numberOfOptions = new Array();// The total number of options that might be displayed, to build dummy options (for Netscape)
	this.currentNode = null;// The current node that has been selected with forValue() or forText()
	this.currentField = null;// The current field that is selected to be used for setValue()
	this.currentNodeDepth = 0;// How far down the tree the currentNode is
	this.fieldNames = new Array();// Lists of dependent fields which use this object
	this.formIndex = null;// The index of the form to associate with this list
	this.formName = null;// The name of the form to associate with this list
	this.fieldListIndexes = new Object();// Hold the field lists index where fields exist
	this.fieldIndexes = new Object();// Hold the index within the list where fields exist
	this.selectFirstOption = true;// Whether or not to select the first option by default if no options are default or preselected, otherwise set the selectedIndex = -1
	this.numberOfOptions = new Array();// Store the max number of options for a given option list
	this.longestString = new Array();// Store the longest possible string 
	this.values = new Object(); // Will hold the preselected values for fields, by field name
	
	// Method mappings
	this.forValue = DOL_forValue;
	this.forText = DOL_forText;
	this.forField = DOL_forField;
	this.forX = DOL_forX;
	this.addOptions = DOL_addOptions;
	this.addOptionsTextValue = DOL_addOptionsTextValue;
	this.setDefaultOptions = DOL_setDefaultOptions;
	this.setValues = DOL_setValues;
	this.setValue = DOL_setValues;
	this.setFormIndex = DOL_setFormIndex;
	this.setFormName = DOL_setFormName;
	this.printOptions = DOL_printOptions;
	this.addDependentFields = DOL_addDependentFields;
	this.change = DOL_change;
	this.child = DOL_child;
	this.selectChildOptions = DOL_selectChildOptions;
	this.populateChild = DOL_populateChild;
	this.change = DOL_change;
	this.addNewOptionToList = DOL_addNewOptionToList;
	this.findMatchingOptionInArray = DOL_findMatchingOptionInArray;

	// Optionally pass in the dependent field names
	if (arguments.length > 0) {
		// Process arguments and add dependency groups
		for (var i=0; i<arguments.length; i++) {
			this.fieldListIndexes[arguments[i].toString()] = this.fieldNames.length;
			this.fieldIndexes[arguments[i].toString()] = i;
		}
		this.fieldNames[this.fieldNames.length] = arguments;
	}
	
	// Add this object to the global array of dynamicoptionlist objects
	this.index = window.dynamicOptionListCount++;
	window["dynamicOptionListObjects"][this.index] = this;
}

// Given an array of Option objects, search for an existing option that matches value, text, or both
function DOL_findMatchingOptionInArray(a,text,value,exactMatchRequired) {
	if (a==null || typeof(a)=="undefined") { return null; }
	var value_match = null; // Whether or not a value has been matched
	var text_match = null; // Whether or not a text has been matched
	for (var i=0; i<a.length; i++) {
		var opt = a[i];
		// If both value and text match, return it right away
		if (opt.value==value && opt.text==text) { return opt; }
		if (!exactMatchRequired) {
			// If value matches, store it until we complete scanning the list
			if (value_match==null && value!=null && opt.value==value) {
				value_match = opt;
			}
			// If text matches, store it for later
			if (text_match==null && text!=null && opt.text==text) {
				text_match = opt;
			}
		}
	}
	return (value_match!=null)?value_match:text_match;
}

// Util function used by forValue and forText
function DOL_forX(s,type) {
	if (this.currentNode==null) { this.currentNodeDepth=0; }
	var useNode = (this.currentNode==null)?this:this.currentNode;
	var o = this.findMatchingOptionInArray(useNode["options"],(type=="text")?s:null,(type=="value")?s:null,false);
	if (o==null) {
		o = new DOLOption(null,null,false,false);
		o[type] = s;
		useNode.options[useNode.options.length] = o;
	}
	this.currentNode = o;
	this.currentNodeDepth++;
	return this;
}

// Set the portion of the list structure that is to be used by a later operation like addOptions
function DOL_forValue(s) { return this.forX(s,"value"); }

// Set the portion of the list structure that is to be used by a later operation like addOptions
function DOL_forText(s) { return this.forX(s,"text"); }

// Set the field to be used for setValue() calls
function DOL_forField(f) { this.currentField = f; return this; }

// Create and add an option to a list, avoiding duplicates
function DOL_addNewOptionToList(a, text, value, defaultSelected) {
	var o = new DOLOption(text,value,defaultSelected,false);
	// Add the option to the array
	if (a==null) { a = new Array(); }
	for (var i=0; i<a.length; i++) {
		if (a[i].text==o.text && a[i].value==o.value) {
			if (o.selected) { 
				a[i].selected=true;
			}
			if (o.defaultSelected) {
				a[i].defaultSelected = true;
			}
			return a;
		}
	}
	a[a.length] = o;
}

// Add sub-options to the currently-selected node, with the same text and value for each option
function DOL_addOptions() {
	if (this.currentNode==null) { this.currentNode = this; }
	if (this.currentNode["options"] == null) { this.currentNode["options"] = new Array(); }
	for (var i=0; i<arguments.length; i++) {
		var text = arguments[i];
		this.addNewOptionToList(this.currentNode.options,text,text,false);
		if (typeof(this.numberOfOptions[this.currentNodeDepth])=="undefined") {
			this.numberOfOptions[this.currentNodeDepth]=0;
		}
		if (this.currentNode.options.length > this.numberOfOptions[this.currentNodeDepth]) {
			this.numberOfOptions[this.currentNodeDepth] = this.currentNode.options.length;
		}
		if (typeof(this.longestString[this.currentNodeDepth])=="undefined" || (text.length > this.longestString[this.currentNodeDepth].length)) {
			this.longestString[this.currentNodeDepth] = text;
		}
	}
	this.currentNode = null;
	this.currentNodeDepth = 0;
}

// Add sub-options to the currently-selected node, specifying separate text and values for each option
function DOL_addOptionsTextValue() {
	if (this.currentNode==null) { this.currentNode = this; }
	if (this.currentNode["options"] == null) { this.currentNode["options"] = new Array(); }
	for (var i=0; i<arguments.length; i++) {
		var text = arguments[i++];
		var value = arguments[i];
		this.addNewOptionToList(this.currentNode.options,text,value,false);
		if (typeof(this.numberOfOptions[this.currentNodeDepth])=="undefined") {
			this.numberOfOptions[this.currentNodeDepth]=0;
		}
		if (this.currentNode.options.length > this.numberOfOptions[this.currentNodeDepth]) {
			this.numberOfOptions[this.currentNodeDepth] = this.currentNode.options.length;
		}
		if (typeof(this.longestString[this.currentNodeDepth])=="undefined" || (text.length > this.longestString[this.currentNodeDepth].length)) {
			this.longestString[this.currentNodeDepth] = text;
		}
	}
	this.currentNode = null;
	this.currentNodeDepth = 0;
}

// Find the first dependent list of a select box
// If it's the last list in a chain, return null because there are no children
function DOL_child(obj) {
	var listIndex = this.fieldListIndexes[obj.name];
	var index = this.fieldIndexes[obj.name];
	if (index < (this.fieldNames[listIndex].length-1)) {
		return this.form[this.fieldNames[listIndex][index+1]];
	}
	return null;
}

// Set the options which should be selected by default for a certain value in the parent
function DOL_setDefaultOptions() {
	if (this.currentNode==null) { this.currentNode = this; }
	for (var i=0; i<arguments.length; i++) {
		var o = this.findMatchingOptionInArray(this.currentNode.options,null,arguments[i],false);
		if (o!=null) {
			o.defaultSelected = true;
		}
	}
	this.currentNode = null;
}

// Set the options which should be selected when the page loads. This is different than the default value and ONLY applies when the page LOADS
function DOL_setValues() {
	if (this.currentField==null) { 
		alert("Can't call setValues() without using forField() first!");
		return;
	}
	if (typeof(this.values[this.currentField])=="undefined") {
		this.values[this.currentField] = new Object();
	}
	for (var i=0; i<arguments.length; i++) {
		this.values[this.currentField][arguments[i]] = true;
	}
	this.currentField = null;
}

// Manually set the form for the object using an index
function DOL_setFormIndex(i) {
	this.formIndex = i;
}

// Manually set the form for the object using a form name
function DOL_setFormName(n) {
	this.formName = n;
}

// Print blank <option> objects for Netscape4, since it refuses to grow or shrink select boxes for new options
function DOL_printOptions(name) {
	// Only need to write out "dummy" options for Netscape4
    if ((navigator.appName == 'Netscape') && (parseInt(navigator.appVersion) <= 4)){
		var index = this.fieldIndexes[name];
		var ret = "";
		if (typeof(this.numberOfOptions[index])!="undefined") {
			for (var i=0; i<this.numberOfOptions[index]; i++) { 
				ret += "<OPTION>";
			}
		}
		ret += "<OPTION>";
		if (typeof(this.longestString[index])!="undefined") {
			for (var i=0; i<this.longestString[index].length; i++) {
				ret += "_";
			}
		}
		document.writeln(ret);
	}
}

// Add a list of field names which use this option-mapping object.
// A single mapping object may be used by multiple sets of fields
function DOL_addDependentFields() {
	for (var i=0; i<arguments.length; i++) {
		this.fieldListIndexes[arguments[i].toString()] = this.fieldNames.length;
		this.fieldIndexes[arguments[i].toString()] = i;
	}
	this.fieldNames[this.fieldNames.length] = arguments;
}

// Called when a parent select box is changed. It populates its direct child, then calls change on the child object to continue the population.
function DOL_change(obj, usePreselected) {
	if (usePreselected==null || typeof(usePreselected)=="undefined") { usePreselected = false; }
	var changedListIndex = this.fieldListIndexes[obj.name];
	var changedIndex = this.fieldIndexes[obj.name];
	var child = this.child(obj);
	if (child == null) { return; } // No child, no need to continue
	if (obj.type == "select-one") {
		// Treat single-select differently so we don't have to scan the entire select list, which could potentially speed things up
		if (child.options!=null) {
			child.options.length=0; // Erase all the options from the child so we can re-populate
		}
		if (obj.options!=null && obj.options.length>0 && obj.selectedIndex>=0) {
			var o = obj.options[obj.selectedIndex];
			this.populateChild(o.DOLOption,child,usePreselected);
			this.selectChildOptions(child,usePreselected);
		}
	}
	else if (obj.type == "select-multiple") {
		// For each selected value in the parent, find the options to fill in for this list
		// Loop through the child list and keep track of options that are currently selected
		var currentlySelectedOptions = new Array();
		if (!usePreselected) {
			for (var i=0; i<child.options.length; i++) {
				var co = child.options[i];
				if (co.selected) {
					this.addNewOptionToList(currentlySelectedOptions, co.text, co.value, co.defaultSelected);
				}
			}
		}
		child.options.length=0;
		if (obj.options!=null) {
			var obj_o = obj.options;
			// For each selected option in the parent...
			for (var i=0; i<obj_o.length; i++) {
				if (obj_o[i].selected) {
					// if option is selected, add its children to the list
 					this.populateChild(obj_o[i].DOLOption,child,usePreselected);
				}
			}
			// Now go through and re-select any options which were selected before
			var atLeastOneSelected = false;
			if (!usePreselected) {
				for (var i=0; i<child.options.length; i++) {
					var m = this.findMatchingOptionInArray(currentlySelectedOptions,child.options[i].text,child.options[i].value,true);
					if (m!=null) {
						child.options[i].selected = true;
						atLeastOneSelected = true;
					}
				}
			}
			if (!atLeastOneSelected) {	
				this.selectChildOptions(child,usePreselected);
			}
		}
	}
	// Change all the way down the chain
	this.change(child,usePreselected);
}
function DOL_populateChild(dolOption,childSelectObj,usePreselected) {
	// If this opton has sub-options, populate the child list with them
	if (dolOption!=null && dolOption.options!=null) {
		for (var j=0; j<dolOption.options.length; j++) {
			var srcOpt = dolOption.options[j];
			if (childSelectObj.options==null) { childSelectObj.options = new Array(); }
			// Put option into select list
			var duplicate = false;
			var preSelectedExists = false;
			for (var k=0; k<childSelectObj.options.length; k++) {
				var csi = childSelectObj.options[k];
				if (csi.text==srcOpt.text && csi.value==srcOpt.value) {
					duplicate = true;
					break;
				}
			}
			if (!duplicate) {
				var newopt = new Option(srcOpt.text, srcOpt.value, false, false);
				newopt.selected = false; // Again, we have to do these two statements for NN4 to work
				newopt.defaultSelected = false;
				newopt.DOLOption = srcOpt;
				childSelectObj.options[childSelectObj.options.length] = newopt;
			}
		}
	}
}

// Once a child select is populated, go back over it to select options which should be selected
function DOL_selectChildOptions(obj,usePreselected) {
	// Look to see if any options are preselected=true. If so, then set then selected if usePreselected=true, otherwise set defaults
	var values = this.values[obj.name];
	var preselectedExists = false;
	if (usePreselected && values!=null && typeof(values)!="undefined") {
		for (var i=0; i<obj.options.length; i++) {
			var v = obj.options[i].value;
			if (v!=null && values[v]!=null && typeof(values[v])!="undefined") {
				preselectedExists = true;
				break;
			}
		}
	}
	// Go back over all the options to do the selection
	var atLeastOneSelected = false;
	for (var i=0; i<obj.options.length; i++) {
		var o = obj.options[i];
		if (preselectedExists && o.value!=null && values[o.value]!=null && typeof(values[o.value])!="undefined") {
			o.selected = true;
			atLeastOneSelected = true;
		}
		else if (!preselectedExists && o.DOLOption!=null && o.DOLOption.defaultSelected) {
			o.selected = true;
			atLeastOneSelected = true;
		}
		else {
			o.selected = false;
		}
	}
	// If nothing else was selected, select the first one by default
	if (this.selectFirstOption && !atLeastOneSelected && obj.options.length>0) {
		obj.options[0].selected = true;
	}
	else if (!atLeastOneSelected &&  obj.type=="select-one") {
		obj.selectedIndex = -1;
	}
}
