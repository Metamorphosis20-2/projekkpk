<?
$upload_not_allowed = $this->lang->line("upload_not_allowed");
?>
<!-- <form action="#" class="form-horizontal"> -->
    <div class="form-body">
        <div class="form-group">
            <label class="control-label col-md-3">Upload File</label>
            <div class="col-md-9">
                <div class="fileinput fileinput-new" data-provides="fileinput">
                    <div class="input-group  input-large">
                        <div class="form-control uneditable-input input-fixed input-medium" data-trigger="fileinput" style="height:40px">
                            <i class="fa fa-file fileinput-exists"></i>&nbsp;
                            <span class="fileinput-filename"> </span>
                        </div>
                        <span class="input-group-addon btn default btn-file">
                            <input type="file" name="file1" id="file1" onChange="validate(this)"> </span>
                            <!-- <a href="javascript:upload()" id="rmvFile" class="input-group-addon btn green fileinput-exists"> Unggah </a> -->
                            <a href="javascript:;" id="rmvFile" class="input-group-addon btn red fileinput-exists" data-dismiss="fileinput"> Remove </a>
                    </div>
                </div>
<!--                
                                <span class="fileinput-new"> Select file </span>
                            <span class="fileinput-exists"> Change </span>

    <div id="progressWrap" style="display: none">
                  <div class="progress progress-striped active" style="margin-bottom:0">
                      <div id="progressBar" class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                          <span class="sr-only"> </span>
                      </div>
                  </div>
                </div>
                <div id="loaded"></div>
                
                
                <div id="progressWrap2">
                  <div class="progress progress-striped active" style="margin-bottom:0">
                      <div id="progressBar2" class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                          <span class="sr-only"> </span>
                      </div>
                  </div>
                </div>
                <div id="loaded2"></div>
                <br />
                -->
                
            </div>

        </div>
    </div>

    <input type="hidden" name="v_uo_id" />
    <input type="hidden" name="v_file_type" />
    <script>
        function validate(element) {
            file = element.value;
            var ext = file.split(".");
            ext = ext[ext.length-1].toLowerCase();      
            var arrayExtensions = ["xls","XLS","xlsx","XLSX"];

            if (arrayExtensions.lastIndexOf(ext) == -1) {
                swal.fire({ 
                    title:"<?=$upload_not_allowed?>", 
                    text: "xls xlsx",
                    icon:"error"
                });

                $(element).val("");
            }
            // if(element.files[0].size > 1100000){
            //     swal({ 
            //         title:"Berkas yang diunggah terlalu besar", 
            //         text: "Maksimum ukuran berkas 1 MB",
            //         type:"error"
            //     });

            //     $(element).val("");
            // }
        }
    </script>
<!-- </form> -->