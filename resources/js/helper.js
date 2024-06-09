export const helper = {
    ConvertDateToStr: (value) => {
        var newVal = value;
        if(value instanceof Date) {
            var day = value.getDate() < 10 ? "0" + value.getDate() : value.getDate();
            var month = (value.getMonth() + 1) < 10 ? "0" + (value.getMonth() + 1) : (value.getMonth() + 1);
            var year = value.getFullYear();
            newVal = year + "-" + month + "-" + day;
        }
        return newVal;
    },
    AddDetailRow: (gridRef, dataRow) => {
        gridRef.addrow(null, dataRow);
    },
    DeleteDetailRow: (gridRef) => {
        let selectedrowindex = gridRef.getselectedrowindex();
        let id = gridRef.getrowid(selectedrowindex);
        gridRef.deleterow(id);
    },
    ConvertDateToTime: (value) => {
        var newVal = value;
        if(value instanceof Date) {
            var hour = value.getHours() < 10 ? "0" + value.getHours() : value.getHours();
            var minute = value.getMinutes() < 10 ? "0" + value.getMinutes() : value.getMinutes();
            var designator = "";
            if(hour >= 0 && hour <= 11) {
                if(hour == 0) {
                    hour = "12";
                }
                designator = "AM";
            } else {
                if(hour != 12) {
                    hour = hour - 12;
                }
                if(hour >= 0 && hour <= 9) {
                    hour = "0" + hour;
                }
                designator = "PM";
            }
            newVal = hour + ":" + minute + " " + designator;
        }
        return newVal;
    },
    GetSelectedDataItem: (gridRef) => {
        let selectedRowIndex = gridRef.getselectedrowindex();
        let selectedRowData = gridRef.getrowdatabyid(selectedRowIndex);
        return selectedRowData;
    },
    GetGridHeight: () => {
        let app_height = document.getElementById("app").offsetHeight;
        let v_main = document.getElementsByClassName("v-main__wrap")[0];
        let v_main_height = v_main.offsetHeight;
        let container = v_main.getElementsByClassName("container")[0];
        container.style.height = '100%';
        container.firstElementChild.style.height = '100%';
        let gridHeight = Math.ceil(((v_main_height / app_height) * 100) + 1) + '%';
        return gridHeight;
    }
};

window.selectChkBox = function(chkBoxClassName) {
    var checkbox = document.getElementsByClassName(chkBoxClassName);
    for(var i = 0; i < checkbox.length; i++) {
        var isChecked = checkbox[i].checked;
        var parent = checkbox[i].offsetParent.offsetParent;
        for(var j = 0; j < parent.cells.length; j++) {
            if(isChecked) {
                if(parent.cells[j].classList.contains("jqx-fill-state-pressed")) {
                    break;
                } else {
                    parent.cells[j].classList.add("jqx-fill-state-pressed");
                }
            } else {
                if(parent.cells[j].classList.contains("jqx-fill-state-pressed")) {
                    parent.cells[j].classList.remove("jqx-fill-state-pressed");
                } else {
                    break;
                }
            }
        }
    }
};
window.selectAllChkBox = function(chkBoxClassName) {
    var isChecked = event.currentTarget.checked;
    var checkbox = document.getElementsByClassName(chkBoxClassName);
    for(var i = 0; i < checkbox.length; i++) {
        checkbox[i].checked = isChecked;
        var parent = checkbox[i].offsetParent.offsetParent;
        for(var j = 0; j < parent.cells.length; j++) {
            if(isChecked) {
                if(parent.cells[j].classList.contains("jqx-fill-state-pressed")) {
                    break;
                } else {
                    parent.cells[j].classList.add("jqx-fill-state-pressed");
                }
            } else {
                if(parent.cells[j].classList.contains("jqx-fill-state-pressed")) {
                    parent.cells[j].classList.remove("jqx-fill-state-pressed");
                } else {
                    break;
                }
            }
        }
    }
    event.stopPropagation();
};