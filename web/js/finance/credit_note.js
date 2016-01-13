/**
 * Created by tunde <tunde@cottacush.com> on 1/13/16.
 */
 $("#viewCreditNoteDetails").click(function (){
     var data = this.dataset;
     var company_name = data['company_name'];
     var credit_note_no = data['credit_note_no'];
     $.get('getcreditnoteparcels',{credit_note_no:credit_note_no,company_name:company_name},'html').success(function(response){
         $('#viewInvoice').html(response);
     }).error(function(){
         $('#viewInvoice').html("");
     })
 });