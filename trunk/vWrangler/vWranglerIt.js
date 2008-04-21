/* vWranglerIt version 0.02
 * - If text link, open in ThickBox
 * 
 * Initialize the containing DIV and store base information in aDIVs
 * 
 * 
   Copyright 2008  Enric Teller  (email: enric@vpip.org)
   License (GPL License)
   ===================================================================
     This file is part of "video Wrangler".

    video Wrangler is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    video Wrangler is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Foobar.  If not, see <http://www.gnu.org/licenses/>.
   ===================================================================
 * 
 */
 
function vWranglerIt() {
	if (typeof vWranglerPlayer.isMovieFile == "function") {
	   var oLinks;
		var i, j;
		
	   oLinks = document.getElementsByTagName("a");
	   for (i = 0; i < oLinks.length; i++) {
	   		if (oLinks[i].onclick == undefined || oLinks[i].onclick == null) {
		   		var movieType = vWranglerPlayer.isMovieFile(oLinks[i]);
			   	
		      	if (movieType != null) {
			      	if (movieType.sMediaFormat.length > 0) {
						var byImage = false;
		   				var children = oLinks[i].childNodes;
		   				var imgChild;
						for (j=0; j < children.length; j++) {
							if (children[j].nodeName.toLowerCase() == "img") {
								imgChild = children[j];
								byImage = true;
								break;
							}
						}
						if (byImage) {
							var videoWidth = imgChild.width;
							var videoHeight = imgChild.height;
							oLinks[i].onclick = new Function("vWrangler(this,'width=" + videoWidth + ",height=" + videoHeight + "'); return false;");
						}
						else {
							oLinks[i].onclick = new Function("vWrangler(this, '', '', 'active=true'); return false;");
						}
			      	}
				}
	   		}
		}
	}
}


function addEvent(obj, evType, fn){
	if (obj.addEventListener) {
		obj.addEventListener(evType, fn, false);
		return true;
	} else if (obj.attachEvent){
		var r = obj.attachEvent("on"+evType, fn);
		return r;
	} else {
		return false;
	}
}

//Run vWranglerInit on body load
addEvent(window, 'load', vWranglerIt);
