<script type="text/javascript" charset="utf-8">
    class StoreAutoCompleteEditor {
        constructor() {
            this.rowData = [];
            this.value = {};
            this.params;
            this.gridApi;
            this.columnDefs;
            this.propertyName;
            this.cellValue;
            this.isCanceled = true;
        }

        init(params) {
            this.params = params;
            if (params.values) {
                this.rowData = params.values;
            } else {
                // 추후 api 사용하는 로직필요
                this.rowData = [{}];
            }
            this.columnDefs = params.colDef;
            this.propertyName = params.column?.colId;
            this.cellValue = params.data[this.propertyName];

            this.container = document.createElement('div');
            this.container.setAttribute('class', 'w-100');
            this.container.tabIndex = '0';

            this.input = document.createElement('input');
            this.input.setAttribute('class', 'border-0 p-1 h-100 form-control shadow-none');
            this.input.style = "width: 138px;";
            if (params.value !== undefined) this.input.value = params.value;

            this.gridDiv = document.createElement('div');
            this.gridDiv.setAttribute('id', 'div-gd-storenm');
            this.gridDiv.setAttribute('class', 'ag-theme-balham dark-grid');
            this.gridDiv.style = "height: 150px;";

            this.pApp = new App('', { gridId: '#div-gd-storenm' });
            this.gx = new HDGrid(
                this.gridDiv,
                [{ field: "store_nm", headerName: "매장을 선택하세요", width: "auto" }],
                {
                    onGridReady: (e) => {
                        this.gridApi = e.api;
                        this.gridApi.sizeColumnsToFit();
                    },
                    onCellClicked: (e) => {
                        this.value = e.data;
                        this.isCanceled = false;
                        this.params.api.stopEditing();
                    }
                }
            );
            this.gx.setRows(this.searchValues());

            this.container.appendChild(this.input);
            this.container.appendChild(this.gridDiv);
            this.container.addEventListener('keydown', (event) => {
                this.onKeyDown(event);
            });
            this.input.addEventListener('keyup', (event) => {
                this.gx.setRows(this.searchValues(event.target.value));
            });
            this.input.addEventListener('keydown', (event) => {
                if (event.key == 'ArrowUp' || event.key == 'ArrowDown') {
                    event.preventDefault();
                    if (!event.isComposing) this.navigateGrid();
                }
            });
        }

        getValue() {
            return this.value?.[this.propertyName];
        }
        getGui() {
            return this.container;
        }
        isCancelAfterEnd() {
            return this.isCanceled;
        }
        afterGuiAttached() {
            this.container.focus();
            this.input.focus();
        }
        destroy() {

        }
        isPopup() {
            return true;
        }

        searchValues(keyword = '') {
            return this.rowData.filter(row => row.store_nm.includes(keyword));
        }

        rowConfirmed() {
            if (this.gridApi.getFocusedCell() && this.gridApi.getFocusedCell().rowIndex !== undefined) {
                this.value = this.gridApi.getDisplayedRowAtIndex(this.gridApi.getFocusedCell().rowIndex).data;
                this.isCanceled = false;
            }
            this.params.api.stopEditing();
            let cell = this.params.api.getFocusedCell();
            if (cell) {
                this.params.api.setFocusedCell( cell.rowIndex, cell.column );
            }
        }

        onKeyDown(event) {
            event.stopPropagation();
            if (event.key == "Escape") {
                this.params.api.stopEditing();
                return false;
            }
            if (event.key == "Enter" || event.key == "Tab") {
                this.rowConfirmed();
            }
            if (event.key == "ArrowUp" || event.key == "ArrowDown") {
                event.preventDefault();
            }
        }

        navigateGrid() {
            if (this.gridApi.getFocusedCell() === undefined || this.gridApi.getDisplayedRowAtIndex(this.gridApi.getFocusedCell()?.rowIndex || 0)) {
                this.gridApi.setFocusedCell(this.gridApi.getDisplayedRowAtIndex(0)?.rowIndex, this.propertyName);
                this.gridApi.getDisplayedRowAtIndex(this.gridApi.getFocusedCell().rowIndex);
            } else {
                this.gridApi.setFocusedCell(this.gridApi.getFocusedCell()?.rowIndex, this.propertyName);
                this.gridApi.getDisplayedRowAtIndex(this.gridApi.getFocusedCell().rowIndex);
            }
        }
    }
</script>
