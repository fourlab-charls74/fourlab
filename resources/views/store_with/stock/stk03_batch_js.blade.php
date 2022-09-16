<script>
    // 업로드한 파일 grid에 적용
    function upload() {
        // if(basic_info.sgr_date !== undefined && !confirm("새로 적용하시는 경우 기존정보는 저장되지 않습니다.\n적용하시겠습니까?")) return;
        
		// const file_data = $('#excel_file').prop('files')[0];
        // if(!file_data) return alert("적용할 파일을 선택해주세요.");

		// const form_data = new FormData();
        // form_data.append('cmd', 'import');
		// form_data.append('file', file_data);
		// form_data.append('_token', "{{ csrf_token() }}");

        // alert("엑셀파일을 적용하고 있습니다. 잠시만 기다려주세요.");
        
        // axios({
        //     method: 'post',
        //     url: '/store/cs/cs02/batch-import',
        //     data: form_data,
        //     headers: {
        //         "Content-Type": "multipart/form-data",
        //     }
        // }).then(async (res) => {
        //     gx.gridOptions.api.setRowData([]);
        //     if (res.data.code == 1) {
        //         const file = res.data.file;
        //         await importExcel("/" + file);
        //     } else {
        //         console.log(res.data.message);
        //     }
        // }).catch((error) => {
        //     console.log(error);
        // });
    }

    // 일괄판매등록
    function save() {

    }
</script>