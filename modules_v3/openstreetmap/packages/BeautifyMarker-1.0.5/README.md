## BeautifyMarker

  Leaflet.BeautifyIcon, a lightweight plugin that adds colorful iconic markers without images for Leaflet by giving full control of style to end user ( i.e. unlimited colors and many more...). It has also ability to adjust font awesome
  and glyphicon icons. Click here for <a href="http://marslan390.github.io/BeautifyMarker">Demo</a>
  
  <div style="text-align: center;"><img src="images/img-demo.PNG" alt="Smiley face"></div>

## JSFiddle Demo
<a href="https://jsfiddle.net/MuhammadArslan/faqok0c9/219/">JSFiddle Demo</a>
  
## Prerequisities
  <ul>
  <li>Font Awesome Icons 4.6.1</li>
  <li>Latest Leaflet Library</li>
  </ul>

## Installing
Add files in following order
<div id="beautify-installing">
1- Font Awesome CSS </br>
2- Bootstrap CSS </br>
3- leaflet-beautify-marker-icon.css</br>
4- leaflet-beautify-marker-icon.js
</div>

## Usage

Create markers as usual with Leaflet with ``L.BeautifyIcon.icon`` using available options from below. Example:

```
options = {
    icon: 'leaf',
    iconShape: 'marker'
};
L.marker([48.13710, 11.57539], {
    icon: L.BeautifyIcon.icon(options),
    draggable: true
}).addTo(map).bindPopup("popup").bindPopup("This is a BeautifyMarker");

```

## Properties
<table>
<thead>
<th>Property</th>
<th>Description</th>
<th>Type</th>
<th>Default</th>
<th>Possible</th>
</thead>
<tbody>
<tr>
<td>icon</td>
<td>Name of icon you want to show on marker</td>
<td>string</td>
<td>leaf</td>
<td>See glyphicons or font-awesome</td>
</tr>
<tr>
<td>iconSize</td>
<td>Size of marker icon</td>
<td><a href="http://leafletjs.com/reference.html#point">Point</a></td>
<td>[22, 22]</td>
<td><a href="http://leafletjs.com/reference.html#icon-options">Icon Options</a></td>
</tr>
<tr>
<td>iconAnchor</td>
<td>Anchor size of marker</td>
<td><a href="http://leafletjs.com/reference.html#point">Point</a></td>
<td>[11, 10]</td>
<td><a href="http://leafletjs.com/reference.html#icon-options">Icon Options</a></td>
</tr>
<tr>
<td>iconShape</td>
<td>Different shapes of marker icon</td>
<td>string</td>
<td>circle</td>
<td>marker, circle-dot, rectangle, rectangle-dot, doughnut</td>
</tr>
<tr>
<td>iconStyle</td>
<td>Give any style to marker div</td>
<td>string</td>
<td>''</td>
<td>Any CSS style</td>
</tr>
<tr>
<td>innerIconAnchor</td>
<td>Anchor size of font awesome or glyphicon with respect to marker</td>
<td><a href="http://leafletjs.com/reference.html#point">Point</a></td>
<td>[0, 3]</td>
<td><a href="http://leafletjs.com/reference.html#icon-options">Icon Options</a></td>
</tr>
<tr>
<td>innerIconStyle</td>
<td>Give any style to font awesome or glyphicon (i.e. HTML i tag)</td>
<td>string</td>
<td>''</td>
<td>Any CSS style</td>
</tr>
<tr>
<td>isAlphaNumericIcon</td>
<td>This tells either you want to create marker with icon or text</td>
<td>bool</td>
<td>false</td>
<td>true</td>
</tr>
<tr>
<td>text</td>
<td>If isAlphaNumericIcon property set to true, then this property use to add text</td>
<td>string</td>
<td>1</td>
<td>Any text you want to display on marker</td>
</tr>
<tr>
<td>borderColor</td>
<td>Border color or marker icon</td>
<td>string</td>
<td>#1EB300</td>
<td>Use any color with name or its code</td>
</tr>
<tr>
<td>borderWidth</td>
<td>Border width of marker icon</td>
<td>Number</td>
<td>2</td>
<td>Any number according to your requirement</td>
</tr>
<tr>
<td>borderStyle</td>
<td>Border style of marker icon</td>
<td>string</td>
<td>solid</td>
<td><a href="http://www.w3schools.com/css/css_border.asp">CSS Border Styles</a></td>
</tr>
<tr>
<td>backgroundColor</td>
<td>Background color of marker icon</td>
<td>string</td>
<td>white</td>
<td>Use any color with name or its code</td>
</tr>
<tr>
<td>textColor</td>
<td>Text color of marker icon</td>
<td>string</td>
<td>white</td>
<td>Use any color with name or its code</td>
</tr>
<tr>
<td>customClasses</td>
<td>Additional custom classes in the created <i>tag</i></td>
<td>string</td>
<td>''</td>
<td>Use any class(es) name</td>
</tr>
<tr>
<td>spin</td>
<td>Either font awesome or glypicon spin or not</td>
<td>bool</td>
<td>false</td>
<td>true</td>
</tr>
<tr>
<td>prefix</td>
<td>According to icon library</td>
<td>string</td>
<td>fa</td>
<td>glyphicon</td>
</tr>
<tr>
<td>html</td>
<td>Create marker by giving own html</td>
<td>string</td>
<td>''</td>
<td>HTML</td>
</tr>
</tbody>
</table>
  
## Supported Icons
All font awesome and glypicons

## Version
Current version of L.BeautyMarker is 1.0
