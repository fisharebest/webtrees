/**
 * @version $Id$
 * @author http://momche.net
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

//misc objects
//a simple encapsulation object

function getObject( sId )
{
	if( bw.dom )
	{
		this.hElement = document.getElementById( sId )
		this.hStyle = this.hElement.style
	}
	else if( bw.ns4 )
	{
		this.hElement = document.layers[ sId ]
		this.hStyle = this.hElement
	}
	else if( bw.ie )
	{
		this.hElement = document.all[ sId ]
		this.hStyle = this.hElement.style
	}

}

getObject.prototype.getWidth = function( )
{
	return  ( bw.ie4 ) ? this.hElement.pixelWidth : this.hElement.offsetWidth
}

getObject.prototype.getHeight = function( )
{
	return ( bw.ie4 ) ? this.hElement.pixelHeight : this.hElement.offsetHeight
}

getObject.prototype.getLeft = function()
{
	if( bw.ie4 ) return this.hElement.style.pixelLeft
	if( bw.ns4 || bw.dom )
	{
		if( this.hElement.style.left.length == 0 )
		{
			return parseInt( this.hElement.style.offsetLeft )
		}
		else
		{
			return parseInt( this.hElement.style.left )
		}
	}
}

getObject.prototype.getTop = function( )
{
	if( bw.ie4 ) return this.hElement.style.pixelTop
	if( bw.ns4 || bw.dom )
	{
		if( this.hElement.style.top.length == 0 )
		{
			return parseInt( this.hElement.style.offsetTop )
		}
		else
		{
			return parseInt( this.hElement.style.top )
		}
	}
}

getObject.getSize = function( sParam, hLayer )
{
	nPos = 0
	while( ( hLayer.tagName ) && !( /(body|html)/i.test( hLayer.tagName ) ) )
	{
		nPos += eval( 'hLayer.' + sParam )

		if( sParam == 'offsetTop' )
		{
			if( hLayer.clientTop )
			{
				nPos += hLayer.clientTop
			}
		}
		if( sParam == 'offsetLeft' )
		{
			if( hLayer.clientLeft )
			{
				nPos += hLayer.clientLeft
			}
		}

		hLayer = hLayer.offsetParent
	}
	return nPos
}



getObject.getScrollOffset = function( sParam, hLayer )
{
	nPos = 0
	while( ( hLayer.tagName ) && !( /(body|html)/i.test( hLayer.tagName ) ) )
	{
		nPos += eval( 'hLayer.scroll' + sParam )
		hLayer = hLayer.parentNode
	}
	return nPos
}
