$("#etka_o-rotulos").submit(function(event){
    var email = document.forms["etka_o-rotulos"]["o_rotulos-email"].value;
    var rotulos_email = document.getElementById("o_rotulos-email");
//    var anexo = document.forms["etka_o-rotulos"]["o_rotulos-anexo"].value;
//    var rotulos_anexo = document.getElementById("o_rotulos-anexo");
    var att = document.createAttribute("required");
    
    //anexo
    var allowed_file_size = "1048576";
    var allowed_files = ['image/png', 'image/gif', 'image/jpeg', 'image/pjpeg'];
    
    if (email == "") {
        rotulos_email.setAttributeNode(att);
        return false;
        // handle the invalid form...
        rotulosError();
    }
//    if (anexo == "") {
//        rotulos_anexo.setAttributeNode(att);
//        return false;
//        // handle the invalid form...
//        rotulosError();
//    }	
    if(window.File && window.FileReader && window.FileList && window.Blob){
        var total_files_size = 0;
        $(this.elements['file_attach[]'].files).each(function(i, ifile){
            if(ifile.value !== ""){ //continue only if file(s) are selected
                if(allowed_files.indexOf(ifile.type) === -1){ //check unsupported file
                    alert( ifile.name + " is unsupported file type!");
                    proceed = false;
                }
                total_files_size = total_files_size + ifile.size; //add file size to total size
            }
        }); 
        if(total_files_size > allowed_file_size){ 
            alert( "Make sure total file size is less than 1 MB!");
            proceed = false;
        }
	} 
    if (event.isDefaultPrevented()) {
        // handle the invalid form...
        rotulosError();
    } else {
        // everything looks good!
        event.preventDefault();
        submitRotulos();
    }
});

function submitRotulos(){
    // Initiate Variables With Form Content
    var nome = $("#o_rotulos-nome").val();
    var email = $("#o_rotulos-email").val();
    var empresa = $("#o_rotulos-empresa").val();
    var telefone = $("#o_rotulos-telefone").val();
    var largura = $("#o_rotulos-largura").val();
    var altura = $("#o_rotulos-altura").val();
    var formato = $("input[name=o_rotulos-formato]:checked").val();
    var quantidade = $("#o_rotulos-quantidade").val();
    var frente = $("#o_rotulos-frente").val();
    var verso = $("#o_rotulos-verso").val();
    var finalidade = $("#o_rotulos-finalidade").val();
    var mensagem = $("#o_rotulos-mensagem").val();
    
    var form_data = new FormData(form); //Creates new FormData object
 
    $.ajax({
        type: "POST",
        url: "./contato/orcamento/rotulos.php",
        //data: form_data,
        contentType: false,
        //dataType: "json",
        cache: false,
        processData:false,
        data: "o_rotulos-nome=" + nome + "&o_rotulos-email=" + email + "&o_rotulos-empresa=" + empresa + "&o_rotulos-telefone=" + telefone + "&o_rotulos-largura=" + largura + "&o_rotulos-altura=" + altura + "&o_rotulos-formato=" + formato + "&o_rotulos-quantidade=" + quantidade + "&o_rotulos-frente=" + frente + "&o_rotulos-verso=" + verso + "&o_rotulos-finalidade=" + finalidade + "&o_rotulos-mensagem=" + mensagem,
        success : function(data){
            if (data == "success"){
                rotulosSuccess();
            } else {
                rotulosError();
            }
        }
    });
}

function rotulosSuccess(){
    $( "#etka_o-rotulos-enviado" ).removeClass( "etka_o-rotulos-enviado" );
    $( '#o_rotulos-nome, #o_rotulos-email, #o_rotulos-empresa, #o_rotulos-telefone, #o_rotulos-largura, #o_rotulos-altura, #o_rotulos-formato, #o_rotulos-quantidade, #o_rotulos-frente, #o_rotulos-verso, #o_rotulos-finalidade, #o_rotulos-mensagem' ).val('');
}

function rotulosError(){
    $( "#etka_o-rotulos-erro" ).removeClass( "etka_o-rotulos-erro" );
}
