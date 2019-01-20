<!DOCTYPE html>
<html>
<head>
<style>
* {
  box-sizing: border-box;
}

#mySearch {
  background-image: url('https://www.w3schools.com/css/searchicon.png');
  background-position: 10px 10px;
  background-repeat: no-repeat;
  width: 100%;
  font-size: 16px;
  padding: 12px 20px 12px 40px;
  border: 1px solid #ddd;
  margin-bottom: 12px;
}

#myTable, #myTableMobo {
  border-collapse: collapse;
  width: 100%;
  border: 1px solid #ddd;
  font-size: 18px;
}

#myTable th, #myTable td, #myTableMobo th, #myTableMobo td {
  text-align: left;
  padding: 12px;
}

#myTable tr, #myTableMobo tr {
  border-bottom: 1px solid #ddd;
}

.show {display: block;}
a {
  color: blue;
  text-decoration: none; /* no underline */
}
.hide {
display:none;
}
</style>
<script
  src="https://code.jquery.com/jquery-3.3.1.min.js"
  integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
  crossorigin="anonymous"></script>
<script>
$(document).ready(function() {
  initialize();
  $("#myBudget").on("keyup", onBudgetFunc);
  $("#mySearch").on("keyup", mySearchFunc);
});

function mySearchFunc () {
  $("#mySearch").on("keyup", function() {
    value = $(this).val().toLowerCase();
    $("#myTable tr:not(.discarded) td:nth-child(1)").filter(function() {
      let toggle = value.split(" ").every(i => !i || $(this).text().toLowerCase().indexOf(i) > -1);
      $(this).parent().toggle(toggle);
    });
  });
}

function initialize(){
	bestrange = 0;
    bestrangeprice = 0;
	bestrangemark = 0;
	minPrice = 10000;
	maxPrice = 0;
	maxPriceMark = 0;
	minPriceMOBO = 10000;
	maxPriceMOBO = 0;
	bestrangesocket = "";
	bestrangemarknot = 0;
	bestrangefamily = "";
	bestSocketMOBO = 0;
}

function onKeyUpFunc() { //new input
    if (priceCPU === '') {
      $("#myTable tr").removeClass('discarded').show();
      return;
    }
	initialize();
	$("#myTable tr td:nth-child(5)").each(currentGenOnly); //cureent gen on off
	$("#myTable tr:not(.old) td:nth-child(2)").each(minPriceFunc);
	$("#myTable tr:not(.old) td:nth-child(2)").each(maxPriceFunc);
    priceLow = priceCPU * 0.9; // - 10%
	priceLow = priceLow.toFixed(2);
    priceHigh = priceCPU * 1.1; // + 10%
	priceHigh = priceHigh.toFixed(2);
	if (priceCPU > minPrice && priceCPU < maxPrice) {
		$("#myTable tr:not(.old) td:nth-child(2)").each(function(e) {
			var value = parseFloat(this.textContent.replace('$', '')); //convert price to float
			if (value >= priceLow && value <= priceHigh) { //check if in range
				$(this).closest('tr').removeClass('discarded').show();
			} else {
				$(this).closest('tr').addClass('discarded').hide(); //hide
			}
		})
		$("#myTable tr:not(.discarded,.old)").each(checkForSomeStuff);
		$("#myTable tr:not(.old)").each(checkForOtherStuff);
		$("#myTable tr:not(.discarded,.old)").each(showOnlyBest);
		$("#myTable tr:not(.discarded,.old)").each(showOnlyBest);
		if (($("#myTable tr:not(.discarded,.old) td:nth-child(1)").length) === 0) {
			$("#myTable tr:not(.old)").each(findOnlyBestNotRange);
			$("#myTable tr:not(.old)").each(showOnlyBestNotRange);
		}
	} else if (priceCPU < minPrice) {
		$("#myTable tr:not(.old)").each(minPriceFindFunc);
	} else if (priceCPU > maxPrice){
		$("#myTable tr:not(.old) td:nth-child(3)").each(maxPriceFindFunc);
		$("#myTable tr").each(maxPriceShowFunc);
	}
	moboBudgetFunc();
	onKeyUpFuncMOBO();
  }
  
function onBudgetFunc() { //new input
    if ($(this).val() === '') {
      $("#myTable tr").removeClass('discarded').show();
	  $("#myTableMobo tr").removeClass('discarded').show();
      return;
    }
	initialize();
	budget = parseFloat($(this).val())
	budget = budget.toFixed(2);
	priceCPU = parseFloat($(this).val()) * 0.65;
	priceCPU = priceCPU.toFixed(2);
	onKeyUpFunc();
  }  
  
function checkForSomeStuff(e) {
	var mark = parseFloat($(this).find("td:nth-child(3)").text());
	var price = parseFloat($(this).find("td:nth-child(2)").text().replace('$', ''));
	var value = parseFloat($(this).find("td:nth-child(4)").text());
	if (value > bestrange) {
	  bestrange = value;
	  bestrangeprice = price;
	  bestrangemark = mark;
	}
}

function checkForOtherStuff(e) {
    var mark = parseFloat($(this).find("td:nth-child(3)").text());
    var price = parseFloat($(this).find("td:nth-child(2)").text().replace('$', ''));
	if (price <= bestrangeprice && mark > bestrangemark) { //check if beat bestrange
      $(this).closest('tr').removeClass('discarded').show();
	  bestrangemark = mark;
    }
  }
  
function showOnlyBest(e) {
    var mark = parseFloat($(this).find("td:nth-child(3)").text());
	var price = parseFloat($(this).find("td:nth-child(2)").text().replace('$', ''));
	var socket = ($(this).find("td:nth-child(5)").text());
	var family = ($(this).find("td:nth-child(7)").text());
    if (mark >= bestrangemark) { //check if beat bestrange
      $(this).closest('tr').removeClass('discarded').show(); //show row
	  bestrangemark = mark;
	  bestrangeprice = price;
	  bestrangesocket = socket;
	  bestrangefamily = family;
    } else {
	  $(this).closest('tr').addClass('discarded').hide();
	}
  }

function findOnlyBestNotRange(e) {
    var mark = parseFloat($(this).find("td:nth-child(3)").text());
	var price = parseFloat($(this).find("td:nth-child(2)").text().replace('$', ''));
	var socket = ($(this).find("td:nth-child(5)").text());
	var family = ($(this).find("td:nth-child(7)").text());
    if (mark > bestrangemarknot && price <= priceCPU) { //check if beat bestrange
	  bestrangemarknot = mark;
	  bestrangeprice = price;
	  bestrangesocket = socket;
	  bestrangefamily = family;
    } else {
	  $(this).closest('tr').addClass('discarded').hide();
	}
  }  
  
function showOnlyBestNotRange(e) {
    var mark = parseFloat($(this).find("td:nth-child(3)").text());
	var price = parseFloat($(this).find("td:nth-child(2)").text().replace('$', ''));
	var socket = ($(this).find("td:nth-child(5)").text());
    if (mark === bestrangemarknot && price === bestrangeprice) { //check if beat bestrange
	  $(this).closest('tr').removeClass('discarded').show();
	  bestrangesocket = socket;
    } else {
	  $(this).closest('tr').addClass('discarded').hide();
	}
  }  
  
function minPriceFindFunc(e) {
	var price = parseFloat($(this).find("td:nth-child(2)").text().replace('$', ''));
	var socket = ($(this).find("td:nth-child(5)").text());
    if (price == minPrice) { //check if beat bestrange
      $(this).closest('tr').removeClass('discarded').show(); //show row
	  bestrangesocket = socket;
    } else {
	  $(this).closest('tr').addClass('discarded').hide();
	}
  }    

function maxPriceFindFunc(e) {
    var mark = parseFloat(this.textContent); //convert price to float
    if (mark > maxPriceMark) { //check if beat bestrange
	  maxPriceMark = mark;
    } else {
	  $(this).closest('tr').addClass('discarded').hide();
	}
  }    
  
function maxPriceShowFunc(e) {
    var mark = parseFloat($(this).find("td:nth-child(3)").text()); //convert price to float
	var socket = ($(this).find("td:nth-child(5)").text());
	var price = parseFloat($(this).find("td:nth-child(2)").text().replace('$', ''));
	var family = ($(this).find("td:nth-child(7)").text());
    if (mark == maxPriceMark) {
	  $(this).closest('tr').removeClass('discarded').show(); //show row
	  bestrangesocket = socket;
	  bestrangeprice = price;
	  bestrangefamily = family;
    } else {
	  $(this).closest('tr').addClass('discarded').hide(); //show row
	}
  }      
  
function minPriceFunc(e) {
   var price = parseFloat(this.textContent.replace('$', '')); //convert price to float
   if (price < minPrice) { //check if beat bestrange
      minPrice = price;
    }
  }  
  
function maxPriceFunc(e) {
   var price = parseFloat(this.textContent.replace('$', '')); //convert price to float
   if (price > maxPrice) { //check if beat bestrange
      maxPrice = price;
    }
  }  
  
function moboBudgetFunc () {
   priceMOBO = (budget - bestrangeprice);
   priceMOBO = priceMOBO.toFixed(2);
  }

function onKeyUpFuncMOBO() { //new input
    if (priceMOBO === '') {
      $("#myTableMobo tr").removeClass('discarded').show();
      return;
    }
	$("#myTableMobo tr").removeClass('discarded').show();
	$("#myTableMobo tr td:nth-child(3)").each(correctSocketFindFuncMOBO);
	$("#myTableMobo tr td:nth-child(4)").each(correctChipsetMOBO);
	$("#myTableMobo tr:not(.discarded) td:nth-child(2)").each(minPriceFuncMOBO);
	$("#myTableMobo tr:not(.discarded) td:nth-child(2)").each(maxPriceFuncMOBO);
    priceLowMOBO = priceMOBO * 0.9; // - 10%
	priceLowMOBO = priceLowMOBO.toFixed(2);
    priceHighMOBO = priceMOBO * 1.1; // + 10%
	priceHighMOBO = priceHighMOBO.toFixed(2);
	if (priceMOBO > minPriceMOBO && priceMOBO < maxPriceMOBO) {
		$("#myTableMobo tr:not(.discarded) td:nth-child(2)").each(function(e) {
			var value = parseFloat(this.textContent.replace('$', '')); //convert price to float
			if (value >= priceLowMOBO && value <= priceHighMOBO) { //check if in range
				$(this).closest('tr').removeClass('discarded').show();
			} else {
				$(this).closest('tr').addClass('discarded').hide(); //hide
			}
		})
		$("#myTableMobo tr:not(.discarded) td:nth-child(4)").each(checkForBestChipsetMOBO);
		console.log(bestSocketMOBO);
		$("#myTableMobo tr:not(.discarded)").each(checkForOutRangeBestChipsetMOBO);
		//$("#myTableMobo tr").each(checkForOtherStuffMOBO);
		//$("#myTableMobo tr:not(.discarded)").each(showOnlyBestMOBO);
		//$("#myTableMobo tr:not(.discarded)").each(showOnlyBestMOBO);
	} else if (priceMOBO <= minPriceMOBO) {
		$("#myTableMobo tr:not(.discarded) td:nth-child(2)").each(minPriceFindFuncMOBO);
	} else {
		//$("#myTableMobo tr td:nth-child(3)").each(maxPriceFindFuncMOBO);
		//$("#myTableMobo tr td:nth-child(3)").each(maxPriceShowFuncMOBO);
	}
  }

function minPriceFindFuncMOBO(e) {
    var price = parseFloat(this.textContent.replace('$', '')); //convert price to float
    if (price == minPriceMOBO) { //check if beat bestrange
      $(this).closest('tr').removeClass('discarded').show(); //show row
    } else {
	  $(this).closest('tr').addClass('discarded').hide();
	}
  }     
  
function checkForBestChipsetMOBO(e) {
    var chipset = this.textContent;
	if (bestrangefamily === "Coffee Lake-S" && chipset === "Z390") {
		if (6 > bestSocketMOBO) {
			bestSocketMOBO = 6;
			
		}		
	} else if (bestrangefamily === "Coffee Lake-S" && chipset === "Z370") {
		if (5 > bestSocketMOBO) {
			bestSocketMOBO = 5;
			
		}		
	} else if (bestrangefamily === "Coffee Lake-S" && chipset === "Q370") {
		if (4 > bestSocketMOBO) {
			bestSocketMOBO = 4;
			
		}
	} else if (bestrangefamily === "Coffee Lake-S" && chipset === "H370") {
		if (3 > bestSocketMOBO) {
			bestSocketMOBO = 3;
			
		}
	} else if (bestrangefamily === "Coffee Lake-S" && chipset === "B360") {
		if (2 > bestSocketMOBO) {
			bestSocketMOBO = 2;
			
		}
	} else if (bestrangefamily === "Coffee Lake-S" && chipset === "H310") {
		if (1 > bestSocketMOBO) {
			bestSocketMOBO = 1;
			
		}
	} else if (bestrangefamily === "Coffee Lake Refresh" && chipset === "Z390") {
		if (6 > bestSocketMOBO) {
			bestSocketMOBO = 6;
			
		}
	} else if (bestrangefamily === "Coffee Lake Refresh" && chipset === "Z370") {
		if (5 > bestSocketMOBO) {
			bestSocketMOBO = 5;
			
		}
	} else if (bestrangefamily === "Coffee Lake Refresh" && chipset === "Q370") {
		if (4 > bestSocketMOBO) {
			bestSocketMOBO = 4;
			
		}
	} else if (bestrangefamily === "Coffee Lake Refresh" && chipset === "H370") {
		if (3 > bestSocketMOBO) {
			bestSocketMOBO = 3;
			
		}
	} else if (bestrangefamily === "Coffee Lake Refresh" && chipset === "B360") {
		if (2 > bestSocketMOBO) {
			bestSocketMOBO = 2;
			
		}
	} else if (bestrangefamily === "Coffee Lake Refresh" && chipset === "H310") {
		if (1 > bestSocketMOBO) {
			bestSocketMOBO = 1;
			
		}
	} else if (bestrangefamily === "Kaby Lake-S" && chipset === "Z270") {
		if (11 > bestSocketMOBO) {
			bestSocketMOBO = 11;
			
		}
	} else if (bestrangefamily === "Kaby Lake-S" && chipset === "Z170") {
		if (10 > bestSocketMOBO) {
			bestSocketMOBO = 10;
			
		}
	} else if (bestrangefamily === "Kaby Lake-S" && chipset === "Q270") {
		if (9 > bestSocketMOBO) {
			bestSocketMOBO = 9;
			
		}
	} else if (bestrangefamily === "Kaby Lake-S" && chipset === "Q170") {
		if (8 > bestSocketMOBO) {
			bestSocketMOBO = 8;
			
		}
	} else if (bestrangefamily === "Kaby Lake-S" && chipset === "H270") {
		if (7 > bestSocketMOBO) {
			bestSocketMOBO = 7;
			
		}
	} else if (bestrangefamily === "Kaby Lake-S" && chipset === "H170") {
		if (6 > bestSocketMOBO) {
			bestSocketMOBO = 6;
			
		}
	} else if (bestrangefamily === "Kaby Lake-S" && chipset === "Q250") {
		if (5 > bestSocketMOBO) {
			bestSocketMOBO = 5;
			
		}
	} else if (bestrangefamily === "Kaby Lake-S" && chipset === "Q150") {
		if (4 > bestSocketMOBO) {
			bestSocketMOBO = 4;
			
		}
	} else if (bestrangefamily === "Kaby Lake-S" && chipset === "B250") {
		if (3 > bestSocketMOBO) {
			bestSocketMOBO = 3;
			
		}
	} else if (bestrangefamily === "Kaby Lake-S" && chipset === "B150") {
		if (2 > bestSocketMOBO) {
			bestSocketMOBO = 2;
			
		}
	} else if (bestrangefamily === "Kaby Lake-S" && chipset === "H110") {
		if (1 > bestSocketMOBO) {
			bestSocketMOBO = 1;
			
		}
	} else if (bestrangefamily === "Skylake" && chipset === "Z270") {
		if (11 > bestSocketMOBO) {
			bestSocketMOBO = 11;
			
		}
	} else if (bestrangefamily === "Skylake" && chipset === "Z170") {
		if (10 > bestSocketMOBO) {
			bestSocketMOBO = 10;
			
		}
	} else if (bestrangefamily === "Skylake" && chipset === "Q270") {
		if (9 > bestSocketMOBO) {
			bestSocketMOBO = 9;
			
		}
	} else if (bestrangefamily === "Skylake" && chipset === "Q170") {
		if (8 > bestSocketMOBO) {
			bestSocketMOBO = 8;
			
		}
	} else if (bestrangefamily === "Skylake" && chipset === "H270") {
		if (7 > bestSocketMOBO) {
			bestSocketMOBO = 7;
			
		}
	} else if (bestrangefamily === "Skylake" && chipset === "H170") {
		if (6 > bestSocketMOBO) {
			bestSocketMOBO = 6;
			
		}
	} else if (bestrangefamily === "Skylake" && chipset === "Q250") {
		if (5 > bestSocketMOBO) {
			bestSocketMOBO = 5;
			
		}
	} else if (bestrangefamily === "Skylake" && chipset === "Q150") {
		if (4 > bestSocketMOBO) {
			bestSocketMOBO = 4;
			
		}
	} else if (bestrangefamily === "Skylake" && chipset === "B250") {
		if (3 > bestSocketMOBO) {
			bestSocketMOBO = 3;
			
		}
	} else if (bestrangefamily === "Skylake" && chipset === "B150") {
		if (2 > bestSocketMOBO) {
			bestSocketMOBO = 2;
			
		}
	} else if (bestrangefamily === "Skylake" && chipset === "H110") {
		if (1 > bestSocketMOBO) {
			bestSocketMOBO = 1;
			
		}
	} else if (bestrangefamily === "Pinnacle Ridge" && chipset === "X470") {
		if (5 > bestSocketMOBO) {
			bestSocketMOBO = 5;
			
		}
	} else if (bestrangefamily === "Pinnacle Ridge" && chipset === "B450") {
		if (4 > bestSocketMOBO) {
			bestSocketMOBO = 4;
			
		}
	} else if (bestrangefamily === "Pinnacle Ridge" && chipset === "X370") {
		if (3 > bestSocketMOBO) {
			bestSocketMOBO = 3;
			
		}
	} else if (bestrangefamily === "Pinnacle Ridge" && chipset === "B350") {
		if (2 > bestSocketMOBO) {
			bestSocketMOBO = 2;
			
		}
	} else if (bestrangefamily === "Pinnacle Ridge" && chipset === "A320") {
		if (1 > bestSocketMOBO) {
			bestSocketMOBO = 1;
			
		}
	} else if (bestrangefamily === "Summit Ridge" && chipset === "X470") {
		if (5 > bestSocketMOBO) {
			bestSocketMOBO = 5;
			
		}
	} else if (bestrangefamily === "Summit Ridge" && chipset === "X370") {
		if (4 > bestSocketMOBO) {
			bestSocketMOBO = 4;
			
		}
	} else if (bestrangefamily === "Summit Ridge" && chipset === "B450") {
		if (3 > bestSocketMOBO) {
			bestSocketMOBO = 3;
			
		}
	} else if (bestrangefamily === "Summit Ridge" && chipset === "B350") {
		if (2 > bestSocketMOBO) {
			bestSocketMOBO = 2;
			
		}
	} else if (bestrangefamily === "Summit Ridge" && chipset === "A320") {
		if (1 > bestSocketMOBO) {
			bestSocketMOBO = 1;
			
		}
	} else if (bestrangefamily === "Raven Ridge" && chipset === "X470") {
		if (5 > bestSocketMOBO) {
			bestSocketMOBO = 5;
			
		}
	} else if (bestrangefamily === "Raven Ridge" && chipset === "X370") {
		if (4 > bestSocketMOBO) {
			bestSocketMOBO = 4;
			
		}
	} else if (bestrangefamily === "Raven Ridge" && chipset === "B450") {
		if (3 > bestSocketMOBO) {
			bestSocketMOBO = 3;
			
		}
	} else if (bestrangefamily === "Raven Ridge" && chipset === "B350") {
		if (2 > bestSocketMOBO) {
			bestSocketMOBO = 2;
			
		}
	} else if (bestrangefamily === "Raven Ridge" && chipset === "A320") {
		if (1 > bestSocketMOBO) {
			bestSocketMOBO = 1;
			
		}
	}
  }      

function checkForOutRangeBestChipsetMOBO(e) {
    var chipset = ($(this).find("td:nth-child(4)").text());
	var price = parseFloat($(this).find("td:nth-child(2)").text().replace('$', ''));
	if (bestrangefamily === "Coffee Lake-S" && chipset === "Z390") {
		if (6 >= bestSocketMOBO && price < bestSocketPrice) {
			bestSocketMOBO = 6;
			
		}		
	} else if (bestrangefamily === "Coffee Lake-S" && chipset === "Z370") {
		if (5 > bestSocketMOBO) {
			bestSocketMOBO = 5;
			
		}		
	} else if (bestrangefamily === "Coffee Lake-S" && chipset === "Q370") {
		if (4 > bestSocketMOBO) {
			bestSocketMOBO = 4;
			
		}
	} else if (bestrangefamily === "Coffee Lake-S" && chipset === "H370") {
		if (3 > bestSocketMOBO) {
			bestSocketMOBO = 3;
			
		}
	} else if (bestrangefamily === "Coffee Lake-S" && chipset === "B360") {
		if (2 > bestSocketMOBO) {
			bestSocketMOBO = 2;
			
		}
	} else if (bestrangefamily === "Coffee Lake-S" && chipset === "H310") {
		if (1 > bestSocketMOBO) {
			bestSocketMOBO = 1;
			
		}
	} else if (bestrangefamily === "Coffee Lake Refresh" && chipset === "Z390") {
		if (6 > bestSocketMOBO) {
			bestSocketMOBO = 6;
			
		}
	} else if (bestrangefamily === "Coffee Lake Refresh" && chipset === "Z370") {
		if (5 > bestSocketMOBO) {
			bestSocketMOBO = 5;
			
		}
	} else if (bestrangefamily === "Coffee Lake Refresh" && chipset === "Q370") {
		if (4 > bestSocketMOBO) {
			bestSocketMOBO = 4;
			
		}
	} else if (bestrangefamily === "Coffee Lake Refresh" && chipset === "H370") {
		if (3 > bestSocketMOBO) {
			bestSocketMOBO = 3;
			
		}
	} else if (bestrangefamily === "Coffee Lake Refresh" && chipset === "B360") {
		if (2 > bestSocketMOBO) {
			bestSocketMOBO = 2;
			
		}
	} else if (bestrangefamily === "Coffee Lake Refresh" && chipset === "H310") {
		if (1 > bestSocketMOBO) {
			bestSocketMOBO = 1;
			
		}
	} else if (bestrangefamily === "Kaby Lake-S" && chipset === "Z270") {
		if (11 > bestSocketMOBO) {
			bestSocketMOBO = 11;
			
		}
	} else if (bestrangefamily === "Kaby Lake-S" && chipset === "Z170") {
		if (10 > bestSocketMOBO) {
			bestSocketMOBO = 10;
			
		}
	} else if (bestrangefamily === "Kaby Lake-S" && chipset === "Q270") {
		if (9 > bestSocketMOBO) {
			bestSocketMOBO = 9;
			
		}
	} else if (bestrangefamily === "Kaby Lake-S" && chipset === "Q170") {
		if (8 > bestSocketMOBO) {
			bestSocketMOBO = 8;
			
		}
	} else if (bestrangefamily === "Kaby Lake-S" && chipset === "H270") {
		if (7 > bestSocketMOBO) {
			bestSocketMOBO = 7;
			
		}
	} else if (bestrangefamily === "Kaby Lake-S" && chipset === "H170") {
		if (6 > bestSocketMOBO) {
			bestSocketMOBO = 6;
			
		}
	} else if (bestrangefamily === "Kaby Lake-S" && chipset === "Q250") {
		if (5 > bestSocketMOBO) {
			bestSocketMOBO = 5;
			
		}
	} else if (bestrangefamily === "Kaby Lake-S" && chipset === "Q150") {
		if (4 > bestSocketMOBO) {
			bestSocketMOBO = 4;
			
		}
	} else if (bestrangefamily === "Kaby Lake-S" && chipset === "B250") {
		if (3 > bestSocketMOBO) {
			bestSocketMOBO = 3;
			
		}
	} else if (bestrangefamily === "Kaby Lake-S" && chipset === "B150") {
		if (2 > bestSocketMOBO) {
			bestSocketMOBO = 2;
			
		}
	} else if (bestrangefamily === "Kaby Lake-S" && chipset === "H110") {
		if (1 > bestSocketMOBO) {
			bestSocketMOBO = 1;
			
		}
	} else if (bestrangefamily === "Skylake" && chipset === "Z270") {
		if (11 > bestSocketMOBO) {
			bestSocketMOBO = 11;
			
		}
	} else if (bestrangefamily === "Skylake" && chipset === "Z170") {
		if (10 > bestSocketMOBO) {
			bestSocketMOBO = 10;
			
		}
	} else if (bestrangefamily === "Skylake" && chipset === "Q270") {
		if (9 > bestSocketMOBO) {
			bestSocketMOBO = 9;
			
		}
	} else if (bestrangefamily === "Skylake" && chipset === "Q170") {
		if (8 > bestSocketMOBO) {
			bestSocketMOBO = 8;
			
		}
	} else if (bestrangefamily === "Skylake" && chipset === "H270") {
		if (7 > bestSocketMOBO) {
			bestSocketMOBO = 7;
			
		}
	} else if (bestrangefamily === "Skylake" && chipset === "H170") {
		if (6 > bestSocketMOBO) {
			bestSocketMOBO = 6;
			
		}
	} else if (bestrangefamily === "Skylake" && chipset === "Q250") {
		if (5 > bestSocketMOBO) {
			bestSocketMOBO = 5;
			
		}
	} else if (bestrangefamily === "Skylake" && chipset === "Q150") {
		if (4 > bestSocketMOBO) {
			bestSocketMOBO = 4;
			
		}
	} else if (bestrangefamily === "Skylake" && chipset === "B250") {
		if (3 > bestSocketMOBO) {
			bestSocketMOBO = 3;
			
		}
	} else if (bestrangefamily === "Skylake" && chipset === "B150") {
		if (2 > bestSocketMOBO) {
			bestSocketMOBO = 2;
			
		}
	} else if (bestrangefamily === "Skylake" && chipset === "H110") {
		if (1 > bestSocketMOBO) {
			bestSocketMOBO = 1;
			
		}
	} else if (bestrangefamily === "Pinnacle Ridge" && chipset === "X470") {
		if (5 > bestSocketMOBO) {
			bestSocketMOBO = 5;
			
		}
	} else if (bestrangefamily === "Pinnacle Ridge" && chipset === "B450") {
		if (4 > bestSocketMOBO) {
			bestSocketMOBO = 4;
			
		}
	} else if (bestrangefamily === "Pinnacle Ridge" && chipset === "X370") {
		if (3 > bestSocketMOBO) {
			bestSocketMOBO = 3;
			
		}
	} else if (bestrangefamily === "Pinnacle Ridge" && chipset === "B350") {
		if (2 > bestSocketMOBO) {
			bestSocketMOBO = 2;
			
		}
	} else if (bestrangefamily === "Pinnacle Ridge" && chipset === "A320") {
		if (1 > bestSocketMOBO) {
			bestSocketMOBO = 1;
			
		}
	} else if (bestrangefamily === "Summit Ridge" && chipset === "X470") {
		if (5 > bestSocketMOBO) {
			bestSocketMOBO = 5;
			
		}
	} else if (bestrangefamily === "Summit Ridge" && chipset === "X370") {
		if (4 > bestSocketMOBO) {
			bestSocketMOBO = 4;
			
		}
	} else if (bestrangefamily === "Summit Ridge" && chipset === "B450") {
		if (3 > bestSocketMOBO) {
			bestSocketMOBO = 3;
			
		}
	} else if (bestrangefamily === "Summit Ridge" && chipset === "B350") {
		if (2 > bestSocketMOBO) {
			bestSocketMOBO = 2;
			
		}
	} else if (bestrangefamily === "Summit Ridge" && chipset === "A320") {
		if (1 > bestSocketMOBO) {
			bestSocketMOBO = 1;
			
		}
	} else if (bestrangefamily === "Raven Ridge" && chipset === "X470") {
		if (5 > bestSocketMOBO) {
			bestSocketMOBO = 5;
			
		}
	} else if (bestrangefamily === "Raven Ridge" && chipset === "X370") {
		if (4 > bestSocketMOBO) {
			bestSocketMOBO = 4;
			
		}
	} else if (bestrangefamily === "Raven Ridge" && chipset === "B450") {
		if (3 > bestSocketMOBO) {
			bestSocketMOBO = 3;
			
		}
	} else if (bestrangefamily === "Raven Ridge" && chipset === "B350") {
		if (2 > bestSocketMOBO) {
			bestSocketMOBO = 2;
			
		}
	} else if (bestrangefamily === "Raven Ridge" && chipset === "A320") {
		if (1 > bestSocketMOBO) {
			bestSocketMOBO = 1;
			
		}
	}
  }
  
function correctChipsetMOBO(e) {
    var chipset = this.textContent;
	if (bestrangefamily === "Coffee Lake-S" && chipset === "Z270") {
	  $(this).closest('tr').addClass('discarded').hide();
	} else if (bestrangefamily === "Coffee Lake-S" && chipset === "Z170") {
	  $(this).closest('tr').addClass('discarded').hide();
	} else if (bestrangefamily === "Coffee Lake-S" && chipset === "Q270") {
	  $(this).closest('tr').addClass('discarded').hide();;
	} else if (bestrangefamily === "Coffee Lake-S" && chipset === "Q170") {
	  $(this).closest('tr').addClass('discarded').hide();
	} else if (bestrangefamily === "Coffee Lake-S" && chipset === "H270") {
	  $(this).closest('tr').addClass('discarded').hide();
	} else if (bestrangefamily === "Coffee Lake-S" && chipset === "H170") {
	  $(this).closest('tr').addClass('discarded').hide();
	} else if (bestrangefamily === "Coffee Lake-S" && chipset === "Q250") {
	  $(this).closest('tr').addClass('discarded').hide();
	} else if (bestrangefamily === "Coffee Lake-S" && chipset === "Q150") {
	  $(this).closest('tr').addClass('discarded').hide();;
	} else if (bestrangefamily === "Coffee Lake-S" && chipset === "B250") {
	  $(this).closest('tr').addClass('discarded').hide();
	} else if (bestrangefamily === "Coffee Lake-S" && chipset === "B150") {
	  $(this).closest('tr').addClass('discarded').hide();
	} else if (bestrangefamily === "Coffee Lake-S" && chipset === "H110") {
	  $(this).closest('tr').addClass('discarded').hide();
	}
  }     
  
function minPriceFuncMOBO(e) {
   var price = parseFloat(this.textContent.replace('$', '')); //convert price to float
   if (price < minPriceMOBO) { //check if beat bestrange
      minPriceMOBO = price;
    }
  }  
  
function maxPriceFuncMOBO(e) {
   var price = parseFloat(this.textContent.replace('$', '')); //convert price to float
   if (price > maxPriceMOBO) { //check if beat bestrange
      maxPriceMOBO = price;
    }
  }  

function correctSocketFindFuncMOBO(e) {
   var socket = this.textContent;
   if (socket == bestrangesocket) { //check if beat bestrange
      $(this).closest('tr').removeClass('discarded').show();
    } else {
	  $(this).closest('tr').addClass('discarded').hide();
	}
  }    

function currentGenOnly(e) {
    var socket = this.textContent;
    if (socket === 'AM4' || socket === 'LGA1151' || socket === 'TR4' || socket === 'LGA2066') { //check if beat bestrange
		$(this).closest('tr').removeClass('old').show();
    } else {
	    $(this).closest('tr').addClass('old').hide();
	}
  }      
</script>
</head>
<body>
<h2>Home</h2>
<a href="index.php">Home</a>
<a href="cpu.php">CPU</a>
<a href="mobo.php">Motherboards</a>
<input type="number" id="myBudget" placeholder="Enter budget.." title="Type in a amount" min="0">
<?php
function db_connect() {

        // Define connection as a static variable, to avoid connecting more than once 
    static $connection;

        // Try and connect to the database, if a connection has not been established yet
    if(!isset($connection)) {
             // Load configuration as an array. Use the actual location of your configuration file
        $config = parse_ini_file('../private/config.ini'); 
        $connection = mysqli_connect($config['servername'],$config['username'],$config['password'],$config['dbname'],$config['port']);
    }

        // If connection was not successful, handle the error
    if($connection === false) {
            // Handle error - notify administrator, log to a file, show an error screen, etc.
        return mysqli_connect_error(); 
    }
    return $connection;
}

// Connect to the database
$connection = db_connect();

// Check connection
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

$sql = "SELECT name, price, id, mark, value, url, socket, family FROM cpu";
$result = $connection->query($sql);

if ($result->num_rows > 0) {
    echo "<table id='myTable'><thead><tr><th>CPU</th><th>Price</th><th>Mark</th><th>Value</th><th>Socket</th><th>Image</th></tr></thead>";
	// output data of each row
    while($row = $result->fetch_assoc()) {
        echo "<tbody><tr><td><a href='mobo.php?cpu_name=".$row["name"]."' target='_blank'>" . $row["name"]. "</a></td><td>" . $row["price"]."</td><td>" . $row["mark"]."</td><td>" . $row["value"]."</td><td>" . $row["socket"]."</td><td><img src=". $row["url"]." height='42' width='42'></td><td class='hide'>" . $row["family"]."</td></tr></tbody>";
    }
    echo "</table>";
} else {
    echo "0 results";
}

$sql = "SELECT name, price, id, socket, ramslots, maxram, chipset, url FROM motherboard";
$result = $connection->query($sql);

if ($result->num_rows > 0) {
    echo "<table id='myTableMobo'><thead><tr><th>Motherboard</th><th>Price</th><th>Socket</th><th>Chipset</th><th>Ram Slots</th><th>Max Ram</th><th>Image</th></tr></thead>";
	// output data of each row
    while($row = $result->fetch_assoc()) {
        echo "<tbody><tr data-socket='". $row['socket'] . "'><td><a href='https://au.pcpartpicker.com/product/" . $row["id"] . "' target='_blank'>" . $row["name"] . "</a></td><td class='price'>" . $row["price"] . "</td><td>" . $row["socket"] . "</td><td>" . $row["chipset"] . "</td><td>" . $row["ramslots"] . "</td><td>" . $row["maxram"] . "</td><td>" . $row["value"]."</td><td><img src=". $row["url"]." height='42' width='42'></td></tr></tbody>";
    }
    echo "</table>";
} else {
    echo "0 results";
}

$connection->close();
?> 
</body>
</html>