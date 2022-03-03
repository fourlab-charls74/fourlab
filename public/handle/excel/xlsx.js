function HDXls(){
}

HDXls.prototype.importExcel = function(url,gx,columns,callback = null){

    var hdxls = this;

    this.makeRequest('GET',
        //'https://www.ag-grid.com/example-excel-import/OlymicData.xlsx',
        url,
        // success
        function (data) {
            //console.log(data);
            var workbook = hdxls.convertDataToWorkbook(data);
            hdxls.importGrid(gx,workbook,columns);

            if(callback) callback();
        },
        // error
        function (error) {
            throw error;
        }
    );
};


HDXls.prototype.importGrid = function(gx,workbook,columns) {
    // our data is in the first sheet
    var firstSheetName = workbook.SheetNames[0];
    var worksheet = workbook.Sheets[firstSheetName];

    // start at the 2nd row - the first row are the headers
    var rowIndex = 2;
    var rowData = [];

    // iterate over the worksheet pulling out the columns we're expecting
    while (worksheet['A' + rowIndex]) {
        var row = {};
        Object.keys(columns).forEach(function(column) {
            //console.log(worksheet[column + rowIndex]);
            if(worksheet[column + rowIndex] !== undefined){
                //console.log(columns[column]);
                row[columns[column]] = worksheet[column + rowIndex].w || worksheet[column + rowIndex].v;
            }
        });
        rowData.push(row);
        rowIndex++;
    }
    //GridData = rowData;
    // finally, set the imported rowData into the grid
    gx.gridOptions.api.setRowData(rowData);
    $("#" + gx.gridTotal).text(numberWithCommas(rowData.length));
};

// read the raw data and convert it to a XLSX workbook
HDXls.prototype.convertDataToWorkbook = function(data) {
    /* convert data to binary string */
    var data = new Uint8Array(data);
    var arr = new Array();

    for (var i = 0; i !== data.length; ++i) {
        arr[i] = String.fromCharCode(data[i]);
    }

    var bstr = arr.join("");

    return XLSX.read(bstr, {type: "binary"});
};

HDXls.prototype.makeRequest = function(method, url, success, error) {
    var httpRequest = new XMLHttpRequest();
    httpRequest.open("GET", url, true);
    httpRequest.responseType = "arraybuffer";

    httpRequest.open(method, url);
    httpRequest.onload = function () {
        success(httpRequest.response);
    };
    httpRequest.onerror = function () {
        error(httpRequest.response);
    };
    httpRequest.send();
};


