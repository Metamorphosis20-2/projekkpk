<!-- <link rel="stylesheet" href='<?=base_url(CSS."custom.css");?>' type="text/css"> -->
<style type="text/css">
	.jqx-menu-wrapper{
  		z-index:99999!important;
  	}
    .swal2-container {
      	z-index: 9999999;
	}

	.dropdown-menu{
  right:0 !important;
}
		/* This only works with JavaScript, 
		   if it's not present, don't show loader */
		/* .jqx-popup{
  		z-index:99999!important;
  	}
		.jqx-listbox-container{
					z-index:999999!important;
				} */

.wrapperGrid {
  display: grid;
  grid-gap: 10px;
  background-color: #fff;
  /* color: #ffba00; */
  /* border:solid #000 1px; */
  box-sizing:border-box;
  justify-items: stretch;
  width : 100%;
  height : 100%;
  padding:10px 10px 10px 10px ;
  align-content: stretch;	
}
.col1{
  grid-template-columns: auto;
}
.col2{
  grid-template-columns: repeat(2  , 1fr);
}
.col3{
  grid-template-columns: 1fr 1fr 1fr;
}
.col4{
  grid-template-columns: 1fr 1fr 1fr 1fr;
}
.row1{
  grid-template-rows: 1fr;
  grid-auto-rows: minmax(min-content, max-content);
}
.row2{
  grid-template-rows: repeat(2  , 1fr);
  grid-auto-rows: minmax(min-content, max-content);
}
.row3{
  grid-template-rows: repeat(3  , 1fr);
  grid-auto-rows: minmax(min-content, max-content);
}
.row4{
  grid-template-rows: repeat(4, 1fr);
}
.select2-results__option:empty {
    height: 40px;
}
</style>