!function() {

	var element = document.createElement('div');
	var propertyName = "boxSizing";
	var vendors = "Webkit Moz ".split(" ");
	for (var i = vendors.length - 1; i >= 0; i--) {
		var px = vendors[i];
		var cssProperty = propertyName;
		if (px.length > 0) cssProperty = px + propertyName.charAt(0).toUpperCase() + propertyName.substr(1);
		if (cssProperty in element.style) break;
	}
	if (i >= 0) return; // Native box-sizing support.

	// Generate classes.
	var columnClassList = [];
	for (var i = 99; i >= 0; i--) {
		if (i % 5 == 0 || i % 33 == 0) {
			var num = ("" + i);
			if (num.length == 1) num = "0" + num;
			columnClassList.push("Column" + num);
		}
	}

	function getDivs(classArray, node) {
		var node = node || document;
		var list = node.getElementsByTagName('div');
		var length = list.length;
		if (typeof classArray == "string") classArray = classArray.split(/\s+/);
		var classes = classArray.length;
		var result = [], i, j;
		for (i = 0; i < length; i++) {
			for (j = 0; j < classes; j++)  {
				if (list[i].className.search('\\b' + classArray[j] + '\\b') != -1) {
					result.push(list[i]);
					break;
				}
			}
		}
		return result;
	};

	function insertAfter(elem, refElem) {
		var parent = refElem.parentNode;
		var next = refElem.nextSibling;
		return (next) ? parent.insertBefore(elem, next) : parent.appendChild(elem);
	}

	function begin() {
		// 1. Old school clearfix.
		var rows = getDivs("Row");
		var clearDiv = document.createElement("div");
		var columnGap;
		clearDiv.style.clear = "both";
		for (var i = 0, length = rows.length; i < length; i++) {
			insertAfter(clearDiv.cloneNode(true), rows[i]);
			// 2. Add gaps between columns.
			var divs = [];
			for (var index = 0; index < rows[i].childNodes.length; index++) {
				var node = rows[i].childNodes[index];
				if (node && node.className && node.className.indexOf("Column") === 0) divs.push(node);
			}
			if (columnGap == undefined && divs.length > 0) {
				columnGap = parseFloat(divs[0].currentStyle.paddingLeft);
			}
			for (var k = 0, divsLength = divs.length, lastIndex = divsLength - 1; k < divsLength; k++) {
				var currentStyle = divs[k].currentStyle;
				var style = divs[k].style;
				if (k == 0) style.paddingLeft = "0"; // First of type.
				if (k == lastIndex) style.paddingRight = "0"; // Last of type.
				// var gaps = lastIndex;
				if (divsLength > 1) {
					var shrink = (1.01 * (2 * columnGap) * lastIndex) / divsLength;
					var newWidth = parseInt(currentStyle.width, 10) - shrink + "%";
					style.width = newWidth;
				}
			}
		}
	}

	document.attachEvent("onreadystatechange", function() {
		if (document.readyState == "complete") begin();
	});

}();