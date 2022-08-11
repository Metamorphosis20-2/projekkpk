<script>
    function jvCancel(pagenya){
		self.location.replace(pagenya);
	}
	function jvSave(validate=false){
		// alert(validate);
		if(validate){
			validator
			.validate()
			.then(function(status){
				if(status!="Invalid"){
					<?=$this->common->swal2(array('title'=> $this->lang->line("save_edit_ubah") . "?",'confirm'=>true,'confirmButtonText'=>'Ya','type'=>'question','cancelButtonText'=>'Tidak','function'=>"document.formgw.submit()"))?>
				}
			})
		}else{
			<?=$this->common->swal2(array('title'=> $this->lang->line("save_edit_ubah") . "?",'confirm'=>true,'confirmButtonText'=>'Ya','type'=>'question','cancelButtonText'=>'Tidak','function'=>"document.formgw.submit()"))?>
		}
	}
</script>