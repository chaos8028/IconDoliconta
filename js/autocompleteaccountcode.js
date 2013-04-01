function retrieve(i,n,mode) {
  //  $( "<div/>" ).text( message ).prependTo( "#log" );
  if(mode==1) $( "#cta" ).val(n);
    if(mode==2) $( "#codcta" ).val(n);
    $( "#accountid" ).val(i);
    

}

jQuery(document).ready(function() {
      $( "#cta" ).autocomplete({
        source: "ajaxaccountcode.php?mode=2",
        minLength: 2,
        select: function( event, ui ) {
            retrieve( ui.item.id,ui.item.code,2);
        }
    });
    
    $( "#codcta" ).autocomplete({
        source: "ajaxaccountcode.php?mode=1",
        minLength: 2,
        select: function( event, ui ) {
            retrieve( ui.item.id,ui.item.label,1);
        }
    });
    
    $('#debe').focus(function() {
			
			$('#haber').val("");
		});
    $('#haber').focus(function() {
			
			$('#debe').val("");
		});
                
                
});