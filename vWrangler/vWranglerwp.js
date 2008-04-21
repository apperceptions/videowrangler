/*
 * vWranglerwp version 0.02
 * Helper functions for vWrangler wordpress plugin
 * 
 * Changed Vlogsplosion to MediaEntry
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

			function setDisplayOrder(sSelect, frmName, tableID) 
			{
				
				iSelectedOrder = document.forms[frmName].elements[sSelect].selectedIndex;
				aSelects = jQuery("#" + tableID).find("select");
				
				//Setup sort
				aSorted = sortOrderSelect(aSelects, sSelect);
				iPriorSelected = getMissingIndex(aSorted, aSelects.length);
				//If selecting down, decrement up, if selected up, increment down
				iMove = iSelectedOrder > iPriorSelected?-1:1;
				iStart = iSelectedOrder;
				iEnd = iPriorSelected;

				// Perform rotation sort
				i = iStart;
				while (i != iEnd)
				{
					//Leave selected choice
					//if (aSorted[i].name != sSelect)
					//{
						document.forms[frmName].elements[aSorted[i].name].selectedIndex += iMove;
					//}

					i += iMove;
				}
				sortvWranglerMediaEntryTable(jQuery("#" + tableID).find("tr"), frmName, tableID);
			}

			function sortOrderSelect(aSelects, sSelect)
			{
				aSorted = Array();
				for (i=0; i<aSelects.length; i++)
				{
					//Bypass selected choice
					if (aSelects[i].name != sSelect)
					{
						oData = new Object();
						oData.selectedIndex = aSelects[i].selectedIndex;
						oData.name = aSelects[i].name;
						aSorted[aSelects[i].selectedIndex] = oData;
					}
				}
				
				return aSorted;
			}

			function getMissingIndex(aArray, iLength) 
			{
				for (i=0; i<iLength; i++)
				{
					if (typeof(aArray[i]) == "undefined")
						return i;
				}
				
			}

			function sortvWranglerMediaEntryTable(aTRs, frmName, tableID) 
			{
				//Change this to recreate and replace the table object
				var oldTable = jQuery("#" + tableID + " >table");
				var newTable = oldTable.clone(false);
				for (i=0; i<oldTable[0].childNodes.length; i++)
				{
					if (oldTable[0].childNodes[i].nodeName == "TBODY")
						break;
					var tableElem = oldTable[0].childNodes[i].cloneNode(true);
					newTable[0].appendChild(tableElem);

				}

				// Create the TBODY.
				var newTBody = document.createElement("TBODY");
				//Add into TBODY up to and including id="tblMediaTitle"
				iOffset = 0;
				while (true)
				{
					newTBody.appendChild(aTRs[iOffset]);
					iOffset++;
					if (aTRs[iOffset-1].id == "tblMediaTitle")
						break;
				}

				aSortedTRs = getSortedTRs(aTRs, iOffset, tableID);

				// TBODY with TR nodes.
				for (i=0; i<aSortedTRs.length; i++)
				{
					newTBody.appendChild(aSortedTRs[i][0]);
				}
 				newTable[0].appendChild(newTBody);

				//Clear old childNodes
				while (oldTable[0].childNodes.length > 0)
				{
					oldTable[0].removeChild(oldTable[0].childNodes[0]);
				}

				// Add new childNodes
				for (i=0; i<newTable[0].childNodes.length; i++)
				{
					oldTable[0].appendChild(newTable[0].childNodes[i].cloneNode(true));
				}

				//Set <SELECT>...</SELECT> in order
				for (i=0; i<aSortedTRs.length; i++)
				{
					document.forms[frmName].elements[aSortedTRs[i][1].name].selectedIndex = aSortedTRs[i][1].selectedIndex;
				}
			}


			function getSortedTRs(aTRs, iOffset, tableID) 
			{
				var oSelects = jQuery("#" + tableID).find("select");

				var aSortedTRs = new Array();
				for (i=iOffset; i<oSelects.length+iOffset; i++)
				{
					var oSelect = oSelects[i-iOffset] //jQuery("#" + sID);
					var oData = new Object();
					oData.name = oSelect.name;
					oData.selectedIndex = oSelect.selectedIndex;

					aSortedTRs[oSelect.selectedIndex] = new Array(aTRs[i].cloneNode(true), oData);
 
				}
				return aSortedTRs;
			}

