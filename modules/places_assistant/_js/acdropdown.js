/**
 * @version $Id$
 * @author http://momche.net
 *
 * Some changes in cAutocomplete.prototype.loadListArray
 * for PGV place edition
 */

//
//  This script was created
//  by Mircho Mirev
//  mo /mo@momche.net/
//	Copyright (c) 2004 Mircho Mirev
//
//	:: feel free to use it BUT
//	:: if you want to use this code PLEASE send me a note
//	:: and please keep this disclaimer intact
//

function cAutocomplete( sInputId )
{
	this.init( sInputId )
}

cAutocomplete.CS_NAME = 'Autocomplete component'
cAutocomplete.CS_OBJ_NAME = 'AC_COMPONENT'
cAutocomplete.CS_LIST_PREFIX = 'ACL_'
cAutocomplete.CS_BUTTON_PREFIX = 'ACB_'
cAutocomplete.CS_INPUT_PREFIX = 'AC_'
cAutocomplete.CS_HIDDEN_INPUT_PREFIX = 'ACH_'
cAutocomplete.CS_INPUT_CLASSNAME = ''

cAutocomplete.CB_AUTOINIT = true

cAutocomplete.CB_AUTOCOMPLETE = false

//match the input string only against the begining of the strings
//or anywhere in the string
cAutocomplete.CB_MATCHSTRINGBEGIN = true

cAutocomplete.CN_OFFSET_TOP = 2
cAutocomplete.CN_OFFSET_LEFT = -1

cAutocomplete.CN_LINE_HEIGHT = 19
cAutocomplete.CN_NUMBER_OF_LINES = 10
cAutocomplete.CN_HEIGHT_FIX = 2

cAutocomplete.CN_CLEAR_TIMEOUT = 300
cAutocomplete.CN_SHOW_TIMEOUT = 100

cAutocomplete.hListDisplayed = null
cAutocomplete.nCount = 0

cAutocomplete.autoInit = function()
{
	var nI = 0
	var hACE = null
	var sLangAtt

	var nInputsLength = document.getElementsByTagName( 'INPUT' ).length
	for( nI = 0; nI < nInputsLength; nI++ )
	{
		if( document.getElementsByTagName( 'INPUT' )[ nI ].type.toLowerCase() == 'text' )
		{
		 	sLangAtt = document.getElementsByTagName( 'INPUT' )[ nI ].getAttribute( 'acdropdown' )
			if( sLangAtt != null && sLangAtt.length > 0 )
			{
				if( document.getElementsByTagName( 'INPUT' )[ nI ].id == null || document.getElementsByTagName( 'INPUT' )[ nI ].id.length == 0 )
				{
					document.getElementsByTagName( 'INPUT' )[ nI ].id = cAutocomplete.CS_OBJ_NAME + cAutocomplete.nCount
				}
				hACE = new cAutocomplete( document.getElementsByTagName( 'INPUT' )[ nI ].id )
			}
		}
	}

	var nSelectsLength = document.getElementsByTagName( 'SELECT' ).length
	var aSelect = null
	for( nI = 0; nI < nSelectsLength; nI++ )
	{
		aSelect = document.getElementsByTagName( 'SELECT' )[ nI ]
		sLangAtt = aSelect.getAttribute( 'acdropdown' )
		if( sLangAtt != null && sLangAtt.length > 0 )
		{
			if( aSelect.id == null || aSelect.id.length == 0 )
			{
				aSelect.id = cAutocomplete.CS_OBJ_NAME + cAutocomplete.nCount
			}
			hACE = new cAutocomplete( aSelect.id )
			nSelectsLength--
			nI--
		}
	}
}

if( cAutocomplete.CB_AUTOINIT )
{
	if( window.attachEvent ) 
	{
		window.attachEvent( 'onload', cAutocomplete.autoInit )
	}
	else if( window.addEventListener )
	{
		window.addEventListener( 'load', cAutocomplete.autoInit, false )
	}
}

cAutocomplete.prototype.init = function( sInputId )
{
	this.sInputId = sInputId
	this.sListId = cAutocomplete.CS_LIST_PREFIX + sInputId

	this.hActiveSelection = null
	
	//the value of the input before the list is displayed
	this.sLastActiveValue = null
	this.sActiveValue = ''
	this.bListDisplayed = false
	this.nItemsDisplayed = 0
	//if we transform a select option we save some more info
	this.bSelectPrototype = false
	this.sHiddenInputId = null
	this.bHasButton = false
	//the search array object
	this.aSearchData = new Array()
	this.bSorted = false
	
	this.bMatchBegin = cAutocomplete.CB_MATCHSTRINGBEGIN
	var sMatchBegin = document.getElementById( this.sInputId ).getAttribute( 'autocomplete_matchbegin' )
	if( sMatchBegin != null && sMatchBegin.length > 0 )
	{
		this.bMatchBegin = eval( sMatchBegin )
	}
	//autocomplete with the first selected option
	this.bAutoComplete = cAutocomplete.CB_AUTOCOMPLETE
	this.bAutocompleted = false
	var sAutoComplete = document.getElementById( this.sInputId ).getAttribute( 'autocomplete_complete' )
	if( sAutoComplete != null && sAutoComplete.length > 0 )
	{
		this.bAutoComplete = eval( sAutoComplete )
	}
	//format function
	this.formatOptions = null
	var sFormatFunction = document.getElementById( this.sInputId ).getAttribute( 'autocomplete_format' )
	if( sFormatFunction != null && sFormatFunction.length > 0 )
	{
		this.formatOptions = eval( sFormatFunction )
	}
	//onselect callback function - get called when a new option is selected
	this.onSelect = null
	var sOnSelectFunction = document.getElementById( this.sInputId ).getAttribute( 'autocomplete_onselect' )
	if( sOnSelectFunction != null && sOnSelectFunction.length > 0 )
	{
		this.onSelect = eval( sOnSelectFunction )
	}
	
	this.sObjName = cAutocomplete.CS_OBJ_NAME + (cAutocomplete.nCount++)
	this.hObj = this.sObjName
	
	//if we have remote list then we postpone the list creation
	if( this.getListArrayType() != 'url' )
	{
		this.bRemoteList = false
	}
	else
	{
		this.bRemoteList = true
		this.sListURL = this.getListURL()
		this.hXMLHttp = XmlHttp.create()
	}
	this.createList()
	this.initInput()

	eval( this.hObj + '= this' )
}

cAutocomplete.prototype.createButton = function()
{
	var hInput = document.getElementById( this.sInputId )
	var nTop = getObject.getSize( 'offsetTop', hInput )
	var nLeft = getObject.getSize( 'offsetLeft', hInput )	
	var hACButton = document.createElement( 'input' )
	hACButton.id = cAutocomplete.CS_BUTTON_PREFIX + this.sInputId
	hACButton.type = 'button'
	hACButton.className = 'acbutton'
	hACButton.style.zIndex = 1000 + cAutocomplete.nCount
	hACButton.style.top = nTop
	hACButton.style.left = nLeft + hInput.offsetWidth + 1
	hACButton.style.height = hInput.offsetHeight - 1
	document.body.appendChild( hACButton )
	hACButton.hAutocomplete = this
	this.bHasButton = true
}

 
cAutocomplete.prototype.initInput = function()
{
	var hInput = document.getElementById( this.sInputId )
	hInput.hAutocomplete = this
	var hContainer = document.getElementById( this.sListId )
	hContainer.hAutocomplete = this

	var nWidth = hInput.offsetWidth
	var sInputName = hInput.name
	var hForm = hInput.form
	var bHasButton = false
	var sHiddenValue = ''
	var sValue = hInput.type.toLowerCase() == 'text' ? hInput.value : ''

 	var sHasButton = hInput.getAttribute( 'autocomplete_button' )
	if( sHasButton != null && sHasButton.length > 0 )
	{
		bHasButton = true
	}

	//if it is a select - I unconditionally add a button
	if( hInput.type.toLowerCase() == 'select-one' )
	{
		this.bSelectPrototype = true
		bHasButton = true
		if( hInput.selectedIndex >= 0 )
		{
			sHiddenValue = hInput.options[ hInput.selectedIndex ].value
			sValue = hInput.options[ hInput.selectedIndex ].text
		}
		
		if( hForm )
		{
			var hHiddenInput = document.createElement( 'INPUT' )
			hHiddenInput.id = cAutocomplete.CS_HIDDEN_INPUT_PREFIX + this.sInputId
			hHiddenInput.type = 'hidden'
			hForm.appendChild( hHiddenInput )
			hHiddenInput.name = sInputName
			hHiddenInput.value = sHiddenValue
			this.sHiddenInputId = hHiddenInput.id
		}
		else
		{
		}
	}

	if( bHasButton )
	{
		this.bHasButton = true

		var hInputContainer = document.createElement( 'DIV' )
		hInputContainer.className = 'acinputContainer'
		hInputContainer.style.width = nWidth
		
		var hInputButton = document.createElement( 'INPUT' )
		hInputButton.id = cAutocomplete.CS_BUTTON_PREFIX + this.sInputId
		hInputButton.type = 'button'
		hInputButton.className = 'button'
		hInputButton.tabIndex = hInput.tabIndex + 1
		hInputButton.hAutocomplete = this
		
		//this.sInputId = cAutocomplete.CS_INPUT_PREFIX + sInputName
		var hNewInput = document.createElement( 'INPUT' )
		if( this.bSelectPrototype )
		{
			hNewInput.name = cAutocomplete.CS_INPUT_PREFIX + sInputName
		}
		else
		{
			hNewInput.name = sInputName
		}
		
		hNewInput.type = 'text'
		hNewInput.value = sValue
		hNewInput.style.width = nWidth-20
		hNewInput.className = cAutocomplete.CS_INPUT_CLASSNAME
		hNewInput.tabIndex = hInput.tabIndex
		hNewInput.hAutocomplete = this
		
		hInputContainer.appendChild( hNewInput )
		hInputContainer.appendChild( hInputButton )
		
		hInput.parentNode.replaceChild( hInputContainer, hInput )
		
		hNewInput.id = this.sInputId
		hInput = hNewInput
	}
	
	if( hInput.attachEvent ) 
	{
		hInput.attachEvent( 'onkeyup', cAutocomplete.onInputKeyUp )
		hInput.attachEvent( 'onkeydown', cAutocomplete.onInputKeyDown )
		hInput.attachEvent( 'onblur', cAutocomplete.onInputBlur )
		hInput.attachEvent( 'onfocus', cAutocomplete.onInputFocus )
		
		if( hInputButton )
		{
			hInputButton.attachEvent( 'onclick', cAutocomplete.onButtonClick )
		}
	}
	else if( hInput.addEventListener )
	{
		hInput.addEventListener( 'keyup', cAutocomplete.onInputKeyUp, false )
		hInput.addEventListener( 'keydown', cAutocomplete.onInputKeyDown, false )
		hInput.addEventListener( 'blur', cAutocomplete.onInputBlur, false )
		hInput.addEventListener( 'focus', cAutocomplete.onInputFocus, false )

		if( hInputButton )
		{
			hInputButton.addEventListener( 'click', cAutocomplete.onButtonClick, false )
		}
	}
	
	if( hForm )
	{
		if( hForm.attachEvent )
		{
			hForm.attachEvent( 'onsubmit', cAutocomplete.onFormSubmit )
		}
		else if( hForm.addEventListener )
		{
			hForm.addEventListener( 'submit', cAutocomplete.onFormSubmit, false )
		}
	}
}

cAutocomplete.prototype.createList = function()
{
	var hInput = document.getElementById( this.sInputId )

	var hContainer = document.getElementById( this.sListId )
	if( hContainer )
	{
		hContainer.parentNode.removeChild( hContainer )
	}
	
	var hContainer = document.createElement( 'DIV' )
	hContainer.className = 'autocomplete_holder'
	hContainer.id = this.sListId
	hContainer.style.zIndex = 10000 + cAutocomplete.nCount
	hContainer.hAutocomplete = this
	
	var hFirstBorder =  document.createElement( 'DIV' )
	hFirstBorder.className = 'autocomplete_firstborder'
	var hSecondBorder =  document.createElement( 'DIV' )
	hSecondBorder.className = 'autocomplete_secondborder'

	var hList = document.createElement( 'UL' )
	hList.className = 'autocomplete'
	
	var hListItem = null
	var hListItemLink = null
	var hArrKey = null
	var sArrEl = null

	if( hInput.type.toLowerCase() == 'text' )
	{
		var hArr = this.getListArray()
		var nI = 0
		for( hArrKey in hArr )
		{
			sArrEl = hArr[ hArrKey ]
			hListItem = document.createElement( 'LI' )
			hListItemLink = document.createElement( 'A' )
			hListItemLink.href = '#'
			hListItemLink.innerHTML = sArrEl
			hListItemLink.realText = sArrEl
			hListItem.appendChild( hListItemLink )
			hList.appendChild( hListItem )
			this.aSearchData[ nI++ ] = sArrEl.toString().toLowerCase()
		}
	}
	else if( hInput.type.toLowerCase() == 'select-one' )
	{
		for( var nI = 0; nI < hInput.options.length; nI++ )
		{
			hArrKey = hInput.options.item( nI ).value
			sArrEl = hInput.options.item( nI ).text
			hListItem = document.createElement( 'LI' )
			hListItemLink = document.createElement( 'A' )
			hListItemLink.setAttribute( 'itemvalue', hArrKey )
			hListItemLink.href = '#'
			hListItemLink.innerHTML = sArrEl
			hListItemLink.realText = sArrEl
			hListItem.appendChild( hListItemLink )
			hList.appendChild( hListItem )
			this.aSearchData[ nI ] = sArrEl.toString().toLowerCase()
		}
	}

	hSecondBorder.appendChild( hList )
	hFirstBorder.appendChild( hSecondBorder )
	hContainer.appendChild( hFirstBorder )
	document.body.appendChild( hContainer )
	
	if( hContainer.attachEvent ) 
	{
		hContainer.attachEvent( 'onblur', cAutocomplete.onListBlur )
		hContainer.attachEvent( 'onfocus', cAutocomplete.onListFocus )
	}
	else if( hInput.addEventListener )
	{
		hContainer.addEventListener( 'blur', cAutocomplete.onListBlur, false )
		hContainer.addEventListener( 'focus', cAutocomplete.onListFocus, false )
	}

	
	if( hContainer.attachEvent ) 
	{
		hContainer.attachEvent( 'onclick', cAutocomplete.onItemClick )
	}
	else if( hContainer.addEventListener )
	{
		hContainer.addEventListener( 'click', cAutocomplete.onItemClick, false )
	} 
}

cAutocomplete.prototype.hasOptionsAvailable = function( sStartsWith )
{
	var hArr = this.getListArray()
	for( hArrKey in hArr )
	{
		if( ( hArr[ hArrKey ].indexOf( sStartsWith ) == 0 ) && ( hArr[ hArrKey ].length > sStartsWith.length ) )
		{
			return true
		}
	}
	return false
}

cAutocomplete.prototype.getListArray = function()
{
	var hInput = document.getElementById( this.sInputId )
	var sAA = hInput.getAttribute( 'autocomplete_list' )
	var sAAS = hInput.getAttribute( 'autocomplete_list_sort' )
	var hArr = null

	if( sAA.indexOf( 'array:' ) >= 0 )
	{
		hArr = eval( sAA.substring( 6 ) )
	}
	else if(  sAA.indexOf( 'list:' ) >= 0 )
	{
		hArr = sAA.substring( 5 ).split( '|' )
	}
	else if(  sAA.indexOf( 'url:' ) >= 0 )
	{
		hArr = this.loadListArray( this.sActiveValue )
	}
	
	if( sAAS != null )
	{
		this.bSorted = true
		return hArr.sort()
	}
	else
	{
		return hArr
	}
}

cAutocomplete.prototype.getListArrayType = function()
{
	var hInput = document.getElementById( this.sInputId )
	var sAA = hInput.getAttribute( 'autocomplete_list' )
	if( sAA != null && sAA.length > 0 )
	{
		if( sAA.indexOf( 'array:' ) >= 0 )
		{
			return 'array'
		}
		else if(  sAA.indexOf( 'list:' ) >= 0 )
		{
			return 'list'
		}
		else if(  sAA.indexOf( 'url:' ) >= 0 )
		{
			return 'url'
		}
	}
}

cAutocomplete.prototype.getListURL = function()
{
	var hInput = document.getElementById( this.sInputId )
	var sAA = hInput.getAttribute( 'autocomplete_list' )
	if( sAA != null && sAA.length > 0 )
	{
		if(  sAA.indexOf( 'url:' ) >= 0 )
		{
			return sAA.substring( 4 )
		}
	}
}

//use this function to change the list of autocomplete values to a new one
//supply as an argument the name as a literal of an JS array object
cAutocomplete.prototype.setListArray = function( sArrayName )
{
	var hInput = document.getElementById( this.sInputId )
	var sAA = hInput.setAttribute( 'autocomplete_list', 'array:'+sArrayName )
	this.createList()
}

cAutocomplete.prototype.setListURL = function( sURL )
{
	var hInput = document.getElementById( this.sInputId )
	var sAA = hInput.setAttribute( 'autocomplete_list', 'url:'+sURL )
	this.createList();
	this.sListURL = sURL;
}

cAutocomplete.prototype.loadListArray = function( sData )
{
	var hInput = document.getElementById( this.sInputId )
	// added for PGV place edition :
	//alert(element_id);
	var ctry = document.getElementsByName(element_id+'_PLAC_CTRY')[0];
	var stae = document.getElementsByName(element_id+'_PLAC_STAE')[0];
	var cnty = document.getElementsByName(element_id+'_PLAC_CNTY')[0];
    if (ctry) sData += '&ctry='+strclean(ctry.value.substr(0,3).toUpperCase());
    if (stae) sData += '&stae='+strclean(stae.value);
    if (cnty) sData += '&cnty='+strclean(cnty.value);
    sData += '&s=';
    // end
	this.hXMLHttp.open( 'GET', this.sListURL + sData, false )
	this.hXMLHttp.send( null )
	return( this.hXMLHttp.responseText.split('|') )
}


cAutocomplete.prototype.showList = function()
{
	if( cAutocomplete.hListDisplayed )
	{
		cAutocomplete.hListDisplayed.clearList()
	}
	var hInput = document.getElementById( this.sInputId )
	var nTop = getObject.getSize( 'offsetTop', hInput )
	var nLeft = getObject.getSize( 'offsetLeft', hInput )
	var hContainer = document.getElementById( this.sListId )
	
	
	var hList = hContainer.getElementsByTagName( 'UL' )[ 0 ]
	if( this.bHasButton )
	{
		hContainer.style.width = document.getElementById( this.sInputId ).parentNode.offsetWidth
	}
	else
	{
		hContainer.style.width = document.getElementById( this.sInputId ).offsetWidth
	}
	var nNumLines = ( this.nItemsDisplayed < cAutocomplete.CN_NUMBER_OF_LINES ) ? this.nItemsDisplayed : cAutocomplete.CN_NUMBER_OF_LINES;
	hList.style.height = nNumLines * cAutocomplete.CN_LINE_HEIGHT + cAutocomplete.CN_HEIGHT_FIX + 'px'
	
	hContainer.style.top = nTop + hInput.offsetHeight + cAutocomplete.CN_OFFSET_TOP + 'px'
	hContainer.style.left = nLeft + cAutocomplete.CN_OFFSET_LEFT + 'px'

	hContainer.style.display = 'none'
	hContainer.style.visibility = 'visible'
	hContainer.style.display = 'block'

	cAutocomplete.hListDisplayed = this
	this.bListDisplayed = true
}

cAutocomplete.prototype.binarySearch = function( sFilter )
{
	var nLow = 0
	var nHigh = this.aSearchData.length - 1
	var nMid
	var nTry, nLastTry
	var sData
	var nLen = sFilter.length

	var lastTry

	while ( nLow <= nHigh )
	{
		nMid = ( nLow + nHigh ) / 2
		nTry = ( nMid < 1 ) ? 0 : parseInt( nMid )
	
		sData = this.aSearchData[ nTry ].substr( 0, nLen ).toLowerCase()

		if ( sData < sFilter ) 
		{
			nLow = nTry + 1
			continue
		}
		if ( sData > sFilter ) 
		{
			nHigh = nTry - 1
			continue
		}
		if ( sData == sFilter ) 
		{
			nHigh = nTry - 1
			nLastTry = nTry
			continue
		}
		
		return nTry
	}

	if ( typeof ( nLastTry ) != "undefined" ) 
	{
		return nLastTry
	} 
	else 
	{
		return null
	}
}

cAutocomplete.prototype.filterOptions = function( bShowAll )
{
	if( this.hActiveSelection )
	{
		this.hActiveSelection.className = ''
	}
	if( typeof bShowAll == 'undefined' )
	{
		bShowAll = false
	}

	var hInput = document.getElementById( this.sInputId )
	this.sActiveValue = hInput.value
	
	if( this.sLastActiveValue == this.sActiveValue )
	{
		this.nItemsDisplayed = this.aSearchData.length
		if( !this.bListDisplayed )
		{
			this.showList()
			this.deselectOption()
		}
		return
	}
	
	if( this.bRemoteList )
	{
		hInput.className = 'search'
		this.createList()
		hInput.className = ''
	}
	
	var sStartWith = this.sActiveValue
	if( bShowAll )
	{
		sStartWith = ''
	}
	
	var hContainer = document.getElementById( this.sListId )

	var hList = hContainer.getElementsByTagName( 'UL' )[ 0 ]
	var nItemsLength = hList.getElementsByTagName( 'LI' ).length
	var hLinkItem = null
	var hLastDisplayed = null
	var hFirstDisplayed = null
	var nCount = 0
	
	if( sStartWith.length == 0 )
	{
		for( var nI = 0; nI < nItemsLength; nI++ )
		{
			if( this.formatOptions )
			{
				hList.getElementsByTagName( 'LI' )[ nI ].getElementsByTagName( 'A' )[ 0 ].innerHTML = this.formatOptions( hList.getElementsByTagName( 'LI' )[ nI ].getElementsByTagName( 'A' )[ 0 ].realText )
			}
			hList.getElementsByTagName( 'LI' )[ nI ].style.display = 'block'
		}
		nCount = nItemsLength
		hFirstDisplayed = hList.getElementsByTagName( 'LI' )[ 0 ]
		hLastDisplayed = hList.getElementsByTagName( 'LI' )[ nItemsLength - 1 ]
	}
	else
	{
		sStartWith = sStartWith.toLowerCase()
		var nStartAt = this.binarySearch( sStartWith )
		var bEnd = false
		if( this.bSorted && this.bMatchBegin )
		{
			for( var nI = 0; nI < nItemsLength; nI++ )
			{
				hList.getElementsByTagName( 'LI' )[ nI ].style.display = 'none'
				if( nI >= nStartAt && !bEnd )
				{
					if( !bEnd && this.aSearchData[ nI ].indexOf( sStartWith ) != 0 )
					{
							bEnd = true
							continue
					}
					if( this.formatOptions )
					{
						hList.getElementsByTagName( 'LI' )[ nI ].getElementsByTagName( 'A' )[ 0 ].innerHTML = this.formatOptions( hList.getElementsByTagName( 'LI' )[ nI ].getElementsByTagName( 'A' )[ 0 ].realText )
					}
					hList.getElementsByTagName( 'LI' )[ nI ].style.display = 'block'
					nCount++
					if( hFirstDisplayed == null )
					{
						hFirstDisplayed = hList.getElementsByTagName( 'LI' )[ nI ]
					}
					hLastDisplayed = hList.getElementsByTagName( 'LI' )[ nI ]
				}
			}
		}
		else
		{
			for( var nI = 0; nI < nItemsLength; nI++ )
			{
				hList.getElementsByTagName( 'LI' )[ nI ].style.display = 'none'
				if( ( this.bMatchBegin && this.aSearchData[ nI ].toLowerCase().indexOf( sStartWith ) == 0 ) || ( !this.bMatchBegin && this.aSearchData[ nI ].toLowerCase().indexOf( sStartWith ) >= 0 ) )
				{
					if( this.formatOptions )
					{
						hList.getElementsByTagName( 'LI' )[ nI ].getElementsByTagName( 'A' )[ 0 ].innerHTML = this.formatOptions( hList.getElementsByTagName( 'LI' )[ nI ].getElementsByTagName( 'A' )[ 0 ].realText )
					}
					hList.getElementsByTagName( 'LI' )[ nI ].style.display = 'block'
					nCount++
					if( hFirstDisplayed == null )
					{
						hFirstDisplayed = hList.getElementsByTagName( 'LI' )[ nI ]
					}
					hLastDisplayed = hList.getElementsByTagName( 'LI' )[ nI ]
				}
			}
		}
	}
	
	this.nItemsDisplayed = nCount
	if( nCount > 0 )
	{
		this.deselectOption()
		if( this.bAutoComplete )
		{
			this.selectOption( hFirstDisplayed.getElementsByTagName( 'A' )[ 0 ] )
		}
		this.showList()
	}
	else
	{
		this.clearList()
	}
	this.sLastActiveValue = this.sActiveValue
}


cAutocomplete.prototype.hideOptions = function()
{
	var hContainer = document.getElementById( this.sListId )
	hContainer.style.visibility = 'hidden'
	cAutocomplete.hListDisplayed = null
}


cAutocomplete.prototype.autocompleteValue = function( sValue )
{
	var hInput = document.getElementById( this.sInputId )
	//var sVal = hInput.value.toLowerCase()
	var sVal = hInput.value
	hInput.value = sValue
	if( hInput.createTextRange )
	{
		hRange = hInput.createTextRange()
		if( sVal.length < sValue.length && hRange.findText( sValue.substr( sVal.length ) ) )
		{
			hRange.select()
		}
	}
	else
	{
		hInput.setSelectionRange( sVal.length, sValue.length )
	}
	this.bAutocompleted = true
}

cAutocomplete.prototype.selectOption = function( hNewOption )
{
	if( this.hActiveSelection )
	{
		if( this.hActiveSelection == hNewOption )
		{
			return
		}
		else
		{
			this.hActiveSelection.className = ''
		}
	}
	this.hActiveSelection = hNewOption
	var hInput = document.getElementById( this.sInputId )
	if( this.hActiveSelection != null )
	{
		this.hActiveSelection.className = 'selected'
		if( this.bAutoComplete )
		{
			this.autocompleteValue( this.hActiveSelection.realText )
		}
		else
		{
			hInput.value = this.hActiveSelection.realText
		}

		this.sActiveValue = hInput.value
		this.sLastActiveValue = hInput.value

		if( this.sHiddenInputId != null )
		{
			document.getElementById( this.sHiddenInputId ).value = this.hActiveSelection.getAttribute( 'itemvalue' )
		}
		
		/*
		if( this.onSelect )
		{
			this.onSelect()
		}
		*/
	}
	else
	{
		hInput.value = this.sActiveValue
	}
}

cAutocomplete.prototype.deselectOption = function( )
{
	if( this.hActiveSelection != null )
	{
		this.hActiveSelection.className = ''
		this.hActiveSelection = null
	}
}

cAutocomplete.prototype.clearList = function( bItemSelected )
{
	if( this.onSelect && bItemSelected )
	{
		this.onSelect()
	}
	this.deselectOption()
	this.hideOptions()
	this.bListDisplayed = false
}

cAutocomplete.prototype.getPrevDisplayedItem = function( hItem )
{
	if( hItem == null )
	{
		var hContainer = document.getElementById( this.sListId )
		hItem = hContainer.getElementsByTagName( 'UL' )[ 0 ].childNodes.item( hContainer.getElementsByTagName( 'UL' )[ 0 ].childNodes.length - 1 )
	}
	else
	{
		hItem = getPrevNodeSibling( hItem.parentNode )
	}
	while( hItem != null )
	{
		if( hItem.style.display == 'block' )
		{
			return hItem
		}
		hItem = hItem.previousSibling
	}
	return null
}

cAutocomplete.prototype.getNextDisplayedItem = function( hItem )
{
	if( hItem == null )
	{
		var hContainer = document.getElementById( this.sListId )
		hItem = hContainer.getElementsByTagName( 'UL' )[ 0 ].childNodes.item( 0 )
	}
	else
	{
		hItem =  getNextNodeSibling( hItem.parentNode )
	}
	while( hItem != null )
	{
		if( hItem.style.display == 'block' )
		{
			return hItem
		}
		hItem = hItem.nextSibling
	}
	return null
}

cAutocomplete.onInputKeyDown = function ( hEvent )
{
	if( hEvent == null )
	{
		hEvent = window.event
	}
	var hElement = ( hEvent.srcElement ) ? hEvent.srcElement : hEvent.originalTarget
	var hAC = hElement.hAutocomplete
	var hContainer = document.getElementById( hAC.sListId )
	var hInput = document.getElementById( hAC.sInputId )
	var hList = hContainer.getElementsByTagName( 'UL' )[ 0 ]
	var hEl = getParentByTagName( hElement, 'A' )
	if( hContainer != null && hAC.bListDisplayed )
	{
		var hLI = null
		var hLINext = null
		//the new active selection
		if( ( hEvent.keyCode == 13 ) || ( hEvent.keyCode == 27 ) )
		{
			var bItemSelected = hEvent.keyCode == 13 ? true : false
			hAC.clearList( bItemSelected )
		}
		if( hEvent.keyCode == 38 )
		{
			//up key pressed
			hLINext = hAC.getPrevDisplayedItem( hAC.hActiveSelection )
			if( hLINext != null )
			{
				hAC.selectOption( hLINext.childNodes.item(0) )
				if( hAC.nItemsDisplayed > cAutocomplete.CN_NUMBER_OF_LINES )
				{
					if( hList.scrollTop < 5 && hLINext.offsetTop > hList.offsetHeight )
					{
						hList.scrollTop = hList.scrollHeight - hList.offsetHeight
					}
					if( hLINext.offsetTop - hList.scrollTop < 0 )
					{
						hList.scrollTop -= hLINext.offsetHeight
					}
				}
			}
			else
			{
				hAC.selectOption( null )
			}
		}
		else if ( hEvent.keyCode == 40 )
		{
			//down key pressed
			hLINext = hAC.getNextDisplayedItem( hAC.hActiveSelection )
			if( hLINext != null )
			{
				hAC.selectOption( hLINext.childNodes.item(0) )
				if( hAC.nItemsDisplayed > cAutocomplete.CN_NUMBER_OF_LINES )
				{
					if( hList.scrollTop > 0 && hList.scrollTop > hLINext.offsetTop )
					{
						hList.scrollTop = 0
					}
					if( Math.abs( hLINext.offsetTop - hList.scrollTop - hList.offsetHeight ) < 5 )
					{
						hList.scrollTop += hLINext.offsetHeight
					}
				}
			}
			else
			{
				hAC.selectOption( null )
			}
		}
	}
	if ( ( hEvent.keyCode == 13 ) && hInput.form )
	{
		hInput.form.bLocked = true
		if( hEvent.preventDefault )
		{
			hEvent.preventDefault()
		}
		hEvent.cancelBubble = true
		hEvent.returnValue = false
		return false
	}
}

cAutocomplete.onInputKeyUp = function ( hEvent )
{
	if( hEvent == null )
	{
		hEvent = window.event
	}
	var hElement = ( hEvent.srcElement ) ? hEvent.srcElement : hEvent.originalTarget
	var hAC = hElement.hAutocomplete
	//if we press the keys for up down enter or escape skip showing the list
	switch( hEvent.keyCode )
	{
		case 37	:
		case 38	:
		case 39	:
		case 13	:
		case 27	:	if( hEvent.preventDefault )
					{
						hEvent.preventDefault()
					}
					hEvent.cancelBubble = true
					hEvent.returnValue = false
					return false
					break
		case 40	:	if( hAC.bListDisplayed )
					{
						if( hEvent.preventDefault )
						{
							hEvent.preventDefault()
						}
						hEvent.cancelBubble = true
						hEvent.returnValue = false
						return false
					}
					break
		//getSel defined at the end of the file
		case 8	:	if( hAC.bAutoComplete && hAC.bAutocompleted && getSel )
					{
						hAC.bAutocompleted = false
						return false
					}
					break
	}
	if( hAC.hShowTimeout )
	{
		clearTimeout( hAC.hShowTimeout )
		hAC.hShowTimeout = null
	}
	hAC.hShowTimeout = setTimeout( hAC.hObj+'.filterOptions()', cAutocomplete.CN_SHOW_TIMEOUT )
}

cAutocomplete.onInputBlur = function( hEvent )
{
	if( hEvent == null )
	{
		hEvent = window.event
	}
	var hElement = ( hEvent.srcElement ) ? hEvent.srcElement : hEvent.originalTarget
	if( hElement.form )
	{
		hElement.form.bLocked = false
	}
	var hAC = hElement.hAutocomplete
	if( !hAC.hClearTimeout )
	{
		hAC.hClearTimeout = setTimeout( hAC.hObj+'.clearList()', cAutocomplete.CN_CLEAR_TIMEOUT )
	}
}

cAutocomplete.onInputFocus = function( hEvent )
{
	if( hEvent == null )
	{
		hEvent = window.event
	}
	var hElement = ( hEvent.srcElement ) ? hEvent.srcElement : hEvent.originalTarget
	var hAC = hElement.hAutocomplete
	if( hAC.hClearTimeout )
	{
		clearTimeout( hAC.hClearTimeout )
		hAC.hClearTimeout = null
	}
}


cAutocomplete.onListBlur = function( hEvent )
{
	if( hEvent == null )
	{
		hEvent = window.event
	}
	var hElement = ( hEvent.srcElement ) ? hEvent.srcElement : hEvent.originalTarget
	hElement = getParentByProperty( hElement, 'className', 'autocomplete_holder' )
	var hAC = hElement.hAutocomplete
	if( !hAC.hClearTimeout )
	{
		hAC.hClearTimeout = setTimeout( hAC.hObj+'.clearList()', cAutocomplete.CN_CLEAR_TIMEOUT )
	}
}

cAutocomplete.onListFocus = function( hEvent )
{
	if( hEvent == null )
	{
		hEvent = window.event
	}
	var hElement = ( hEvent.srcElement ) ? hEvent.srcElement : hEvent.originalTarget
	hElement = getParentByProperty( hElement, 'className', 'autocomplete_holder' )
	var hAC = hElement.hAutocomplete
	if( hAC.hClearTimeout )
	{
		clearTimeout( hAC.hClearTimeout )
		hAC.hClearTimeout = null
	}
}

cAutocomplete.onItemClick = function( hEvent )
{
	if( hEvent == null )
	{
		hEvent = window.event
	}
	var hElement = ( hEvent.srcElement ) ? hEvent.srcElement : hEvent.originalTarget
	var hContainer = getParentByProperty( hElement, 'className', 'autocomplete_holder' )
	var hEl = getParentByTagName( hElement, 'A' )
	if( hContainer != null )
	{
		var hAC = hContainer.hAutocomplete
		hAC.selectOption( hEl )
		document.getElementById( hAC.sInputId ).focus()
		hAC.clearList( true )
	}
	if( hEvent.preventDefault )
	{
		hEvent.preventDefault()
	} 
	hEvent.cancelBubble = true
	hEvent.returnValue = false
	return false	
}


cAutocomplete.onButtonClick = function ( hEvent )
{
	if( hEvent == null )
	{
		hEvent = window.event
	}
	var hElement = ( hEvent.srcElement ) ? hEvent.srcElement : hEvent.originalTarget
	var hAC = hElement.hAutocomplete
	var hInput = document.getElementById( hAC.sInputId )
	if( hInput.disabled )
	{	
		return
	}
	hAC.filterOptions( true )
	var hInput = document.getElementById( hAC.sInputId )
	hInput.focus()
}

cAutocomplete.onFormSubmit = function ( hEvent )
{
	if( hEvent == null )
	{
		hEvent = window.event
	}
	var hElement = ( hEvent.srcElement ) ? hEvent.srcElement : hEvent.originalTarget
	//alert( hElement.bLocked )
	if( hElement.bLocked )
	{
		hElement.bLocked = false
		hEvent.returnValue = false
		if( hEvent.preventDefault )
		{
			hEvent.preventDefault()
		} 
		return false
	}
}

function getSel() 
{
    if( window.getSelection ) 
    {
    	return window.getSelection()
    }
    else if( typeof document.selection != 'undefined' )
    {
		return document.selection
	}
}
